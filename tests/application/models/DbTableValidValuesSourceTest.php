<?php
require_once 'TestConfiguration.php';

class models_DbTableValidValuesSourceTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testGetValValsFromValidTableAndField()
    {
        $tableName = "ramp_valsTableTesting";

        $sourceTable =
                new Application_Model_DbTable_ValidValuesSource($tableName);
        $validVals = $sourceTable->getValidValues('term');

        $this->assertSame(12, count($validVals));
        $this->assertSame('2008-09 Sem 1', $validVals[0]);
    }

    /**
     * @expectedException           Exception
     */
    public function testGetValValsFromInvalidTable()
    {
        $tableName = "someTable";

        $sourceTable =
                new Application_Model_DbTable_ValidValuesSource($tableName);
        $validVals = $sourceTable->getValidValues('term');

        $this->assertSame(12, count($validVals));
        $this->assertSame('2008-09 Sem 1', $validVals[0]);
    }

    /**
     * @expectedException           Exception
     */
    public function testGetValValsFromInvalidField()
    {
        $tableName = "ramp_valsTableTesting";

        $sourceTable =
                new Application_Model_DbTable_ValidValuesSource($tableName);
        $validVals = $sourceTable->getValidValues('nonField');

        $this->assertSame(12, count($validVals));
        $this->assertSame('2008-09 Sem 1', $validVals[0]);
    }

}
