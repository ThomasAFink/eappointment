<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\ExchangeClientorganisation as Query;
use \BO\Zmsentities\Exchange;
use \DateTimeImmutable as DateTime;

class ExchangeClientorganisationTest extends Base
{
    public function testBasic()
    {
        $query = new Query();
        $entity = $query->readEntity(71, new DateTime('2016-04-01'), new DateTime('2016-04-01'));
        $this->assertEntity("\\BO\\Zmsentities\\Exchange", $entity);
        $this->assertEquals(1, count($entity->data));
        $this->assertEquals(84, $entity->data[0][4]); // clients COUNT
        $this->assertEquals(16, $entity->data[0][5]); // clients missed COUNT
        $this->assertEquals(84, $entity->data[0][6]); // clients with appointment COUNT
        $this->assertEquals(61, $entity->data[0][8]); // requests COUNT
    }

    public function testSubjectList()
    {
        $query = new Query();
        $entity = $query->readSubjectList();
        $this->assertEntity("\\BO\\Zmsentities\\Exchange", $entity);
        $this->assertEquals(1, count($entity->data));
        $this->assertEquals(71, $entity->data[0][0]); // department id
        $this->assertContains('Charlottenburg-Wilmersdorf', $entity->data[0][3]); //department name
    }

    public function testPeriodList()
    {
        $query = new Query();
        $entity = $query->readPeriodList(71);
        $this->assertEntity("\\BO\\Zmsentities\\Exchange", $entity);
        $this->assertEquals(3, count($entity->data));
    }
}
