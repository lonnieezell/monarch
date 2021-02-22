<?php

// Start Time
define('START_TIME', time());

// Start Memory
define('START_MEMORY', memory_get_usage());

// Hook up Composer
include '../vendor/autoload.php';

// constants
define('ROOTPATH', realpath('..') .'/');
define('APPPATH', realpath(ROOTPATH.'app') .'/');

// Default timezone of server
date_default_timezone_set('UTC');

// Load .env file
(new \Myth\DotEnv(ROOTPATH .'/.env'))->load();

//---------------------------------------------------------
// LOAD HELPERS
//---------------------------------------------------------
include ROOTPATH .'myth/helpers/common.php';

//---------------------------------------------------------
// RUN CONTROLLER
//---------------------------------------------------------
$controllerName = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$controllerName = empty($controllerName) ? 'index.php' : $controllerName;
$controllerPath = ROOTPATH.'app/controllers/'.$controllerName;

if (file_exists($controllerPath)) {
	include $controllerPath;
}



