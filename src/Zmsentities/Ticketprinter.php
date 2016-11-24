<?php

namespace BO\Zmsentities;

use \BO\Mellon\Validator;

class Ticketprinter extends Schema\Entity
{
    const PRIMARY = 'hash';

    public static $schema = "ticketprinter.json";

    protected $allowedButtonTypes = array('s' => 'scope', 'c' => 'cluster', 'l' => 'link');

    public function getDefaults()
    {
        return [
            'enabled' => true,
        ];
    }

    public function getHashWith($organisiationId)
    {
        $this->hash = $organisiationId . bin2hex(openssl_random_pseudo_bytes(16));
        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function toStructuredButtonList()
    {
        $this->buttons = array();
        $buttonList = explode(',', $this->buttonlist);
        foreach ($buttonList as $string) {
            $button = array();
            $button = $this->getValidButtonWithType($string);
            $this->buttons[] = $this->getButtonData($string, $button);
        }
        return $this;
    }

    public function getScopeList()
    {
        $scopeList = new Collection\ScopeList();
        foreach ($this->buttons as $button) {
            if ('scope' == $button['type']) {
                $scopeList->addEntity(new Scope($button['scope']));
            }
        }
        return $scopeList;
    }

    protected function getValidButtonWithType($string)
    {
        $type = $this->getButtonType($string);
        $value = $this->getButtonValue($string, $type);
        if (! array_key_exists($type, $this->allowedButtonTypes) || ! $value) {
            throw new Exception\TicketprinterUnvalidButton();
        }
        return array(
            'type' => $this->allowedButtonTypes[$type]
        );
    }

    protected function getButtonData($string, $button)
    {
        $value = $this->getButtonValue($string, $this->getButtonType($string));
        if ('link' == $button['type']) {
            $button = $this->getExternalLinkData($value, $button);
        } else {
            $button['url'] = '/'. $button['type'] .'/'. $value .'/';
            $button[$button['type']]['id'] = $value;
        }
        return $button;
    }

    protected function getButtonValue($string, $type)
    {
        $value = ('l' == $type) ?
            Validator::value(substr($string, 1))->isString() :
            Validator::value(substr($string, 1))->isNumber();
        return $value->getValue();
    }

    protected function getButtonType($string)
    {
        return substr($string, 0, 1);
    }

    protected function getExternalLinkData($value, $button)
    {
        if (preg_match("/\[([^\]]*)\]/", $value, $matches)) {
            $data = explode('|', $matches[1]);
            $button['url'] = (isset($data[0])) ? $data[0] : '';
            $button['name'] = (isset($data[1])) ? $data[1] : "Information";
        }
        return $button;
    }
}
