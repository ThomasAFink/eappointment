<?php
/**
 *
 * @package Zmscalldisplay
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmscalldisplay;

/**
 * Handle requests concerning services
 */
class Healthcheck extends BaseController
{

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $response = \BO\Zmsclient\Status::testStatus($response, function () {
            return \App::$http->readGetResult('/status/', ['includeProcessStats' => 0])->getEntity();
        });
        $response = \BO\Slim\Render::withLastModified($response, time(), '0');
        return $response;
    }
}
