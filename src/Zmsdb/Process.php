<?php
namespace BO\Zmsdb;

use \BO\Zmsentities\Process as Entity;
use \BO\Zmsentities\Collection\ProcessList;
use BO\Zmsdb\Helper\ProcessStatus as Status;

class Process extends Base
{

    public function readEntity($processId, $authKey, $resolveReferences = 2)
    {
        $query = new Query\Process(Query\Base::SELECT);
        $query->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionProcessId($processId)
            ->addConditionAuthKey($authKey);
        //\App::$log->debug($query->getSql());
        // var_dump($this->fetchOne($query, new Entity()));
        $process = $this->fetchOne($query, new Entity());
        $process['requests'] = (new Request())->readRequestByProcessId($processId, $resolveReferences);
        $process['status'] = (new Status())->readProcessStatus($processId, $authKey);
        $process = $this->addDldbData($process, $resolveReferences);
        return $process;
    }

    public function updateEntity(\BO\Zmsentities\Process $process)
    {
        $query = new Query\Process(Query\Base::UPDATE);
        if (array_key_exists('id', $process) && !empty($process['id'])) {
            $processId = $process['id'];
        } else {
            $processId = $this->writeNewProcess();
        }
        if (array_key_exists('authKey', $process) && !empty($process['authKey'])) {
            $authKey = $process['authKey'];
        } else {
            $authKey = self::readAuthKeyByProcessId($processId);
        }

        $query->addConditionProcessId($processId);
        $query->addConditionAuthKey($authKey);

        $values = $query->reverseEntityMapping($process);
        $query->addValues($values);
        $this->writeItem($query, 'process', $query::TABLE);
        $this->writeRequestsToDb($processId, $process['requests']);

        $process = $this->readEntity($processId, $authKey);
        $process['status'] = (new Status())->readProcessStatus($processId, $authKey);
        return $process;
    }

    protected function readAuthKeyByProcessId($processId)
    {
        $query = new Query\Process(Query\Base::SELECT);
        $query->addEntityMapping()->addConditionProcessId($processId);
        $process = $this->fetchOne($query, new Entity());
        return $process['authKey'];
    }

    /**
     * Markiere einen Termin als bestätigt
     *
     * @param
     * process
     *
     * @return Resource Status
     */
    public function updateProcessStatus(\BO\Zmsentities\Process $process, $status = 'free')
    {
        //\App::$log->debug('UPDATE STATUS');
        $process = (new Status())->readUpdatedStatus($process, $status);
        return $process;
    }

    /**
     * Markiere einen Termin als abgesagt
     *
     * @param
     *            processId and authKey
     *
     * @return Resource Status
     */
    public function deleteEntity($processId, $authKey)
    {
        $query = Query\Process::QUERY_DELETE;
        $statement = $this->getWriter()->prepare($query);
        $status = $statement->execute(
            array(
                $processId,
                $authKey,
                $processId
            )
        );
        if ($status) {
            $query = Query\XRequest::QUERY_DELETE;
            $statement = $this->getWriter()->prepare($query);
            $status = $status = $statement->execute(
                array(
                $processId
                )
            );
        }
            return $status;
    }

    public function writeRequestsToDb($processId, $requests)
    {
        $checkRequests = (new Request())->readRequestByProcessId($processId);
        if (null === $checkRequests) {
            $query = new Query\XRequest(Query\Base::INSERT);
            foreach ($requests as $request) {
                $query->addValues(
                    [
                    'AnliegenID' => $request['id'],
                    'BuergerID' => $processId
                    ]
                );
                $this->writeItem($query);
            }
        } else {
            foreach ($requests as $request) {
                $query = new Query\XRequest(Query\Base::UPDATE);
                $query->addConditionXRequestId($request['id']);
                $query->addConditionProcessId($processId);
                $query->addValues(
                    [
                        'AnliegenID' => $request['id']
                    ]
                );
                $this->writeItem($query);
            }
        }

    }

    public function writeNewProcess()
    {
        $query = new Query\Process(Query\Base::INSERT);
        $lock = $this->getLock($query);
        $dateTime = new \DateTime();
        if ($lock == 1) {
            $query->addValues(
                [
                'BuergerID' => $this->getNewProcessId($query),
                'IPTimeStamp' => (int) $dateTime->getTimestamp(),
                'absagecode' => substr(md5(rand()), 0, 4)
                ]
            );
        } else {
            $query->addValues(
                [
                'BuergerID' => null,
                'IPTimeStamp' => (int) $dateTime->getTimestamp(),
                'absagecode' => substr(md5(rand()), 0, 4)
                ]
            );
        }
        $this->writeItem($query);
        $lastInsertId = $this->getWriter()
            ->lastInsertId();
        $this->releaseLock($query);
        return $lastInsertId;
    }

    public function getNewProcessId($query)
    {
        return $this->getReader()
        ->fetchValue($query->getQueryNewProcessId());
    }

    public function getLock($query)
    {
        return $this->getReader()
            ->fetchValue($query::QUERY_SET_LOCK);
    }

    public function releaseLock($query)
    {
        return $this->getReader()
            ->fetchValue($query::QUERY_RELEASE_LOCK);
    }

    /* prüfen ob das benötigt wird begin */
    public function readFreeProcesses(\BO\Zmsentities\Calendar $calendar, \DateTimeInterface $now)
    {
        $resolvedCalendar = new Calendar();
        $selectedDate = $calendar->getFirstDay();
        $calendar = $resolvedCalendar->readResolvedEntity($calendar, $now, $selectedDate);
        if (isset($calendar['freeProcesses'])) {
            return $calendar['freeProcesses'];
        }
        return new ProcessList();
    }

    protected function addDldbData($process, $resolveReferences)
    {
        if (isset($process['scope']['provider'])) {
            $provider = $process['scope']['provider'];
            if ($resolveReferences >= 2 && $provider['source'] == 'dldb') {
                $process['scope']['provider']['data'] = Helper\DldbData::readExtendedProviderData(
                    $provider['source'],
                    $provider['id']
                );
            }
        }
        return $process;
    }
}
