<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsstatistic;

use BO\Slim\Render;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ReportRequestDepartment extends BaseController
{
    protected $hashset = [
        'requestscount'
    ];

    protected $groupfields = [
        'name',
        'date'
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
        $requestPeriod = \App::$http
          ->readGetResult('/warehouse/requestdepartment/' . $this->department->id . '/')
          ->getEntity();
        $exchangeRequest = null;
        if (isset($args['period'])) {
            $exchangeRequest = \App::$http
            ->readGetResult('/warehouse/requestdepartment/' . $this->department->id . '/'. $args['period']. '/')
            ->getEntity()
            ->toGrouped($this->groupfields, $this->hashset)
            ->withRequestsSum();
        }

        $type = $validator->getParameter('type')->isString()->getValue();
        if ($type) {
            $args['category'] = 'requestdepartment';
            $args['reports'][] = $exchangeRequest;
            $args['department'] = $this->department;
            $args['organisation'] = $this->organisation;
            return (new Download\RequestReport(\App::$slim->getContainer()))->readResponse($request, $response, $args);
        }

        return Render::withHtml(
            $response,
            'page/reportRequestIndex.twig',
            array(
              'title' => 'Dienstleistungsstatistik Behörde',
              'activeDepartment' => 'active',
              'menuActive' => 'request',
              'department' => $this->department,
              'organisation' => $this->organisation,
              'requestPeriod' => $requestPeriod,
              'showAll' => 1,
              'period' => (isset($args['period'])) ? $args['period'] : null,
              'exchangeRequest' => $exchangeRequest,
              'source' => ['entity' => 'RequestDepartment'],
              'workstation' => $this->workstation->getArrayCopy()
            )
        );
    }
}
