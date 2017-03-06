<?php

namespace BO\Zmsentities\Tests;

class QueueListTest extends EntityCommonTests
{
    const DEFAULT_TIME = '2016-11-19 08:50:00';

    const FAKE_WAITINGNUMBER = 1002;

    public $entityclass = '\BO\Zmsentities\Queue';

    public $collectionclass = '\BO\Zmsentities\Collection\QueueList';

    public function testBasic()
    {
        $now = new \DateTimeImmutable(self::DEFAULT_TIME);

        $entityWithAppointment = (new $this->entityclass())->getExample();
        $entityWithoutAppointment = (clone $entityWithAppointment);
        $entityWithAppointment->withAppointment = true;
        $entityWithAppointment->status = 'called';
        $entityWithAppointment->number = '122';

        $collection = new $this->collectionclass();
        $collection->addEntity($entityWithAppointment);
        $collection->addEntity($entityWithoutAppointment);
        $collection->withSortedArrival();

        $this->assertEquals(1, $collection->withAppointment()->count());
        $this->assertEquals(1, $collection->withOutAppointment()->count());

        $this->assertEquals(1, $collection->withStatus(['called'])->count());

        $withWaitingTime = $collection->withEstimatedWaitingTime(10, 1, $now);
        $estimatedWaitingData = $withWaitingTime->getEstimatedWaitingTime(10, 1, $now);
        $this->assertEquals(
            $estimatedWaitingData['waitingTimeEstimate'],
            $withWaitingTime->getLast()->waitingTimeEstimate + 10
        );

        $withWaitingTime = $collection->withEstimatedWaitingTime(10, 2, $now);
        $estimatedWaitingData = $withWaitingTime->getEstimatedWaitingTime(10, 2, $now);
        $this->assertEquals(5, $withWaitingTime->getLast()->waitingTimeEstimate);
        $this->assertEquals(2, $estimatedWaitingData['amountBefore']);

        $this->assertEquals(null, $collection->getQueueByNumber(999));
        $this->assertEquals(null, $collection->getQueuePositionByNumber(999));
    }

    public function testDestinationManipulation()
    {
        $scope = (new \BO\Zmsentities\Scope())->getExample();
        $cluster = (new \BO\Zmsentities\Cluster())->getExample();
        $entity = (new $this->entityclass())->getExample();
        $collection = new $this->collectionclass();
        $collection->addEntity($entity);

        $withPickup = $collection->withPickupDestination($scope);
        $this->assertEquals("Ausgabe von Dokumenten", $withPickup->getFirst()->destination);

        $withShortName = $collection->withShortNameDestinationHint($cluster, $scope);
        $this->assertEquals("Zentrale", $withShortName->getFirst()->destinationHint);
    }
}
