<?php

// set some directory names that we will need
if (!defined('APP_ROOT')) {
    define('APP_ROOT', __DIR__ . '/');
}

if (!defined('LIB_ROOT')) {                                                     
        define('LIB_ROOT', APP_ROOT . 'lib/');                                      
} 

// Load the autoloader
//require LIB_ROOT . 'psr0.autoloader.php';
require APP_ROOT . 'vendor/.composer/autoload.php';

// Initalize our Dependency Injection Container
$container = new \Pimple();
$container['db_connection'] = function ($c) {
    return new PDO(
        'pgsql:host=localhost;dbname=ibl_stats', 
        'stats',
        'st@ts=Fun'
    );
};

// Slim doesn't like being autoloaded WTF
$app = new \Slim();


