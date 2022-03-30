<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Zmsdb\Availability as Query;

class AvailabilityDelete extends BaseController
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
        (new Helper\User($request))->checkRights();
        $query = new Query();
        $entity = $query->readEntity($args['id'], 2);

        if ($entity->hasId() && $query->deleteEntity($entity->getId())) {
            (new \BO\Zmsdb\Helper\CalculateSlots(\App::DEBUG))->writePostProcessingByScope($entity->scope, \App::$now);
        }

        $message = Response\Message::create($request);
        $message->data = $entity;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), 200);
        return $response;
    }
}
