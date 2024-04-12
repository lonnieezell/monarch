<?php

use Monarch\App;

// Hook up Composer
include_once '../vendor/autoload.php';

// constants
define('START_TIME', microtime(true));
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');
define('ROOTPATH', realpath('..') .'/');
define('APPPATH', realpath(ROOTPATH.'app') .'/');
define('TESTPATH', realpath(ROOTPATH.'tests') .'/');

echo App::createFromGlobals()->run();
