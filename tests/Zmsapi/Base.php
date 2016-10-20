<?php
/**
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi\Tests;

abstract class Base extends \BO\Slim\PhpUnit\Base
{
    protected $namespace = '\\BO\\Zmsapi\\';

    public function readFixture($filename)
    {
        $path = dirname(__FILE__) . '/fixtures/' . $filename;
        if (!is_readable($path) || !is_file($path)) {
            throw new \Exception("Fixture $path is not readable");
        }
        return file_get_contents($path);
    }
}
