<?php

namespace BO\Zmsdb\Query;

class Notification extends Base
{
    /**
     * @var String TABLE mysql table reference
     */
    const TABLE = 'notificationqueue';

    const QUERY_DELETE_BY_PROCESS = '
        DELETE nq FROM
            '. self::TABLE .' nq
        WHERE
            nq.processID=?
    ';

    public function getEntityMapping()
    {
        return [
            'id' => 'notification.id',
            'createIP' => 'notification.createIP',
            'createTimestamp' => 'notification.createTimestamp',
            'message' => 'notification.message',
            'client__telephone' => 'notification.clientTelephone',
            'client__familyName' => 'notification.clientFamilyName',
            'process__id' => 'notification.processID',
            'department__id' => 'notification.departmentID'
        ];
    }

    public function addConditionItemId($itemId)
    {
        $this->query->where('notification.id', '=', $itemId);
        return $this;
    }
}
