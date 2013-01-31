<?php

define('BASE_PATH', realpath(dirname(__FILE__) . '/../'));
define('INDEX_PATH', BASE_PATH . '/public');
define('APPLICATION_PATH', BASE_PATH . '/application');
define('TEST_PATH', BASE_PATH . '/tests');

define('LIBRARY_PATH', BASE_PATH . '/library');
set_include_path( '.' . PATH_SEPARATOR . LIBRARY_PATH
        . PATH_SEPARATOR . get_include_path()
    );
 
/**
 * Salt for general hashing (security)
 */
// define('GENERIC_SALT', 'asdDSasd4asdAd1GH4sdWsd1');
 
// Define application environment
define('APPLICATION_ENV', 'regressiontesting');

// Zend_Application
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV,
                                    APPLICATION_PATH . '/configs/application.ini');
$application->bootstrap();
