<?php

require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class ControllerTestCase extends
Zend_Test_PHPUnit_ControllerTestCase
{
    protected function setUp()
    {
        $this->bootstrap =
            new Zend_Application(APPLICATION_ENV,
                                 APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }
}

