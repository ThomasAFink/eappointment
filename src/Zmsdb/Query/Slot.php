<?php

namespace BO\Zmsdb\Query;

class Slot extends Base implements MappingInterface
{
    /**
     *
     * @var String TABLE mysql table reference
     */
    const TABLE = 'slot';

    const QUERY_LAST_CHANGED = 'SELECT MAX(updateTimestamp) AS dateString FROM slot;';

    const QUERY_INSERT_SLOT_PROCESS = '
        INSERT INTO slot_process
        SELECT 
          s.slotID,
          b.BuergerID,
          NOW()
        FROM slot s
          INNER JOIN buerger b ON
            s.year = YEAR(b.Datum)
            AND s.month = MONTH(b.Datum)
            AND s.day = DAY(b.Datum)
            AND s.scopeID = b.StandortID
            AND b.Uhrzeit BETWEEN s.time AND SEC_TO_TIME(TIME_TO_SEC(s.time) + (s.slotTimeInMinutes * 60) - 1)
          LEFT JOIN slot_process sp ON b.BuergerID = sp.processID
        WHERE
          sp.processID IS NULL
    ';

    const QUERY_UPDATE_SLOT_STATUS = "
        UPDATE slot
          LEFT JOIN (
          SELECT s.slotID,
          IF(s.status IN ('free', 'full'), IF(IFNULL(COUNT(p.slotID), 0) < intern, 'free', 'full'), s.status) newstatus
          FROM slot s
            LEFT JOIN slot_process p ON s.slotID = p.slotID
          GROUP BY s.slotID
          ) calc ON slot.slotID = calc.slotID
        SET
          slot.status = calc.newstatus
        WHERE slot.status != calc.newstatus
";

    const QUERY_SELECT_MULTIPLE_SLOTS = '
SELECT s.*, COUNT(r.processID)
FROM slot s JOIN slot_process r 
  ON r.scopeID = s.scopeID 
     AND r.year = s.year 
     AND r.month = s.month 
     AND r.day = s.day
     AND r.time BETWEEN s.time AND SEC_TO_TIME(TIME_TO_SEC(s.time) + (s.slotTimeInMinutes * 60) - 1)
WHERE s.scopeID = 141
GROUP BY s.scopeID, s.year, s.month, s.day, s.time

';

    const QUERY_SELECT_SLOT = '
    SELECT slotID FROM slot WHERE
      scopeID = :scopeID
      AND year = :year
      AND month = :month
      AND day = :day
      AND time = :time
      AND availabilityID = :availabilityID
    LIMIT 1
';

    const QUERY_INSERT_ANCESTOR = '
    INSERT INTO slot_hiera SET slotID = :slotID, ancestorID = :ancestorID, ancestorLevel = :ancestorLevel
';

    const QUERY_DELETE_ANCESTOR = '
    DELETE FROM slot_hiera WHERE slotID = :slotID
';

    public function getEntityMapping()
    {
        return [
        ];
    }

    public function reverseEntityMapping(
        \BO\Zmsentities\Slot $slot,
        \BO\Zmsentities\Availability $availability,
        \DateTimeInterface $date
    ) {
        $data = array();
        $data['scopeID'] = $availability->scope->id;
        $data['availabilityID'] = $availability->id;
        $data['year'] = $date->format('Y');
        $data['month'] = $date->format('m');
        $data['day'] = $date->format('d');
        $data['time'] = $slot->getTimeString();
        $data['public'] = $availability->workstationCount['public'];
        $data['callcenter'] = $availability->workstationCount['callcenter'];
        $data['intern'] = $availability->workstationCount['intern'];
        $data['slotTimeInMinutes'] = $availability->slotTimeInMinutes;
        return $data;
    }

    public function addConditionSlotId($slotID)
    {
        $this->query->where('slot.slotID', '=', $slotID);
        return $this;
    }
}
