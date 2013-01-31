<?php
require_once 'TestConfiguration.php';

class models_TVSFactory extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testConstructorAndGetName()
    {
        $testName = 'Users';
        $table = new Application_Model_DbTable_Table($testName);
        $name = $table->getName();

        $this->assertSame('a', 'b');
    }
}
