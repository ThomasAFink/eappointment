<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\Status as Query;

class StatusTest extends Base
{
    public function testBasic()
    {
        $status = (new Query())->readEntity();
        //var_dump(json_encode($status, JSON_PRETTY_PRINT));
        $this->assertInstanceOf("\\BO\\Zmsentities\\Status", $status);
        //var_dump(\BO\Zmsdb\Connection\Select::getReadConnection()->getProfiler()->getProfiles());
    }
}
