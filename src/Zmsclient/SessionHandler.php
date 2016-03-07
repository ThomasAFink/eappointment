<?php
namespace BO\Zmsclient;

/**
 * Session handler for mysql
 */
class SessionHandler implements \SessionHandlerInterface
{

    public $sessionName;

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     *
     */
    public function open($save_path, $name)
    {
        $this->sessionName = $name;
    }

    public function close()
    {
        return true;
    }

    public function read($sessionId)
    {
        $session = \App::$http->readGetResult('/session/' . $this->sessionName .'/'. $sessionId .'/')->getEntity();
        return (isset($session) && array_key_exists('content', $session)) ? $session['content'] : null;
    }

    public function write($sessionId, $sessionData)
    {
        $entity = new \BO\Zmsentities\Session();
        $entity->id = $sessionId;
        $entity->name = $this->sessionName;
        $entity->content = $sessionData;
        $session = \App::$http->readPostResult('/session/', $entity)->getEntity();
        return ($session) ? true : false;
    }

    public function destroy($sessionId)
    {
        $result = \App::$http->readDeleteResult('/session/' . $this->sessionName .'/'. $sessionId .'/');
        return ($result) ? true : false;
    }

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     * @SuppressWarnings(ShortMethodName)
     *
     */
    public function gc($maxlifetime)
    {
        /*
         $compareTs = time() - $maxlifetime;
         $query = '
         DELETE FROM
         sessiondata
         WHERE
         UNIX_TIMESTAMP(`ts`) < ? AND
         sessionname=?
         ';
         $statement = $this->getWriter()->prepare($query);
         return $statement->execute(array(
         $compareTs,
         $this->sessionName
         ));
         */
    }
}
