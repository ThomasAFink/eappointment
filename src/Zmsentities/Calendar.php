<?php
namespace BO\Zmsentities;

class Calendar extends Schema\Entity
{

    public static $schema = "calendar.json";

    public function getDefaults()
    {
        return [
            'calendar' => [],
            'days' => [],
            'clusters' => [],
            'providers' => [],
            'scopes' => [],
            'requests' => [],
        ];
    }

    public function addProvider($source, $idList)
    {
        foreach (explode(',', $idList) as $id) {
            $provider = new Provider();
            $provider->source = $source;
            $provider->id = $id;
            $this->providers[] = $provider;
        }
        return $this;
    }

    public function addRequest($source, $requestList)
    {
        foreach (explode(',', $requestList) as $id) {
            $request = new Request();
            $request->source = $source;
            $request->id = $id;
            $this->requests[] = $request;
        }
        return $this;
    }

    public function getScopeList()
    {
        $list = array();
        foreach ($this->scopes as $scope) {
            $list[] = $scope['id'];
        }
        return $list;
    }

    public function getProviderIdList()
    {
        $list = array();
        foreach ($this->providers as $provider) {
            $list[] = $provider['id'];
        }
        return $list;
    }

    /**
     * Returns a list of contained month given by firstDay and lastDay
     * The return value is a DateTime object for the first day of the month
     * @return [\DateTime]
     */
    public function getMonthList()
    {
        $startDate = new \DateTime();
        $startDate->setDate($this['firstDay']['year'], $this['firstDay']['month'], $this['firstDay']['day']);
        $endDate = new \DateTime();
        $endDate->setDate($this['lastDay']['year'], $this['lastDay']['month'], $this['lastDay']['day']);
        $currentDate = $startDate;
        if ($startDate->getTimestamp() > $endDate->getTimestamp()) {
            // swith first and last day if necessary
            $currentDate = $endDate;
            $endDate = $startDate;
        }
        $endDate = new \DateTime($endDate->format('Y-m-t'));
        $monthList = [];
        do {
            $monthList[] = new \DateTime($currentDate->format('Y-m-1'));
            $currentDate->modify('+1 month');
        } while ($currentDate->getTimestamp() < $endDate->getTimestamp());
        return $monthList;
    }


    protected $dayIndex = [];

    public function getDay($year, $month, $dayNumber)
    {
        $indexKey = "$year.$month.$dayNumber";
        if (array_key_exists($indexKey, $this->dayIndex)) {
            return $this->dayIndex[$indexKey];
        }
        foreach ($this['days'] as $key => $day) {
            if ($day['year'] == $year && $day['month'] == $month && $day['day'] == $dayNumber) {
                if (!($day instanceof Day)) {
                    $day = new Day($day);
                    $this['days'][$key] = $day;
                }
                return $day;
            }
        }
        $day = new Day([
            'year' => $year,
            'month' => $month,
            'day' => $dayNumber,
        ]);
        $this['days'][] = $day;
        $this->dayIndex[$indexKey] = $day;
        return $day;
    }
}
