<?php
// Define the application path as a single global constant
if (!defined('APP_PATH')) {
    define('APP_PATH', realpath(__DIR__));
}

if (!defined('ZMS_API_URL')) {
    define('ZMS_API_URL', getenv('ZMS_API_URL') ? getenv('ZMS_API_URL') : 'http://mockup:8083');
}

// Check if vendor autoload exists and set VENDOR_PATH accordingly
if (file_exists(APP_PATH . '/../vendor/autoload.php')) {
    define('VENDOR_PATH', APP_PATH . '/../vendor');
} else {
    define('VENDOR_PATH', APP_PATH . '/../../../');
}

// Debugging VENDOR_PATH
echo 'VENDOR_PATH: ' . VENDOR_PATH . PHP_EOL;

// Require the Composer autoloader
require_once(VENDOR_PATH . '/autoload.php');

// Check if Application.php can be manually required
require_once(VENDOR_PATH . '/eappointment/zmsslim/src/Slim/Application.php');

// Require configuration
require(APP_PATH . '/config.php');

// Set curl options for the Zmsclient
\BO\Zmsclient\Psr7\Client::$curlopt = [
    CURLOPT_SSLVERSION    => 0,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT       => 15,
    // CURLOPT_VERBOSE    => true,
];

// Set the base URL for HTTP requests in tests
\BO\Zmsclient\Tests\Base::$http_baseurl = ZMS_API_URL;
