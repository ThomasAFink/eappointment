<?php

namespace BO\Zmsapi\Tests;

class OrganisationAddDepartmentTest extends Base
{
    protected $classname = "OrganisationAddDepartment";

    public function testRendering()
    {
        $this->setWorkstation()->getUseraccount()->setRights('organisation');
        $response = $this->render(['id' => 71], [
            '__body' => '{
                  "name": "Test Department"
              }'
        ], []);
        $this->assertContains('department.json', (string)$response->getBody());
        $this->assertContains('"name":"Test Department"', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testUnvalidDepartment()
    {
        $this->setWorkstation()->getUseraccount()->setRights('organisation');
        $this->expectException('\BO\Mellon\Failure\Exception');
        $this->render([], [], []);
    }
}
