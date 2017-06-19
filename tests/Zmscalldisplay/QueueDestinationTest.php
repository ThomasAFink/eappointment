<?php

namespace BO\Zmscalldisplay\Tests;

class QueueDestinationTest extends Base
{
    protected $classname = "Queue";

    protected $arguments = [ ];

    protected $parameters = [ ];

    protected function getApiCalls()
    {
        return [
            [
                'function' => 'readPostResult',
                'url' => '/calldisplay/queue/',
                'response' => $this->readFixture("GET_queue_multipleDestination.json")
            ]
        ];
    }

    public function testRendering()
    {
        $response = $this->render([ ], [
            'collections' => [
                'scopelist' => '141,140'
            ],
            'tableLayout' => [
                "multiColumns"  => 2,
                "maxResults"    => 8,
                "head" => [
                    "left"  =>  "Nummer",
                    "right" =>  "Platz"
                ]
            ]
        ], [ ]);
        $this->assertContains('31316', (string) $response->getBody());
        $this->assertContains('52230', (string) $response->getBody());
        $this->assertContains('data="10"', (string) $response->getBody());
        $this->assertContains('data="12"', (string) $response->getBody());
    }
}
