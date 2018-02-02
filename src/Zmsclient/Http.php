<?php

namespace BO\Zmsclient;

/**
 * Access api method_exists
 */
class Http
{
    /**
     * @var Psr7\ClientInterface $client
     */
    protected $client = null;

    /**
     * @var String $http_username
     */
    protected $http_username = null;

    /**
     * @var String $http_password
     */
    protected $http_password = null;

    /**
     * @var String $http_baseurl
     */
    protected $http_baseurl = null;

    /**
     * @var Psr7\Uri $uri
     */
    protected $uri = null;

    /**
     * @var Boolean $logEnabled Log requests and responses if true
     */
    public static $logEnabled = true;

    /**
     * @var Array $log Contains a list of requests and responses if logging is enabled
     */
    public static $log = [];

    /**
     *
     * @param Psr7\ClientInterface $client
     */
    public function __construct($baseUrl, Psr7\ClientInterface $client = null)
    {
        $this->http_baseurl = parse_url($baseUrl, PHP_URL_PATH);
        $this->uri = new Psr7\Uri();
        $this->uri = $this->uri->withScheme(parse_url($baseUrl, PHP_URL_SCHEME));
        $this->uri = $this->uri->withHost(parse_url($baseUrl, PHP_URL_HOST));
        $port = parse_url($baseUrl, PHP_URL_PORT);
        // @codeCoverageIgnoreStart
        if ($port) {
            $this->uri = $this->uri->withPort($port);
        }
        // @codeCoverageIgnoreEnd
        $user = parse_url($baseUrl, PHP_URL_USER);
        $pass = parse_url($baseUrl, PHP_URL_PASS);
        if ($user) {
            $this->setUserInfo($user, $pass);
        }
        if (null === $client) {
            $client = new Psr7\Client();
        }
        $this->client = $client;
    }

    public function setUserInfo($user, $pass)
    {
        $this->uri = $this->uri->withUserInfo($user, $pass);
        return $this;
    }

    /**
     * Start request and fetch response
     * The request is extended by auth informations
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function readResponse(\Psr\Http\Message\RequestInterface $request)
    {
        $request = $this->getAuthorizedRequest($request);
        $startTime = microtime(true);
        $response = $this->client->readResponse($request);
        if (self::$logEnabled) {
            self::$log[] = $request;
            self::$log[] = $response;
            $responseSizeKb = round(strlen($response) / 1024);
            self::$log[] = "Response ($responseSizeKb kb) time in s: " . round(microtime(true) - $startTime, 3);
        }
        return $response;
    }

    /**
     * Extend the request by auth informations
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getAuthorizedRequest(\Psr\Http\Message\RequestInterface $request)
    {
        $userInfo = $request->getUri()->getUserInfo();
        $xAuthKey = Auth::getKey();
        if (null !== $xAuthKey && ! $userInfo) {
            $request = $request->withHeader('X-Authkey', $xAuthKey);
        } elseif ($userInfo) {
            $request = $request->withHeader('Authorization', 'Basic '. base64_encode($userInfo));
        }
        return $request;
    }

    /**
     * Creates a GET-Http-Request and fetches the response
     *
     * @param String $relativeUrl
     * @param Array $getParameters (optional)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function readGetResult($relativeUrl, array $getParameters = null, $xToken = null)
    {
        $uri = $this->uri->withPath($this->http_baseurl . $relativeUrl);
        if (null !== $getParameters) {
            $uri = $uri->withQuery(http_build_query($getParameters));
        }
        $request = new Psr7\Request('GET', $uri);
        if (null !== $xToken) {
            $request = $request->withHeader('X-Token', $xToken);
        }
        $response = $this->readResponse($request);
        return new Result($response, $request);
    }

    /**
     * Creates a POST-Http-Request and fetches the response
     *
     * @param String $relativeUrl
     * @param \BO\Zmsentities\Schema\Entity $entity
     * @param Array $getParameters (optional)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function readPostResult($relativeUrl, $entity, array $getParameters = null)
    {
        $uri = $this->uri->withPath($this->http_baseurl . $relativeUrl);
        if (null !== $getParameters) {
            $uri = $uri->withQuery(http_build_query($getParameters));
        }
        $request = new Psr7\Request('POST', $uri);
        $body = new Psr7\Stream();
        $body->write(json_encode($entity));
        $request = $request->withBody($body);
        $response = $this->readResponse($request);
        return new Result($response, $request);
    }

    /**
     * Creates a DELETE-Http-Request and fetches the response
     *
     * @param String $relativeUrl
     * @param Array $getParameters (optional)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function readDeleteResult($relativeUrl, array $getParameters = null)
    {
        $uri = $this->uri->withPath($this->http_baseurl . $relativeUrl);
        if (null !== $getParameters) {
            $uri = $uri->withQuery(http_build_query($getParameters));
        }
        $request = new Psr7\Request('DELETE', $uri);
        $response = $this->readResponse($request);
        return new Result($response, $request);
    }
}
