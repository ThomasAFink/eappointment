<?php
namespace BO\Zmsentities;

class Scope extends Schema\Entity
{
    const PRIMARY = 'id';

    public static $schema = "scope.json";

    public function getProviderId()
    {
        $refString = '$ref';
        $providerId = $this->toProperty()->provider->id->get();
        $providerRef = $this->toProperty()->provider->$refString->get();
        $providerId = ($providerId) ? $providerId : preg_replace('#^.*/(\d+)/$#', '$1', $providerRef);
        if ($providerId) {
            return $providerId;
        }
        throw new Exception\ScopeMissingProvider("No reference to a provider found");
    }

    public function getNotificationPreferences()
    {
        return $this->toProperty()->preferences->notifications->get();
    }

    public function getConfirmationContent()
    {
        return $this->toProperty()->preferences->notifications->confirmationContent->get();
    }

    public function getHeadsUpContent()
    {
        return $this->toProperty()->preferences->notifications->headsUpContent->get();
    }

    public function getPreference($preferenceKey, $index, $isBool = false)
    {
        $preference = $this->toProperty()->preferences->$preferenceKey->$index->get();
        if (!$isBool && null !== $preference) {
            return $preference;
        }
        return ($isBool && null !== $preference) ? 1 : 0;
    }

    public function getStatus($statusKey, $index)
    {
        return $this->toProperty()->status->$statusKey->$index->get();
    }

    public function getContactEmail()
    {
        return $this->toProperty()->contact->email->get();
    }

    public function getName()
    {
        return $this->toProperty()->contact->name->get();
    }

    public function getScopeInfo()
    {
        $hint = explode('|', $this->hint);
        return (1 <= count($hint)) ? trim(current($hint)) : null;
    }

    public function getScopeHint()
    {
        $hint = explode('|', $this->hint);
        return (1 < count($hint)) ? trim(end($hint)) : null;
    }

    public function __toString()
    {
        $string = 'scope#';
        $string .= $this['id'];
        $string .= ' ';
        $string .= $this->getName();
        return $string;
    }
}
