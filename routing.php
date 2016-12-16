<?php
// @codingStandardsIgnoreFile
/**
 *
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
use BO\Slim\Helper;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;
/*
 * ---------------------------------------------------------------------------
 * html, basic routes
 * -------------------------------------------------------------------------
 */
\App::$slim->get('/', '\BO\Zmscalldisplay\Index')
    ->setName("index");

\App::$slim->post('/queue/', '\BO\Zmscalldisplay\Queue')
    ->setName("queue");

/*
 * ---------------------------------------------------------------------------
 * maintenance
 * -------------------------------------------------------------------------
 */
\App::$slim->get('/healthcheck/', '\BO\Zmscalldisplay\Healthcheck')
    ->setName("healthcheck");
\App::$slim->getContainer()
    ->offsetSet('notFoundHandler',
    function ($container) {
        return function (RequestInterface $request, ResponseInterface $response) {
            return \BO\Slim\Render::withHtml($response, '404.twig');
        };
    });
\App::$slim->getContainer()
    ->offsetSet('errorHandler',
    function ($container) {
        return function (RequestInterface $request, ResponseInterface $response, \Exception $exception) {
            return \BO\Slim\TwigExceptionHandler::withHtml($request, $response, $exception);
        };
    });

