<?php

namespace BO\Zmscitizenapi\Tests;

use BO\Slim\Render;

class ScopeByIdGetTest extends Base
{

    public function testRendering()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_MultipleScopes.json"),
            ]
        ]);
        $response = $this->render([], [
            'scopeId' => '1'
        ], []);
        $expectedResponse = [
            'scopes' => [
                [
                    'id' => '1',
                    'provider' => [
                        'id' => '9999998',
                        'source' => 'unittest',
                    ],
                    'shortName' => 'Scope 1',
                    'telephoneActivated' => '1',
                    'telephoneRequired' => '0',
                    'customTextfieldActivated' => '1',
                    'customTextfieldRequired' => '0',
                    'captchaActivatedRequired' => '1',
                ]
            ]
        ];
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
    }
  
    public function testRenderingMulti()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_MultipleScopes.json"),
            ]
        ]);
        $response = $this->render([], [
            'scopeId' => '1,2'
        ], []);
        $expectedResponse = [
            'scopes' => [
                [
                    'id' => '1',
                    'provider' => [
                        'id' => '9999998',
                        'source' => 'unittest',
                    ],
                    'shortName' => 'Scope 1',
                    'telephoneActivated' => '1',
                    'telephoneRequired' => '0',
                    'customTextfieldActivated' => '1',
                    'customTextfieldRequired' => '0',
                    'captchaActivatedRequired' => '1',
                ],
                [
                    'id' => '2',
                    'provider' => [
                        'id' => '9999999',
                        'source' => 'unittest',
                    ],
                    'shortName' => 'Scope 2',
                    'telephoneActivated' => '0',
                    'telephoneRequired' => '1',
                    'customTextfieldActivated' => '0',
                    'customTextfieldRequired' => '1',
                    'captchaActivatedRequired' => '0',
                ],
            ]
        ];
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
    }

    public function testScopeNotFound()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_MultipleScopes.json"),
            ]
        ]);
        $response = $this->render([], [
            'scopeId' => '99'
        ], []);
        $expectedResponse = [
            'error' => 'Scope(s) not found'
        ];
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNoScopeIdProvided()
    {
        $response = $this->render([], [], []);
        $expectedResponse = [
            'error' => 'Invalid scopeId(s)'
        ];
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testPartialResultsWithWarning()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_MultipleScopes.json"),
            ]
        ]);    
        $response = $this->render([], [
            'scopeId' => '1,99'
        ], []);   
        $expectedResponse = [
            'scopes' => [
                [
                    'id' => '1',
                    'provider' => [
                        'id' => '9999998',
                        'source' => 'unittest',
                    ],
                    'shortName' => 'Scope 1',
                    'telephoneActivated' => '1',
                    'telephoneRequired' => '0',
                    'customTextfieldActivated' => '1',
                    'customTextfieldRequired' => '0',
                    'captchaActivatedRequired' => '1',
                ]
            ],
            'warning' => 'The following scopeId(s) were not found: 99'
        ];    
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
        $this->assertEquals(200, $response->getStatusCode());
    }       
}
