<?php
/**
 *
 * @package Zmsticketprinter
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsticketprinter;

use \BO\Zmsentities\Ticketprinter as Entity;

class Process extends BaseController
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
        $config = \App::$http->readGetResult('/config/')->getEntity();
        $validator = $request->getAttribute('validator');
        $scopeId = $validator->getParameter('scopeId')->isNumber()->getValue();
        $clusterId = $validator->getParameter('clusterId')->isNumber()->getValue();
        $ticketprinter = Helper\Ticketprinter::readWithHash($request);

        if ($scopeId) {
            $process = \App::$http->readGetResult(
                '/scope/'. $scopeId .'/waitingnumber/'. $ticketprinter->hash .'/'
            )->getEntity();
        } elseif ($clusterId) {
            $process = \App::$http->readGetResult(
                '/cluster/'. $clusterId .'/waitingnumber/'. $ticketprinter->hash .'/'
            )->getEntity();
            $scope = $process->scope;
        }

        $scope = new \BO\Zmsentities\Scope($process->scope);
        $queueList = \App::$http->readGetResult('/scope/'. $scope->id . '/queue/')->getCollection();
        $estimatedData = ($queueList) ? $scope->getWaitingTimeFromQueueList($queueList, \App::$now) : null;

        return \BO\Slim\Render::withHtml(
            $response,
            'page/process.twig',
            array(
                'debug' => \App::DEBUG,
                'title' => 'Anmeldung an der Warteschlange',
                'ticketprinter' => $ticketprinter,
                'organisation' => \App::$http->readGetResult(
                    '/scope/'. $scope->id . '/organisation/',
                    ['resolveReferences' => 2]
                )->getEntity(),
                'process' => $process,
                'queueList' => $queueList,
                'estimatedData' => $estimatedData,
                'config' => $config
            )
        );
    }
}
