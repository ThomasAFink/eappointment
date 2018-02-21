<?php

namespace BO\Zmsadmin\Tests;

class PickupTest extends Base
{
    protected $arguments = [];

    protected $parameters = [];

    protected $classname = "Pickup";

    public function testRendering()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, $this->parameters, []);
        $this->assertContains('pickup-view', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRenderingWithSelectedProcess()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, [
            'selectedprocess' => 82252
        ], [], 'POST');
        $this->assertContains('pickup-view', (string)$response->getBody());
        $this->assertContains('data-selected-process="82252"', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
