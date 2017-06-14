<?php
namespace BO\Zmsdb;

use \BO\Zmsentities\Process as Entity;
use \BO\Zmsentities\Collection\ProcessList as Collection;

/**
 *
 */
class ProcessStatusQueued extends Process
{
    public function writeNewFromTicketprinter(\BO\Zmsentities\Scope $scope, \DateTimeInterface $dateTime)
    {
        $process = Entity::createFromScope($scope, $dateTime);
        $process->setStatus('queued');
        $newQueueNumber = (new Scope())->readWaitingNumberUpdated($scope->id, $dateTime);
        $process->addQueue($newQueueNumber, $dateTime);
        return $this->writeNewProcess($process, $dateTime);
    }

    public function writeNewFromAdmin(Entity $process, \DateTimeInterface $dateTime)
    {
        $process->setStatus('queued');
        $newQueueNumber = (new Scope())->readWaitingNumberUpdated($process->scope['id'], $dateTime);
        $process->addQueue($newQueueNumber, $dateTime);
        $process = $this->writeNewProcess($process, $dateTime);
        $this->writeRequestsToDb($process);
        return $this->readEntity($process->id, $process->authKey, 2);
    }

    /**
     * Read process by queue number and scopeId
     *
     * @param
     * scopeId
     *
     * @return String authKey
     */
    public function readByQueueNumberAndScope($queueNumber, $scopeId, $resolveReferences = 0)
    {
        $query = new Query\Process(Query\Base::SELECT);
        $query
            ->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionScopeId($scopeId)
            ->addConditionAssigned()
            ->addConditionQueueNumber($queueNumber);
        $process = $this->fetchOne($query, new Entity());
        $process = $this->readResolvedReferences($process, $resolveReferences);
        return $process;
    }
}
