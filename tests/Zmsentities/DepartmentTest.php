<?php

namespace BO\Zmsentities\Tests;

use BO\Zmsentities\Collection\ClusterList;
use BO\Zmsentities\Collection\ScopeList;
use BO\Zmsentities\Cluster;
use BO\Zmsentities\Scope;

class DepartmentTest extends EntityCommonTests
{

    public $entityclass = '\BO\Zmsentities\Department';

    public function testBasic()
    {
        $entity = $this->getExample();
        $this->assertTrue(4 == count($entity->getNotificationPreferences()), 'preferences not accessible');
        $this->assertContains('Flughafen Schönefeld', $entity->getContactPerson(), 'getting contact person failed');
        $this->assertTrue(15831 == $entity->getContact()->postalCode, 'contact not accessible');
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
        $example['clusters'][] = $cluster;
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
}
