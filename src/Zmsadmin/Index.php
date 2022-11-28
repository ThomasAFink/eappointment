<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use \BO\Zmsentities\Workstation;
use \BO\Zmsadmin\Helper\LoginForm;
use \BO\Mellon\Validator;

class Index extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        try {
            $workstation = \App::$http->readGetResult('/workstation/')->getEntity();
        } catch (\Exception $workstationexception) {
            $workstation = null;
        }
        $config = \App::$http->readGetResult('/config/', [], \App::CONFIG_SECURE_TOKEN)->getEntity();
        $input = $request->getParsedBody();
        $oidclogin = $request->getAttribute('validator')->getParameter('oidclogin')->isString()->getValue();
        if ($request->isPost()) {
            $loginData = $this->testLogin($input);
            if ($loginData instanceof Workstation && $loginData->offsetExists('authkey')) {
                \BO\Zmsclient\Auth::setKey($loginData->authkey);
                return \BO\Slim\Render::redirect('workstationSelect', array(), array());
            }
            \BO\Slim\Render::withHtml(
                $response,
                'page/index.twig',
                array(
                'title' => 'Anmeldung gescheitert',
                'loginfailed' => true,
                'workstation' => null,
                'exception' => $loginData,
                'showloginform' => true,
                'oidcproviderlist' => $this->getProviderList($config)
                )
            );
        }
        return \BO\Slim\Render::withHtml(
            $response,
            'page/index.twig',
            array(
                'title' => 'Anmeldung',
                'config' => $config,
                'workstation' => $workstation,
                'oidcproviderlist' => $this->getProviderList($config),
                'oidclogin' => $oidclogin,
                'showloginform' => (! $oidclogin)
            )
        );
    }

    protected function testLogin($input)
    {
        $userAccount = new \BO\Zmsentities\Useraccount(array(
            'id' => $input['loginName'],
            'password' => $input['password'],
            'departments' => array('id' => 0) // required in schema validation
        ));
        try {
            /** @var \BO\Zmsentities\Workstation $workstation */
            $workstation = \App::$http->readPostResult('/workstation/login/', $userAccount)->getEntity();
            return $workstation;
        } catch (\BO\Zmsclient\Exception $exception) {
            $template = Helper\TwigExceptionHandler::getExceptionTemplate($exception);
            if ('BO\Zmsentities\Exception\SchemaValidation' == $exception->template) {
                $exceptionData = [
                  'template' => 'exception/bo/zmsapi/exception/useraccount/invalidcredentials.twig'
                ];
                $exceptionData['data']['password']['messages'] = [
                    'Der Nutzername oder das Passwort wurden falsch eingegeben'
                ];
            } elseif ('BO\Zmsapi\Exception\Useraccount\UserAlreadyLoggedIn' == $exception->template) {
                \BO\Zmsclient\Auth::setKey($exception->data['authkey']);
                throw $exception;
            } elseif ('' != $exception->template
                && \App::$slim->getContainer()->view->getLoader()->exists($template)
            ) {
                $exceptionData = [
                  'template' => $template,
                  'data' => $exception->data
                ];
            } else {
                throw $exception;
            }
        }
        return $exceptionData;
    }

    private function getProviderList($config)
    {
        $allowedProviderList = explode(',', $config->getPreference('oidc', 'provider'));
        $oidcproviderlist = [];
        foreach (\BO\Slim\Middleware\OAuthMiddleware::$authInstances as $provider => $authInstance) {
            if (0 < count($allowedProviderList) &&
                class_exists($authInstance) &&
                in_array($provider, $allowedProviderList)
            ) {
                $oidcproviderlist[] = $provider;
            }
        }
        return $oidcproviderlist;
    }
}
