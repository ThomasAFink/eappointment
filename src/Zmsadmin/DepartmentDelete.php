<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Mellon\Validator;
use BO\Slim\Render;
use BO\Zmsentities\Department as Entity;

/**
  * Handle requests concerning services
  *
  */
class DepartmentDelete extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function invokeHook(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $entityId = Validator::value($args['id'])->isNumber()->getValue();
        \App::$http->readDeleteResult(
            '/department/'. $entityId .'/'
        )->getEntity();
        return Helper\Render::redirect(
            'owner_overview',
            array(),
            array(
                'success' => 'department_deleted'
            )
        );
    }
}
