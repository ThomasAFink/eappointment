<?php
namespace BO\Zmsdb;

use \BO\Zmsentities\Process as Entity;
use \BO\Zmsentities\Collection\ProcessList as Collection;

/**
 *
 */
class ProcessStatusArchived extends Process
{
    public function readArchivedEntity($archiveId, $resolveReferences = 0)
    {
        if (!$archiveId) {
            return null;
        }
        $query = new Query\ProcessStatusArchived(Query\Base::SELECT);
        $query->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionArchiveId($archiveId);
        $archive = $this->fetchOne($query, new Entity());
        $archive = $this->readResolvedReferences($archive, $resolveReferences);
        return $archive;
    }

    public function readListByScopeId($scopeId, $resolveReferences = 0)
    {
        $query = new Query\ProcessStatusArchived(Query\Base::SELECT);
        $query->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionScopeId($scopeId);
        return $this->readResolvedList($query, $resolveReferences);
    }

    public function readListByDate($dateTime, $resolveReferences = 0)
    {
        $query = new Query\ProcessStatusArchived(Query\Base::SELECT);
        $query->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionTime($dateTime);
        return $this->readResolvedList($query, $resolveReferences);
    }

    public function readListIsMissed($isMissed = 1, $resolveReferences = 0)
    {
        $query = new Query\ProcessStatusArchived(Query\Base::SELECT);
        $query->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionIsMissed($isMissed);
        return $this->readResolvedList($query, $resolveReferences);
    }

    public function readListWithAppointment($withAppointment = 1, $resolveReferences = 0)
    {
        $query = new Query\ProcessStatusArchived(Query\Base::SELECT);
        $query->addEntityMapping()
            ->addResolvedReferences($resolveReferences)
            ->addConditionWithAppointment($withAppointment);
        return $this->readResolvedList($query, $resolveReferences);
    }

    protected function readResolvedList($query, $resolveReferences)
    {
        $processList = new Collection();
        $resultList = $this->fetchList($query, new Entity());
        if (count($resultList)) {
            foreach ($resultList as $entity) {
                if (0 == $resolveReferences) {
                    $processList->addEntity($entity);
                } else {
                    if ($entity instanceof Entity) {
                        $entity = $this->readResolvedReferences($entity, $resolveReferences);
                        $processList->addEntity($entity);
                    }
                }
            }
        }
        return $processList;
    }

    public function writeEntityFinished(
        \BO\Zmsentities\Process $process,
        \DateTimeInterface $now
    ) {
        $process = $this->updateEntity($process, 1);
        $archived = null;
        if ($this->writeBlockedEntity($process)) {
            $archived = $this->writeNewArchivedProcess($process, $now);
        }
        // update xRequest entry and update process id as well as archived id
        if ($archived) {
            $this->writeXRequestsArchived($process->id, $archived->archiveId);
        }
        //ToDo write to statistic Table
        return $archived;
    }

    /**
     * write a new archived process to DB
     *
     */
    public function writeNewArchivedProcess(
        \BO\Zmsentities\Process $process,
        \DateTimeInterface $now,
        $resolveReferences = 0
    ) {
        $query = new Query\ProcessStatusArchived(Query\Base::INSERT);
        $query->addValuesNewArchive($process, $now);
        $this->writeItem($query);
        $archiveId = $this->getWriter()->lastInsertId();
        Log::writeLogEntry("ARCHIVE (Archive::writeNewArchivedProcess) $archiveId -> $process ", $process->id);
        return $this->readArchivedEntity($archiveId, $resolveReferences);
    }

    protected function writeXRequestsArchived($processId, $archiveId)
    {
        $query = new Query\XRequest(Query\Base::UPDATE);
        $query->addConditionProcessId($processId);
        $query->addValues([
            'BuergerID' => 0,
            'BuergerarchivID' => $archiveId
        ]);
        $this->writeItem($query);
    }
}
