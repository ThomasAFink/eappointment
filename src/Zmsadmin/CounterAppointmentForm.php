<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

use \BO\Zmsentities\Scope;

class CounterAppointmentForm extends BaseController
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
        $selectedDate = $validator->getParameter('selecteddate')->isString()->getValue();

        return \BO\Slim\Render::withHtml(
            $response,
            'block/appointment/form.twig',
            array(
                'selectedDate' => ($selectedDate) ? $selectedDate : \App::$now->format('Y-m-d'),
            )
        );
    }
}
