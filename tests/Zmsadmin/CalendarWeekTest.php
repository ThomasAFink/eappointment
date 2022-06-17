<?php

namespace BO\Zmsadmin\Tests;

class CalendarWeekTest extends Base
{
    protected $arguments = ['year' => 2016, 'weeknr' => 13];

    protected $parameters = [
        'slotType' => 'intern',
        'slotsRequired' => 0
    ];

    protected $classname = "CalendarWeek";

    public function testRendering()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/cluster/',
                    'response' => $this->readFixture("GET_cluster_109.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/process/status/free/',
                    'parameters' => ['slotType' => 'intern', 'slotsRequired' => 0],
                    'response' => $this->readFixture("GET_freeprocesslist_empty.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-04-01/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-04-02/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-04-03/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, $this->parameters, []);
        $this->assertContains(
            'Standort: Bürgeramt Heerstraße, 13. Kalenderwoche',
            (string)$response->getBody()
        );
        $this->assertContains('<span class="pid">184432</span>', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testWithFreeProcessList()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/cluster/',
                    'response' => $this->readFixture("GET_cluster_109.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/process/status/free/',
                    'parameters' => ['slotType' => 'intern', 'slotsRequired' => 0],
                    'response' => $this->readFixture("GET_freeprocesslist_20160530.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-04-01/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-04-02/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-04-03/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, $this->parameters, []);
        $this->assertContains('timeslot--free', (string)$response->getBody());
        $this->assertContains('Jetzt einen Termin um 15:20 buchen', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testYearChange()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 2],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/cluster/',
                    'response' => $this->readFixture("GET_cluster_109.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/process/status/free/',
                    'parameters' => ['slotType' => 'intern', 'slotsRequired' => 0],
                    'response' => $this->readFixture("GET_freeprocesslist_empty.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-12-26/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],[
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-12-27/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],[
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-12-28/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],[
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-12-29/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-12-30/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2016-12-31/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/process/2017-01-01/',
                    'response' => $this->readFixture("GET_processList_141_20160401.json")
                ]
            ]
        );
        $response = $this->render(['year' => 2016, 'weeknr' => 52], $this->parameters, []);
        $this->assertContains('/2017/1/', (string)$response->getBody());
        $this->assertContains('/2016/51/', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
