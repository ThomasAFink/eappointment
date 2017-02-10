<?php

namespace BO\Zmsentities;

class Workstation extends Schema\Entity
{
    const PRIMARY = 'id';

    public static $schema = "workstation.json";

    public function getDefaults()
    {
        return [
            'useraccount' => new Useraccount(),
            'name' => '',
        ];
    }

    public function getQueuePreference($key, $isBoolean = false)
    {
        $result = null;
        if (array_key_exists($key, $this['queue'])) {
            if ($isBoolean) {
                $result = ($this['queue'][$key]) ? 1 : 0;
            } else {
                $result = $this['queue'][$key];
            }
        }
        return $result;
    }

    public function getUseraccount()
    {
        if (!$this->useraccount instanceof Useraccount) {
            $this->useraccount = new Useraccount($this->useraccount);
        }
        return $this->useraccount;
    }

    public function getDepartmentById($departmentId)
    {
        return $this->getUseraccount()->getDepartmentById($departmentId);
    }

    public function getDepartmentList()
    {
        $departmentList = new Collection\DepartmentList();
        foreach ($this->getUseraccount()->departments as $department) {
            $departmentList->addEntity(new Department($department));
        }
        return $departmentList;
    }

    public function hasDepartmentList()
    {
        if (0 == $this->getDepartmentList()->count()) {
            throw new Exception\WorkstationMissingAssignedDepartments();
        }
        return true;
    }

    public function getProviderOfGivenScope()
    {
        return $this->toProperty()->scope->provider->id->get();
    }

    public function getUseraccountRights()
    {
        $rights = null;
        if (array_key_exists('rights', $this->useraccount)) {
            $rights = $this->useraccount['rights'];
        }
        return $rights;
    }

    public function hasSuperUseraccount()
    {
        $isSuperuser = false;
        $userRights = $this->getUseraccountRights();
        if (isset($userRights['superuser']) && $userRights['superuser']) {
            $isSuperuser = true;
        }
        return $isSuperuser;
    }

    public function getAuthKey()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    public function hasAuthKey()
    {
        return (isset($this->authkey)) ? true : false;
    }
}
