<?php

namespace BO\Zmsticketprinter\Tests;

class TicketprinterByScopeTest extends Base
{
    protected $classname = "TicketprinterByScope";

    protected $arguments = [ ];

    protected $parameters = [ ];

    protected function getApiCalls()
    {
        return [
            [
                'function' => 'readGetResult',
                'url' => '/scope/312/organisation/',
                'parameters' => ['resolveReferences' => 2],
                'response' => $this->readFixture("GET_organisation_78.json"), //Treptow Köpenick
            ],
            [
                'function' => 'readGetResult',
                'url' => '/ticketprinter/71ac9df1f2983c3f94aebc1a9bd121bfecf5b374f2/',
                'response' => $this->readFixture("GET_ticketprinter.json"),
            ],
            [
                'function' => 'readPostResult',
                'url' => '/ticketprinter/',
                'response' => $this->readFixture("GET_ticketprinter_buttonlist_single.json"),
            ],
            [
                'function' => 'readGetResult',
                'url' => '/scope/312/workstationcount/',
                'response' => $this->readFixture("GET_scope_312.json"),
            ],
            [
                'function' => 'readGetResult',
                'url' => '/scope/312/queue/',
                'response' => $this->readFixture("GET_queuelist_312.json"), //Bürgeramt 1 in Köpenick
            ]
        ];
    }

    public function testRendering()
    {
        $response = $this->render([
            'scopeId' => 312
        ], [
            '__cookie' => [
                'Ticketprinter' => '71ac9df1f2983c3f94aebc1a9bd121bfecf5b374f2',
            ]
        ], [ ]);
        $this->assertContains('Wartenummer für', (string) $response->getBody());
        $this->assertContains('Köpenick', (string) $response->getBody());
    }
}
