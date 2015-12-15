<?php
// @codingStandardsIgnoreFile
/**
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 **/

/* ---------------------------------------------------------------------------
 * html, basic routes
 * -------------------------------------------------------------------------*/

\App::$slim->get('/',
    '\BO\Zmsadmin\Index:render')
    ->name("pagesindex");

\App::$slim->get('/workstation/process/:id/precall/',
    '\BO\Zmsadmin\WorkstationClientPreCall:render')
    ->name("workstationClientPreCall");

\App::$slim->get('/workstation/process/:id/called/',
    '\BO\Zmsadmin\WorkstationClientCalled:render')
    ->name("workstationClientCalled");

\App::$slim->get('/workstation/process/:id/',
    '\BO\Zmsadmin\WorkstationClientActive:render')
    ->name("workstationClientActive");

\App::$slim->get('/workstation/',
    '\BO\Zmsadmin\Workstation:render')
    ->name("workstation");

\App::$slim->get('/counter/',
    '\BO\Zmsadmin\Counter:render')
    ->name("counter");

\App::$slim->get('/scope/',
    '\BO\Zmsadmin\Scope:render')
    ->name("scope");

\App::$slim->get('/cluster/',
    '\BO\Zmsadmin\Cluster:render')
    ->name("cluster");

\App::$slim->get('/department/',
    '\BO\Zmsadmin\Department:render')
    ->name("department");

\App::$slim->get('/organisation/',
    '\BO\Zmsadmin\Organisation:render')
    ->name("organisation");

\App::$slim->get('/owner/',
    '\BO\Zmsadmin\Owner:render')
    ->name("owner");

\App::$slim->get('/availability/day',
    '\BO\Zmsadmin\Availability:render')
    ->name("availability_day");

\App::$slim->get('/availability/calendar',
    '\BO\Zmsadmin\AvailabilityCalendar:render')
    ->name("availability_month");

\App::$slim->get('/testpage/',
    '\BO\Zmsadmin\Testpage:render')
    ->name("testpage");

//\App::$slim->get('/dienstleistung/:service_id',
//    '\BO\D115Mandant\Controller\ServiceDetail:render')
//    ->conditions([
//        'service_id' => '\d{3,10}',
//        ])
//    ->name("servicedetail");

/* ---------------------------------------------------------------------------
 * externals
 * -------------------------------------------------------------------------*/

// external link to stadplan
\App::$slim->get('http://www.Berlin.de/stadtplan/',
    function () {})
    ->name("citymap");

/* ---------------------------------------------------------------------------
 * maintenance
 * -------------------------------------------------------------------------*/

\App::$slim->get('/healthcheck/',
    '\BO\Zmsadmin\Healthcheck:render')
    ->name("healthcheck");

\App::$slim->notfound(function () {
    \BO\Slim\Render::html('404.twig');
});

\App::$slim->error(function (\Exception $exception) {
    \BO\Slim\Render::lastModified(time(), '0');
    \BO\Slim\Render::html('failed.twig', array(
        "failed" => $exception->getMessage(),
        "error" => $exception,
    ));
    \App::$slim->stop();
});
