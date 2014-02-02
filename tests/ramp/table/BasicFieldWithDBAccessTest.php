<?php
require_once 'TestConfiguration.php';

class models_BasicFieldWithDBAccessTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testRecommendedField()
    {
        // 'last_name' is not required, but recommended
        $table = new Ramp_Table_DbTable_Table('ramp_auth_users');
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'last_name';
        $fieldSetting = array('recommended' => true);

        $field = new Ramp_Table_Field($whichField, $fieldSetting,
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isRequired());
        $this->assertTrue($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
        // User does not HAVE to provide this value.
        $this->assertFalse($field->valueNecessaryForAdd());
    }

    public function testDiscouragedField()
    {
        $table = new Ramp_Table_DbTable_Table('ramp_auth_users');
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'last_name';
        $fieldSetting = array('discouraged' => true);

        $field = new Ramp_Table_Field($whichField, $fieldSetting,
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->isRecommended());
        $this->assertTrue($field->isDiscouraged());
    }

    public function testDiscouragedAndRecommended()
    {
        $table = new Ramp_Table_DbTable_Table('ramp_auth_users');
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'last_name';
        $fieldSetting = array(
                'recommended' => true,
                'discouraged' => true);

        $field = new Ramp_Table_Field($whichField, $fieldSetting,
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isRecommended());
        $this->assertTrue($field->isDiscouraged());
    }

    public function testFieldIsRequiredAndAutoIncremented()
    {
        // 'id' is required and primary key (and auto-incremented)
        $tableName = 'ramp_auth_users';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'id';

        $field = new Ramp_Table_Field($whichField, array(),
                                             $metaInfo[$whichField]);
        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isRequired());
        $this->assertTrue($field->isPrimaryKey());
        $this->assertTrue($field->isAutoIncremented());
        $this->assertNull($field->getDefault());
        // User does not have to provide this value; system will provide it.
        $this->assertFalse($field->valueNecessaryForAdd());
    }

    public function testFieldIsRequiredAndHasDefault()
    {
        // 'gender' is required but not primary key; has a default
        $tableName = 'ramp_enumTesting';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'gender';

        $field = new Ramp_Table_Field($whichField, array(),
                                             $metaInfo[$whichField]);
        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isRequired());
        $this->assertFalse($field->isPrimaryKey());
        $this->assertFalse($field->isAutoIncremented());
        $this->assertSame('Unknown', $field->getDefault());
        // User does not have to provide this value; default will serve.
        $this->assertFalse($field->valueNecessaryForAdd());
    }

    public function testFieldIsRequiredAndHasNoDefault()
    {
        // 'status' is required but has no default
        $tableName = 'ramp_enumTesting';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'status';

        $field = new Ramp_Table_Field($whichField, array(),
                                             $metaInfo[$whichField]);
        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isRequired());
        $this->assertFalse($field->isPrimaryKey());
        $this->assertFalse($field->isAutoIncremented());
        $this->assertNull($field->getDefault());
        // User must provide this value.
        $this->assertTrue($field->valueNecessaryForAdd());
    }

    public function testEnumDataType()
    {
        // Test isEnum, enum data type & values, and default.
        $tableName = 'ramp_enumTesting';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'gender';

        $field = new Ramp_Table_Field($whichField, array(),
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isEnum());
        $this->assertSame("enum('Unknown','M','F')", $field->getDataType());
        $this->assertSame(array('Unknown','M','F'),
                          array_keys($field->getEnumValues()));
        $this->assertSame('Unknown', $field->getDefault());
        $this->_assertWholelyLocal($field);
        $this->_assertMetaInfoValues($tableName, $whichField, $field);
    }

    public function testDataTypeWithLengthSpecified()
    {
        // Non-enum type (varchar); length specified
        $tableName = 'ramp_tabletest1';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'name';

        $field = new Ramp_Table_Field($whichField, array(),
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isEnum());
        $this->assertSame("varchar", $field->getDataType());
        $this->assertSame("100", $field->getLength());
        $this->_assertWholelyLocal($field);
        $this->_assertMetaInfoValues($tableName, $whichField, $field);
    }

    public function testDataTypeWithLengthNotSpecified()
    {
        // Non-enum type (int); length not specified
        $tableName = 'ramp_tabletest1';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'id';

        $field = new Ramp_Table_Field($whichField, array(),
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isEnum());
        $this->assertSame("int", $field->getDataType());
        $this->assertNull($field->getLength());
        $this->_assertWholelyLocal($field);
        $this->_assertMetaInfoValues($tableName, $whichField, $field);
    }

    private function _assertWholelyLocal($field)
    {
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertNull($field->getInitTableName());
        $this->assertFalse($field->isImported());
        $this->assertNull($field->getImportTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    private function _assertMetaInfoValues($tableName, $fieldName, $field)
    {
        $metaInfo = $field->getMetaInfo();
        $this->assertSame($tableName, $metaInfo['TABLE_NAME']);
        $this->assertSame($fieldName, $metaInfo['COLUMN_NAME']);
        $this->assertSame($field->getDataType(), $metaInfo['DATA_TYPE']);
        $this->assertSame($field->getDefault(), $metaInfo['DEFAULT']);
        $this->assertSame(! $field->isRequired(), $metaInfo['NULLABLE']);
        $lengthIsValid = $metaInfo['LENGTH'] == null ||
                         $metaInfo['LENGTH'] == $field->getLength();
        $this->assertTrue($lengthIsValid);
        $this->assertSame($field->isPrimaryKey(), $metaInfo['PRIMARY']);
        $this->assertSame($field->isAutoIncremented(), $metaInfo['IDENTITY']);
    }

}
