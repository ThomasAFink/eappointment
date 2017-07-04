<?php

namespace BO\Zmsadmin\Tests;

class ScopeTest extends Base
{
    protected $arguments = [
        'id' => 141
    ];

    protected $parameters = [];

    protected $classname = "Scope";

    public function testRendering()
    {
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 1],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/provider/dldb/',
                    'parameters' => ['isAssigned' => true],
                    'response' => $this->readFixture("GET_providerlist_assigned.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/provider/dldb/',
                    'parameters' => ['isAssigned' => false],
                    'response' => $this->readFixture("GET_providerlist_notassigned.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/',
                    'parameters' => ['resolveReferences' => 1],
                    'response' => $this->readFixture("GET_scope_141.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/department/',
                    'response' => $this->readFixture("GET_department_74.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/organisation/',
                    'response' => $this->readFixture("GET_organisation_71_resolved3.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/imagedata/calldisplay/',
                    'response' => $this->readFixture("GET_imagedata_empty.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, $this->parameters, []);
        $this->assertContains('Bürgeramt Heerstraße', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSaveWithUploadImage()
    {
        \App::$now = new \DateTime('2016-04-01 11:55:00', new \DateTimeZone('Europe/Berlin'));
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 1],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/provider/dldb/',
                    'parameters' => ['isAssigned' => true],
                    'response' => $this->readFixture("GET_providerlist_assigned.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/provider/dldb/',
                    'parameters' => ['isAssigned' => false],
                    'response' => $this->readFixture("GET_providerlist_notassigned.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/',
                    'parameters' => ['resolveReferences' => 1],
                    'response' => $this->readFixture("GET_scope_141.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/department/',
                    'response' => $this->readFixture("GET_department_74.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/organisation/',
                    'response' => $this->readFixture("GET_organisation_71_resolved3.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/imagedata/calldisplay/',
                    'response' => $this->readFixture("GET_imagedata_empty.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/scope/141/',
                    'response' => $this->readFixture("GET_scope_141.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/scope/141/imagedata/calldisplay/',
                    'response' => $this->readFixture("GET_imagedata.json")
                ]
            ]
        );
        $response = $this->render($this->arguments, [
            'provider' => [
                'source' => 'dldb',
                'id' => '122217',
            ],
            'contact' => [
                'name' => 'Bürgeramt Heerstraße',
                'street' => 'Heerstr. 12',
                'email' => '',
            ],
            'hint' => [
                'Nr. wird zum Termin aufgerufen ',
                ' Nr. wird zum Termin aufgerufen'
            ],
            'save' => 'save',
            '__file' => [
                'uploadCallDisplayImage' => new \Slim\Http\UploadedFile(
                    dirname(__FILE__) . '/fixtures/baer.png',
                    'baer.png',
                    'image/png',
                    13535
                )
            ]
        ], [], 'POST');
        $this->assertRedirect($response, '/scope/141/?confirm_success=1459504500');
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testSaveWithUploadImageFailed()
    {
        $this->expectException('\Exception');
        $this->expectExceptionMessage('Wrong Mediatype given, use gif, jpg or png');
        \App::$now = new \DateTime('2016-04-01 11:55:00', new \DateTimeZone('Europe/Berlin'));
        $this->setApiCalls(
            [
                [
                    'function' => 'readGetResult',
                    'url' => '/workstation/',
                    'parameters' => ['resolveReferences' => 1],
                    'response' => $this->readFixture("GET_Workstation_Resolved2.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/provider/dldb/',
                    'parameters' => ['isAssigned' => true],
                    'response' => $this->readFixture("GET_providerlist_assigned.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/provider/dldb/',
                    'parameters' => ['isAssigned' => false],
                    'response' => $this->readFixture("GET_providerlist_notassigned.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/',
                    'parameters' => ['resolveReferences' => 1],
                    'response' => $this->readFixture("GET_scope_141.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/department/',
                    'response' => $this->readFixture("GET_department_74.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/organisation/',
                    'response' => $this->readFixture("GET_organisation_71_resolved3.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/scope/141/imagedata/calldisplay/',
                    'response' => $this->readFixture("GET_imagedata_empty.json")
                ],
                [
                    'function' => 'readPostResult',
                    'url' => '/scope/141/',
                    'response' => $this->readFixture("GET_scope_141.json")
                ]
            ]
        );
        $this->render($this->arguments, [
            'provider' => [
                'source' => 'dldb',
                'id' => '122217',
            ],
            'contact' => [
                'name' => 'Bürgeramt Heerstraße',
                'street' => 'Heerstr. 12',
                'email' => '',
            ],
            'hint' => [
                'Nr. wird zum Termin aufgerufen ',
                ' Nr. wird zum Termin aufgerufen'
            ],
            'save' => 'save',
            '__file' => array(
                'uploadCallDisplayImage' => new \Slim\Http\UploadedFile(
                    dirname(__FILE__) . '/fixtures/baer.png',
                    'baer.png',
                    'application/json',
                    13535
                )
            )
        ], [], 'POST');
    }

    public function testTwigExceptionHandler()
    {
        //$response = \BO\Zmsadmin\Helper\TwigExceptionHandler::withHtml($request, $response, $exception, $status);
    }
}
