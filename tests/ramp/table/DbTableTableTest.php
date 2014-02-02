<?php
require_once 'TestConfiguration.php';

class models_DbTableTableTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testConstructorAndGetName()
    {
        $testName = 'Users';
        $table = new Ramp_Table_DbTable_Table($testName);
        $name = $table->getName();

        $this->assertSame($testName, $name);
    }

}
