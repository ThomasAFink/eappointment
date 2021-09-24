<?php

namespace BO\Zmsdb\Tests;

use \BO\Zmsdb\Workstation as Query;
use \BO\Zmsentities\Workstation as Entity;
use \BO\Zmsentities\Useraccount as UserAccountEntity;

class LogoutTest extends Base
{
    public function testBasic()
    {
        $query = new Query();
        $now = new \DateTimeImmutable("2016-04-01 11:55");
        //superuser bo
        $userAccount = new UserAccountEntity(array(
            'id' => 'superuser',
            'password' => md5("vorschau")
        ));

        $workstation = $query->writeEntityLoginByName($userAccount->id, $userAccount->password, $now, 2);
        $this->assertEquals(true, $workstation->hasAuthKey());
        $workstation = $query->writeEntityLogoutByName($userAccount->id, 2);
        $this->assertEntity("\\BO\\Zmsentities\\Workstation", $workstation);
        $this->assertEquals(false, $workstation->hasAuthKey());
    }
}
