<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use BO\Zmsentities\Availability;

use BO\Zmsentities\Collection\AvailabilityList;

/**
 * Check if new Availability is in conflict with existing availability
 *
 */
class AvailabilityConflicts extends BaseController
{
    /**
     * @SuppressWarnings(Param)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $validator = $request->getAttribute('validator');
        $input = $validator->getInput()->isJson()->assertValid()->getValue();
        $data = static::getAvailabilityData($input);
        return \BO\Slim\Render::withJson(
            $response,
            $data
        );
    }

    protected static function getAvailabilityData($input)
    {
        $conflictList = new \BO\Zmsentities\Collection\ProcessList();
        $selectedAvailability = new Availability($input['selectedAvailability']);
        $conflictedList = new AvailabilityList();
        foreach ($input['availabilityList'] as $item) {
            $entity = new Availability($item);
            if ($entity->getConflict()) {
                $conflictedList->addEntity($entity);
            }
        }
        if ($conflictedList->count()) {
            $startTime = $conflictedList->sortByCustomKey('startDate')->getFirst()->getStartDateTime();
            $endTime = $conflictedList->sortByCustomKey('endDate')->getLast()->getEndDateTime();
            if (in_array($selectedAvailability->getId(), $conflictedList->getIds())) {
                $startTime = $selectedAvailability->getStartDateTime();
                $endTime = $selectedAvailability->getEndDateTime();
            }
            $conflictList = $conflictedList->getConflicts($startTime, $endTime);
        }
        

        return [
            'conflictList' => $conflictList->toConflictListByDay(),
            'conflictedList' => $conflictedList->getIds()
        ];
    }

    /*
    protected static function getAvailabilityList($scope, $dateTime)
    {
        try {
            $availabilityList = \App::$http
                ->readGetResult(
                    '/scope/' . $scope->getId() . '/availability/',
                    [
                        'resolveReferences' => 0,
                        'startDate' => $dateTime->format('Y-m-d') //for skipping old availabilities
                    ]
                )
                ->getCollection();
        } catch (\BO\Zmsclient\Exception $exception) {
            if ($exception->template != 'BO\Zmsapi\Exception\Availability\AvailabilityNotFound') {
                throw $exception;
            }
            $availabilityList = new \BO\Zmsentities\Collection\AvailabilityList();
        }
        return $availabilityList->withScope($scope);
    }
    */
}
