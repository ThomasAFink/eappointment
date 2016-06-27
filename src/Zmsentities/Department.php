<?php

namespace BO\Zmsentities;

class Department extends Schema\Entity
{
    public static $schema = "department.json";

    public function hasId()
    {
        return (array_key_exists('id', $this)) ? true : false;
    }

    public function hasNotificationEnabled()
    {
        return ($this->preferences['notifications']['enabled']) ? true : false;
    }

    public function setNotificationPreferences($status = true)
    {
        if ($status) {
            $this->preferences['notifications']['enabled'] = 1;
        } else {
            unset($this->preferences['notifications']['enabled']);
        }
        return $this;
    }

    public function getNotificationPreferences()
    {
        return ($this->preferences['notifications']);
    }

    public function getContactPerson()
    {
        return $this->contact['name'];
    }

    public function getContact()
    {
        return new Contact($this->contact);
    }
}
