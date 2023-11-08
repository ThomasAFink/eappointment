<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsstatistic;

use BO\Slim\Render;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ReportWaitingOrganisation extends BaseController
{
    protected $hashset = [
        'waitingcount',
        'waitingtime',
        'waitingcalculated'
    ];

    protected $groupfields = [
        'date',
        'hour'
    ];

    /**
     * @SuppressWarnings(Param)
     * @return ResponseInterface
     */
    public function readResponse(
        RequestInterface $request,
        ResponseInterface $response,
        array $args
    ) {
        $validator = $request->getAttribute('validator');
        $waitingPeriod = \App::$http
          ->readGetResult('/warehouse/waitingorganisation/' . $this->organisation->id . '/')
          ->getEntity();
        $exchangeWaiting = null;
        if (isset($args['period'])) {
            $exchangeWaiting = \App::$http
            ->readGetResult('/warehouse/waitingorganisation/' . $this->organisation->id . '/'. $args['period']. '/')
            ->getEntity()
            ->toGrouped($this->groupfields, $this->hashset)
            ->withMaxByHour($this->hashset)
            ->withMaxAndAverageFromWaitingTime();
        }

        $type = $validator->getParameter('type')->isString()->getValue();
        if ($type) {
            $args['category'] = 'waitingscope';
            $args['reports'][] = $exchangeWaiting;
            $args['organisation'] = $this->organisation;
            return (new Download\WaitingReport(\App::$slim->getContainer()))->readResponse($request, $response, $args);
        }

        return Render::withHtml(
            $response,
            'page/reportWaitingIndex.twig',
            array(
              'title' => 'Wartestatistik Bezirk',
              'activeOrganisation' => 'active',
              'menuActive' => 'waiting',
              'department' => $this->department,
              'organisation' => $this->organisation,
              'waitingPeriod' => $waitingPeriod,
              'showAll' => 1,
              'period' => (isset($args['period'])) ? $args['period'] : null,
              'exchangeWaiting' => $exchangeWaiting,
              'source' => ['entity' => 'WaitingOrganisation'],
              'workstation' => $this->workstation->getArrayCopy()
            )
        );
    }
}
