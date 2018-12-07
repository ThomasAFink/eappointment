<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\Process as Query;
use \BO\Zmsentities\Process as Entity;
use \BO\Zmsentities\Calendar;

/**
 * @SuppressWarnings(TooManyPublicMethods)
 * @SuppressWarnings(Coupling)
 *
 */
class ProcessConflictTest extends Base
{
    public function testBasic()
    {
        $startDate = new \DateTimeImmutable("2016-04-01 11:55");
        $endDate = new \DateTimeImmutable("2016-04-30 23:59");
        $scope = (new \BO\Zmsdb\Scope())->readEntity(141, 1, true);
        $conflictList = (new \BO\Zmsdb\Process())->readConflictListByScopeAndTime($scope, $startDate, $endDate, 0);
        $this->assertEquals(10, $conflictList->count());
    }

    public function testOverbookedOnDay()
    {
        $startDate = new \DateTimeImmutable("2016-05-13 11:55");
        $scope = (new \BO\Zmsdb\Scope())->readEntity(147, 1, true);
        $conflictList = (new \BO\Zmsdb\Process())->readConflictListByScopeAndTime($scope, $startDate, null, 0);
        $this->assertEquals(21, $conflictList->count());
    }

    public function testEqual()
    {
        $startDate = new \DateTimeImmutable("2016-05-13 11:55");
        $scope = (new \BO\Zmsdb\Scope())->readEntity(141, 1, true);
        $availabilityList = (new \BO\Zmsdb\Availability())->readAvailabilityListByScope($scope, 1);
        $availabilityCopy = clone $availabilityList->withDateTime($startDate)->getFirst();
        $availabilityCopy->endTime = $availabilityCopy->getEndDateTime()->format('H:i');
        (new \BO\Zmsdb\Availability())->writeEntity($availabilityCopy);
        $conflictList = (new \BO\Zmsdb\Process())->readConflictListByScopeAndTime($scope, $startDate, null, 0);
        $this->assertEquals(1, $conflictList->count());
        $this->assertEquals('Zwei Öffnungszeiten sind gleich.', $conflictList->getFirst()->getAmendment());
        $this->assertEquals('conflict', $conflictList->getFirst()->getStatus());
    }

    public function testOverLap()
    {
        $startDate = new \DateTimeImmutable("2016-05-13 11:55");
        $scope = (new \BO\Zmsdb\Scope())->readEntity(141, 1, true);
        $availabilityList = (new \BO\Zmsdb\Availability())->readAvailabilityListByScope($scope, 1);
        $availabilityCopy = clone $availabilityList->withDateTime($startDate)->getFirst();
        $availabilityCopy->endTime = $availabilityCopy->getEndDateTime()->modify('+2 hour')->format('H:i');
        (new \BO\Zmsdb\Availability())->writeEntity($availabilityCopy);
        $conflictList = (new \BO\Zmsdb\Process())->readConflictListByScopeAndTime($scope, $startDate, null, 0);
        $this->assertEquals(1, $conflictList->count());
        $this->assertEquals('Zwei Öffnungszeiten überschneiden sich.', $conflictList->getFirst()->getAmendment());
        $this->assertEquals('conflict', $conflictList->getFirst()->getStatus());
    }
}
