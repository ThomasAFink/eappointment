<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;

/**
  * Handle requests concerning services
  *
  */
class WorkstationDelete extends BaseController
{
    /**
     * @return String
     */
    public static function render($loginname)
    {
        $message = Response\Message::create();
        $message->data = \BO\Zmsentities\Workstation::createExample();
        $message->data->useraccount['id'] = $loginname;
        Render::lastModified(time(), '0');
        Render::json($message);
    }
}
