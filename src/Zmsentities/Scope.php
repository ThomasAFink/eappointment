<?php
namespace BO\Zmsentities;

class Scope extends Schema\Entity
{
    const PRIMARY = 'id';

    public static $schema = "scope.json";

    public function getProviderId()
    {
        if (array_key_exists('provider', $this)) {
            if (array_key_exists('id', $this['provider'])) {
                return $this['provider']['id'];
            } elseif (array_key_exists('$ref', $this['provider'])) {
                $providerId = preg_replace('#^.*/(\d+)/$#', '$1', $this['provider']['$ref']);
                return $providerId;
            }
        }
        throw new \Exception("No reference to a provider found");
    }

    public function getNotificationPreferences()
    {
        return ($this->preferences['notifications']);
    }

    public function getConfirmationContent()
    {
        return ($this->preferences['notifications']['confirmationContent']);
    }

    public function getHeadsUpContent()
    {
        return ($this->preferences['notifications']['headsUpContent']);
    }

    public function getPreference($preferenceKey, $index, $isCheckBox = false)
    {
        if (isset($this->preferences) && array_key_exists($preferenceKey, $this->preferences)) {
            if (array_key_exists($index, $this->preferences[$preferenceKey])) {
                return ($isCheckBox) ? 1 : $this->preferences[$preferenceKey][$index];
            }
        }
        return ($isCheckBox) ? 0 : null;
    }

    public function getStatus($statusKey, $index)
    {
        if (isset($this->status) && array_key_exists($statusKey, $this->status)) {
            if (array_key_exists($index, $this->status[$statusKey])) {
                return $this->status[$statusKey][$index];
            }
        }
        return null;
    }

    public function getContactEmail()
    {
        if (isset($this->contact) && array_key_exists('email', $this->contact)) {
            return $this->contact['email'];
        }
        return null;
    }

    public function getName()
    {
        if (isset($this->contact) && array_key_exists('name', $this->contact)) {
            return $this->contact['name'];
        }
    }

    public function getScopeInfo()
    {
        $hint = explode('|', $this->hint);
        return trim(current($hint));
    }

    public function getScopeHint()
    {
        $hint = explode('|', $this->hint);
        return trim(end($hint));
    }
}
