<?php

namespace BO\Zmsapi\Tests;

use BO\Zmsentities\Availability as Entity;
use BO\Zmsdb\Availability as Query;
use BO\Zmsapi\Exception\Availability\AvailabilityUpdateFailed;

class AvailabilityUpdateTest extends Base
{
    protected $classname = "AvailabilityUpdate";

    public function testRendering()
    {
        // Create an example entity
        $input = (new Entity)->createExample();

        // Set test data
        $currentTimestamp = time();
        $input['startDate'] = $currentTimestamp + (2 * 24 * 60 * 60); // 2 days in the future
        $input['endDate'] = $currentTimestamp + (5 * 24 * 60 * 60);   // 5 days in the future
        $input['startTime'] = "09:00:00";
        $input['endTime'] = "17:00:00";
        $input['scope'] = ["id" => 312, "dayoff"=> [
            [
                "id" => 35,
                "date" => $currentTimestamp + (7 * 24 * 60 * 60), // 7 days in the future
                "name" => "1. Mai",
                "lastChange" => $currentTimestamp
            ],
            [
                "id" => 36,
                "date" => $currentTimestamp + (14 * 24 * 60 * 60), // 14 days in the future
                "name" => "Christi Himmelfahrt",
                "lastChange" => $currentTimestamp
            ]
        ]];
        $input['kind'] = "default";


        // Write the entity to the database
        $entity = (new Query())->writeEntity($input);
        $this->setWorkstation();

        // Prepare and send the request with the updated availability and dayoff data
        $response = $this->render([
            "id" => $entity->getId()
        ], [
            '__body' => json_encode([
                'availabilityList' => [
                    [
                        "id" => $entity->getId(),
                        "description" => "Test Öffnungszeit update",
                        "startDate" => $currentTimestamp + (2 * 24 * 60 * 60),
                        "endDate" => $currentTimestamp + (5 * 24 * 60 * 60),
                        "startTime" => "09:00:00",
                        "endTime" => "17:00:00",
                        "kind" => "default",
                        "scope" => [
                            "id" => 312,
                            "dayoff" => [  // Moved dayoff inside the scope object
                                [
                                    "id" => 35,
                                    "date" => $currentTimestamp + (7 * 24 * 60 * 60),
                                    "name" => "1. Mai",
                                    "lastChange" => $currentTimestamp
                                ],
                                [
                                    "id" => 36,
                                    "date" => $currentTimestamp + (14 * 24 * 60 * 60),
                                    "name" => "Christi Himmelfahrt",
                                    "lastChange" => $currentTimestamp
                                ]
                            ]
                        ]
                    ]
                ],
                'selectedDate' => date('Y-m-d')
            ])
        ], []);
        

        // Assertions to check if the response is correct
        $this->assertStringContainsString('availability.json', (string) $response->getBody());
        $this->assertTrue(200 == $response->getStatusCode());
    }

    public function testEmpty()
    {
        $this->setWorkstation();
        $this->expectException('\BO\Mellon\Failure\Exception');
        $this->render([], [], []);
    }

    public function testNotFound()
    {
        $this->setWorkstation();
        $this->expectException('\BO\Zmsapi\Exception\Availability\AvailabilityNotFound');
        $this->expectExceptionCode(404);

        $this->render(
            ["id" => 1],
            [
                '__body' => json_encode([
                    'availabilityList' => [
                        [
                            "id" => 1,
                            "description" => "Test Öffnungszeit not found",
                            "scope" => ["id" => 312]
                        ]
                    ],
                    'selectedDate' => date('Y-m-d')
                ])
            ],
            []
        );
    }
}
