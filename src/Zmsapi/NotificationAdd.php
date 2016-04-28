<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Zmsdb\Notification as Query;
use \BO\Mellon\Validator;

/**
  * Handle requests concerning services
  */
class NotificationAdd extends BaseController
{
    /**
     * @return String
     */
    public static function render($input = null)
    {
        $message = Response\Message::create();
        $input = (null === $input) ? Validator::input()->isJson()->getValue() : $input;
        $entity = new \BO\Zmsentities\Notification($input);
        (new Query())->writeInQueue($entity);
        $message->data = $entity;
        Render::lastModified(time(), '0');
        Render::json($message);
    }
}
