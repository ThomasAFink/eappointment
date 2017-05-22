<?php

namespace BO\Zmsapi\Tests;

class ProcessGetTest extends Base
{
    protected $classname = "ProcessGet";

    public function testRendering()
    {
        $response = $this->render(['id' => 10030, 'authKey' => '1c56'], [], []);
        $this->assertContains('process.json', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testEmpty()
    {
        $this->expectException('\ErrorException');
        $this->render([], [], []);
    }

    public function testNotFound()
    {
        $this->expectException('\BO\Zmsapi\Exception\Process\ProcessNotFound');
        $this->expectExceptionCode(404);
        $this->render(['id' => 999999, 'authKey' => null], [], []);
    }

    public function testAuthKeyMatchFailed()
    {
        $this->expectException('\BO\Zmsapi\Exception\Process\AuthKeyMatchFailed');
        $this->expectExceptionCode(403);
        $this->render(['id' => 10030, 'authKey' => null], [], []);
    }
}
