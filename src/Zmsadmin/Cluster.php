<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Cluster as Entity;
use BO\Mellon\Validator;

class Cluster extends BaseController
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
        $workstation = \App::$http->readGetResult('/workstation/')->getEntity();
        $entityId = Validator::value($args['clusterId'])->isNumber()->getValue();
        $departmentId = Validator::value($args['departmentId'])->isNumber()->getValue();
        $entity = \App::$http->readGetResult('/cluster/' . $entityId . '/', ['resolveReferences' => 2])->getEntity();
        $confirm_success = $request->getAttribute('validator')->getParameter('confirm_success')->isString()->getValue();

        $department = \App::$http->readGetResult(
            '/department/' . $departmentId . '/',
            ['resolveReferences' => 2]
        )->getEntity();

        $callDisplayImage = \App::$http->readGetResult('/cluster/'. $entityId .'/imagedata/calldisplay/')->getEntity();
        $input = $request->getParsedBody();
        if (is_array($input) && array_key_exists('save', $input)) {
            $entity = (new Entity($input))->withCleanedUpFormData();
            $entity->id = $entityId;
            $entity = \App::$http->readPostResult('/cluster/' . $entity->id . '/', $entity)->getEntity();
            (new Helper\FileUploader($request, 'uploadCallDisplayImage'))->writeUploadToCluster($entityId);
            return \BO\Slim\Render::redirect('cluster', [
                'clusterId' => $entityId,
                'departmentId' => $departmentId,
            ], [
                'confirm_success' => \App::$now->getTimeStamp()
            ]);
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'page/cluster.twig',
            array(
                'title' => 'Cluster',
                'menuActive' => 'owner',
                'workstation' => $workstation,
                'cluster' => $entity->getArrayCopy(),
                'department' => $department,
                'scopeList' => $department->getScopeList()->sortByContactName(),
                'callDisplayImage' => $callDisplayImage,
                'confirm_success' => $confirm_success,
            )
        );
    }
}
