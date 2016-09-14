<?php

namespace BO\Slim\Middleware\Session;

class SessionData implements SessionInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var boolean
     */
    private $isLocked = false;


    protected $entityClass = null;

    /**
     * __construct is not allowed, use
     * - {@see SessionData::getSessionFromName}
     * instead
     */
    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     *
     * @SuppressWarnings(Superglobals)
     *
     * @return self
     */
    public static function getSessionFromName($name)
    {
        $session = array();
        if (headers_sent() === false && session_status() !== PHP_SESSION_ACTIVE) {
            session_name($name);
            session_start();
            if (!count($_SESSION)) {
                $_SESSION['status'] = 'start';
            };
            $session = $_SESSION;
        } else {
            throw  new \BO\Slim\Exception\SessionFailed("Headers sent or a session is already active");
        }
        $instance = new self();
        $instance->data = $session;
        return $instance;
    }

    public function writeData()
    {
        session_write_close();
        $this->isLocked = true;
    }

    public function setGroup(array $group)
    {
        foreach ($group as $index => $items) {
            foreach ($items as $key => $value) {
                $this->set($key, $value, $index);
            }
        }
    }

    /**
     *
     * @SuppressWarnings(Superglobals)
     *
     * @return array
     */
    public function set($key, $value, $groupIndex = null)
    {
        if (null === $groupIndex) {
            $this->data[$key] = self::convertValueToScalar($value);
        } else {
            $this->data[$groupIndex][$key] = self::convertValueToScalar($value);
        }
        if ($this->isLocked) {
            throw new \BO\Slim\Exception\SessionLocked();
        }
        $_SESSION = $this->data;
    }

    public function get($key, $groupIndex = null, $default = null)
    {
        if (! $this->has($key, $groupIndex)) {
            return self::convertValueToScalar($default);
        } elseif (null === $groupIndex) {
            return $this->data[$key];
        } else {
            return $this->data[$groupIndex][$key];
        }
    }

    public function getEntity()
    {
        if (null === $this->entityClass) {
            throw new \Exception("Entity-Class not set");
        }
        $sessionContent = clone $this->entityClass;
        $sessionContent->content = $this->data;
        return $sessionContent;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function remove($key, $groupIndex = null)
    {
        if (null === $groupIndex) {
            unset($this->data[$key]);
        } else {
            unset($this->data[$groupIndex][$key]);
        }
    }

    /**
     *
     * @SuppressWarnings(Superglobals)
     *
     * @return self
     */
    public function clearGroup($groupIndex = null)
    {
        if (null !== $groupIndex) {
            $this->data[$groupIndex] = [];
            $_SESSION = $this->data;
        }
    }

    /**
     *
     * @SuppressWarnings(Superglobals)
     *
     * @return self
     */
    public function clear()
    {
        session_destroy();
        setcookie(
            session_name(),
            null,
            time() - 42000,
            "/"
        );
        session_regenerate_id(true);
        $this->data = [];
        $_SESSION = [];
    }

    public function has($key, $groupIndex = null)
    {
        if (null === $groupIndex) {
            return array_key_exists($key, $this->data);
        } else {
            if (array_key_exists($groupIndex, $this->data)) {
                return array_key_exists($key, $this->data[$groupIndex]);
            }
        }
    }

    public function isEmpty()
    {
        return empty($this->data);
    }

    public function jsonSerialize()
    {
        return json_encode($this->data);
    }

    private static function convertValueToScalar($value)
    {
        return json_decode(json_encode($value, \JSON_PRESERVE_ZERO_FRACTION), true);
    }
}
