<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use \BO\Mellon\Validator;

class WorkstationProcessCalled extends BaseController
{
    /**
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $validator = $request->getAttribute('validator');
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 2])->getEntity();
        $processId = Validator::value($args['id'])->isNumber()->getValue();
        if (! $workstation->process->hasId() && ! $workstation->process->queue->callTime) {
            $process = new \BO\Zmsentities\Process(['id' => $processId]);
            $workstation = \App::$http->readPostResult('/workstation/process/called/', $process, [
                'allowClusterWideCall' => \App::$allowClusterWideCall
            ])->getEntity();
        }

        $excludedIds = $validator->getParameter('exclude')->isString()->getValue();
        if ($excludedIds) {
            $exclude = explode(',', $excludedIds);
        }
        $exclude[] = $workstation->process->getId();
        
        $error = $validator->getParameter('error')->isString()->getValue();
        if (isset($processId) && $workstation->process->getId() != $processId) {
            $error = ('pickup' == $workstation->process->getStatus()) ?
                'has_called_pickup' :
                'has_called_process';
        }
    
        if ($workstation->process->getStatus() == 'processing') {
            return \BO\Slim\Render::redirect('workstationProcessProcessing', [], ['error' => $error]);
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'block/process/called.twig',
            array(
                'title' => 'Sachbearbeiter',
                'workstation' => $workstation,
                'menuActive' => 'workstation',
                'exclude' => join(',', $exclude),
                'error' => $error
            )
        );
    }
}
