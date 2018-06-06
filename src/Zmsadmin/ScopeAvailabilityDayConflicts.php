<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Availability;
use BO\Zmsentities\Collection\AvailabilityList;

class ScopeAvailabilityDayConflicts extends ScopeAvailabilityDay
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
        $scope = \App::$http->readGetResult('/scope/' . intval($args['id']) . '/', ['resolveReferences' => 1])
            ->getEntity();
        $data = static::getAvailabilityData($scope, $args['date']);
        return \BO\Slim\Render::withJson(
            $response,
            $data
        );
    }
}
