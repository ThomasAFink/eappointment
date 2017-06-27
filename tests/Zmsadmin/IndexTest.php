<?php

namespace BO\Zmsadmin\Tests;

class IndexTest extends Base
{
    protected $arguments = [];

    protected $parameters = [
        'loginName' => 'testadmin',
        'password' => 'vorschau',
        'login_form_validate' => 1
    ];

    protected $classname = "Index";

    public function testRendering()
    {
        $response = $this->render($this->arguments, [], []);
        $this->assertContains('Anmeldung', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testLogin()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readPostResult',
                    'url' => '/workstation/login/',
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, $this->parameters, [], 'POST');
        $this->assertRedirect($response, '/workstation/select/');
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testLoginFailed()
    {
        $this->expectException('\BO\Zmsclient\Exception');
        $exception = new \BO\Zmsclient\Exception();
        $exception->template = 'BO\Zmsapi\Exception\Useraccount\UserAlreadyLoggedIn';
        $exception->data = json_decode($this->readFixture("GET_Workstation_Resolved2.json"), 1)['data'];
        $this->setApiCalls(
            [
                [
                    'function' => 'readPostResult',
                    'url' => '/workstation/login/',
                    'exception' => $exception
                ]
            ]
        );
        $this->render($this->arguments, $this->parameters, [], 'POST');
    }

    public function testLoginValidationError()
    {
        $response = $this->render($this->arguments, [
            'loginName' => 'testadmin',
            'login_form_validate' => 1
        ], [], 'POST');
        $this->assertContains('Es muss ein Passwort eingegeben werden', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
