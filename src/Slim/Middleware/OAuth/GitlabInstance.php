<?php

namespace BO\Slim\Middleware\OAuth;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use \BO\Zmsclient\Psr7\ClientInterface as HttpClientInterface;
use \BO\Zmsclient\PSR7\Client;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\GenericProvider;

class GitlabInstance extends GenericProvider
{
    protected $provider = null;

    public function __construct()
    {
        $this->provider = new Gitlab\Provider();
        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function doLogin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $accessToken = $this->getProviderAccessToken($request->getParam("code"));
        $ownerInputData = $this->provider->getResourceOwner($accessToken)->toArray();
        try {
            $this->writeTokenToSession($accessToken);
            \App::$http
                ->readPostResult('/workstation/oauth/', $ownerInputData, ['state' => \BO\Zmsclient\Auth::getKey()])
                ->getEntity();
        } catch (\BO\Zmsclient\Exception $exception) {
            \BO\Zmsclient\Auth::removeKey();
            \BO\Zmsclient\Auth::removeOidcProvider();
            throw $exception;
        }
        return $response;
    }

    public function doLogout(ResponseInterface $response)
    {
        $this->writeDeleteSession();
        $realmData = $this->provider::getOptionsFromJsonFile();
        $logoutUrl = $this->provider->getLogoutUrl(['redirect_uri' => $realmData['logoutUri']]);
        return $response->withRedirect($logoutUrl, 301);
    }

    public function writeNewAccessTokenIfExpired($lastLogin)
    {
        try {
            $accessTokenData = $this->readTokenDataFromSession();
            $accessTokenData = (is_array($accessTokenData)) ? $accessTokenData : [];
            $existingAccessToken = new AccessToken($accessTokenData);
            if ($existingAccessToken && $existingAccessToken->hasExpired()) {
                $newAccessToken = $this->provider->getAccessToken('refresh_token', [
                    'refresh_token' => $existingAccessToken->getRefreshToken()
                ]);
                $this->writeDeleteSession();
                $this->writeTokenToSession($newAccessToken);
            }
        } catch (\Exception $exception) {
            return false;
        }
        return true;
    }

    private function getProviderAccessToken($code)
    {
        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', ['code' => $code]);
        } catch (\Exception $exception) {
            if ('League\OAuth2\Client\Provider\Exception\IdentityProviderException' === get_class($exception)) {
                throw new \BO\Slim\Exception\OAuthFailed();
            }
            throw $exception;
        }
        return $accessToken;
    }

    private function writeTokenToSession($token)
    {
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        $sessionHandler = (new \BO\Zmsclient\SessionHandler(\App::$http));
        $sessionHandler->open('/'. $realmData['realm'] . '/', $realmData['clientName']);
        $sessionHandler->write(\BO\Zmsclient\Auth::getKey(), serialize($token), ['oidc' => true]);
        return $sessionHandler->close();
    }

    private function writeDeleteSession()
    {
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        $sessionHandler = (new \BO\Zmsclient\SessionHandler(\App::$http));
        $sessionHandler->open('/'. $realmData['realm'] . '/', $realmData['clientName']);
        $sessionHandler->destroy(\BO\Zmsclient\Auth::getKey());
    }

    private function readTokenDataFromSession()
    {
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        $sessionHandler = (new \BO\Zmsclient\SessionHandler(\App::$http));
        $sessionHandler->open('/'. $realmData['realm'] . '/', $realmData['clientName']);
        $tokenData = unserialize($sessionHandler->read(\BO\Zmsclient\Auth::getKey(), ['oidc' => true]));
        return $tokenData;
    }
}
