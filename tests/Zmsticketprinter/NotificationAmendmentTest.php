<?php

namespace BO\Zmsticketprinter\Tests;

class NotificationAmendmentTest extends Base
{

    protected $classname = "NotificationAmendment";

    protected $arguments = [ ];

    protected $parameters = [ ];

    protected function getApiCalls()
    {
        return [
            [
                'function' => 'readGetResult',
                'url' => '/ticketprinter/71ac9df1f2983c3f94aebc1a9bd121bfecf5b374f2/',
                'response' => $this->readFixture("GET_ticketprinter.json"),
            ],
            [
                'function' => 'readGetResult',
                'url' => '/scope/141/',
                'response' => $this->readFixture("GET_scope_lessdata.json"),
            ],
            [
                'function' => 'readGetResult',
                'url' => '/organisation/scope/141/',
                'response' => $this->readFixture("GET_organisation_71.json"),
            ]
        ];
    }

    public function testRendering()
    {
        $response = $this->render([], [
            '__cookie' => [
                'Ticketprinter' => '71ac9df1f2983c3f94aebc1a9bd121bfecf5b374f2',
            ],
            'scopeId' => 141
        ], [ ]);
        $this->assertContains('Bitte geben Sie hier Ihre Wartenummer ein:', (string) $response->getBody());
    }
}
