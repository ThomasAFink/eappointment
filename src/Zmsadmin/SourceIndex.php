<?php

/**
 *
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsadmin;

class Useraccount extends BaseController
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
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 1])->getEntity();
        if (!$workstation->hasSuperUseraccount()) {
            throw new Exception\NotAllowed();
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'page/sourceindex.twig',
            array(
                'title' => 'Mandanten',
                'menuActive' => 'source',
                'workstation' => $workstation
            )
        );
    }
}
