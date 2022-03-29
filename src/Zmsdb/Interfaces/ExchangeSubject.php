<?php

namespace BO\Zmsdb\Interfaces;

interface ExchangeSubject
{
    public function readEntity(
        $subjectid,
        \DateTimeInterface $datestart,
        \DateTimeInterface $dateend,
        $period = 'DAY'
    );
    public function readSubjectList();
    public function readPeriodList($subjectid, $period = 'DAY');
}
