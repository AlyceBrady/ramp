<?php
require_once 'TestSettings.php';

class models_SessionTimerTest extends PHPUnit_Framework_TestCase
{

    // TODO: How do we test the startSessionTimer function?  Have to 
    // have a way to check that identity doesn't disappear in a certain 
    // amount of time and does disappear after a longer period of time.  
    // If we're also going to test the timer reset in the ACL controller 
    // plugin, we also have to test that identity does not disappear 
    // after the longer period of time if some action has been invoked 
    // in the meantime.  (All this has been tested manually.)

}
