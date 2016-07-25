<?php

namespace BO\Zmsentities;

class Slot extends Schema\Entity
{
    public static $schema = "slot.json";

    /**
     *  the values represent possible free appointments without confirmed
     *  appointments
     *
     */
    const FREE = 'free';

    /**
     *  the values represent free appointments for a given day. Confirmed and
     *  reserved appointments on processes are substracted.
     */
    const TIMESLICE = 'timeslice';

    /**
     * like timeslice, but for more than one scope
     */
    const SUM = 'sum';

    /**
     * like timeslice, but numbers were reduced due to required slots on a
     * given request
     *
     */
    const REDUCED = 'reduced';

    /**
     * the values represent a unix timestamp to when there are free processes
     *
     */
    const TIMESTAMP = 'timestamp';

    public function getDefaults()
    {
        return [
            'public' => 0,
            'intern' => 0,
            'callcenter' => 0,
            'type' => self::FREE,
        ];
    }

    public function setTime(Helper\DateTime $slotTime)
    {
        $this->time = $slotTime->format('H:i');
    }

    public function hasTime()
    {
        return $this->time ? true : false;
    }

    public function getTimeString()
    {
        return isset($this['time']) ? $this['time'] : '0:00';
    }

    public function __toString()
    {
        return "Slot {$this->type}@{$this->getTimeString()} p/c/i={$this->public}/{$this->callcenter}/{$this->intern}";
    }
}
