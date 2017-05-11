<?php
/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Cluster as Query;

class ClusterByScopeId extends BaseController
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
        (new Helper\User($request))->checkRights('cluster');
        $resolveReferences = Validator::param('resolveReferences')->isNumber()->setDefault(1)->getValue();
        $cluster = (new Query)->readByScopeId($args['id'], $resolveReferences);
        if (! $cluster) {
            throw new Exception\Cluster\ClusterNotFound();
        }

        $message = Response\Message::create($request);
        $message->data = ($cluster) ? $cluster : array();

        $response = Render::withLastModified($response, time(), '0');
        $response = Render::withJson($response, $message->setUpdatedMetaData(), $message->getStatuscode());
        return $response;
    }
}
