<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Monarch\App;
use Tests\TestCase;

uses(TestCase::class)
    ->beforeAll(function () {
        // Reset the HTTP request to a blank slate.
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];

        // Prepare the App environment.
        if (! defined('ENVIRONMENT')) {
            define('ENVIRONMENT', 'testing');
        }
        if (! defined('ROOTPATH')) {
            define('ROOTPATH', realpath('.') .'/');
        }
        if (! defined('APPPATH')) {
            define('APPPATH', realpath(ROOTPATH.'app') .'/');
        }
        if (! defined('TESTPATH')) {
            define('TESTPATH', realpath(ROOTPATH.'tests') .'/');
        }
        if (! defined('WRITEPATH')) {
            define('WRITEPATH', realpath(ROOTPATH.'writable') .'/');
        }


        $app = App::createFromGlobals();
        $app->prepareEnvironment();
    })
    ->beforeEach(function () {
        // Reset the HTTP request to a blank slate.
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
    })
    ->in(__DIR__); // All files/folders

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
