<?php

namespace BO\Zmsclient\Tests;

use \BO\Mellon\Validator;

class HttpTest extends Base
{
    public function testStatus()
    {
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/status/');
        $response = new \BO\Zmsclient\Psr7\Response();
        $entity = $result->getEntity();
        $response = \BO\Zmsclient\Status::testStatus($response, $entity);
        $this->assertTrue($entity instanceof \BO\Zmsentities\Schema\Entity);
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result->getResponse());
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface', $result->getRequest());
    }

    public function testCollection()
    {
        $http = $this->createHttpClient();
        $calendar = new \BO\Zmsentities\Calendar();
        $calendar->setFirstDayTime(new \DateTime('2016-05-30'));
        $calendar->setLastDayTime(new \DateTime('2016-05-30'));
        $calendar->addScope("141");
        $result = $http->readPostResult('/process/status/free/', $calendar);
        $collection = $result->getCollection();
        $this->assertTrue($collection instanceof \BO\Zmsentities\Collection\Base);
    }

    public function testMails()
    {
        $http = $this->writeTestLogin();
        $entity = \BO\Zmsentities\Mail::createExample();
        $entity->process = $http->readGetResult('/process/82252/12a2/')->getEntity();
        $result = $http->readPostResult('/mails/', $entity, array('resolveReferences' => 0));
        $entity = $result->getEntity();
        $this->assertTrue($entity instanceof \BO\Zmsentities\Mail);
        $mailId = $entity->id;

        $result = $http->readGetResult('/mails/', array('resolveReferences' => 0));
        $data = $result->getData();
        $this->assertTrue($data[0] instanceof \BO\Zmsentities\Mail);

        $result = $http->readDeleteResult("/mails/$mailId/", array('resolveReferences' => 0));
        $entity = $result->getEntity();
        $this->assertTrue($entity instanceof \BO\Zmsentities\Mail);
        $this->writeTestLogout($http);
    }

    public function testHtml()
    {
        $this->expectException('\BO\Zmsclient\Exception');
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/doc/index.html');
        $result->getEntity();
    }

    public function testToken()
    {
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/config/', null, 'a9b215f1-e460-490c-8a0b-6d42c274d5e4');
        $entity = $result->getEntity();
        $this->assertTrue($entity instanceof \BO\Zmsentities\Config);
    }

    public function testTokenFailed()
    {
        $this->expectException('\BO\Zmsclient\Exception');
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/config/');
        $result->getEntity();
    }

    public function testWrongFormat()
    {
        $this->expectException('\BO\Zmsclient\Exception');
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/doc/swagger.json');
        $result->getEntity();
    }

    public function testUnknownUrl()
    {
        $this->expectException('\BO\Zmsclient\Exception');
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/unknownUri/');
        $result->getEntity();
    }

    public function testDeadlock()
    {
        $this->expectException('\BO\Zmsclient\Exception');
        $http = $this->createHttpClient();
        $result = $http->readGetResult('/status/deadlock/');
        $result->getEntity();
    }

    protected function writeTestLogin()
    {
        $http = $this->createHttpClient();
        $userAccount = new \BO\Zmsentities\Useraccount(array(
            'id' => 'berlinonline',
            'password' => '1palme1'
        ));
        try {
            $workstation = $http->readPostResult('/workstation/login/', $userAccount)->getEntity();
        } catch (\BO\Zmsclient\Exception $exception) {
            if (isset($exception->data)) {
                $workstation = new \BO\Zmsentities\Workstation($exception->data);
            } else {
                throw $exception;
            }
        }
        if (isset($workstation->authkey)) {
            \BO\Zmsclient\Auth::setKey($workstation->authkey);
            $this->assertEquals($workstation->authkey, \BO\Zmsclient\Auth::getKey());
        }
        return $http;
    }

    protected function writeTestLogout($http)
    {
        $http->readDeleteResult('/workstation/login/berlinonline/')->getEntity();
    }
}
