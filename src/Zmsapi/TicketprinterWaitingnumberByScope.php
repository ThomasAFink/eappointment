<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Ticketprinter as Query;

/**
  * Handle requests concerning services
  */
class TicketprinterWaitingnumber extends BaseController
{
    /**
     * @return String
     */
    public static function render($scopeId)
    {
        $message = Response\Message::create(Render::$request);
        $input = Validator::input()->isJson()->getValue();
        $scopeId = $scopeId; // @todo fetch data
        $input = $input;
        $message->data = array(\BO\Zmsentities\Process::createExample());
        Render::lastModified(time(), '0');
        Render::json($message->setUpdatedMetaData(), $message->getStatuscode());
    }
}
