<?php
namespace BO\Zmsdb;

use \BO\Zmsentities\Calendar as Entity;
use \BO\Zmsdb\Query\SlotList;
use \BO\Zmsentities\Collection\ProcessList;

class Calendar extends Base
{

    public function readResolvedEntity(Entity $calendar, $freeProcessesDate = null)
    {
        $calendar['processing'] = [];
        $calendar['freeProcesses'] = new ProcessList();
        $calendar['processing']['slotlist'] = new SlotList();
        $calendar = $this->readResolvedProviders($calendar);
        $calendar = $this->readResolvedClusters($calendar);
        $calendar = $this->readResolvedRequests($calendar);
        $calendar = $this->readResolvedDays($calendar, $freeProcessesDate);
        unset($calendar['processing']['slotlist']);
        return $calendar;
    }

    protected function readResolvedRequests(Entity $calendar)
    {
        $requestReader = new Request($this->getWriter(), $this->getReader());
        if (! isset($calendar['processing']['slotinfo'])) {
            $calendar['processing']['slotinfo'] = [];
        }
        foreach ($calendar['requests'] as $key => $request) {
            $request = $requestReader->readEntity('dldb', $request['id']);
            $calendar['requests'][$key] = $request;
            foreach ($requestReader->readSlotsOnEntity($request) as $slotinfo) {
                if (! isset($calendar['processing']['slotinfo'][$slotinfo['provider__id']])) {
                    $calendar['processing']['slotinfo'][$slotinfo['provider__id']] = 0;
                }
                $calendar['processing']['slotinfo'][$slotinfo['provider__id']] += $slotinfo['slots'];
            }
        }
        return $calendar;
    }

    protected function readResolvedClusters(Entity $calendar)
    {
        $scopeReader = new Scope($this->getWriter(), $this->getReader());
        foreach ($calendar['clusters'] as $cluster) {
            $scopeList = $scopeReader->readByClusterId($cluster['id']);
            foreach ($scopeList as $scope) {
                $calendar['scopes'][] = $scope;
            }
        }
        return $calendar;
    }

    protected function readResolvedProviders(Entity $calendar)
    {
        $scopeReader = new Scope($this->getWriter(), $this->getReader());
        $providerReader = new Provider($this->getWriter(), $this->getReader());
        $calendar['scopes'] = array();
        foreach ($calendar['providers'] as $key => $provider) {
            $calendar['providers'][$key] = $providerReader->readEntity('dldb', $provider['id']);
            $scopeList = $scopeReader->readByProviderId($provider['id']);
            foreach ($scopeList as $scope) {
                $calendar['scopes'][] = $scope;
            }
        }
        return $calendar;
    }

    protected function readResolvedDays(Entity $calendar, $freeProcessesDate)
    {
        $query = SlotList::getQuery();
        $monthList = $calendar->getMonthList();
        $statement = $this->getReader()->prepare($query);
        foreach ($monthList as $monthDateTime) {
            $month = new \DateTimeImmutable($monthDateTime->format('c'));
            foreach ($calendar->scopes as $scope) {
                $scope = new \BO\Zmsentities\Scope($scope);
                $statement->execute(SlotList::getParameters($scope['id'], $monthDateTime));
                //error_log(var_export(SlotList::getParameters($scope['id'], $monthDateTime), true));
                $slotsRequired = $calendar['processing']['slotinfo'][$scope->getProviderId()];
                while ($slotData = $statement->fetch(\PDO::FETCH_ASSOC)) {
                    $calendar = $this->addDayInfoToCalendar(
                        $calendar,
                        $slotData,
                        $month,
                        $slotsRequired,
                        $freeProcessesDate
                    );
                    //error_log("|".$slotData['slottime'].":".$slotData['slotdate'].":".$slotData['availability__id']);
                }
                // Process the last processed slotlist missed by addDayInfoToCalendar
                $calendar = $this->addDayInfoToCalendar(
                    $calendar,
                    ['availability__id' => null],
                    $month,
                    $slotsRequired,
                    $freeProcessesDate
                );
                $calendar['processing']['slotlist'] = new SlotList();
            }
        }
        return $calendar;
    }

    /**
     * ATTENTION: performance critical function, keep highly optimized!
     */
    protected function addDayInfoToCalendar(
        Entity $calendar,
        array $slotData,
        \DateTimeImmutable $month,
        $slotsRequired,
        $freeProcessesDate
    ) {
        if (! $calendar['processing']['slotlist']->isSameAvailability($slotData)) {
            $calendar['processing']['slotlist']->toReducedBySlots($slotsRequired);
            $calendar['processing']['slotlist']->addToCalendar($calendar, $freeProcessesDate);
            $calendar['processing']['slotlist'] = new SlotList(
                $slotData,
                $month->modify('first day of')->modify('00:00:00'),
                $month->modify('last day of')->modify('23:59:59')
            );
        } else {
            $calendar['processing']['slotlist']->addSlotData($slotData);
        }
        return $calendar;
    }
}
