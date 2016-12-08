<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Zmsdb\Notification as Query;

/**
  * Handle requests concerning services
  */
class NotificationDelete extends BaseController
{
    /**
     * @return String
     */
    public static function render($itemId)
    {
        Helper\User::checkRights('department');

        $query = new Query();
        $message = Response\Message::create(Render::$request);
        $notification = $query->readEntity($itemId);
        if ($query->deleteEntity($itemId)) {
            $message->data = $notification;
        } else {
            $message->meta->statuscode = 500;
            $message->meta->error = true;
            $message->meta->message = "Could not delete notification";
        }
        Render::lastModified(time(), '0');
        Render::json($message->setUpdatedMetaData(), $message->getStatuscode());
    }
}
