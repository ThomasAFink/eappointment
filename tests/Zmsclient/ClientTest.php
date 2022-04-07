<?php

namespace BO\Zmsclient\Tests;

use BO\Zmsclient\Psr7\Client;
use BO\Zmsclient\Psr7\Request;
use BO\Zmsclient\Psr7\Uri;
use \BO\Mellon\Validator;

class ClientTest extends Base
{
    public function testStatus()
    {
        $uri = new Uri(self::$http_baseurl . '/status/');
        $request = new Request('GET', $uri);
        $response = Client::readResponse($request);
        $bodyContent = (string)$response->getBody();
        $body = Validator::value($bodyContent)->isJson();
        $this->assertFalse($body->hasFailed());
    }

    public function testStatusFailed()
    {
        $this->expectException('\BO\Zmsclient\Psr7\RequestException');
        $uri = new Uri(self::$http_baseurl . '/status/');
        $uri = $uri->withPort(4444);
        $request = new Request('GET', $uri);
        Client::readResponse($request);
    }
}
