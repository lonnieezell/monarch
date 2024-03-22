<?php

// Start Time
define('START_TIME', microtime(true));

// Hook up Composer
include '../vendor/autoload.php';

// constants
define('ROOTPATH', realpath('.') .'/');
define('APPPATH', realpath(ROOTPATH.'app') .'/');
define('TESTPATH', realpath(ROOTPATH.'tests') .'/');

// Default timezone of server
date_default_timezone_set('UTC');

// Load .env file
(new \Myth\DotEnv(ROOTPATH .'/.env'))->load();

//---------------------------------------------------------
// LOAD HELPERS
//---------------------------------------------------------
include ROOTPATH .'myth/helpers/common.php';
