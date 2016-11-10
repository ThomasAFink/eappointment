<?php

/**
 * @package ZMS API
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsapi;

use \BO\Slim\Render;
use \BO\Mellon\Validator;
use \BO\Zmsdb\Calendar as Query;

class CalendarGet extends BaseController
{
    /**
     *
     * @return String
     */
    public static function render()
    {
        $query = new Query();
        $message = Response\Message::create(Render::$request);
        $input = Validator::input()->isJson()->assertValid()->getValue();
        $calendar = new \BO\Zmsentities\Calendar($input);
        if (!isset($calendar['firstDay']) || !isset($calendar['lastDay'])) {
            throw new Exception\Calendar\InvalidFirstDay();
        } else {
            $calendar = $query->readResolvedEntity($calendar, \App::getNow())->withLessData();
            $message->data = $calendar;
        }
        if (0 == count($message->data['days'])) {
            throw new Exception\Calendar\AppointmentsMissed();
        }
        Render::lastModified(time(), '0');
        Render::json($message->setUpdatedMetaData(), $message->getStatuscode());
    }
}
