<?php
namespace BO\Zmsentities\Collection;

class ProcessList extends Base
{
    public function toProcessListByTime()
    {
        $list = new self();
        foreach ($this as $process) {
            $appointment = $process->getFirstAppointment();
            $list[$appointment['date']][] = clone $process;
        }
        return $list;
    }

    public function toProcessListByStatusAndTime()
    {
        $list = $this->getWithHoursByDay();
        $oldList = clone $this->sortByArrivalTime();
        foreach ($oldList as $process) {
            foreach ($list as $hour => $timeList) {
                $timeList = new self();
                $appointment = $process->getFirstAppointment();
                if ($hour == $appointment->toDateTime()->format('H')) {
                    if (! $list[$hour][intval($appointment['date'])] instanceof ProcessList) {
                        $list[$hour][intval($appointment['date'])] = $timeList;
                    }
                    $list[$hour][intval($appointment['date'])]->addEntity(clone $process);
                    ksort($list[$hour]);
                }
            }
        }
        return $list;
    }

    public function getWithHoursByDay()
    {
        $list = array();
        $start = 7;
        $endTime = 18;
        $hour = $start;
        while ($hour <= $endTime) {
            $list[$hour] = array();
            $hour++;
        }
        ksort($list);
        return $list;
    }

    public function getFirstProcess()
    {
        return reset($this);
    }

    public function getScopeList()
    {
        $list = new ScopeList();
        foreach ($this as $process) {
            if (array_key_exists('scope', $process)) {
                $list[] = new \BO\Zmsentities\Scope($process['scope']);
            }
        }
        return $list->withUniqueScopes();
    }

    public function getAppointmentList()
    {
        $appointmentList = new AppointmentList();
        foreach ($this as $process) {
            foreach ($process["appointments"] as $appointment) {
                $appointmentList->addEntity(new \BO\Zmsentities\Appointment($appointment));
            }
        }
        return $appointmentList;
    }

    public function toQueueList($now)
    {
        $queueList = new QueueList();
        foreach ($this as $process) {
            $queue = $process->toQueue($now);
            $queueList->addEntity($queue);
        }
        return $queueList;
    }

    public function withAvailability(\BO\Zmsentities\Availability $availability)
    {
        $processList = new static();
        foreach ($this as $process) {
            if ($availability->hasAppointment($process->getFirstAppointment())) {
                $processList[] = clone $process;
            }
        }
        return $processList;
    }

    public function withAvailabilityStrict(\BO\Zmsentities\Availability $availability)
    {
        $processList = new static();
        $slotList = $availability->getSlotList();
        foreach ($this as $process) {
            if ($slotList->removeAppointment($process->getFirstAppointment())) {
                $processList[] = clone $process;
            }
        }
        return $processList;
    }

    public function withOutAvailability(\BO\Zmsentities\Collection\AvailabilityList $availabilityList)
    {
        $processList = new static();
        $slotList = $availabilityList->withType('appointment')->getSlotList();
        foreach ($this as $process) {
            if (!$slotList->removeAppointment($process->getFirstAppointment())) {
                $processList[] = clone $process;
            }
        }
        return $processList;
    }

    public function withScopeId($scopeId)
    {
        $processList = new static();
        foreach ($this as $process) {
            if ($process->scope['id'] == $scopeId) {
                $processList[] = clone $process;
            }
        }
        return $processList;
    }

    public function withOutScopeId($scopeId)
    {
        $processList = new static();
        foreach ($this as $process) {
            if ($process->scope['id'] != $scopeId) {
                $processList[] = clone $process;
            }
        }
        return $processList;
    }
}
