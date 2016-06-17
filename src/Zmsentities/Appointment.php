<?php

namespace BO\Zmsentities;

class Appointment extends Schema\Entity
{
    public static $schema = "appointment.json";

    public function toDate($lang)
    {
        return ($lang == 'en') ? date('l F d, Y', $this->date) : strftime("%A %d. %B %Y", $this->date);
    }

    public function toTime($lang)
    {
        $suffix = ($lang == 'en') ? ' o\'clock' : ' Uhr';
        return date('H:i', $this->date) . $suffix;
    }

    public function addDate($date)
    {
        $this->date = $date;
        return $this;
    }

    public function addScope($scopeId)
    {
        $this->scope['id'] = $scopeId;
        return $this;
    }

    public function addSlotCount()
    {
        $this->slotCount += 1;
        return $this;
    }

    public function getAvailability()
    {
        $data = array();
        if (array_key_exists('availability', $this)) {
            $data = $this['availability'];
        }
        return new Availability($data);
    }

    public function toDateTime($timezone = 'Europe/Berlin')
    {
        $date = \DateTime::createFromFormat("U", $this->date);
        if ($date) {
            $date->setTimeZone(new \DateTimeZone($timezone));
        }
        return $date;
    }

    public function getStartTime()
    {
        $time = $this->toDateTime();
        return $time;
    }

    public function getEndTime()
    {
        $time = $this->getStartTime();
        $availability = $this->getAvailability();
        return $time->modify('+' . $availability->slotTimeInMinutes . ' minutes');
    }

    public function setDateByString($dateString, $format = 'Y-m-d H:i')
    {
        $appointmentDateTime = \DateTime::createFromFormat($format, $dateString);
        $this->date = $appointmentDateTime->format('U');
        return $this;
    }
}
