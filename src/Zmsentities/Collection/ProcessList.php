<?php
namespace BO\Zmsentities\Collection;

use \BO\Zmsentities\Helper\Sorter;

use \BO\Zmsentities\Helper\Property;

/**
 * @SuppressWarnings(Complexity)
 * @SuppressWarnings(Public)
 * @SuppressWarnings(Coupling)
 *
 */
class ProcessList extends Base
{
    const ENTITY_CLASS = '\BO\Zmsentities\Process';

    public function toProcessListByTime($format = null)
    {
        $list = new self();
        foreach ($this as $process) {
            $appointment = $process->getFirstAppointment();
            $formattedDate = $appointment['date'];
            if ($format) {
                $formattedDate = $appointment->toDateTime()->format($format);
            }
            $list[$formattedDate][] = clone $process;
        }
        return $list;
    }

    public function withRequest($requestId)
    {
        $list = new self();
        foreach ($this as $process) {
            if ($process->requests->hasEntity($requestId)) {
                $list->addEntity(clone $process);
            }
        }
        return $list;
    }

    public function sortByAppointmentDate()
    {
        $this->uasort(function ($a, $b) {
            return ($a->appointments->getFirst()->date - $b->appointments->getFirst()->date);
        });
        return $this;
    }

    public function sortByArrivalTime()
    {
        $this->uasort(function ($a, $b) {
            return ($a->queue['arrivalTime'] - $b->queue['arrivalTime']);
        });
        return $this;
    }

    public function sortByEstimatedWaitingTime()
    {
        $this->uasort(function ($a, $b) {
            return ($a->queue['waitingTimeEstimate'] - $b->queue['waitingTimeEstimate']);
        });
        return $this;
    }

    public function sortByClientName()
    {
        $this->uasort(function ($a, $b) {
            return strcmp(
                Sorter::toSortableString($a->getFirstClient()['familyName']),
                Sorter::toSortableString($b->getFirstClient()['familyName'])
            );
        });
        return $this;
    }

    public function sortByTimeKey()
    {
        $this->uksort(function ($a, $b) {
            return ($a - $b);
        });
        return $this;
    }

    /* todo: remove if not used anymore
    public function toProcessListByStatus($selectedDate, array $status)
    {
        $selectedDateTime = new \DateTimeImmutable($selectedDate);
        return $this
            ->toQueueList($selectedDateTime)
            ->withStatus($status)
            ->toProcessList()
            ->sortByArrivalTime()
            ->sortByEstimatedWaitingTime();
    }
    */

    public function toConflictListByDay()
    {
        $list = [];
        $oldList = clone $this;
        foreach ($oldList as $process) {
            $appointmentList = [];
            if (!isset($list[$process->getFirstAppointment()->getStartTime()->format('Y-m-d')])) {
                $list[$process->getFirstAppointment()->getStartTime()->format('Y-m-d')] = [];
            }
            foreach ($process->getAppointments() as $appointment) {
                $appointmentList[] = [
                    'startTime' => $appointment->getStartTime()->format('H:i'),
                    'endTime' => $appointment->getEndTime()->format('H:i'),
                    'availability' => $appointment->getAvailability()->getId()
                ];
            }
            $list[$process->getFirstAppointment()->getStartTime()->format('Y-m-d')][] = [
                'message' => $process->amendment,
                'appointments' => $appointmentList
            ];
        }
        return $list;
    }

    public function getScopeList()
    {
        $list = new ScopeList();
        foreach ($this as $process) {
            if (Property::__keyExists('scope', $process)) {
                $list[] = new \BO\Zmsentities\Scope($process['scope']);
            }
        }
        return $list->withUniqueScopes();
    }

    public function getRequestList()
    {
        $list = new RequestList();
        foreach ($this as $process) {
            if (Property::__keyExists('requests', $process)) {
                $list->addList($process->getRequests());
            }
        }
        return $list->withUniqueRequests();
    }

    public function getAppointmentList()
    {
        $appointmentList = new AppointmentList();
        foreach ($this as $process) {
            if (Property::__keyExists('appointments', $process)) {
                foreach ($process["appointments"] as $appointment) {
                    $appointmentList->addEntity(new \BO\Zmsentities\Appointment($appointment));
                }
            }
        }
        return $appointmentList;
    }

    public function setTempAppointmentToProcess($dateTime, $scopeId)
    {
        $addedAppointment = false;
        $appointment = (new \BO\Zmsentities\Appointment)->addDate($dateTime->getTimestamp())->addScope($scopeId);
        foreach ($this as $process) {
            if ($process->hasAppointment($dateTime->getTimestamp(), $scopeId) && !$addedAppointment) {
                $entity = clone $process;
                $this->addEntity($entity);
                $addedAppointment = true;
            }
        }
        if (!$addedAppointment) {
            $entity = new \BO\Zmsentities\Process();
            $entity->addAppointment($appointment);
            $this->addEntity($entity);
        }
        return $this;
    }

    public function toQueueList($now)
    {
        $queueList = new QueueList();
        foreach ($this as $process) {
            $queue = $process->toQueue($now);
            $queueList->addEntity($queue);
        }
        $queueList->setTransferedProcessList(true);
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
        foreach ($this->toProcessListByTime('Y-m-d') as $processListByDate) {
            $dateTime = $processListByDate[0]->getFirstAppointment()->toDateTime();
            $slotList = $availabilityList->withType('appointment')->withDateTime($dateTime)->getSlotList();
            foreach ($processListByDate as $process) {
                if (!$slotList->removeAppointment($process->getFirstAppointment())) {
                    $processList[] = clone $process;
                }
            }
        }
        return $processList;
    }

    public function withUniqueScope($oncePerHour = false)
    {
        $processList = new static();
        $scopeKeyList = [];
        foreach ($this as $process) {
            $scopeKey = $process->scope->id . '-';
            if ($oncePerHour) {
                $scopeKey .= $process->getFirstAppointment()->toDateTime()->format('H');
            } else {
                $scopeKey .= $process->getFirstAppointment()->toDateTime()->format('H:i');
            }
            if (!in_array($scopeKey, $scopeKeyList)) {
                $processList[] = clone $process;
                $scopeKeyList[] = $scopeKey;
            }
        }
        return $processList;
    }

    public function withAccess(\BO\Zmsentities\Useraccount $useraccount)
    {
        $list = new static();
        foreach ($this as $process) {
            $process = clone $process;
            if ($process->getCurrentScope()->hasAccess($useraccount)) {
                $list[] = $process;
            }
        }
        return $list;
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

    public function withoutExpiredAppointmentDate(\DateTimeInterface $now)
    {
        $conflictList = new self();
        foreach ($this as $process) {
            if ($process->getFirstAppointment()->date > $now->getTimestamp()) {
                $conflictList->addEntity($process);
            }
        }
        return $conflictList;
    }
}
