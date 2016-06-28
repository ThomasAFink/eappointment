<?php
/**
 *
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsadmin;

use BO\Zmsentities\Scope as Entity;
use BO\Mellon\Validator;

/**
 * Handle requests concerning services
 */
class Scope extends BaseController
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

        $entityId = Validator::value($args['id'])->isNumber()->getValue();
        $entity = \App::$http->readGetResult('/scope/' . $entityId . '/')->getEntity();

        if (!$entity->hasId()) {
            return Helper\Render::withHtml($response, 'page/404.twig', array());
        }

        $input = $request->getParsedBody();
        if (is_array($input) && array_key_exists('save', $input)) {
            try {
                $entity = new Entity($input);
                $entity->id = $entityId;
                $entity = \App::$http->readPostResult('/scope/' . $entity->id . '/', $entity)
                    ->getEntity();
                self::$errorHandler->success = 'scope_saved';
            } catch (\Exception $exception) {
                return Helper\Render::error($exception);
            }
        }

        return Helper\Render::checkedHtml(
            self::$errorHandler,
            $response,
            'page/scope.twig',
            array(
                'title' => 'Standort',
                'menuActive' => 'owner',
                'scope' => $entity->getArrayCopy(),
                'parentId' => $entity->getDepartmentId(),
                'providerList' => array(
                    'notAssigned' => $providerNotAssigned,
                    'assigned' => $providerAssigned
                )
            )
        );
    }
}
