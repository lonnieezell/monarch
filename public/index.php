<?php

use Monarch\App;
use Tracy\Debugger;

// Hook up Composer
include_once '../vendor/autoload.php';

// constants
define('START_TIME', microtime(true));
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');
define('ROOTPATH', realpath('..') .'/');
define('APPPATH', realpath(ROOTPATH.'app') .'/');
define('TESTPATH', realpath(ROOTPATH.'tests') .'/');
define('MONARCHPATH', realpath(ROOTPATH.'monarch') .'/');
define('WRITEPATH', realpath(ROOTPATH.'writable') .'/');

$app = App::createFromGlobals();
$app->prepareEnvironment();

if (! defined('DEBUG')) {
    define('DEBUG', (bool)getenv('DEBUG'));
}

// Setup Tracy Debugger
if (getenv('DEBUG')) {
    Debugger::enable(Debugger::Development);
}

echo $app->run();
