<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

/**
  * Handle requests concerning services
  *
  */
class Healthcheck extends BaseController
{
    /**
     * @return String
     */
    public static function render()
    {
        echo "OK";
    }
}
