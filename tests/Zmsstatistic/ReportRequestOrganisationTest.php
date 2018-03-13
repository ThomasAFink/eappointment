<?php

namespace BO\Zmsstatistic\Tests;

class ReportRequestOrganisationTest extends Base
{
    protected $classname = "ReportRequestOrganisation";

    protected $arguments = [ ];

    protected $parameters = [ ];

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
                  'url' => '/scope/141/department/',
                  'response' => $this->readFixture("GET_department_74.json")
              ],
              [
                  'function' => 'readGetResult',
                  'url' => '/department/74/organisation/',
                  'response' => $this->readFixture("GET_organisation_71_resolved3.json")
              ],
              [
                  'function' => 'readGetResult',
                  'url' => '/warehouse/requestorganisation/71/',
                  'response' => $this->readFixture("GET_requestorganisation_71.json")
              ]
            ]
        );
        $response = $this->render([ ], [ ], [ ]);
        $this->assertContains('Dienstleistungsstatistik Bezirk', (string) $response->getBody());
        $this->assertContains(
            '<a class="active" href="/report/request/organisation/">Charlottenburg-Wilmersdorf</a>',
            (string) $response->getBody()
        );
        $this->assertContains('<a href="/report/request/organisation/2016-04/">April</a>', (string) $response->getBody());
        $this->assertContains('Bitte wählen Sie eine Zeit aus.', (string) $response->getBody());
    }

    public function testWithPeriod()
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
                    'url' => '/scope/141/department/',
                    'response' => $this->readFixture("GET_department_74.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/department/74/organisation/',
                    'response' => $this->readFixture("GET_organisation_71_resolved3.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/warehouse/requestorganisation/71/',
                    'response' => $this->readFixture("GET_requestorganisation_71.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/warehouse/requestorganisation/71/2016-04/',
                    'response' => $this->readFixture("GET_requestorganisation_71_042016.json")
                ]
            ]
        );
        $response = $this->render(['period' => '2016-04'], [], []);
        $this->assertContains(
            '<th class="statistik">Apr</th>',
            (string) $response->getBody()
        );
        $this->assertContains(
            'Auswertung für Charlottenburg-Wilmersdorf im Zeitraum April 2016',
            (string) $response->getBody()
        );
        $this->assertContains('Reisepass beantragen', (string) $response->getBody());
    }

    public function testWithDownloadXLSX()
    {
        $this->setOutputCallback(function () {
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
                        'url' => '/scope/141/department/',
                        'response' => $this->readFixture("GET_department_74.json")
                    ],
                    [
                        'function' => 'readGetResult',
                        'url' => '/department/74/organisation/',
                        'response' => $this->readFixture("GET_organisation_71_resolved3.json")
                    ],
                    [
                        'function' => 'readGetResult',
                        'url' => '/warehouse/requestorganisation/71/',
                        'response' => $this->readFixture("GET_requestorganisation_71.json")
                    ],
                    [
                        'function' => 'readGetResult',
                        'url' => '/warehouse/requestorganisation/71/2016-04/',
                        'response' => $this->readFixture("GET_requestorganisation_71_042016.json")
                    ]
                ]
            );
            $response = $this->render(
                [
                    'period' => '2016-04'
                ],
                [
                    'type' => 'xlsx'
                ],
                [ ]
            );
            $this->assertContains('xlsx', $response->getHeaderLine('Content-Disposition'));
        });
    }

    public function testWithDownloadCSV()
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
                    'url' => '/scope/141/department/',
                    'response' => $this->readFixture("GET_department_74.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/department/74/organisation/',
                    'response' => $this->readFixture("GET_organisation_71_resolved3.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/warehouse/requestorganisation/71/',
                    'response' => $this->readFixture("GET_requestorganisation_71.json")
                ],
                [
                    'function' => 'readGetResult',
                    'url' => '/warehouse/requestorganisation/71/2016-04/',
                    'response' => $this->readFixture("GET_requestorganisation_71_042016.json")
                ]
            ]
        );
        ob_start();
        $response = $this->render(
            [
                'period' => '2016-04'
            ],
            [
                'type' => 'csv'
            ],
            [ ]
        );
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains('csv', $response->getHeaderLine('Content-Disposition'));
        $this->assertContains(
            '"Personalausweis beantragen";"14";"14";',
            $output
        );
    }
}
