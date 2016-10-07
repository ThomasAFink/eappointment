<?php
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    // catch errors on bootstrapping
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

require(dirname(__DIR__) . '/bootstrap.php');

App::$now = new DateTimeImmutable('2016-04-01 11:55:00', new DateTimeZone('Europe/Berlin'));
\BO\Zmsdb\Helper\DldbData::$dataPath = \App::APP_PATH . '/vendor/bo/zmsdb/tests/Zmsdb/fixtures';
