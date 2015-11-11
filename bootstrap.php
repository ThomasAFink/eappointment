<?php
// @codingStandardsIgnoreFile

// define the application path as single global constant
define("APP_PATH", realpath(__DIR__));

// use autoloading offered by composer, see composer.json for path settings
require(APP_PATH . '/vendor/autoload.php');

// initialize the static \App singleton
require(APP_PATH . '/config.php');

// Set option for environment, routing, logging and templating
\BO\Slim\Bootstrap::init();
//\BO\Slim\Bootstrap::addTwigExtension(new \BO\Dldb\TwigExtension());
//\BO\Slim\Bootstrap::addTwigTemplateDirectory('dldb', APP_PATH . '/vendor/bo/clientdldb/templates');

// load routing
require(\App::APP_PATH . '/routing.php');
