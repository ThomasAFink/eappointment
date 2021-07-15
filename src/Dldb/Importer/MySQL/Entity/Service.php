<?php

namespace BO\Dldb\Importer\MySQL\Entity;

class Service extends Base
{
    protected $fieldMapping = [
        'id' => 'id',
        'name' => 'name',
        'hint' => 'hint',
        'leika' => 'leika',
        'fees' => 'fees',
        'appointment.link' => 'appointment_all_link',
        'responsibility' => 'responsibility',
        'responsibility_all' => 'responsibility_all',
        'description' => 'description',
        'processing_time' => 'processing_time',
        'relation.root_topic' => 'root_topic_id',
        'meta.locale' => 'locale',
        'residence' => 'residence',
        'representation' => 'representation',
        'authorities' => 'authorities_json',
        'onlineprocessing' => 'onlineprocessing_json',
        'relation' => 'relation_json',
        '__RAW__' => 'data_json'
    ];

    protected function setupMapping() {
        $this->referanceMapping = [
            'meta' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Meta',
                'neededFields' => ['id' => 'object_id', 'meta.locale' => 'locale'],
                'addFields' => ['type' => static::getTableName()],
                'deleteFields' => [
                    'object_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => static::getTableName()
                ],
                'multiple' => false,
                'clearFields' => [
                    'type' => static::getTableName(), 
                    'locale' => $this->get('meta.locale')
                ],
            ],
            'locations' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Location_Service',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale')
                ],
                'clearFields' => [
                    'locale' => $this->get('meta.locale')
                ]
            ],
            'requirements' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Service_Information',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [
                    'type' => 'requirements',
                    'sort' => function($position, $key, $value) {
                        return $position;
                    }
                ],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => 'requirements',
                ],
                'clearFields' => [
                    'type' => 'requirements',
                    'locale' => $this->get('meta.locale')
                ],
            ],
            'forms' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Service_Information',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [
                    'type' => 'forms',
                    'sort' => function($position, $key, $value) {
                        return $position;
                    }
                ],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => 'forms',
                ],
                'clearFields' => [
                    'type' =>'forms',
                    'locale' => $this->get('meta.locale')
                ],
            ],
            'prerequisites' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Service_Information',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [
                    'type' => 'prerequisites',
                    'sort' => function($position, $key, $value) {
                        return $position;
                    }
                ],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => 'prerequisites',
                ],
                'clearFields' => [
                    'type' => 'prerequisites',
                    'locale' => $this->get('meta.locale')
                ],
            ],
            'links' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Service_Information',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [
                    'type' => 'links',
                    'sort' => function($position, $key, $value) {
                        return $position;
                    }
                ],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => 'links',
                ],
                'clearFields' => [
                    'type' => 'links',
                    'locale' => $this->get('meta.locale')
                ],
            ],
            'publications' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Service_Information',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [
                    'type' => 'publications',
                    'sort' => function($position, $key, $value) {
                        return $position;
                    }
                ],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => 'publications',
                ],
                'clearFields' => [
                    'type' => 'publications',
                    'locale' => $this->get('meta.locale')
                ],
            ],
            'legal' => [
                'class' => 'BO\\Dldb\\Importer\\MySQL\\Entity\\Service_Information',
                'neededFields' => ['id' => 'service_id', 'meta.locale' => 'locale'],
                'addFields' => [
                    'type' => 'legal',
                    'sort' => function($position, $key, $value) {
                        return $position;
                    }
                ],
                'deleteFields' => [
                    'service_id' => $this->get('id'), 
                    'locale' => $this->get('meta.locale'),
                    'type' => 'legal',
                ],
                'clearFields' => [
                    'type' => 'legal',
                    'locale' => $this->get('meta.locale')
                ],
            ]
        ];
    }
    
    public function preSetup()
    {
        try {
            $fields = $this->get(['id', 'meta.locale', 'meta.hash']);
            $fields[] = static::getTableName();
            
            $this->setStatus(static::STATUS_OLD);
            if ($this->itemNeedsUpdate(...array_values($fields))) {
                $this->setStatus(static::STATUS_NEW);
                $this->setupFields();
                $this->deleteEntity();
                $this->deleteReferences();
            }
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function deleteEntity(): bool
    {
        try {
            return $this->deleteWith(
                array_combine(['id', 'locale'], array_values($this->get(['id', 'meta.locale'])))
            );
        }
        catch (\Exception $e) {
            throw $e;
        }
    }

    public function clearEntity(array $addWhere = []) : bool {
        try {
            #print_r((array)$this->get(['meta.locale']));exit;
            return $this->deleteWith(
                ['locale' => $this->get('meta.locale')]
            );
        }
        catch (\Exception $e) {
            throw $e;
        }
    }
}