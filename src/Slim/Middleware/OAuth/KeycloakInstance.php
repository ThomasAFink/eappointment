<?php

namespace BO\Slim\Middleware\OAuth;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Token\AccessToken;

/**
 * @SuppressWarnings(PHPMD)
 */

class KeycloakInstance
{
    protected $provider = null;

    public function __construct()
    {
        $this->provider = new Keycloak\Provider();
        return $this;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    public function doLogin(ServerRequestInterface $request, ResponseInterface $response)
    {
        $accessToken = $this->getAccessToken($request->getParam("code"));
        $this->testAccess($accessToken);
        $ownerInputData = $this->provider->getResourceOwnerData($accessToken);
        $this->testOwnerData($ownerInputData);
        try {
            if (\BO\Zmsclient\Auth::getKey()) {
                $this->writeDeleteSession();
            }
            $this->writeTokenToSession($accessToken);
            \App::$http
                ->readPostResult('/workstation/oauth/', $ownerInputData, ['state' => \BO\Zmsclient\Auth::getKey()])
                ->getEntity();
        } catch (\BO\Zmsclient\Exception $exception) {
            $this->writeDeleteSession();
            \BO\Zmsclient\Auth::removeKey();
            \BO\Zmsclient\Auth::removeOidcProvider();
            throw $exception;
        }
        return $response;
    }

    public function doLogout(ResponseInterface $response)
    {
        $this->writeDeleteSession();
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        return $response->withRedirect($realmData['logoutUri'], 301);
    }

    public function writeNewAccessTokenIfExpired()
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

    private function testAccess(AccessToken $token)
    {
        list($header, $payload, $signature)  = explode('.', $token->getToken());
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        $accessTokenPayload = json_decode(base64_decode($payload), true);
        $clientRoles = is_array($accessTokenPayload['resource_access']) ? array_values($accessTokenPayload['resource_access'][\App::IDENTIFIER]['roles']) : array();
        if (!in_array($realmData['accessRole'], $clientRoles)) {
            throw new \BO\Slim\Exception\OAuthFailed();
        }
    }

    private function testOwnerData(array $ownerInputData)
    {
        $config = \App::$http->readGetResult('/config/', [], \App::CONFIG_SECURE_TOKEN)->getEntity();
        if (! \array_key_exists('email', $ownerInputData) && 1 == $config->getPreference('oidc', 'onlyVerifiedMail')) {
            throw new \BO\Slim\Exception\OAuthPreconditionFailed();
        }
    }


    private function getAccessToken($code)
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
        $sessionHandler->open('/'. $realmData['realm'] . '/', $realmData['clientId']);
        $sessionHandler->write(\BO\Zmsclient\Auth::getKey(), serialize($token), ['oidc' => true]);
        return $sessionHandler->close();
    }

    private function writeDeleteSession()
    {
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        $sessionHandler = (new \BO\Zmsclient\SessionHandler(\App::$http));
        $sessionHandler->open('/'. $realmData['realm'] . '/', $realmData['clientId']);
        $sessionHandler->destroy(\BO\Zmsclient\Auth::getKey());
    }

    private function readTokenDataFromSession()
    {
        $realmData = $this->provider::getBasicOptionsFromJsonFile();
        $sessionHandler = (new \BO\Zmsclient\SessionHandler(\App::$http));
        $sessionHandler->open('/'. $realmData['realm'] . '/', $realmData['clientId']);
        $tokenData = unserialize($sessionHandler->read(\BO\Zmsclient\Auth::getKey(), ['oidc' => true]));
        return $tokenData;
    }
}
