<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\Notification as Entity;
use \BO\Zmsentities\Collection\NotificationList as Collection;

class Notification extends Base
{
    /**
     * Fetch status from db
     *
     * @return \BO\Zmsentities\Mail
     */
    public function readEntity($itemId, $resolveReferences = 1)
    {
        $query = new Query\Notification(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionItemId($itemId);
        return $this->fetchOne($query, new Entity());
    }

    public function readList($resolveReferences = 1)
    {
        $notificationList = new Collection();
        $query = new Query\Notification(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences);
        $result = $this->fetchList($query, new Entity());
        if (count($result)) {
            foreach ($result as $notification) {
                $scope = (new Scope())->readEntity($notification->getScopeId(), $resolveReferences);
                $notification->addScope($scope);
                $notificationList->addNotification($notification);
            }
            $notificationList = new Collection($result);
        }
        return $notificationList;
    }

    public function writeInQueue(Entity $notification)
    {
        $scope = (new Scope())->readEntity($notification->getScopeId());
        if (!$scope->hasNotification()) {
            return false;
        }
        $query = new Query\Notification(Query\Base::INSERT);
        $query->addValues(array(
            'processID' => $notification->process['id'],
            'departmentID' => $notification->department['id'],
            'createIP' => $notification->createIP,
            'createTimestamp' => time(),
            'message' => $notification->message
        ));
        $result = $this->writeItem($query);
        if ($result) {
            $this->updateProcess($notification);
        } else {
            return false;
        }
        return true;
    }

    public function deleteEntity($itemId)
    {
        $query =  new Query\Notification(Query\Base::DELETE);
        $query->addConditionItemId($itemId);
        return $this->deleteItem($query);
    }

    private function updateProcess(\BO\Zmsentities\Notification $notification)
    {
        $query = new Process();
        $process = $query->readEntity($notification->getProcessId(), $notification->getProcessAuthKey());
        //error_log(var_export($process,1));
        $client = $process->getFirstClient();
        $client->notificationsSendCount += 1;
        //update process
        $process->updateClients($client);
        $query->updateEntity($process);
    }
}
