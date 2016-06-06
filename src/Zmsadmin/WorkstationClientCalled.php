<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

/**
  * Handle requests concerning services
  *
  */
class WorkstationClientCalled extends BaseController
{
    /**
     * @return String
     */
    public static function render()
    {
        \BO\Slim\Render::html('page/workstationClientCalled.twig', array(
            'title' => 'Sachbearbeiter',
            'menuActive' => 'workstation'
        ));
    }
}
