<?php
/**
 *
* @package Zmsmessaging
* @copyright BerlinOnline Stadtportal GmbH & Co. KG
*
*/
namespace BO\Zmsmessaging;

class BaseController
{
    protected $workstation = null;

    public function __construct()
    {
        $this->workstation = $this->writeLogin();
    }

    protected function writeLogin()
    {
        $userAccount = new \BO\Zmsentities\Useraccount(array(
            'id' => '_system_messenger',
            'password' => 'zmsmessaging'
        ));
        try {
            $workstation = \App::$http
                ->readPostResult('/workstation/login/', $userAccount)->getEntity();
        } catch (\BO\Zmsclient\Exception $exception) {
            //ignore double login exception on quick login
            $workstation = new \BO\Zmsentities\Workstation($exception->data);
        }

        if (array_key_exists('authkey', $workstation)) {
            \BO\Zmsclient\Auth::setKey($workstation->authkey);
        }
        $workstation = \App::$http->readPostResult('/workstation/', $workstation)->getEntity();
        return $workstation;
    }

    protected function writeLogout()
    {
        \App::$http->readDeleteResult('/workstation/_system_messenger/');
    }

    protected function sendMailer(\BO\Zmsentities\Schema\Entity $entity, $mailer = null, $action = false)
    {
        if (false !== $action) {
            // @codeCoverageIgnoreStart
            if (null !== $mailer) {
                if (! $mailer->Send()) {
                    throw new \Exception('Zmsmessaging Failed');
                    \App::$log->debug('Zmsmessaging Failed', [$mailer->ErrorInfo]);
                }
            }
            // @codeCoverageIgnoreEnd
        }
        \App::$http->readPostResult('/log/process/'. $entity->process['id'] .'/', $entity);
        return $mailer;
    }

    public function deleteEntityFromQueue($entity)
    {
        $type = ($entity instanceof \BO\Zmsentities\Mail) ? 'mails' : 'notification';
        try {
            $entity = \App::$http->readDeleteResult('/'. $type .'/'. $entity->id .'/')->getEntity();
        } catch (\BO\Zmsclient\Exception $exception) {
            throw $exception;
        }
        return ($entity) ? true : false;
    }
}
