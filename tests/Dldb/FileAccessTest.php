<?php
/**
 * @package 115Mandant
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/
 
namespace BO\Dldb\Tests;

use BO\Dldb\FileAccess;

class FileTest extends \PHPUnit_Framework_TestCase
{

    public function testIds()
    {
        $access = new FileAccess(LOCATION_JSON, SERVICE_JSON);
        $location = $access->fetchLocation(LOCATION_SINGLE);
        $this->assertNotFalse($location);
        $this->assertArrayHasKey('name', $location);
        $service = $access->fetchService(SERVICE_SINGLE);
        $this->assertNotFalse($service);
        $locationList = $access->fetchLocationList(SERVICE_CSV);
        $this->assertArrayHasKey(LOCATION_SINGLE, $locationList);
        $serviceList = $access->fetchServiceList(LOCATION_CSV);
        $this->assertArrayHasKey(SERVICE_SINGLE, $serviceList);
        $authorityList = $access->fetchAuthorityList([SERVICE_SINGLE]);
        $this->assertArrayHasKey(12675, $authorityList);
        $serviceList = $access->fetchServiceFromCsv(SERVICE_CSV);
        $this->assertArrayHasKey(SERVICE_SINGLE, $serviceList);
        $locationList = $access->fetchLocationFromCsv(LOCATION_CSV);
        $this->assertArrayHasKey(LOCATION_SINGLE, $locationList);
        $results = $access->searchLocation('Spandau', SERVICE_CSV);
        $this->assertTrue(count($results) > 0, "No locations found");
        $results = $access->searchService('Pass', LOCATION_CSV);
        $this->assertTrue(count($results) > 0, "No services found");
    }

    public function testFail()
    {
        $access = new FileAccess(LOCATION_JSON, SERVICE_JSON);
        $location = $access->fetchLocation(1);
        $this->assertFalse($location);
        $service = $access->fetchService(1);
        $this->assertFalse($service);
    }

    public function testFailLocation()
    {
        $this->setExpectedException("\BO\Dldb\Exception");
        new FileAccess('dummy', 'dummy');
    }

    public function testFailService()
    {
        $this->setExpectedException("\BO\Dldb\Exception");
        new FileAccess(LOCATION_JSON, 'dummy');
    }

    public function testFailJsonLocation()
    {
        $this->setExpectedException("\BO\Dldb\Exception");
        new FileAccess(__FILE__, 'dummy');
    }

    public function testFailJsonService()
    {
        $this->setExpectedException("\BO\Dldb\Exception");
        new FileAccess(LOCATION_JSON, __FILE__);
    }
}
