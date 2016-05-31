<?php

namespace BO\Zmsentities;

class Department extends Schema\Entity
{
    public static $schema = "department.json";

    public function hasNotificationEnabled()
    {
        return ($this->preferences['notifications']['enabled']) ? true : false;
    }

    public function getNotificationPreferences()
    {
        return ($this->preferences['notifications']);
    }
    
    public function toContact($address = array(), $contactPerson = null)
    {
        $address = explode(' ', str_replace(',', '', $address));
        $this->contact['street'] = trim($address[0]);
        $this->contact['streetNumber'] = trim($address[1]);
        $this->contact['postalCode'] = trim($address[2]);
        $this->contact['region'] = trim($address[3]);
        $this->contact['name'] = $contactPerson;
        return $this;
    }
    
    public function toAddress()
    {
        $address =
            $this->contact['street'] .' '.
            $this->contact['streetNumber'] .', '.
            $this->contact['postalCode'] .' '.
            $this->contact['region'];
        return $address;
    }
    
    public function getContactPerson()
    {
        return $this->contact['name'];
    }
}
