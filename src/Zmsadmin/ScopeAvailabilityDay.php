<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Availability;
use BO\Zmsentities\Collection\AvailabilityList;

class ScopeAvailabilityDay extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 1])->getEntity();
        $data = static::getAvailabilityData(intval($args['id']), $args['date']);
        $data['title'] = 'Behörden und Standorte - Öffnungszeiten';
        $data['menuActive'] = 'owner';
        $data['workstation'] = $workstation;
        return \BO\Slim\Render::withHtml(
            $response,
            'page/availabilityday.twig',
            $data
        );
    }

    protected static function getScope($scopeId)
    {
        return \App::$http->readGetResult('/scope/' . $scopeId . '/', ['resolveReferences' => 1])->getEntity();
    }

    protected static function getAvailabilityData($scopeId, $dateString)
    {
        $scope = static::getScope($scopeId);
        $dateTime = new \BO\Zmsentities\Helper\DateTime($dateString);
        $dateWithTime = $dateTime->setTime(\App::$now->format('H'), \App::$now->format('i'));
        $availabilityList = static::readAvailabilityList($scopeId, $dateTime);
        $processList = \App::$http
            ->readGetResult('/scope/' . $scopeId . '/process/' . $dateTime->format('Y-m-d') . '/')
                ->getCollection()
                ->toQueueList($dateWithTime)
                ->withoutStatus(['fake'])
                ->toProcessList();
        if (!$processList->count()) {
            $processList = new \BO\Zmsentities\Collection\ProcessList();
        }
        $processConflictList = \App::$http
            ->readGetResult('/scope/' . $scopeId . '/conflict/', [
                'startDate' => $dateTime->format('Y-m-d'),
                'endDate' => $dateTime->format('Y-m-d')
            ])
            ->getCollection();

        $maxSlots = $availabilityList->getSummerizedSlotCount();
        $busySlots = $availabilityList->getCalculatedSlotCount($processList);

        return [
            'scope' => $scope,
            'availabilityList' => $availabilityList->getArrayCopy(),
            'availabilityListSlices' => $availabilityList->withCalculatedSlots()->getArrayCopy(),
            'conflicts' => ($processConflictList) ? $processConflictList->getArrayCopy() : [],
            'processList' => $processList->getArrayCopy(),
            'dateString' => $dateString,
            'timestamp' => $dateWithTime->getTimestamp(),
            'menuActive' => 'availability',
            'maxWorkstationCount' => $availabilityList->getMaxWorkstationCount(),
            'maxSlotsForAvailabilities' => $maxSlots,
            'busySlotsForAvailabilities' => $busySlots,
            'today' => \App::$now->getTimestamp()
        ];
    }

    protected static function readAvailabilityList($scopeId, $dateTime)
    {
        try {
            $availabilityList = \App::$http
                ->readGetResult(
                    '/scope/' . $scopeId . '/availability/',
                    [
                        'startDate' => $dateTime->format('Y-m-d'), //for skipping old availabilities
                    ]
                )
                ->getCollection()->sortByCustomKey('startDate');
        } catch (\BO\Zmsclient\Exception $exception) {
            if ($exception->template != 'BO\Zmsapi\Exception\Availability\AvailabilityNotFound') {
                throw $exception;
            }
            $availabilityList = new \BO\Zmsentities\Collection\AvailabilityList();
        }
        return $availabilityList->withDateTime($dateTime); //withDateTime to check if opened
    }
}
