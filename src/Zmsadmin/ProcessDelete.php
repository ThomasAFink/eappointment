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
 * Delete a process
 */
class ProcessDelete extends BaseController
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
        $processId = Validator::value($args['id'])->isNumber()->getValue();
        $process = \App::$http->readGetResult('/process/'. $processId .'/')->getEntity();
        $process->status = 'deleted';
        $workstation->testMatchingProcessScope((new Helper\ClusterHelper($workstation))->getScopeList(), $process);
        $authKey = $process->authKey;

        $initiator = Validator::param('initiator')->isString()->getValue();
        $deleted = \App::$http
            ->readDeleteResult('/process/'. $process->id .'/'. $authKey . '/', ['initiator' => $initiator])
            ->getEntity();
        \App::$http->readPostResult('/process/'. $process->id .'/'. $authKey .'/delete/mail/', $process);
        \App::$http->readPostResult('/process/'. $process->id .'/'. $authKey .'/delete/notification/', $process);

        if (! $deleted) {
            throw \Exception('Deleting Process failed');
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'block/process/deleted.twig',
            array(
                'process' => $process
            )
        );
    }
}
