<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\ExchangeClientscope as Query;
use \BO\Zmsentities\Exchange;
use \DateTimeImmutable as DateTime;

class ExchangeClientscopeTest extends Base
{
    public function testBasic()
    {
        $query = new Query();
        $entity = $query->readEntity(141, new DateTime('2016-04-01'), new DateTime('2016-04-01'));
        $this->assertEntity("\\BO\\Zmsentities\\Exchange", $entity);
        $this->assertEquals(1, count($entity->data));
        $this->assertEquals(84, $entity->data[0][4]); // clients COUNT
        $this->assertEquals(16, $entity->data[0][5]); // clients missed COUNT
        $this->assertEquals(84, $entity->data[0][6]); // clients with appointment COUNT
        $this->assertEquals(59, $entity->data[0][8]); // requests COUNT
        $this->assertEquals('Charlottenburg-Wilmersdorf', $entity->data[0][11]); // organisation
    }

    public function testSubjectList()
    {
        $query = new Query();
        $entity = $query->readSubjectList();
        $this->assertEntity("\\BO\\Zmsentities\\Exchange", $entity);
        $this->assertEquals(1, count($entity->data));
        $this->assertEquals(141, $entity->data[0][0]); // scope id
        $this->assertContains('Heerstraße', $entity->data[0][3]); //scope name
    }

    public function testPeriodList()
    {
        $query = new Query();
        $entity = $query->readPeriodList(141);
        $this->assertEntity("\\BO\\Zmsentities\\Exchange", $entity);
        $this->assertEquals(17, count($entity->data));
    }
}
