<?php
require(__DIR__ . '/../vendor/autoload.php');
\BO\Zmsclient\Psr7\Client::$curlopt = [
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 3,
    //CURLOPT_VERBOSE => true,
];
$baseurl = getenv('ZMS_API_URL') ? getenv('ZMS_API_URL') :  'https://localhost/terminvereinbarung/api/2';
\BO\Zmsclient\Tests\Base::$http_baseurl = $baseurl;
