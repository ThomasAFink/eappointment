<?php

namespace BO\Zmsentities\Tests;

class AvailabilityTest extends EntityCommonTests
{

    const DEFAULT_TIME = '2016-01-01 12:50:00';

    public $entityclass = '\BO\Zmsentities\Availability';

    public $collectionclass = '\BO\Zmsentities\Collection\AvailabilityList';

    protected static $weekdayNameList = [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday'
    ];

    public function testHasDay()
    {
        $entity = new $this->entityclass();
        $time = new \DateTimeImmutable(self::DEFAULT_TIME);
        $now = new \DateTimeImmutable(self::DEFAULT_TIME);
        $entity['startDate'] = $time->getTimestamp();
        $entity['startTime'] = $time->format('H:i');
        $entity['endDate'] = $time->modify("+2month")
            ->getTimestamp();
        $entity['endTime'] = $time->modify("+2month 17:10:00")
            ->format('H:i');
        $entity['weekday']['friday'] = 1;
        $entity['repeat']['afterWeeks'] = 2;

        $this->assertTrue($entity->getDuration() == 60, "Availability duration does not match");
        $this->assertFalse(
            $entity->hasDate($time, $now),
            'Availability should not be valid on startDate, case bookable on 1 day in the future'
        );
        $this->assertTrue(
            $entity->hasDate($time->modify('+2week'), $now),
            'Availability should be valid in the second week afterwards'
        );
        $this->assertFalse(
            $entity->hasDate($time->modify('+3days'), $now),
            'Availability should not be valid on a monday if only friday is given'
        );
        $entity['weekday']['monday'] = 1;
        $this->assertTrue(
            $entity->hasDate($time->modify('+3days'), $now),
            'Availability should be valid on a monday if friday and monday is given'
        );
        $this->assertFalse(
            $entity->hasDate($time->modify('+1week'), $now),
            'Availability should not be valid in the first week afterwards if repeating is set to every 2 weeks'
        );
        $entity['repeat']['weekOfMonth'] = 2;
        $this->assertTrue(
            $entity->hasDate($time->modify('+1week'), $now),
            'Availability should be valid in the second week of the month'
        );
        $this->assertFalse(
            $entity->hasDate($time->modify('+3week'), $now),
            'Availability should be valid in the third week of the month'
        );

        $entity['startDate'] = $time->modify('+1day')
            ->getTimestamp();
        $entity['repeat']['afterWeeks'] = 2;
        $entity['repeat']['weekOfMonth'] = 0;
        $this->assertTrue(
            $entity->hasDate($time->modify('+3week'), $now),
            'Availability on afterWeeks=2 should be valid in the third week after startDate +1 day'
        );
        $entity['endDate'] = $time->modify("-1day")
            ->getTimestamp();
        $this->assertFalse($entity->hasDate($time->modify('+1week'), $now), 'EndDate is smaller than startDate');
    }

    public function testDayOff()
    {
        $dayOffTime = new \DateTimeImmutable(self::DEFAULT_TIME);
        $time = new \DateTimeImmutable('2015-11-26 12:30:00');
        $now = new \DateTimeImmutable('2015-11-26 12:30:00');
        $entity = new $this->entityclass();
        $entity['startDate'] = $time->getTimestamp();
        $entity['endDate'] = $time->modify("+2month")->getTimestamp();
        $dayOff[] = new \BO\Zmsentities\DayOff(array (
            'date' => 1451649000,
            'name' => 'Neujahr'
        ));
        $entity['scope'] = (new \BO\Zmsentities\Scope(
            array('dayoff' => $dayOff)
        ));
        $this->assertTrue(
            $entity->hasDayOff($dayOffTime),
            'Time '. $time->format('Y-m-d') .' must be dayoff time'
        );
        $this->assertFalse(
            $entity->hasDayOff($time),
            'Time '. $time->format('Y-m-d') .' is not a dayoff time'
        );

        $weekDayName = self::$weekdayNameList[$dayOffTime->format('w')];
        $entity['weekday'][$weekDayName] = 1;
        $this->assertFalse(
            $entity->hasDate($dayOffTime, $now),
            'Time '. $dayOffTime->format('Y-m-d') .' is a dayoff time'
        );
    }

