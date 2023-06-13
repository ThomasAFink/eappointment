<?php
/**
 * @package Zmsstatistic
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsstatistic;

use BO\Slim\Render;

class Healthcheck extends BaseController
{
    protected $withAccess = false;
    
    /**
     * @SuppressWarnings(UnusedFormalParameter)
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $response = \BO\Zmsclient\Status::testStatus($response, function () {
            return \App::$http->readGetResult('/status/')->getEntity();
        });
        $response = \BO\Slim\Render::withLastModified($response, time(), '0');
        return $response;
    }
}
