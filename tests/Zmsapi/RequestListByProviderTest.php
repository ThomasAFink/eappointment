<?php

namespace BO\Zmsapi\Tests;

class RequestListByProviderTest extends Base
{
    protected $classname = "RequestListByProvider";

    public function testRendering()
    {
        $response = $this->render(['source' => 'dldb', 'id' => 122217], [], []);
        $this->assertContains('request.json', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testEmpty()
    {
        $this->expectException('\ErrorException');
        $this->render([], [], []);
    }

    public function testSourceFailed()
    {
        $this->expectException('\BO\Zmsdb\Exception\UnknownDataSource');
        $this->expectExceptionCode(404);
        $this->render(['source' => 'test', 'id' => 122217], [], []);
    }

    public function testNotFound()
    {
        $this->expectException('\BO\Zmsapi\Exception\Provider\ProviderNotFound');
        $this->expectExceptionCode(404);
        $this->render(['source' => 'dldb', 'id' => 11111111], [], []);
    }
}