    public function testIsBookable()
    {
        $time = new \DateTimeImmutable(self::DEFAULT_TIME);
        $entity = new $this->entityclass();
        $entity['startDate'] = $time->getTimestamp();
        $entity['endDate'] = $time->modify("+2month")->getTimestamp();
        $endInDays = $entity->bookable['endInDays'];

        $this->assertFalse(
            $entity->isBookable($time, $time),
            'Availability default should be, that you cannot reserve an appointment for today'
        );
        $this->assertTrue(
            $entity->isBookable($time->modify('+1day'), $time),
            'Availability default should be, that you cannot reserve an appointment for the next day'
        );

        try {
            $entity->bookable['endInDays'] = null;
            $entity->isBookable($time, $time);
            $this->fail("Expected exception ProcessBookableFailed not thrown");
        } catch (\BO\Zmsentities\Exception\ProcessBookableFailed $exception) {
            $this->assertEquals(
                'Undefined end time for booking, try to set the scope properly',
                $exception->getMessage()
            );
        }
        $entity->bookable['endInDays'] = $endInDays;
        try {
            $entity->bookable['startInDays'] = null;
            $entity->isBookable($time, $time);
            $this->fail("Expected exception ProcessBookableFailed not thrown");
        } catch (\BO\Zmsentities\Exception\ProcessBookableFailed $exception) {
            $this->assertEquals(
                'Undefined start time for booking, try to set the scope properly',
                $exception->getMessage()
            );
        }
        $entity->bookable['endInDays'] = null;

        $entity['scope'] = (new \BO\Zmsentities\Scope())->getExample();
        $this->assertFalse(
            $entity->isBookable($time, $time),
            'Availability default should be, that you cannot reserve an appointment for today'
        );

        $entity->bookable['startInDays'] = 2;
        $entity->bookable['endInDays'] = 1;
        $this->assertFalse(
            $entity->isBookable($time->modify("+1month"), $time),
            'Availability endInDays is before startInDays'
        );
    }

    public function testSlotList()
    {
        $slotListResult = new \BO\Zmsentities\Collection\SlotList(
            array (
                new \BO\Zmsentities\Slot(
                    array (
                        'time' => '12:00',
                        'public' => 0,
                        'callcenter' => 0,
                        'intern' => 3
                    )
                ),
                new \BO\Zmsentities\Slot(
                    array (
                        'time' => '13:30',
                        'public' => 0,
                        'callcenter' => 0,
                        'intern' => 3
                    )
                ),
                new \BO\Zmsentities\Slot(
                    array (
                        'time' => '15:00',
                        'public' => 0,
                        'callcenter' => 0,
                        'intern' => 3
                    )
                ),
                new \BO\Zmsentities\Slot(
                    array (
                        'time' => '16:30',
                        'public' => 0,
                        'callcenter' => 0,
                        'intern' => 3
                    )
                )
            )
            // If the last slot is equal to the stop time, there should not be a slot! (Do not remove this comment)
            // 4 => array (
            // 'time' => '18:00',
            // 'public' => 0,
            // 'callcenter' => 0,
            // 'intern' => 3,
            // ),
        );
        $time = new \DateTimeImmutable('12:00:00');
        $entity = new $this->entityclass(
            [
                'startTime' => $time->format('H:i'),
                'endTime' => $time->modify("18:00:00")
                    ->format('H:i'),
                'slotTimeInMinutes' => 90
            ]
        );
        $entity['workstationCount']['intern'] = 3;
        $slotList = $entity->getSlotList();
        $this->assertEquals($slotList, $slotListResult);
        $entity['slotTimeInMinutes'] = 0;
        $slotList = $entity->getSlotList();
        $this->assertEquals($slotList, new \BO\Zmsentities\Collection\SlotList());
        // var_dump((string)$entity);
    }

