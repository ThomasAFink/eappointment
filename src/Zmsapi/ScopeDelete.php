<?php

/**
 *
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Zmsdb\Scope as Query;

/**
 * Handle requests concerning services
 */
class ScopeDelete extends BaseController
{

    /**
     *
     * @return String
     */
    public static function render($itemId)
    {
        $query = new Query();
        $message = Response\Message::create(Render::$request);
        $entity = $query->readEntity($itemId);
        $processList = (new \BO\Zmsdb\Process())->readProcessListByScopeId($itemId);
        error_log(count($processList));
        if (count($processList)) {
            throw new Exception\Scope\ScopeHasProcesses();
        } else {
            //$query->deleteEntity($itemId);
        }
        $message->data = $entity;
        Render::lastModified(time(), '0');
        Render::json($message->setUpdatedMetaData(), $message->getStatuscode());
    }
}
