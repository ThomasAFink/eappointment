<?php
/**
 * @package
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Workstation as Query;

/**
  * Handle requests concerning services
  */
class WorkstationLogin extends BaseController
{
    /**
     * @return String
     */
    public static function render($loginName)
    {
        $query = new Query();
        $resolveReferences = Validator::param('resolveReferences')->isNumber()->setDefault(1)->getValue();
        $input = Validator::input()->isJson()->getValue();

        $workstation = null;
        if ($query->isUserExisting($loginName, $input['password'])) {
            $workstation = $query->readUpdatedLoginEntity($loginName, $input['password'], $resolveReferences);
        }

        $message = Response\Message::create(Render::$request);
        $message->data = $workstation;
        Render::lastModified(time(), '0');
        Render::json($message, Helper\User::getStatus($workstation));
    }
}
