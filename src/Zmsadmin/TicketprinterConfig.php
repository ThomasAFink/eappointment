<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Mellon\Validator;

use BO\Zmsentities\Department;
use BO\Zmsentities\Collection\DepartmentList;

class TicketprinterConfig extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function readResponse(
        \psr\http\message\requestinterface $request,
        \psr\http\message\responseinterface $response,
        array $args
    ) {
        $workstation = \App::$http->readGetResult('/workstation/')->getEntity();
        $scopeId = $workstation['scope']['id'];
        $entityId = Validator::value($scopeId)->isNumber()->getValue();

        $config = \App::$http->readGetResult('/config/')->getEntity();

        $entity = \App::$http->readGetResult(
            '/scope/'. $entityId .'/organisation/',
            ['resolveReferences' => 3]
        )->getEntity();

        $departments = new DepartmentList();

        foreach ($entity->departments as $departmentData) {
            $department = (new Department($departmentData))->withCompleteScopeList();
            $departments->addEntity($department);
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'page/ticketprinterConfig.twig',
            array(
                'title' => 'Anmeldung an Warteschlange',
                'config' => $config->getArrayCopy(),
                'organisation' => $entity->getArrayCopy(),
                'departments' => $departments->getArrayCopy(),
                'workstation' => $workstation,
                'menuActive' => 'ticketprinter'
            )
        );
    }
}
