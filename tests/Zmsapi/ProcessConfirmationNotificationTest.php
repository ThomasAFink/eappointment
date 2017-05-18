<?php

namespace BO\Zmsapi\Tests;

class ProcessConfirmationNotificationTest extends Base
{
    protected $classname = "ProcessConfirmationNotification";

    const PROCESS_ID = 10029;

    const AUTHKEY = '1c56';

    public function testRendering()
    {
        $response = $this->render([], [
            '__body' => $this->readFixture('GetProcess_10029.json')
        ], []);
        $this->assertContains('notification.json', (string)$response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
        return $response;
    }

    public function testEmpty()
    {
        $this->expectException('\BO\Mellon\Failure\Exception');
        $this->render([], [], []);
    }

    public function testNotFound()
    {
        $this->expectException('\BO\Zmsapi\Exception\Process\ProcessNotFound');
        $this->expectExceptionCode(404);
        $this->render([], [
            '__body' => '{
                "id": 123456,
                "authKey": "'. self::AUTHKEY .'",
                "scope": {
                    "id": 141
                },
                "clients": [
                    {
                        "familyName": "Max Mustermann",
                        "email": "max@service.berlin.de",
                        "telephone": "030 115"
                    }
                ],
                "appointments" : [
                    {
                        "date": 1447869172,
                        "scope": {
                            "id": 141
                        },
                        "slotCount": 2
                    }
                ],
                "status": "confirmed"
            }'
        ], []);
    }

    public function testAuthKeyMatchFailed()
    {
        $this->expectException('\BO\Zmsapi\Exception\Process\AuthKeyMatchFailed');
        $this->expectExceptionCode(403);
        $this->render([], [
            '__body' => '{
                "id": '. self::PROCESS_ID .',
                "authKey": "1234",
                "scope": {
                    "id": 141
                },
                "clients": [
                    {
                        "familyName": "Max Mustermann",
                        "email": "max@service.berlin.de",
                        "telephone": "030 115"
                    }
                ],
                "appointments" : [
                    {
                        "date": 1447869172,
                        "scope": {
                            "id": 141
                        },
                        "slotCount": 2
                    }
                ],
                "status": "confirmed"
            }'
        ], []);
    }

    public function testUnvalidInput()
    {
        $this->expectException('\BO\Zmsentities\Exception\SchemaValidation');
        $this->expectExceptionCode(400);
        $this->render([], [
            '__body' => '{
                "status": "confirmed"
            }'
        ], []);
    }
}
