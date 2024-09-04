<?php

namespace BO\Zmsdb\Helper;

/**
 * @codeCoverageIgnore
 */
class CalculateSlots
{
    protected $verbose = false;

    protected $startTime;

    protected $logList = [];

    public function __construct($verbose = false)
    {
        $this->verbose = $verbose;
        $this->startTime = microtime(true);
    }

    public function log($message)
    {
        $time = $this->getSpendTime();
        $memory = memory_get_usage()/(1024*1024);
        $text = sprintf("[CalculateSlots %07.3fs %07.1fmb] %s", "$time", $memory, $message);
        $this->logList[] = $text;
        if ($this->verbose) {
            error_log($text);
        }
        return $this;
    }

    public function dumpLogs()
    {
        foreach ($this->logList as $text) {
            if (!$this->verbose) {
                error_log($text);
            }
        }
        $this->verbose = true;
    }

    public function getSpendTime()
    {
        $time = round(microtime(true) - $this->startTime, 3);
        return $time;
    }

    protected function readCalculateSkip()
    {
        $skip = (new \BO\Zmsdb\Config)->readProperty('status__calculateSlotsSkip');
        return $skip;
    }

    protected function readForceVerbose()
    {
        $force = (new \BO\Zmsdb\Config)->readProperty('status__calculateSlotsForceVerbose');
        if ($force) {
            $this->log("Forced verbose, see table config.status__calculateSlotsForceVerbose");
            $this->dumpLogs();
        }
        return $force;
    }

    protected function readLastRun()
    {
        $updateTimestamp = (new \BO\Zmsdb\Config)->readProperty('status__calculateSlotsLastRun', true);
        return $updateTimestamp;
    }

    public function writeMaintenanceQueries()
    {
        $sqlList = (new \BO\Zmsdb\Config)->readProperty('status__calculateSlotsMaintenanceSQL');
        if ($sqlList) {
            $pdo = \BO\Zmsdb\Connection\Select::getWriteConnection();
            foreach (explode("\n", $sqlList) as $sql) {
                $this->log("Maintenance query: $sql");
                $pdo->exec($sql);
            }
            return \BO\Zmsdb\Connection\Select::writeCommit();
        }
        return false;
    }


    public function writeCalculations(\DateTimeInterface $now, $delete = false)
    {
        \BO\Zmsdb\Connection\Select::setTransaction();
        \BO\Zmsdb\Connection\Select::getWriteConnection();
        $this->readForceVerbose();
        $this->log("Calculate with time ". $now->format('c'));
        if ($this->readCalculateSkip()) {
            $this->log("Skip calculation due to config setting status.calculateSlotsSkip");
            return false;
        }
        $startTimestamp = (new \BO\Zmsdb\Config)->readProperty('status__calculateSlotsLastStart', true);
        $updateTimestamp = (new \BO\Zmsdb\Config)->readProperty('status__calculateSlotsLastRun', false);
        if ($startTimestamp > $updateTimestamp && ((strtotime($startTimestamp) + (60 * 15)) > $now->getTimestamp())) {
            $this->log("Skip calculation, last start on $startTimestamp is not yet finished, waiting 15 minutes.");
            return false;
        }
        (new \BO\Zmsdb\Config)->replaceProperty('status__calculateSlotsLastStart', $now->format('Y-m-d H:i:s'));
        \BO\Zmsdb\Connection\Select::writeCommit();
        $updateTimestamp = $this->readLastRun();
        $this->log("Last Run on time=" . $updateTimestamp);
        if ($delete) {
            $this->deleteOldSlots($now);
        }
        $scopeList = (new \BO\Zmsdb\Scope())->readList(1);
        $scopeLength = count($scopeList) - 1;
        foreach ($scopeList as $key => $scope) {
            if ($this->writeCalculatedScope($scope, $now)) {
                $this->log("Calculated slots $key/$scopeLength for $scope");
            }
        }

        $this->writeCanceledSlots($now);

        $slotQuery = new \BO\Zmsdb\Slot();
        if ($slotsProcessed = $slotQuery->deleteSlotProcessOnProcess()) {
            $this->log("Finished to free $slotsProcessed slots for changed/deleted processes");
        }

        $slotQuery->deleteSlotProcessOnSlot();

        if ($slotsProcessed = $slotQuery->updateSlotProcessMapping()) {
            $this->log("Updated Slot-Process-Mapping, mapped $slotsProcessed processes");
        }

        (new \BO\Zmsdb\Config)->replaceProperty('status__calculateSlotsLastRun', $now->format('Y-m-d H:i:s'));

        \BO\Zmsdb\Connection\Select::writeCommit();
        $this->log("Slot calculation finished");
        $this->writeMaintenanceQueries();
    }

