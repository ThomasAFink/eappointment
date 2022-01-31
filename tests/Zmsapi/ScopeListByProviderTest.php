<?php

namespace BO\Zmsapi\Tests;

class ScopeListByProviderTest extends Base
{
    protected $classname = "ScopeListByProvider";

    public function testRendering()
    {
        $this->setWorkstation();
        $response = $this->render(['source' => 'dldb', 'id' => 122217], [], []); //Bürgeramt Heerstraße
        $this->assertStringContainsString('scope.json', (string)$response->getBody());
        $this->assertStringContainsString('preferences', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testLessData()
    {
        $response = $this->render(['source' => 'dldb', 'id' => 122217], [], []); //Bürgeramt Heerstraße
        $this->assertStringContainsString('scope.json', (string)$response->getBody());
        $this->assertStringNotContainsString('preferences', (string)$response->getBody());
        $this->assertStringContainsString('"reducedData":true', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testEmpty()
    {
        $this->setWorkstation();
        $this->expectException('\ErrorException');
        $this->render([], [], []);
    }

    public function testNotFound()
    {
        $this->setWorkstation();
        $this->expectException('\BO\Zmsapi\Exception\Provider\ProviderNotFound');
        $this->expectExceptionCode(404);
        $this->render(['source' => 'dldb', 'id' => 999], [], []);
    }
}
