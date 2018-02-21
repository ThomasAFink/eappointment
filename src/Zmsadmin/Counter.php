<?php
/**
 * @package Zmsadmin
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

namespace BO\Zmsadmin;

/**
  * Handle requests concerning services
  *
  */
class Counter extends BaseController
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
        $workstation = \App::$http->readGetResult('/workstation/', ['resolveReferences' => 2])->getEntity();
        $validator = $request->getAttribute('validator');
        $selectedDate = $validator->getParameter('date')->isString()->getValue();
        $selectedDate = ($selectedDate) ? $selectedDate : \App::$now->format('Y-m-d');
        $selectedTime = $validator->getParameter('time')->isString()->getValue();
        $selectedTime = ($selectedTime) ? $selectedTime : null;
        $waitingnumber = $validator->getParameter('waitingnumber')->isNumber()->getValue();

        if (!$workstation->hasId()) {
            return \BO\Slim\Render::redirect(
                'index',
                array(
                    'error' => 'login_failed'
                )
            );
        }

        return \BO\Slim\Render::withHtml(
            $response,
            'page/counter.twig',
            array(
                'title' => 'Tresen',
                'menuActive' => 'counter',
                'selectedDate' => $selectedDate,
                'selectedTime' => $selectedTime,
                'waitingnumber' => $waitingnumber,
                'workstation' => $workstation->getArrayCopy()            )
        );
    }
}
