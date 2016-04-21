<?php
/**
 *
 * @package Zmsmessaging
 * @copyright BerlinOnline Stadtportal GmbH & Co. KG
 *
 */
namespace BO\Zmsmessaging;

class Application
{

    /**
     * Name of the application
     */
    const IDENTIFIER = 'Zmsmessaging';

    const DEBUG = false;

    /*
     * -----------------------------------------------------------------------
     * ZMS Messaging access
     */

    public static $messaging = null;

    /*
     * -----------------------------------------------------------------------
     * ZMS API access
     */
    public static $http = null;

    public static $http_curl_config = array();

    /**
     * HTTP url for api
     */
    const HTTP_BASE_URL = 'http://user:pass@host.tdl';
}
