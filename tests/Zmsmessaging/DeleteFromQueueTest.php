<?php

namespace BO\Zmsmessaging\Tests;

class DeleteFromQueueTest extends Base
{
    protected function getApiCalls()
    {
        return [
            [
                'function' => 'setUserInfo',
                'parameters' => [
                    '_system_messenger',
                    'zmsmessaging'
                ]
            ],
            [
                'function' => 'readGetResult',
                'url' => '/mails/',
                'response' => $this->readFixture("GET_mails_queue.json"),
            ],
            [
                'function' => 'readDeleteResult',
                'url' => '/mails/1234/',
                'response' => $this->readFixture("GET_mail.json"),
            ],
        ];
    }

    public function testDeleteMailFromQueue()
    {
        $entity = (new \BO\Zmsentities\Mail())->getExample();
        \App::$messaging = new \BO\Zmsmessaging\Mail();
        $this->assertTrue(\App::$messaging->deleteEntityFromQueue($entity));
    }
}
