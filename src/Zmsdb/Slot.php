<?php
namespace BO\Zmsdb;

use \BO\Zmsentities\Slot as Entity;
use \BO\Zmsentities\Collection\SlotList as Collection;
use \BO\Zmsentities\Availability as AvailabilityEntity;

class Slot extends Base
{

    /**
     * maximum number of slots per appointment
     */
    const MAX_SLOTS = 10;

    /**
     * @return \BO\Zmsentities\Collection\SlotList
     *
     */
    public function readByAppointment(\BO\Zmsentities\Appointment $appointment)
    {
        $availability = (new Availability())->readByAppointment($appointment);
        $slotList = $availability->getSlotList()->withSlotsForAppointment($appointment);
        return $slotList;
    }

    public function readByAvailability(
        \BO\Zmsentities\Slot $slot,
        AvailabilityEntity $availability,
        \DateTimeInterface $date
    ) {
        $data = array();
        $data['scopeID'] = $availability->scope->id;
        $data['availabilityID'] = $availability->id;
        $data['year'] = $date->format('Y');
        $data['month'] = $date->format('m');
        $data['day'] = $date->format('d');
        $data['time'] = $slot->getTimeString();
        $slotID = $this->fetchRow(
            Query\Slot::QUERY_SELECT_SLOT,
            $data
        );
        return $slotID ? $slotID['slotID'] : false ;
    }

    public function isAvailabilityOutdated(
        \BO\Zmsentities\Availability $availability,
        \DateTimeInterface $now,
        \DateTimeInterface $slotLastChange = null
    ) {
        //error_log(sprintf("Now=%s Last=%s %s < %s = %s", $now->format('c'), $slotLastChange->format('c'), $now->getTimestamp(), $slotLastChange->getTimestamp() + 3600, $now->getTimestamp() < ($slotLastChange->getTimestamp() + 3600)));
        if ($availability->isNewerThan($slotLastChange)) {
            error_log("Outdated on availability: $availability");
            return true;
        }
        if ($availability->scope->isNewerThan($slotLastChange)) {
            error_log("Outdated on scope: $availability ". $availability->lastChange);
            return true;
        }
        if ($availability->scope->dayoff->isNewerThan($slotLastChange, $availability, $now)) {
            error_log("Outdated on dayoff: $availability");
            return true;
        }
        if ($now->getTimestamp() > ($slotLastChange->getTimestamp() + 3600)
                //&& $availability->hasDateBetween($availability->getBookableEnd(), $now)
        ) {
            // TODO Check if there could be a difference in slots based on $slotLastChange, i.e. new day
            error_log("Outdated on time: $availability");
            return true;
        }
        error_log("Not outdated: $availability");
        return false;
    }

    public function hasAvailabilityBookableSlots(
        \BO\Zmsentities\Availability $availability,
        \DateTimeInterface $now
    ) {
        if ($availability->workstationCount['intern'] <= 0) {
            return false;
        }
        if ($availability->getEndDateTime()->getTimestamp() < $now->getTimestamp()) {
            return false;
        }
        $stopDate = $availability->getBookableEnd($now);
        if ($availability->getStartDateTime()->getTimestamp() > $stopDate->getTimestamp()) {
            return false;
        }
        return true;
    }

    public function writeByAvailability(
        \BO\Zmsentities\Availability $availability,
        \DateTimeInterface $now
    ) {
        $now = \BO\Zmsentities\Helper\DateTime::create($now);
        $slotLastChange = $this->readLastChangedTimeByAvailability($availability);
        if (!$this->isAvailabilityOutdated($availability, $now, $slotLastChange)) {
            return false;
        }
        // Order is import, the following cancels all slots
        // and should only happen, if rebuild is triggered or not necessary
        $this->perform(Query\Slot::QUERY_CANCEL_AVAILABILITY, [
            'availabilityID' => $availability->id,
        ]);
        if (!$availability->hasAvailabilityBookableSlots($availability, $now)) {
            return false;
        }
        $stopDate = $availability->getBookableEnd($now);

        $slotlist = $availability->getSlotList();
        $time = $now;
        $status = false;
        do {
            if ($availability->hasDate($time, $now)) {
                $ancestors = [];
                foreach ($slotlist as $slot) {
                    $slot = clone $slot;
                    $slotID = $this->readByAvailability($slot, $availability, $time);
                    if ($slotID) {
                        $query = new Query\Slot(Query\Base::UPDATE);
                        $query->addConditionSlotId($slotID);
                    } else {
                        $query = new Query\Slot(Query\Base::INSERT);
                    }
                    $slot->status = 'free';
                    $values = $query->reverseEntityMapping($slot, $availability, $time);
                    $values['createTimestamp'] = time();
                    $query->addValues($values);
                    $writeStatus = $this->writeItem($query);
                    if ($writeStatus && !$slotID) {
                        $slotID = $this->getWriter()->lastInsertId();
                    }
                    $ancestors[] = $slotID;
                    $this->writeAncestorIDs($slotID, $ancestors);
                    $status = $writeStatus ? $writeStatus : $status;
                }
            }
            $time = $time->modify('+1day');
        } while ($time->getTimestamp() <= $stopDate->getTimestamp());

        return $status;
    }

    protected function writeAncestorIDs($slotID, array $ancestors)
    {
        $this->perform(Query\Slot::QUERY_DELETE_ANCESTOR, [
            'slotID' => $slotID,
        ]);
        $ancestorLevel = count($ancestors);
        foreach ($ancestors as $ancestorID) {
            if ($ancestorLevel <= self::MAX_SLOTS) {
                $this->perform(Query\Slot::QUERY_INSERT_ANCESTOR, [
                    'slotID' => $slotID,
                    'ancestorID' => $ancestorID,
                    'ancestorLevel' => $ancestorLevel,
                ]);
            }
            $ancestorLevel--;
        }
    }

    public function readLastChangedTime()
    {
        $last = $this->fetchRow(
            Query\Slot::QUERY_LAST_CHANGED
        );
        if (!$last['dateString']) {
            $last['dateString'] = '1970-01-01 12:00';
        }
        return new \DateTimeImmutable($last['dateString']);
    }

    public function readLastChangedTimeByAvailability(AvailabilityEntity $availabiliy)
    {
        $last = $this->fetchRow(
            Query\Slot::QUERY_LAST_CHANGED_AVAILABILITY,
            [
                'availabilityID' => $availabiliy->id,
            ]
        );
        if (!$last['dateString']) {
            $last['dateString'] = '1970-01-01 12:00';
        }
        return new \DateTimeImmutable($last['dateString']);
    }

    public function updateSlotProcessMapping()
    {
        $this->perform(Query\Slot::QUERY_DELETE_SLOT_PROCESS, [
        ]);
        $this->perform(Query\Slot::QUERY_INSERT_SLOT_PROCESS, [
        ]);
        return $this;
    }

    public function writeSlotProcessMappingFor($processId)
    {
        $this->perform(Query\Slot::QUERY_INSERT_SLOT_PROCESS_ID, [
            'processId' => $processId,
        ]);
        return $this;
    }

    public function deleteSlotProcessMappingFor($processId)
    {
        $this->perform(Query\Slot::QUERY_DELETE_SLOT_PROCESS_ID, [
            'processId' => $processId,
        ]);
        return $this;
    }
}
