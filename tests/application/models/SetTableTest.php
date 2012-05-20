<?php
require_once dirname(__FILE__) . '/../TestConfiguration.php';
require_once '../application/models/SetTable.php';

class models_SetTableTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testFetchAll()
    {
        $setTable = new SetTable();
        $places = $placesFinder->fetchAll();

        $this->assertSame(3, $places->count());
    }
}
