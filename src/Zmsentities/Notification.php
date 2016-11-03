<?php

namespace BO\Zmsentities;

class Notification extends Schema\Entity
{
    const PRIMARY = 'id';

    public static $schema = "notification.json";

    public function getScopeId()
    {
        return $this->toProperty()->process->scope->id->get();
    }

    public function getProcessId()
    {
        return $this->toProperty()->process->id->get();
    }

    public function getProcessAuthKey()
    {
        return $this->toProperty()->process->authKey->get();
    }

    public function getDepartmentId()
    {
        return $this->toProperty()->department->id->get();
    }

    public function addScope($scope)
    {
        $this->process['scope'] = $scope;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getIdentification()
    {
        return $this->toProperty()->department->preferences->notifications->identification->get();
    }

    public function toResolvedEntity(Process $process, Config $config, Department $department)
    {
        $entity = clone $this;
        $entity->process = $process;
        $entity->message = Helper\Messaging::getNotificationContent($process, $config);
        $entity->createIP = $process->createIP;
        $entity->department = $department;
        return $entity;
    }

    public function hasProperties()
    {
        $requiredProperties = func_get_args();
        foreach ($requiredProperties as $property) {
            if (!array_key_exists($property, $this)) {
                throw new Exception\NotificationMissedProperty("Missing property $property");
            }
        }
        return true;
    }
}
