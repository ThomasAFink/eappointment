<?php

namespace BO\Zmsdb\Query;

class MailQueue extends Base
{
    /**
     * @var String TABLE mysql table reference
     */
    const TABLE = 'mailqueue';

    const QUERY_DELETE = '
        DELETE mq,  mp
            FROM '. self::TABLE .' mq
            LEFT JOIN '. MailPart::TABLE .' mp ON mp.queueId = mq.id
            WHERE mq.id=?
    ';

    const QUERY_DELETE_BY_PROCESS = '
        DELETE mq,  mp
        FROM '. self::TABLE .' mq
        LEFT JOIN '. MailPart::TABLE .' mp ON mp.queueId = mq.id
        WHERE mq.processID=?
    ';

    public function getEntityMapping()
    {
        return [
            'id' => 'mailQueue.id',
            'createIP' => 'mailQueue.createIP',
            'createTimestamp' => 'mailQueue.createTimestamp',
            'subject' => 'mailQueue.subject',
            'client__email' => 'mailQueue.clientEmail',
            'client__familyName' => 'mailQueue.clientFamilyName',
            'process__id' => 'mailQueue.processID',
            'department__id' => 'mailQueue.departmentID'
        ];
    }

    public function addConditionItemId($itemId)
    {
        $this->query->where('mailQueue.id', '=', $itemId);
        return $this;
    }
}
