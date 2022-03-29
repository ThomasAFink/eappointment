<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\Exchange;
use \BO\Zmsentities\Scope as ScopeEntity;

class Warehouse extends Base
{
    protected $subjects = [
        [
            'subject' => 'waitingscope',
            'description' => 'Wartestatistik Standort',
            'right' => 'scope'
        ],
        [
            'subject' => 'waitingdepartment',
            'description' => 'Wartestatistik Behörde',
            'right' => 'department'
        ],
        [
            'subject' => 'waitingorganisation',
            'description' => 'Wartestatistik Organisation',
            'right' => 'organisation'
        ],
        [
            'subject' => 'clientscope',
            'description' => 'Kundenstatistik Standort',
            'right' => 'scope'
        ],
        [
            'subject' => 'clientdepartment',
            'description' => 'Kundenstatistik Behörde',
            'right' => 'department'
        ],
        [
            'subject' => 'clientorganisation',
            'description' => 'Kundenstatistik Organisation',
            'right' => 'organisation'
        ],
        [
            'subject' => 'notificationscope',
            'description' => 'SMS-Statistik Standort',
            'right' => 'scope'
        ],
        [
            'subject' => 'notificationdepartment',
            'description' => 'SMS-Statistik Behörde',
            'right' => 'department'
        ],
        [
            'subject' => 'notificationorganisation',
            'description' => 'SMS-Statistik Organisation',
            'right' => 'organisation'
        ],
        [
            'subject' => 'requestscope',
            'description' => 'Dienstleistungsstatistik Standort',
            'right' => 'scope'
        ],
        [
            'subject' => 'requestdepartment',
            'description' => 'Dienstleistungsstatistik Behörde',
            'right' => 'department'
        ],
        [
            'subject' => 'requestorganisation',
            'description' => 'Dienstleistungsstatistik Organisation',
            'right' => 'organisation'
        ],
        [
            'subject' => 'useraccount',
            'description' => 'Nutzerdaten mit E-Mail-Adresse und Rechten',
            'right' => 'superuser'
        ],
        [
            'subject' => 'slotscope',
            'description' => 'Gebuchte Zeitschlitze eines Standortes gruppiert nach Datum',
            'right' => 'superuser'
        ],
        [
            'subject' => 'unassignedscope',
            'description' => 'Standorte ohne Zuordnung zur DLDB mit Terminen',
            'right' => 'superuser'
        ],
    ];

    public function readSubjectsList()
    {
        $entity = (new Exchange)->withLessData();
        $entity->addDictionaryEntry('subject', 'string', 'subject name');
        $entity->addDictionaryEntry('description', 'string', 'subject description');
        $entity->addDictionaryEntry('right', 'string', 'useraccount right for this subject', 'useraccount.rights');
        foreach ($this->subjects as $subject) {
            $entity->addDataSet(array_values($subject));
        }
        return $entity;
    }
}
