<?php

namespace BO\Zmsclient\Tests;

use \BO\Mellon\Validator;

class AuthTest extends Base
{
    /**
     * @runInSeparateProcess
     */
    public function testBasic()
    {
        $this->assertFalse(\BO\Zmsclient\Auth::getKey());
        \BO\Zmsclient\Auth::setKey(123456);
        $this->assertEquals(123456, \BO\Zmsclient\Auth::getKey());
        \BO\Zmsclient\Auth::removeKey();
        $this->assertEquals("", \BO\Zmsclient\Auth::getKey());
    }
}
