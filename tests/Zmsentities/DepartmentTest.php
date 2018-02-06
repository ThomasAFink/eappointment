<?php

namespace BO\Zmsentities\Tests;

use BO\Zmsentities\Collection\ClusterList;
use BO\Zmsentities\Collection\ScopeList;
use BO\Zmsentities\Cluster;
use BO\Zmsentities\Scope;

class DepartmentTest extends EntityCommonTests
{
    public $entityclass = '\BO\Zmsentities\Department';

    public $collectionclass = '\BO\Zmsentities\Collection\DepartmentList';

    public function testBasic()
    {
        $entity = $this->getExample();
        $this->assertTrue(4 == count($entity->getNotificationPreferences()), 'preferences not accessible');
        $this->assertContains('Flughafen Schönefeld', $entity->getContactPerson(), 'getting contact person failed');
        $this->assertTrue(15831 == $entity->getContact()->postalCode, 'contact not accessible');
        $this->assertTrue($entity->hasNotificationEnabled());
    }

    public function testClusterDuplicates()
    {
        $example = $this->getExample();
        $example['scopes'] = new ScopeList();
        $cluster = new Cluster();
        $scope = new Scope([
            'id' => 1234
        ]);
        $cluster['scopes'][] = $scope;
        $example['clusters'] = (new ClusterList())->addEntity($cluster);
        $example['scopes'][] = $scope;
        $reduced = $example->withOutClusterDuplicates();
        $this->assertTrue($example['scopes']->hasEntity(1234));
        $this->assertFalse($reduced['scopes']->hasEntity(1234));
        $this->assertTrue($example['clusters'][0]['scopes']->hasEntity(1234));
        $this->assertTrue($reduced['clusters'][0]['scopes']->hasEntity(1234));

        $this->assertFalse($example['scopes']->hasEntity(141));
        $example['scopes']->addEntity(new \BO\Zmsentities\Scope(array('id' => 141)));
        $reduced = $example->withOutClusterDuplicates();
        $this->assertTrue($example['scopes']->hasEntity(141));
    }

    public function testCollection()
    {
        $entity = $this->getExample();

        $entity['scopes'] = new ScopeList();
        $cluster = (new Cluster())->getExample();
        $scope = new Scope([
            'id' => 1234
        ]);
        $cluster['scopes'][] = $scope;
        $entity['clusters'] = (new ClusterList());
        $entity['clusters']->addEntity($cluster);
        $entity['clusters']->addEntity($cluster);
        $entity['scopes'][] = $scope;
        $entity['scopes'][] = $scope;

        $collection = new $this->collectionclass();
        $collection->addEntity($entity);
        $this->assertEquals(2, $collection->getUniqueScopeList()->count());

        $this->assertEquals(2, $collection->getFirst()->scopes->count());
        $collection = $collection->withOutClusterDuplicates();
        $this->assertEquals(0, $collection->getFirst()->scopes->count());
    }

    public function testCollectionSortByName()
    {
        $departmentA = new \BO\Zmsentities\Department();
        $departmentA->name = 'A-Department';
        $departmentB = clone $departmentA;
        $departmentB->name = 'B-Department';

        $clusterA = new Cluster();
        $clusterA->name = 'A-Cluster';
        $clusterB = clone $clusterA;
        $clusterB->name = 'B-Cluster';

        $scopeA = (new Scope)->getExample();
        $scopeA->provider['name'] = 'A-Scope';
        $scopeB = clone $scopeA;
        $scopeB->provider['name'] = 'B-Scope';

        $clusterA->scopes->addEntity($scopeB);
        $clusterA->scopes->addEntity($scopeA);
        $clusterB->scopes->addEntity($scopeB);
        $clusterB->scopes->addEntity($scopeA);

        $departmentA['clusters'] = (new ClusterList());
        $departmentA['clusters']->addEntity($clusterB);
        $departmentA['clusters']->addEntity($clusterA);

        $departmentA['scopes'] = new ScopeList();
        $departmentA['scopes']->addEntity($scopeB);
        $departmentA['scopes']->addEntity($scopeA);

        $departmentB['clusters'] = (new ClusterList());
        $departmentB['clusters']->addEntity($clusterB);
        $departmentB['clusters']->addEntity($clusterA);

        $departmentB['scopes'] = new ScopeList();
        $departmentB['scopes']->addEntity($scopeB);
        $departmentB['scopes']->addEntity($scopeA);

        $collection = new $this->collectionclass();
        $collection->addEntity($departmentB);
        $collection->addEntity($departmentA);

        $this->assertEquals('A-Department', $collection->sortByName()->getFirst()->name);
        $this->assertEquals('A-Cluster', $collection->sortByName()->getFirst()->clusters->getFirst()->name);
        $this->assertEquals(
            'A-Scope',
            $collection->sortByName()->getFirst()->clusters->getFirst()->scopes->getFirst()->provider['name']
        );
    }

    public function testCollectionWithAccess()
    {
        $entity = $this->getExample();
        $collection = new $this->collectionclass();
        $collection->addEntity($entity);

        $useraccount = (new \BO\Zmsentities\Useraccount())->getExample();
        $useraccount->departments = $collection;
        $collection->addEntity(clone $entity);

        $accessibleList = $collection->withAccess($useraccount);
        $this->assertEquals(2, $accessibleList->count());

        $useraccount->setRights('organisation');
        $accessibleList = $collection->withAccess($useraccount);
        $this->assertEquals(2, $accessibleList->count());
    }

    public function testGetDayoffList()
    {
        $entity = $this->getExample();
        $entity->dayoff = $entity->getDayoffList()->getArrayCopy();
        $entity->dayoff[] = [
            "date" => 1447922381000,
            "name" => "TestAsArray"
        ];
        $this->assertEquals(3, $entity->getDayoffList()->count());
        $this->assertEntityList('\BO\Zmsentities\Dayoff', $entity->getDayoffList());
    }

    public function testWithCompleteScopeList()
    {
        $entity = $this->getExample();
        $cluster = (new Cluster())->getExample();
        $scope = (new Scope())->getExample();
        $scope->id = 141;
        $cluster['scopes'][] = $scope;
        $entity['clusters'] = (new ClusterList());
        $entity['clusters']->addEntity($cluster);
        $this->assertEquals(4, $entity->withCompleteScopeList()->scopes->count());
    }
}
