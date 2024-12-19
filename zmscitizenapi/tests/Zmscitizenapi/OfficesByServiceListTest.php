<?php

namespace BO\Zmscitizenapi\Tests;

class OfficesByServiceListTest extends Base
{

    protected $classname = "\BO\Zmscitizenapi\Controllers\OfficesByServiceList";

    public function testRendering()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);
        $response = $this->render([], [
            'serviceId' => '2'
        ], []);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]
            ]
        ];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
    }

    public function testRenderingRequestRelation()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);
        $response = $this->render([], [
            'serviceId' => '1'
        ], []);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999998,
                    'name' => 'Unittest Source Dienstleister',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 1,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999998',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 1',
                        'telephoneActivated' => true,
                        'telephoneRequired' => false,
                        'customTextfieldActivated' => true,
                        'customTextfieldRequired' => false,
                        'customTextfieldLabel' => 'Custom Label',
                        'captchaActivatedRequired' => true,
                        'displayInfo' => null
                    ]
                ],
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]
            ]
        ];
        $this->assertEquals(200, $response->getStatusCode());
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
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);
        $response = $this->render([], [
            'serviceId' => '1,2'
        ], []);
        $responseBody = json_decode((string)$response->getBody(), true);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999998,
                    'name' => 'Unittest Source Dienstleister',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 1,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999998',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 1',
                        'telephoneActivated' => true,
                        'telephoneRequired' => false,
                        'customTextfieldActivated' => true,
                        'customTextfieldRequired' => false,
                        'customTextfieldLabel' => 'Custom Label',
                        'captchaActivatedRequired' => true,
                        'displayInfo' => null
                    ]
                ],
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]
            ]
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, $responseBody);
    }
    
    public function testServiceNotFound()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);
    
        $response = $this->render([], [
            'serviceId' => '99999999'
        ], []);
        $expectedResponse = [
            'errors' => [
                [
                    'errorCode' => 'officesNotFound',
                    'errorMessage' => 'Office(s) not found for the provided serviceId(s).',                
                    'status' => 404,
                ]
            ],
            'status' => 404
        ];
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));

    }

    public function testNoServiceIdProvided()
    {
        $response = $this->render([], [], []);

        $expectedResponse = [
            'errors' => [
                [
                    'offices' => [],
                    'errorCode' => 'invalidServiceId',
                    'errorMessage' => 'serviceId should be a 32-bit integer.',
                    'status' => 400
                ]
            ],
            'status' => 400
        ];
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
    
    }

    public function testPartialResults()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);    
        $response = $this->render([], [
            'serviceId' => '2,99999999'
        ], []);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]              
            ]
        ];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
        
    }

    public function testPartialResultsRequestRelation()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);    
        $response = $this->render([], [
            'serviceId' => '1,99999999'
        ], []);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999998,
                    'name' => 'Unittest Source Dienstleister',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 1,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999998',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 1',
                        'telephoneActivated' => true,
                        'telephoneRequired' => false,
                        'customTextfieldActivated' => true,
                        'customTextfieldRequired' => false,
                        'customTextfieldLabel' => 'Custom Label',
                        'captchaActivatedRequired' => true,
                        'displayInfo' => null
                    ]
                ],
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]              
            ]
        ];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
        
    }

    public function testDuplicateServiceIds()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);
        $response = $this->render([], [
            'serviceId' => '2,2'
        ], []);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]
            ]
        ];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
    }

    public function testDuplicateServiceIdsCombinable()
    {
        $this->setApiCalls([
            [
                'function' => 'readGetResult',
                'url' => '/source/unittest/',
                'parameters' => [
                    'resolveReferences' => 2,
                ],
                'response' => $this->readFixture("GET_SourceGet_dldb.json"),
            ]
        ]);
        $response = $this->render([], [
            'serviceId' => '1,1'
        ], []);
        $expectedResponse = [
            'offices' => [
                [
                    'id' => 9999998,
                    'name' => 'Unittest Source Dienstleister',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 1,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999998',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 1',
                        'telephoneActivated' => true,
                        'telephoneRequired' => false,
                        'customTextfieldActivated' => true,
                        'customTextfieldRequired' => false,
                        'customTextfieldLabel' => 'Custom Label',
                        'captchaActivatedRequired' => true,
                        'displayInfo' => null
                    ]
                ],
                [
                    'id' => 9999999,
                    'name' => 'Unittest Source Dienstleister 2',
                    'address' => null,
                    'geo' => null,
                    'scope' => [
                        'id' => 2,
                        'provider' => [
                            '$schema' => 'https://schema.berlin.de/queuemanagement/provider.json',
                            'id' => '9999999',
                            'source' => 'unittest'
                        ],
                        'shortName' => 'Scope 2',
                        'telephoneActivated' => false,
                        'telephoneRequired' => true,
                        'customTextfieldActivated' => false,
                        'customTextfieldRequired' => true,
                        'customTextfieldLabel' => '',
                        'captchaActivatedRequired' => false,
                        'displayInfo' => null
                    ]
                ]
            ]
        ];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
        
    }

    public function testInvalidServiceId()
    {
        $response = $this->render([], [
            'serviceId' => 'blahblahblah'
        ], []);
    
        $expectedResponse = [
            'errors' => [
                [
                    'offices' => [],
                    'errorCode' => 'invalidServiceId',
                    'errorMessage' => 'serviceId should be a 32-bit integer.',
                    'status' => 400
                ]
            ],
            "status" => 400
        ];
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEqualsCanonicalizing($expectedResponse, json_decode((string)$response->getBody(), true));
    }
    
}
