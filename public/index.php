<?php

use Myth\App;

// Hook up Composer
include_once '../vendor/autoload.php';

echo App::instance()->run();
