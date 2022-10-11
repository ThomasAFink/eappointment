<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin\Helper;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;
use Stevenmaguire\OAuth2\Client\Provider\Keycloak;
use GuzzleHttp\Client;
use \BO\Slim\Profiler as Profiler;
use \BO\Zmsentities\Cluster as Entity;

class OAuthMiddleware
{
    private $provider = null;
    private $accessTokenPayload = "";

    public function __invoke(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        if(\App::ZMS_AUTHORIZATION_TYPE === "Keycloak"){
            try {
                $workstation = \App::$http->readGetResult('/workstation/')->getEntity();
            } catch (\Exception $workstationexception) {
                $workstation = null;
            }
            if($workstation !== NULL){
                $userAccount = $workstation->getUseraccount();
                if ($userAccount->hasId()) {
                    return $next($request, $response);
                }
            }

            $this->setProvider();
            $this->Authorization($request);

            if($this->checkAccessRight()){
                $workstation = \App::$http->readPostResult('/workstation/oauth/', $this->accessTokenPayload, ['code' => $request->getParam("code")] )->getEntity();

                if (array_key_exists('authkey', $workstation)) {
                    \BO\Zmsclient\Auth::setKey($workstation->authkey);
                    return $next($request, $response);
                }
            } else {
                $exceptionData = [
                    'template' => 'exception/bo/zmsapi/exception/useraccount/keycloakAuthError.twig'
                ];
                return \BO\Slim\Render::withHtml(
                    $response,
                    'page/index.twig',
                    array(
                        'title' => 'Anmeldung gescheitert',
                        'loginfailed' => true,
                        'workstation' => null,
                        'exception' => $exceptionData,
                        'showloginform' => false,
                    )
                );
            }
        }

        return $next($request, $response);
    }

    private function Authorization($request){
        $code = $request->getParam("code");
        $state = $request->getParam("state");

        if (!isset($code)) {
            // If we don't have an authorization code then get one
            $authUrl = $this->provider->getAuthorizationUrl();
            \BO\Zmsclient\Auth::setKey($this->provider->getState());
            header('Location: ' . $authUrl);
            exit;
            // Check given state against previously stored one to mitigate CSRF attack
        } elseif (empty($state) || ($state !== \BO\Zmsclient\Auth::getKey())) {
            \BO\Zmsclient\Auth::removeKey();
            throw new \Exception('Invalid state.');
        } else {
            // Try to get an access token (using the authorization code grant)
            try {
                Profiler::add("Start AccessToken");
                $token = $this->provider->getAccessToken('authorization_code', [
                    'code' => $code
                ]);
                Profiler::add("End AccessToken");
            } catch (Exception $e) {
                throw new \Exception('Failed to get access token: '.$e->getMessage());
            }

            list($header, $payload, $signature)  = explode('.', $token->getToken());
            $this->accessTokenPayload = json_decode(base64_decode($payload), true);
        }
    }

    private function checkAccessRight(){
        $resource_access_roles = $this->accessTokenPayload['resource_access'][\App::ZMS_AUTHORIZATION_CLIENT_ID]['roles'];
        return in_array( \App::ZMS_AUTHORIZATION_ACCESS_ROLE ,$resource_access_roles );
    }

    private function setProvider(){
        /*$guzzyClient = new Client([
            'defaults' => [
                \GuzzleHttp\RequestOptions::CONNECT_TIMEOUT => \App::ZMS_AUTHORIZATION_CONNECT_TIMEOUT,
                \GuzzleHttp\RequestOptions::ALLOW_REDIRECTS => true],
                \GuzzleHttp\RequestOptions::VERIFY => \App::ZMS_AUTHORIZATION_SSL_VERIFY,
        ]);*/
        \App::$http = new \BO\Zmsclient\Http(\App::HTTP_BASE_URL); \BO\Zmsclient\Psr7\Client::$curlopt = \App::$http_curl_config;
        $this->provider = new Keycloak([
            'authServerUrl'         => \App::ZMS_AUTHORIZATION_AUTHSERVERURL,
            'realm'                 => \App::ZMS_AUTHORIZATION_REALM,
            'clientId'              => \App::ZMS_AUTHORIZATION_CLIENT_ID,
            'clientSecret'          => \App::ZMS_AUTHORIZATION_CLIENT_SECRET,
            'redirectUri'           => \App::ZMS_AUTHORIZATION_REDIRECTURI,
        ]);
        //$this->provider->setHttpClient($guzzyClient);
    }
}
