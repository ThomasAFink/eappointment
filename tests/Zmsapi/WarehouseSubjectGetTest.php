<?php

namespace BO\Zmsapi\Tests;

use BO\Zmsapi\Helper\User;

class WarehouseSubjectGetTest extends Base
{
    protected $classname = "WarehouseSubjectGet";

    public function testRendering()
    {
        $workstation = $this->setWorkstation(138, 'berlinonline', 141);
        $workstation->getUseraccount()->setRights('scope');
        $response = $this->render(['subject' => 'waitingscope'], [], []);
        $this->assertContains('exchange.json', (string)$response->getBody());
        $this->assertContains('"141","2015-01-02","2016-04-01"', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testFilteredByScope()
    {
        $workstation = $this->setWorkstation(138, 'berlinonline', 140);
        $workstation->getUseraccount()->setRights('scope');
        $response = $this->render(['subject' => 'waitingscope'], [], []);
        $this->assertNotContains('"141","2015-01-02","2016-04-01"', (string)$response->getBody());
    }

    public function testSubjectDepartment()
    {
        $workstation = $this->setWorkstation(138, 'berlinonline', 141);
        $workstation->getUseraccount()->setRights('scope', 'department');
        $this->setDepartment(74);
        $response = $this->render(['subject' => 'waitingdepartment'], [], []);
        $this->assertContains('"data":[["74"', (string)$response->getBody());
    }

    public function testFilteredByDepartment()
    {
        $workstation = $this->setWorkstation(138, 'berlinonline', 141);
        $workstation->getUseraccount()->setRights('scope', 'department');
        $this->setDepartment(75);
        $response = $this->render(['subject' => 'waitingdepartment'], [], []);
        $this->assertNotContains('"data":[["74"', (string)$response->getBody());
    }

    public function testSubjectOrganisation()
    {
        $workstation = $this->setWorkstation(138, 'berlinonline', 141);
        $workstation->getUseraccount()->setRights('scope', 'department');
        $response = $this->render(['subject' => 'waitingorganisation'], [], []);
        $this->assertContains('"data":[["71"', (string)$response->getBody());
    }

    public function testFilteredByOrganisation()
    {
        //there is no statistic entry for scope 143, department 75, organisation 72, so is not available
        $workstation = $this->setWorkstation(137, 'berlinonline', 143); //organisation 72
        $workstation->getUseraccount()->setRights('scope', 'department', 'organisation');
        $response = $this->render(['subject' => 'waitingorganisation'], [], []);
        $this->assertNotContains('"data":[["72"', (string)$response->getBody());
    }

    public function testNoRights()
    {
        $this->setWorkstation(137, 'testuser', 141);
        $this->expectException('\BO\Zmsentities\Exception\UserAccountMissingRights');
        $this->render([], [], []);
    }

    public function testNotFound()
    {
        $this->expectException('\BO\Zmsapi\Exception\Warehouse\UnknownReportType');
        $workstation = $this->setWorkstation(138, 'berlinonline', 140);
        $workstation->getUseraccount()->setRights('scope');
        $response = $this->render(['subject' => 'unittest'], [], []);
    }
}
