<?php

namespace BO\Zmsentities;

use \Shrink0r\Monatic\Maybe;

class Availability extends Schema\Entity
{
    public static $schema = "availability.json";

    /**
     * @var array $weekday english localized weekdays to avoid problems with setlocale()
     */
    protected static $weekdayNameList = [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday'
    ];

    /**
     * Set Default values
     */
    public function getDefaults()
    {
        return [
            'weekday' => array_fill_keys(self::$weekdayNameList, 0),
            'repeat' => [
                'afterWeeks' => 1,
                'weekOfMonth' => 0,
            ],
            'bookable' => [
                'startInDays' => 1,
                'endInDays' => 60,
            ],
        ];
    }

    /**
     * Check, if the dateTime contains a day and time given by the settings
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return Bool
     */
    public function hasDate(\DateTimeInterface $dateTime)
    {
        $dateTime = Helper\DateTime::create($dateTime);
        $weekDayName = self::$weekdayNameList[$dateTime->format('w')];
        $start = $this->getStartDateTime();
        $end = $this->getEndDateTime();
        if (!$this['weekday'][$weekDayName]) {
            // Wrong weekday
            return false;
        }
        if ($dateTime->getTimestamp() < $start->getTimestamp() || $dateTime->getTimestamp() > $end->getTimestamp()) {
            // Out of date range
            return false;
        }
        if (!$this->hasWeek($dateTime)) {
            // series settings for the week do not match
            return false;
        }
        return true;
    }

    /**
     * Check, if the dateTime contains a week given by the week repetition settings
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return Bool
     */
    public function hasWeek(\DateTimeInterface $dateTime)
    {
        $dateTime = Helper\DateTime::create($dateTime);
        $start = $this->getStartDateTime();
        if ($this['repeat']['afterWeeks']
            && 0 === ($dateTime->getWeeks() - $start->getWeeks()) % $this['repeat']['afterWeeks']
        ) {
            return true;
        }
        if ($this['repeat']['weekOfMonth']
            && (
                $dateTime->isWeekOfMonth($this['repeat']['weekOfMonth'])
                // On a value of 5, always take the last week
                || ($this['repeat']['weekOfMonth'] >= 5 && $dateTime->isLastWeekOfMonth())
            )
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get DateTimeInterface for start time of availability
     *
     * @return \DateTimeInterface
     */
    public function getStartDateTime()
    {
        $time = Helper\DateTime::create()
            ->setTimestamp($this['startDate'])
            ->modify('today ' .  $this['startTime']);
        return $time;
    }

    /**
     * Get DateTimeInterface for end time of availability
     *
     * @return \DateTimeInterface
     */
    public function getEndDateTime()
    {
        $time = Helper\DateTime::create()
            ->setTimestamp($this['endDate'])
            ->modify('today ' .  $this['endTime']);
        return $time;
    }

    /**
     * Get DateTimeInterface for start booking time of availability
     *
     * @param \DateTimeInterface $now relative time to compare booking settings
     *
     * @return \DateTimeInterface
     */
    public function getBookableStart(\DateTimeInterface $now)
    {
        $now = Helper\DateTime::create($now);
        $availabilityStart = Helper\Property::create($this)->bookable->startInDays->get();
        if (null !== $availabilityStart) {
            return $now->modify('+' . $availabilityStart . 'days');
        }
        $scopeStart = Helper\Property::create($this)->scope->preferences->appointment->startInDaysDefault->get();
        if (null !== $scopeStart) {
            return $now->modify('+' . $scopeStart . 'days');
        }
        throw new Exception("Undefined start time for booking, try to set the scope properly");
    }

    /**
     * Get DateTimeInterface for end booking time of availability
     *
     * @param \DateTimeInterface $now relative time to compare booking settings
     *
     * @return \DateTimeInterface
     */
    public function getBookableEnd(\DateTimeInterface $now)
    {
        $now = Helper\DateTime::create($now);
        $availabilityStart = Helper\Property::create($this)->bookable->endInDays->get();
        if (null !== $availabilityStart) {
            return $now->modify('+' . $availabilityStart . 'days');
        }
        $scopeStart = Helper\Property::create($this)->scope->preferences->appointment->endInDaysDefault->get();
        if (null !== $scopeStart) {
            return $now->modify('+' . $scopeStart . 'days');
        }
        throw new Exception("Undefined end time for booking, try to set the scope properly");
    }

    /**
     * Check, if the dateTime contains is within the bookable range (usually for public access)
     *
     * @param \DateTimeInterface $dateTime
     * @param \DateTimeInterface $now relative time to compare booking settings
     *
     * @return Bool
     */
    public function isBookable(\DateTimeInterface $dateTime, \DateTimeInterface $now)
    {
        $dateTime = Helper\DateTime::create($dateTime);
        $start = $this->getBookableStart($now);
        $end = $this->getBookableEnd($now);
        if ($dateTime->getTimestamp() < $start->getTimestamp()) {
            return false;
        }
        if ($dateTime->getTimestamp() > $end->getTimestamp()) {
            return false;
        }
        return true;
    }
}
