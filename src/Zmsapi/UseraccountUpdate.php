<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\UserAccount as Query;

/**
  * Handle requests concerning services
  */
class UseraccountUpdate extends BaseController
{
    /**
     * @return String
     */
    public static function render($itemId)
    {
        Helper\User::checkRights('useraccount');

        $query = new Query();
        $resolveReferences = Validator::param('resolveReferences')->isNumber()->setDefault(2)->getValue();
        $input = Validator::input()->isJson()->getValue();
        $entity = new \BO\Zmsentities\Useraccount($input);
        $userAccount = $query->updateEntity($itemId, $entity, $resolveReferences);

        $message = Response\Message::create(Render::$request);
        $message->data = ($userAccount->hasId()) ? $userAccount : null;
        Render::lastModified(time(), '0');
        Render::json($message->setUpdatedMetaData(), $message->getStatuscode());
    }
}
