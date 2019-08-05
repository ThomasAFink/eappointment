<?php
/**
 * @package Mellon
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Mellon;

/**
  * Validation of URLs
  *
  */
class ValidMail extends \BO\Mellon\ValidString
{
    /**
     * For unit tests, it might be necessary to disable DNS checks globally
     * Use \BO\Mellon\ValidMail::$disableDnsChecks = true;
     */
    public static $disableDnsChecks = false;

    /**
     * Allow only valid mails
     *
     * @param String $message error message in case of failure
     *
     * @return self
     */
    public function isMail($message = 'no valid email')
    {
        $this->isString($message);
        if ($this->value) {
            $this->validate($message, FILTER_SANITIZE_EMAIL);
            return $this->validate($message, FILTER_VALIDATE_EMAIL);
        }
        return $this;
    }

    public function hasDNS($message = 'no valid DNS entry found')
    {
        $this->validated = true;
        if ($this->value && !$this::$disableDnsChecks) {
            $domain = substr($this->value, strpos($this->value, '@') + 1);
            $hasDNS = ($domain) ? checkdnsrr($domain, 'ANY') : false;
            if (false === $hasDNS) {
                $this->setFailure($message);
            }
        }
        return $this;
    }

    public function hasMX($message = 'no valid DNS entry of type MX found')
    {
        $this->validated = true;
        if ($this->value) {
            $domain = substr($this->value, strpos($this->value, '@') + 1);
            $hasMX = checkdnsrr($domain, 'MX');
            if (false === $hasMX) {
                $this->setFailure($message);
            }
        }
        return $this;
    }
}
