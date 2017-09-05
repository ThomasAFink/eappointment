<?php

/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Zmsdb\Status;

class StatusGet extends BaseController
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
        $status = (new Status())->readEntity();
        $status['version']['major'] = \App::VERSION_MAJOR;
        $status['version']['minor'] = \App::VERSION_MINOR;
        $status['version']['patch'] = \App::VERSION_PATCH;
        $status['version'] = Helper\Version::getArray();

        $message = Response\Message::create($request);
        $message->data = $status;

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), $message->getStatuscode());
        return $response;
    }
}
