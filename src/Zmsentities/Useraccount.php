<?php

namespace BO\Zmsentities;

class Useraccount extends Schema\Entity
{
    const PRIMARY = 'id';

    public static $schema = "useraccount.json";

    public function getDefaults()
    {
        return [
            'rights' => [
                "availability" => false,
                "basic" => true,
                "cluster" => false,
                "department" => false,
                "organisation" => false,
                "scope" => false,
                "sms" => false,
                "superuser" => false,
                "ticketprinter" => false,
                "useraccount" => false,
            ],
            'departments' => new Collection\DepartmentList(),
        ];
    }

    public function hasProperties()
    {
        foreach (func_get_args() as $property) {
            if (!$this->toProperty()->$property->get()) {
                throw new Exception\UserAccountMissingProperties("Missing property " . htmlspecialchars($property));
                return false;
            }
        }
        return true;
    }

    public function getDepartmentList()
    {
        if (!$this->departments instanceof Collection\DepartmentList) {
            $this->departments = new Collection\DepartmentList($this->departments);
            foreach ($this->departments as $key => $department) {
                $this->departments[$key] = new Department($department);
            }
        }
        return $this->departments;
    }

    public function addDepartment($department)
    {
        $this->departments[] = $department;
        return $this;
    }

    public function getDepartment($departmentId)
    {
        if (count($this->departments)) {
            foreach ($this->getDepartmentList() as $department) {
                if ($department['id'] == $departmentId) {
                    return $department;
                }
            }
        }
        return new Department(['name' => 'Not existing']);
    }

    public function hasDepartment($departmentId)
    {
        return $this->getDepartment($departmentId)->hasId();
    }

    public function setRights()
    {
        $givenRights = func_get_args();
        foreach ($givenRights as $right) {
            if (array_key_exists($right, $this->rights)) {
                $this->rights[$right] = true;
            }
        }
        return $this;
    }

    public function hasRights(array $requiredRights)
    {
        foreach ($requiredRights as $required) {
            if (!$this->toProperty()->rights->$required->get()) {
                return false;
            }
        }
        return true;
    }

    public function testRights(array $requiredRights)
    {
        if ($this->hasId()) {
            if (!$this->hasRights($requiredRights)) {
                throw new Exception\UserAccountMissingRights(
                    "Missing rights " . htmlspecialchars(implode(',', $requiredRights))
                );
            }
        } else {
            throw new Exception\UserAccountMissingLogin();
        }
        return $this;
    }

    public function isSuperUser()
    {
        return $this->toProperty()->rights->superuser->get();
    }

    public function getDepartmentById($departmentId)
    {
        foreach ($this->departments as $department) {
            if ($departmentId == $department['id']) {
                return new Department($department);
            }
        }
        return new Department();
    }

    public function testDepartmentById($departmentId)
    {
        $department = $this->getDepartmentById($departmentId);
        if (!$department->hasId()) {
            throw new Exception\UserAccountMissingDepartment(
                "Missing department " . htmlspecialchars($departmentId)
            );
        }
        return $department;
    }
}
