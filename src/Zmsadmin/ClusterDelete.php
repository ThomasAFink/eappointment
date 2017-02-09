<?php

/**
 *
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsadmin;

use BO\Mellon\Validator;
use BO\Slim\Render;

/**
 * Delete a cluster
 */
class ClusterDelete extends BaseController
{

    /**
     *
     * @return String
     */
    public function __invoke(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {

        $entityId = Validator::value($args['id'])->isNumber()
                  ->getValue();
        \App::$http->readDeleteResult('/cluster/' . $entityId . '/')
                                                  ->getEntity();
        return Helper\Render::redirect(
            'owner_overview',
            array (),
            array (
                'success' => 'cluster_deleted'
            )
        );
    }
}
