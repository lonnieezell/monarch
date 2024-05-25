<?php

// Include autoload file

use Monarch\DotEnv;

define('START_TIME', microtime(true));
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');
define('ROOTPATH', realpath(__DIR__ .'/../../') .'/');
define('APPPATH', realpath(ROOTPATH.'app') .'/');
define('TESTPATH', realpath(ROOTPATH.'tests') .'/');

// Load .env file
(new DotEnv(ROOTPATH . '/.env'))->load();

require_once __DIR__ . '/../../vendor/autoload.php';

// Include common functions
require_once ROOTPATH . 'monarch/Helpers/common.php';
