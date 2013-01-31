<?php
require_once 'TestConfiguration.php';

class models_SetTableTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testFetchAll()
    {
        $setTable = new Application_Model_SetTable();
        $places = $placesFinder->fetchAll();

        $this->assertSame('a', 'b');
    }
}
