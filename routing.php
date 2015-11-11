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

\App::$slim->get('/workstation/',
    '\BO\Zmsadmin\Workstation:render')
    ->name("workstation");

\App::$slim->get('/counter/',
    '\BO\Zmsadmin\Counter:render')
    ->name("counter");

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
