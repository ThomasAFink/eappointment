<?php

namespace BO\Slim\Middleware;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

class SessionMiddleware
{
    const SESSION_ATTRIBUTE = 'session';

    protected $sessionClass = null;

    public function __construct($name = 'default', $sessionClass = null)
    {
        session_name($name);
        $this->sessionName = $name;
        $this->sessionClass = $sessionClass;
    }

    public function __invoke(
        ServerRequestInterface $requestInterface,
        ResponseInterface $response,
        callable $next
    ) {
        $sessionContainer = Session\SessionHuman::fromContainer(function () use ($requestInterface) {
            return $this->getSessionContainer($requestInterface);
        });

        if (null !== $next) {
            $response = $next($requestInterface->withAttribute(self::SESSION_ATTRIBUTE, $sessionContainer), $response);
        }
        return $response;
    }

    public function getSessionContainer($request)
    {
        $session = Session\SessionData::getSession($request);
        $session->setEntityClass($this->sessionClass);
        return $session;
    }
}
