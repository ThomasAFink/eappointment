<?php
namespace BO\Zmsentities\Collection;

use \BO\Zmsentities\Day;

class DayList extends Base implements JsonUnindexed
{
    const ENTITY_CLASS = '\BO\Zmsentities\Day';

    /**
     * ATTENTION: Performance critical, keep highly optimized
     *
     */
    public function getDay($year, $month, $dayNumber, $createDay = true)
    {
        $dateHash = "$dayNumber-$month-$year";
        if (array_key_exists($dateHash, $this)) {
            return $this[$dateHash];
        }
        foreach ($this as $key => $day) {
            if (!$day instanceof Day) {
                $day = new Day($day);
                $this[$key] = $day;
            }
            if ($day->day == $dayNumber && $day->month == $month && $day->year == $year) {
                unset($this[$key]);
                $this[$dateHash] = $day;
                return $day;
            }
        }
        if ($createDay) {
            $day  = new \BO\Zmsentities\Day([
                'year' => $year,
                'month' => $month,
                'day' => $dayNumber
            ]);
            $this[$dateHash] = $day;
            return $day;
        }
        return null;
    }

    public function getDayByDateTime(\DateTimeInterface $datetime)
    {
        return $this->getDay($datetime->format('Y'), $datetime->format('m'), $datetime->format('d'));
    }

    public function getDayByDay(\BO\Zmsentities\Day $day)
    {
        return $this->getDay($day->year, $day->month, $day->day);
    }


    public function hasDay($year, $month, $dayNumber)
    {
        $day = $this->getDay($year, $month, $dayNumber, false);
        return ($day === null) ? false : true;
    }

    public function getMonthIndex()
    {
        $daysByMonth = array();
        foreach ($this as $day) {
            $day = new Day($day);
            $daysByMonth[$day->toDateTime()->format('m')][] = $day;
        }
        return array_keys($daysByMonth);
    }

    public function withAssociatedDays($currentDate)
    {
        $dayList = new self();
        foreach ($this->getMonthIndex() as $monthIndex) {
            if ($currentDate->format('m') == $monthIndex) {
                for ($dayNumber = 1; $dayNumber <= $currentDate->format('t'); $dayNumber ++) {
                    $day = str_pad($dayNumber, 2, '0', STR_PAD_LEFT);
                    $entity = $this->getDay($currentDate->format('Y'), $currentDate->format('m'), $day);
                    $dayList->addEntity($entity);
                }
            }
        }
        return $dayList->sortByCustomKey('day');
    }

    public function setStatusByType($slotType, \DateTimeInterface $dateTime)
    {
        foreach ($this as $day) {
            $day->getWithStatus($slotType, $dateTime);
        }
        return $this;
    }

    public function withAddedDayList(DayList $dayList)
    {
        $merged = new DayList();
        foreach ($dayList as $day) {
            if (!$day instanceof Day) {
                $day = new Day($day);
            }
            $merged->addEntity($day->withAddedDay($this->getDayByDay($day)));
        }
        return $merged;
    }

    public function setSortByDate()
    {
        $this->uasort(function ($dayA, $dayB) {
            return (
                (new \DateTimeImmutable($dayA['year'].'-'.$dayA['month'].'-'.$dayA['day'])) >
                (new \DateTimeImmutable($dayB['year'].'-'.$dayB['month'].'-'.$dayB['day']))
            );
        });
        return $this;
    }

    public function setSort($property = 'day')
    {
        $this->uasort(function ($dayA, $dayB) use ($property) {
            return strnatcmp($dayA[$property], $dayB[$property]);
        });
        return $this;
    }

    public function hasDayWithAppointments()
    {
        foreach ($this as $hash => $day) {
            $hash = null;
            $day = new Day($day);
            if ($day->isBookable()) {
                return true;
            }
        }
        return false;
    }

    public function getFirstBookableDay()
    {
        foreach ($this as $day) {
            $day = new Day($day);
            if ($day->isBookable()) {
                return $day->toDateTime();
            }
        }
        return null;
    }


    public function toSortedByHour()
    {
        $list = array();
        foreach ($this as $day) {
            $list['days'][] = $day;
            $dayKey = $day->year .'-'. $day->month .'-'. $day->day;
            foreach ($day['processList'] as $hour => $processList) {
                $list['hours'][$hour][$dayKey] = $processList;
            }
        }
        return $list;
    }
}
