<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Scope as Query;

class ProcessListByScopeAndDate extends BaseController
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
        (new Helper\User($request))->checkRights('scope');
        $resolveReferences = Validator::param('resolveReferences')->isNumber()->setDefault(0)->getValue();
        $dateTime = new \BO\Zmsentities\Helper\DateTime($args['date']);

        $query = new Query();
        $scope = $query->readEntity($args['id'], $resolveReferences);
        if (! $scope) {
            throw new Exception\Scope\ScopeNotFound();
        }
        $queueList = $query->readQueueList($scope->id, $dateTime, $resolveReferences);

        $message = Response\Message::create($request);
        $message->data = $queueList->toProcessList();

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message, 200);
        return $response;
    }
}
