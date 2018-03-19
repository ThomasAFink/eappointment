<?php
/**
 *
 * @package Zmsticketprinter
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsticketprinter;

class Home extends BaseController
{

    /**
     * @SuppressWarnings(UnusedFormalParameter)
     * @return String
     */
    public function readResponse(
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response,
        array $args
    ) {
        $homeUrl = \BO\Zmsclient\Ticketprinter::getHomeUrl();
        if (! $homeUrl) {
            throw new Exception\HomeNotFound();
        }
        $response = \BO\Slim\Render::withLastModified($response, time(), '0');
        return $response->withRedirect($homeUrl);
    }
}
