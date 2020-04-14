<?php

namespace BO\Zmsadmin\Tests;

class WorkstationProcessCalledTest extends Base
{
    protected $arguments = [
        'id' => 82252
    ];

    protected $parameters = [];

    protected $classname = "WorkstationProcessCalled";

    public function testRendering()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/workstation/process/called/',
                    'response' => $this->readFixture("GET_workstation_with_process.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, $this->parameters, []);
        $this->assertContains('Kundeninformationen', (string)$response->getBody());
        $this->assertContains('H52452625 (Wartenr. 82252)', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRenderingAlreadyCalledProcessWithExcludes()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_workstation_with_process_called.json")
                ]
            ]
        );
        $response = $this->render(['id' => 161275], [
            'exclude' => 82252
        ], []);
        $this->assertRedirect($response, '/workstation/process/processing/?error=has_called_process');
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testRenderingAlreadyCalledPickup()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_workstation_with_process_pickup.json")
                ]
            ]
        );
        $response = $this->render(['id' => 161275], [
            'exclude' => 82252
        ], []);
        $this->assertRedirect($response, '/workstation/process/processing/?error=has_called_pickup');
        $this->assertEquals(302, $response->getStatusCode());
    }
}
