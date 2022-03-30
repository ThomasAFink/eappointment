<?php

namespace BO\Zmsapi\Tests;

use BO\Zmsapi\Helper\User;

class ProcessLogTest extends Base
{
    protected $classname = "ProcessLog";

    const PROCESS_ID = 10030;

    const AUTHKEY = '1c56';

    public function testRendering()
    {
        (new ProcessUpdateTest)->testRendering();
        $this->setWorkstation()->getUseraccount()->setRights('superuser');
        $response = $this->render(['id' => self::PROCESS_ID], [], []);
        $this->assertContains('log.json', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testNotFound()
    {
        $this->setWorkstation()->getUseraccount()->setRights('superuser');
        $response = $this->render(['id' => 123], [], []);
        $this->assertContains('"data":{}', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }
}
