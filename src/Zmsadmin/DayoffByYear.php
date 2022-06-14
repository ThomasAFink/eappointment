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
class DayoffByYear extends BaseController
{
    /**
     * @return String
     */
    public static function render()
    {
        \BO\Slim\Render::html('page/dayoffByYear.twig', array(
            'title' => 'Allgemein gültige Feiertage',
            'year' => '2016',
            'menuActive' => 'dayoff'
        ));
    }
}
