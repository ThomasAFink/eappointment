<?php

/**
 *
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsadmin;

use BO\Zmsentities\Owner as Entity;
use BO\Mellon\Validator;

class Owner extends BaseController
{
    /**
     *
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 1])->getEntity();
        $confirm_success = $request->getAttribute('validator')->getParameter('confirm_success')->isString()->getValue();
        $entityId = Validator::value($args['id'])->isNumber()->getValue();
        $entity = \App::$http->readGetResult('/owner/' . $entityId . '/')->getEntity();

        $input = $request->getParsedBody();
        if (array_key_exists('save', (array) $input)) {
            $entity = (new Entity($input))->withCleanedUpFormData();
            $entity->id = $entityId;
            $entity = \App::$http->readPostResult('/owner/' . $entity->id . '/', $entity)
                ->getEntity();
            return \BO\Slim\Render::redirect(
                'owner',
                [
                    'id' => $entityId
                ],
                [
                    'confirm_success' => \App::$now->getTimeStamp()
                ]
            );
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'page/owner.twig',
            array(
                'title' => 'Kunde','workstation' => $workstation->getArrayCopy(),'menuActive' => 'owner',
                'owner' => $entity->getArrayCopy(),
                'workstation' => $workstation->getArrayCopy(),
                'confirm_success' => $confirm_success
            )
        );
    }
}
