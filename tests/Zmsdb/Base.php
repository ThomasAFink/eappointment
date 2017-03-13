<?php

namespace BO\Zmsdb\Tests;

abstract class Base extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        \BO\Zmsdb\Connection\Select::setTransaction();
        \BO\Zmsdb\Connection\Select::setProfiling();
        \BO\Zmsdb\Connection\Select::setQueryCache(false);
    }

    public function tearDown()
    {
        //error_log("Memory usage " . round(memory_get_peak_usage() / 1024, 0) . "kb");
        \BO\Zmsdb\Connection\Select::writeRollback();
        \BO\Zmsdb\Connection\Select::closeWriteConnection();
        \BO\Zmsdb\Connection\Select::closeReadConnection();
    }

    public function readFixture($filename)
    {
        $path = dirname(__FILE__) . '/fixtures/' . $filename;
        if (!is_readable($path) || !is_file($path)) {
            throw new \Exception("Fixture $path is not readable");
        }
        return file_get_contents($path);
    }

    public function assertEntity($entityClass, $entity)
    {
        $this->assertInstanceOf($entityClass, $entity);
        $this->assertTrue($entity->testValid());
    }

    public function assertEntityList($entityClass, $entityList)
    {
        foreach ($entityList as $entity) {
            $this->assertEntity($entityClass, $entity);
        }
    }

    protected function dumpProfiler()
    {
        echo "\nProfiler:\n";
        /*
        $profiles = \BO\Zmsdb\Connection\Select::getReadConnection()->getProfiler()->getProfiles();
        foreach ($profiles as $profile) {
            $this->dumpProfile($profile);
        }*/
        $profiling = \BO\Zmsdb\Connection\Select::getReadConnection()->fetchAll('SHOW PROFILES');
        foreach ($profiling as $profile) {
            echo $profile['Query_ID']. ' ' . $profile['Duration']. ' ' . $profile['Query'] . "\n";
        }
        //var_dump($profiling);
    }

    protected function dumpProfile($profile)
    {
        $statement = $profile['statement'];
        $statement = preg_replace('#\s+#', ' ', $statement);
        $statement = substr($statement, 0, 250);
        echo round($profile['duration'] * 1000, 6) . "ms $statement \n";
    }
}