    public function testSlotListRealExample()
    {
        $entity = new $this->entityclass(
            [
                'id' => '93181',
                'weekday' => array (
                    'monday' => '0',
                    'tuesday' => '4',
                    'wednesday' => '0',
                    'thursday' => '0',
                    'friday' => '0',
                    'saturday' => '0',
                    'sunday' => '0'
                ),
                'repeat' => array (
                    'afterWeeks' => '2',
                    'weekOfMonth' => '0'
                ),
                'bookable' => array (
                    'startInDays' => '0',
                    'endInDays' => '60'
                ),
                'workstationCount' => array (
                    'public' => '2',
                    'callcenter' => '2',
                    'intern' => '2'
                ),
                'slotTimeInMinutes' => '15',
                'startDate' => '1461024000',
                'endDate' => '1461024000',
                'startTime' => '12:00:00',
                'endTime' => '16:00:00',
                'multipleSlotsAllowed' => '0'
            ]
        );
        $slotList = $entity->getSlotList();
        $this->assertTrue(16 == count($slotList));
        //$this->assertTrue($entity->hasDate(new \DateTime('2016-04-19')));
    }

    public function testWithCalculatedSlots()
    {
        $entity = (new $this->entityclass())->getExample();
        $withCalculatedSlots = $entity->withCalculatedSlots();
        $this->assertTrue(81 == $withCalculatedSlots['workstationCount']['public'], $withCalculatedSlots);
    }

    public function testToString()
    {
        $entity = (new $this->entityclass())->getExample();
        $time = new \DateTimeImmutable(self::DEFAULT_TIME);
        $entity['startDate'] = $time->getTimestamp();
        $entity['endDate'] = $time->modify("+2month")->getTimestamp();
        $entity['repeat']['afterWeeks'] = 1;
        $entity['repeat']['weekOfMonth'] = 1;
        $this->assertContains('Availability #1234', $entity->__toString());
    }

    public function testCollection()
    {
        $time = new \DateTimeImmutable(self::DEFAULT_TIME);
        $collection = new $this->collectionclass();
        $entity = (new $this->entityclass())->getExample();
        $collection->addEntity($entity);
        
        $entityOH = $this->getExampleWithTypeOpeningHours();
        $collection->addEntity($entityOH);
        $this->assertTrue($collection->isOpened($time));

        $this->assertEntityList($this->entityclass, $collection);
        $this->assertTrue(
            2 == count($collection),
            'Amount of entities in collection failed, 2 expected (' .
            count($collection) . ' found)'
        );

        $this->assertTrue(
            10 == $collection->getMaxWorkstationCount(),
            'Failed to get correct max workstation count, 10 expected'
        );

        $this->assertTrue(
            81 == $collection->withCalculatedSlots()[0]['workstationCount']['public'],
            'Failed to get list with calculated slots'
        );
    }
    
    public function testUnopened()
    {
        $time = new \DateTimeImmutable(self::DEFAULT_TIME);
        $collection = new $this->collectionclass();
        $entityOH = $this->getExampleWithTypeOpeningHours();
        $entityOH->endTime = '11:00:00';
        $collection->addEntity($entityOH);
        $this->assertFalse($collection->isOpened($time));
    }

    protected function getExampleWithTypeOpeningHours()
    {
        return new $this->entityclass(
            [
                'id' => '93181',
                'weekday' => array (
                    'monday' => '0',
                    'tuesday' => '4',
                    'wednesday' => '0',
                    'thursday' => '0',
                    'friday' => '0',
                    'saturday' => '0',
                    'sunday' => '0'
                ),
                'repeat' => array (
                    'afterWeeks' => '2',
                    'weekOfMonth' => '0'
                ),
                'bookable' => array (
                    'startInDays' => '0',
                    'endInDays' => '60'
                ),
                'workstationCount' => array (
                    'public' => '2',
                    'callcenter' => '2',
                    'intern' => '2'
                ),
                'slotTimeInMinutes' => '15',
                'startDate' => '1461024000',
                'endDate' => '1461024000',
                'startTime' => '12:00:00',
                'endTime' => '16:00:00',
                'multipleSlotsAllowed' => '0',
                'scope' => array('id'=>141),
                'type' => 'openinghours'
            ]
        );
    }
}
