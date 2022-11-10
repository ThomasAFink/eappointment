<?php
namespace BO\Slim\Middleware;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \BO\Slim\Middleware\OAuth\KeycloakAuth;

class OAuthMiddleware
{
    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if ("keycloak" === \App::OIDC_AUTHORIZATION_TYPE){
            $response = $this->handleKeycloakInstance($request, $response);
        }
        if ("gitlab" === \App::OIDC_AUTHORIZATION_TYPE){
            $response = $this->handleGitlabInstance($request, $response);
        }
        return $next($request, $response);
    }

    protected function handleKeycloakInstance(ServerRequestInterface $request, ResponseInterface $response)
    {
        $instance = new KeycloakAuth();
        if ('logout/' === $request->getUri()->getPath()) {
            return $instance->doLogout($response);
        }
        if ('oidc/' === $request->getUri()->getPath()) {
            if (! $request->getParam("code") && '' == \BO\Zmsclient\Auth::getKey()) {
                $authUrl = $instance->getProvider()->getAuthorizationUrl();
                \BO\Zmsclient\Auth::setKey($instance->getProvider()->getState());
                return $response->withRedirect($authUrl, 301);
            }
            elseif ($request->getParam("state") !== \BO\Zmsclient\Auth::getKey()) {
                \BO\Zmsclient\Auth::removeKey();   
            }
            return $instance->doLogin($request, $response);
        }
        elseif ('workstation/' === $request->getUri()->getPath()) {
            if (! $instance->writeNewAccessTokenIfExpired($response)) {
                return $instance->doLogout($response);
            }
        }
        return $response;
    }

    protected function handleGitlabInstance(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response;
    }
}
