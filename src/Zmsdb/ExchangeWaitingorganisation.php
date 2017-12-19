<?php

namespace BO\Zmsdb;

use \BO\Zmsentities\Exchange;

class ExchangeWaitingorganisation extends Base implements Interfaces\ExchangeSubject
{
    public function readEntity(
        $subjectid,
        \DateTimeInterface $datestart,
        \DateTimeInterface $dateend,
        $period = 'day'
    ) {
        $entity = new Exchange();
        $entity->setPeriod($datestart, $dateend, $period);
        $entity->addDictionaryEntry('subjectid', 'string', 'ID of an organisation', 'organisation.id');
        $entity->addDictionaryEntry('date', 'string', 'date of report entry');
        $entity->addDictionaryEntry('hour', 'string', 'hour of report entry');
        $entity->addDictionaryEntry('waitingcount', 'number', 'amount of waiting clients');
        $entity->addDictionaryEntry('waitingtime', 'number', 'real waitingtime');
        $entity->addDictionaryEntry('waitingcalculated', 'number', 'calculated waitingtime');
        $subjectIdList = explode(',', $subjectid);

        foreach ($subjectIdList as $subjectid) {
            $raw = $this
                ->getReader()
                ->fetchAll(
                    constant("\BO\Zmsdb\Query\ExchangeWaitingorganisation::QUERY_READ_". strtoupper($period)),
                    [
                        'organisationid' => $subjectid,
                        'datestart' => $datestart->format('Y-m-d'),
                        'dateend' => $dateend->format('Y-m-d'),
                    ]
                );

            $entry = array_shift($raw);
            while ($entry) {
                foreach (range(0, 23) as $hour) {
                    $entity->addDataSet([
                        $subjectid,
                        $entry['datum'],
                        $hour,
                        $entry[sprintf('wartende_ab_%02s', $hour)],
                        $entry[sprintf('echte_zeit_ab_%02s', $hour)],
                        $entry[sprintf('zeit_ab_%02s', $hour)]
                    ]);
                }
                $entry = array_shift($raw);
            }
        }
        return $entity;
    }

    public function readSubjectList()
    {
        $raw = $this->getReader()->fetchAll(Query\ExchangeWaitingorganisation::QUERY_SUBJECTS, []);
        $entity = new Exchange();
        $entity->setPeriod(new \DateTimeImmutable(), new \DateTimeImmutable());
        $entity->addDictionaryEntry('subject', 'string', 'Organisation ID', 'organisation.id');
        $entity->addDictionaryEntry('periodstart', 'string', 'Datum von');
        $entity->addDictionaryEntry('periodend', 'string', 'Datum bis');
        $entity->addDictionaryEntry('description', 'string', 'Name der Organisation');

        foreach ($raw as $entry) {
            $entity->addDataSet(array_values($entry));
        }
        return $entity;
    }

    public function readPeriodList($subjectid, $period = 'day')
    {
        $entity = new Exchange();
        $entity->setPeriod(new \DateTimeImmutable(), new \DateTimeImmutable(), $period);
        $entity->addDictionaryEntry('period');

        $montsList = $this->getReader()->fetchAll(
            constant("\BO\Zmsdb\Query\ExchangeWaitingorganisation::QUERY_PERIODLIST_MONTH"),
            [
                'organisationid' => $subjectid,
            ]
        );
        $raw = [];
        foreach ($montsList as $month) {
            $date = new \DateTimeImmutable($month['date']);
            $raw[$date->format('Y')][] = $month['date'];
            rsort($raw[$date->format('Y')]);
        }
        krsort($raw);

        foreach ($raw as $year => $months) {
            $entity->addDataSet([$year]);
            foreach ($months as $month) {
                $entity->addDataSet([$month]);
            }
        }
        return $entity;
    }
}
