<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\Cluster as Query;
use \BO\Zmsentities\Cluster as Entity;

class ClusterTest extends Base
{
    public function testBasic()
    {
        $query = new Query();
        $entity = $query->readEntity(4);
        $this->assertEntity("\\BO\\Zmsentities\\Cluster", $entity);

        $entity = $query->readEntity(999);
        $this->assertTrue(null === $entity);
    }

    public function testReadList()
    {
        $query = new Query();
        $entityList = $query->readList(1);
        $this->assertEntityList("\\BO\\Zmsentities\\Cluster", $entityList);
    }

    public function testReadListByDepartment()
    {
        $query = new Query();
        $input = $this->getTestEntity();
        $entityList = $query->readByDepartmentId(72, 1); //by Egon-Erwin-Kisch-Str.
        $this->assertEntityList("\\BO\\Zmsentities\\Cluster", $entityList);
        //Bürgeramt 1 (Neu- Hohenschönhausen) Egon-Erwin-Kisch-Straße exists
        $this->assertEquals(true, $entityList->hasScope('134'));
        $this->assertEquals(true, $entityList->hasScope('135')); //Bürgeramt 2 (Lichtenberg) Normannenstr. exists
    }

    public function testReadIsOpenedScopeList()
    {
        $query = new Query();
        $input = $this->getTestEntity();
        $now = new \DateTimeImmutable("2016-04-01 11:55");
        $entityList = $query->readIsOpenedScopeList(60, $now); //by Egon-Erwin-Kisch-Str. Cluster
        $this->assertEquals(true, 0 <= count($entityList));
    }

    /*
    public function testReadScopeWithShortestWaitingTime()
    {
        $query = new Query();
        $input = $this->getTestEntity();
        $now = new \DateTimeImmutable("2016-04-01 11:55");
        //by Schöneberg with test ghostWorkstationCount of 3
        $scope = $query->readScopeWithShortestWaitingTime(4, $now);
        $this->assertTrue(146 == $scope->id);
    }
    */

    public function testWriteEntity()
    {
        $query = new Query();
        $input = $this->getTestEntity();
        $entity = $query->writeEntity($input);
        $this->assertEquals('Bürger- und Standesamt', $entity->getName());

        $entity->name = 'Bürger- und Standesamt Test';
        $entity = $query->updateEntity($entity->id, $entity);
        $this->assertEquals('Bürger- und Standesamt Test', $entity->getName());

        $entityList = $query->readList(1);
        $this->assertEquals(true, $entityList->hasEntity($entity->id)); //last inserted

        $deleteTest = $query->deleteEntity($entity->id);
        $this->assertTrue($deleteTest, "Failed to delete Cluster from Database.");
    }

    protected function getTestEntity()
    {
        return $input = (new Entity())->getExample();
    }
}
