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
        $scopeId = Validator::value($args['id'])->isNumber()->getValue();
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

        $scope = \App::$http->readGetResult('/scope/' . $scopeId . '/')
            ->getEntity();

        if (! isset($scope['id'])) {
            return Helper\Render::withError($response, 'page/404.twig', array());
        }

        $input = $request->getParsedBody();
        if (is_array($input) && array_key_exists('save', $input)) {
            try {
                $entity = new Entity($input);
                $entity->id = $scopeId;
                $scope = \App::$http->readPostResult('/scope/' . $entity->id . '/', $entity)
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
                'scope' => $scope->getArrayCopy(),
                'providerList' => array(
                    'notAssigned' => $providerNotAssigned,
                    'assigned' => $providerAssigned
                )
            )
        );
    }
}
