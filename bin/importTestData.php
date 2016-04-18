<?php

require(__DIR__ . '/../config.php');
$dsn = \BO\Zmsdb\Connection\Select::$writeSourceName;
$dbname_zms =& \BO\Zmsdb\Connection\Select::$dbname_zms;
$dbname_dldb =& \BO\Zmsdb\Connection\Select::$dbname_dldb;
$fixtures = realpath(__DIR__ . '/../tests/Zmsdb/fixtures/');

\BO\Zmsdb\Connection\Select::$writeSourceName = str_replace("$dbname_zms", "mysql", $dsn);
$pdo = BO\Zmsdb\Connection\Select::getWriteConnection();
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname_zms`;");
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname_dldb`;");
\BO\Zmsdb\Connection\Select::closeWriteConnection();

$execSqlFile = function ($file) {
    $pdo = BO\Zmsdb\Connection\Select::getWriteConnection();
    $startTime = microtime(true);
    //var_dump($pdo->fetchPairs('SHOW STATUS'));
    //var_dump($pdo->fetchAll('SHOW TABLES;'));
    $sqlFile = fopen($file, 'r');
    echo "Importing " . basename($file) . "\n";
    $query = '';
    while ($line = fgets($sqlFile)) {
        $query .= $line;
        if (false !== strpos($line, ';')) {
            try {
                $pdo->exec($query);
                echo ".";
                //echo "Successful:\n$query\n";
                $query = '';
            } catch (Exception $exception) {
                echo "Offending query: \n$query\n";
                throw $exception;
            }
        }
    }
    $time = round(microtime(true) - $startTime, 3);
    echo "\nTook $time seconds\n";
};
\BO\Zmsdb\Connection\Select::$writeSourceName = str_replace($dbname_zms, $dbname_dldb, $dsn);
$execSqlFile($fixtures . '/mysql_startinfo.sql');
\BO\Zmsdb\Connection\Select::$writeSourceName = $dsn;
\BO\Zmsdb\Connection\Select::closeWriteConnection();
$execSqlFile($fixtures . '/mysql_zmsbo.sql');
