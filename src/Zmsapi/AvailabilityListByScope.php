<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Availability as Query;

class AvailabilityListByScope extends BaseController
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
        $scope = (new \BO\Zmsdb\Scope)->readEntity($args['id'], $resolveReferences - 1);
        if (! $scope) {
            throw new Exception\Scope\ScopeNotFound();
        }
        (new Helper\User($request, 2))->checkRights(
            'availability',
            new \BO\Zmsentities\Useraccount\EntityAccess($scope)
        );
        $resolveReferences = Validator::param('resolveReferences')->isNumber()->setDefault(1)->getValue();
        $reserveEntityIds = Validator::param('reserveEntityIds')->isNumber()->setDefault(0)->getValue();
        $startDateFormatted = Validator::param('startDate')->isString()->getValue();
        $endDateFormatted = Validator::param('endDate')->isString()->getValue();

        $startDate = ($startDateFormatted) ? new \BO\Zmsentities\Helper\DateTime($startDateFormatted) : null;
        $endDate = ($endDateFormatted) ? new \BO\Zmsentities\Helper\DateTime($endDateFormatted) : null;
        
        
        $availabilities = (new Query())->readList($scope->id, 0, $startDate, $endDate);
        if (0 == $availabilities->count()) {
            throw new Exception\Availability\AvailabilityNotFound();
        }
        if ($resolveReferences > 0) {
            $availabilities = $availabilities->withScope($scope);
        }
        $message = Response\Message::create($request);
        $message->data = $availabilities;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), 200);
        return $response;
    }
}
