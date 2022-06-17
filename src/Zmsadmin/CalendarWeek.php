<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use \BO\Zmsentities\Scope;

use BO\Mellon\Validator;

class CalendarWeek extends BaseController
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
        // parameters
        $selectedYear = Validator::value($args['year'])->isNumber()->getValue();
        $selectedWeek = Validator::value($args['weeknr'])->isString()->getValue();
        
        // HTTP requests
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 2])->getEntity();
        $workstationRequest = new \BO\Zmsclient\WorkstationRequests(\App::$http, $workstation);
        $cluster = $workstationRequest->readCluster();
        $calendar = new Helper\Calendar(null, $selectedWeek, $selectedYear);
        $dayList = $calendar->readWeekDayListWithProcessList($workstation->getScopeList());
        //var_dump($dayList);exit;

        // data refinement
        
        // rendering
        return \BO\Slim\Render::withHtml(
            $response,
            'page/calendarWeek.twig',
            array(
                'title' => 'Kalender',
                'workstation' => $workstation,
                'source' => $workstation->getVariantName(),
                'cluster' => $cluster,
                'calendar' => $calendar,
                'selectedYear' => $selectedYear,
                'selectedWeek' => number_format($selectedWeek),
                'selectedDate' => $calendar->getDateTime()->format('Y-m-d'),
                'dayList' => $dayList,
            )
        );
    }
}
