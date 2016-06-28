<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Scope as Entity;
use BO\Mellon\Validator;

/**
  * Handle requests concerning services
  *
  */
class ScopeAdd extends BaseController
{
    /**
     * @return String
     */
    public function __invoke(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $providerAssigned = \App::$http->readGetResult(
            '/provider/dldb/',
            array(
                'isAssigned' => true
            )
        )->getCollection()->sortByName();

        $providerNotAssigned = \App::$http->readGetResult(
            '/provider/dldb/',
            array(
                'isAssigned' => false
            )
        )->getCollection()->sortByName();

        $input = $request->getParsedBody();
        if (is_array($input) && array_key_exists('save', $input)) {
            try {
                $entity = new Entity($input);
                $entity = \App::$http->readPostResult('/scope/add/', $entity)
                    ->getEntity();
                return Helper\Render::redirect(
                    'scope',
                    array(
                        'id' => $entity->id
                    ),
                    array(
                        'success' => 'scope_created'
                    )
                );
            } catch (\Exception $exception) {
                return Helper\Render::error($exception);
            }
        }

        return Helper\Render::checkedHtml(self::$errorHandler, $response, 'page/scope.twig', array(
            'title' => 'Standort',
            'action' => 'add',
            'menuActive' => 'owner',
            'parentId' => Validator::value($args['parent_id'])->isNumber()->getValue(),
            'providerList' => array(
                'notAssigned' => $providerNotAssigned,
                'assigned' => $providerAssigned
            )
        ));
    }
}