    protected function writeCalculatedScope(\BO\Zmsentities\Scope $scope, \DateTimeInterface $now)
    {
        $slotQuery = new \BO\Zmsdb\Slot();
        $updatedList = $slotQuery->writeByScope($scope, $now);
        foreach ($updatedList as $availability) {
            $this->log("Updated $availability with reason " . json_encode($availability['processingNote']));
        }
        if (count($updatedList)) {
            $this->writePostProcessingByScope($scope, $now);
            \BO\Zmsdb\Connection\Select::writeCommitWithStartLock();
            $this->readLastRun();
            return true;
        }
        return false;
    }

    public function writePostProcessingByScope(\BO\Zmsentities\Scope $scope, \DateTimeInterface $now)
    {
        $slotQuery = new \BO\Zmsdb\Slot();
        if ($slotsProcessed = $slotQuery->deleteSlotProcessOnProcess($scope->id)) {
            $this->log("Finished to free $slotsProcessed slots for changed/deleted processes");
        }
        $slotQuery->writeCanceledByTimeAndScope($now, $scope);
        $slotQuery->deleteSlotProcessOnSlot($scope->id);
        //$this->log("Finished to free slots for cancelled availabilities");

        if ($slotsProcessed = $slotQuery->updateSlotProcessMapping($scope->id)) {
            $this->log("Updated Slot-Process-Mapping, mapped $slotsProcessed processes");
        }
    }

    public function writeCanceledSlots(\DateTimeInterface $now, $modify = '+10 minutes')
    {
        \BO\Zmsdb\Connection\Select::getWriteConnection();
        $slotQuery = new \BO\Zmsdb\Slot();
        if ($slotQuery->writeCanceledByTime($now->modify($modify))) {
            \BO\Zmsdb\Connection\Select::writeCommit();
            $this->log("Cancelled slots older than ".$now->modify($modify)->format('c'));
            return true;
        }
        return false;
    }

    public function deleteOldSlots(\DateTimeInterface $now)
    {
        $this->log("Maintenance: Delete slots older than " . $now->format('Y-m-d'));
        error_log("\nMaintenance: Delete slots older than " . $now->format('Y-m-d') . "\n");
        $slotQuery = new \BO\Zmsdb\Slot();
        $pdo = \BO\Zmsdb\Connection\Select::getWriteConnection();
        $pdo->exec('SET SESSION innodb_lock_wait_timeout=600');
    
        // Log lock information before attempting to delete
        $this->logInnoDBLockStatus($pdo, 'Before deletion');
    
        try {
            if ($slotQuery->deleteSlotsOlderThan($now)) {
                \BO\Zmsdb\Connection\Select::writeCommit();
                $this->log("Deleted old slots successfully");
                error_log("\nDeleted old slots successfully\n");
    
                // Log lock information after successful deletion
                $this->logInnoDBLockStatus($pdo, 'After successful deletion');
    
                $slotQuery->writeOptimizedSlotTables();
                $this->log("Optimized tables successfully");
                error_log("\nOptimized tables successfully\n");
            }
        } catch (\Exception $exception) {
            $this->log("Error during deletion: " . $exception->getMessage());
            error_log("\nError during deletion: " . $exception->getMessage() . "\n");
    
            // Log lock information after an error occurs
            $this->logInnoDBLockStatus($pdo, 'After error during deletion attempt');
    
            throw $exception;
        }
    }
    
    private function logInnoDBLockStatus($pdo, $stage)
    {
        $this->log("Logging InnoDB lock status: $stage");
        echo("\nLogging InnoDB lock status: $stage\n");
    
        try {
            // Fetch and log the 'Innodb_row_lock_current_waits' status
            $stmt = $pdo->query("SHOW STATUS LIKE 'Innodb_row_lock_current_waits'");
            $statusVariable = $stmt->fetch(\PDO::FETCH_ASSOC);
    
            $this->log("Innodb_row_lock_current_waits: " . $statusVariable['Value']);
            echo("\nInnodb_row_lock_current_waits: " . $statusVariable['Value'] . "\n");
    
            // Fetch and log the full InnoDB status
            $stmt = $pdo->query("SHOW ENGINE INNODB STATUS");
            $status = $stmt->fetch(\PDO::FETCH_ASSOC);
    
            $this->log("InnoDB STATUS: \n" . $status['Status']);
            echo("\nInnoDB STATUS: \n" . $status['Status'] . "\n");
        } catch (\PDOException $e) {
            $this->log("Failed to retrieve InnoDB status or lock status: " . $e->getMessage());
            echo("\nFailed to retrieve InnoDB status or lock status: " . $e->getMessage() . "\n");
        }
    }
    
    
}
