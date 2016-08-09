<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;

/**
  * Handle requests concerning services
  */
class ClusterQueue extends BaseController
{
    /**
     * @return String
     */
    public static function render($itemId)
    {
        $message = Response\Message::create(Render::$request);
        $itemId = $itemId; // @todo fetch data
        $message->data = array(\BO\Zmsentities\Queue::createExample());
        Render::lastModified(time(), '0');
        Render::json($message->setUpdatedMetaData(), $message->getStatuscode());
    }
}
