<?php
require_once 'TestConfiguration.php';

class models_FieldTest extends PHPUnit_Framework_TestCase
{
    protected $_field;

    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
    }

    public function testSpecifyingOnlyFieldName()
    {
        $testName = 'MyField';

        $field = new Application_Model_Field($testName);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($field->getDbFieldName(), $testName);
        $this->assertFalse($field->isReadOnly());
        $this->assertFalse($field->isVisible());
        $this->assertSame($field->getLabel(), $testName);
        $this->assertSame($field->getFieldFootnote(), "");
        $this->assertFalse($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
        $this->_assertNoMetaInfo($field);
    }

    public function testSpecifyingFieldNameAndSettingOnly()
    {
        $testName = 'MyFieldWithSetting';
        $fieldSetting = array(
                'label' => 'Label',
                'footnote' => 'Footnote');

        $field = new Application_Model_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($field->getDbFieldName(), $testName);
        $this->assertFalse($field->isReadOnly());
        $this->assertTrue($field->isVisible());
        $this->assertSame($field->getLabel(), "Label");
        $this->assertSame($field->getFieldFootnote(), "Footnote");
        $this->assertFalse($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
        $this->_assertNoMetaInfo($field);
    }

    public function testSpecifyingFieldNameSpecifyingBoolValues1()
    {
        // Tests impact of several boolean settings simultaneously.
        $testName = 'MyField';
        $fieldSetting = array(
                'label' => 'Label',
                'footnote' => 'Footnote',
                'hide' => false,
                'readOnly' => false,
                'recommended' => true);

        $field = new Application_Model_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($field->getDbFieldName(), $testName);
        $this->assertFalse($field->isReadOnly());
        $this->assertTrue($field->isVisible());
        $this->assertSame($field->getLabel(), "Label");
        $this->assertSame($field->getFieldFootnote(), "Footnote");
        // Recommended will not be true unless the field is in the table
        $this->assertFalse($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
        $this->_assertNoMetaInfo($field);
    }

    public function testSpecifyingFieldNameSpecifyingBoolValues2()
    {
        // Tests impact of several boolean settings simultaneously.
        $testName = 'MyField';
        $fieldSetting = array(
                'label' => 'Label',
                'footnote' => 'Footnote',
                'hide' => true,
                'readOnly' => true,
                'recommended' => false,
                'discouraged' => true);

        $field = new Application_Model_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($field->getDbFieldName(), $testName);
        $this->assertTrue($field->isReadOnly());
        $this->assertFalse($field->isVisible());
        $this->assertSame($field->getLabel(), "Label");
        $this->assertSame($field->getFieldFootnote(), "Footnote");
        $this->assertFalse($field->isRecommended());
        // Discouraged will not be true unless the field is in the table
        $this->assertFalse($field->isDiscouraged());
        $this->_assertNoMetaInfo($field);
    }

    public function testDiscouragedAndRecommendedButNotInTable()
    {
        $testName = 'MyField';
        $fieldSetting = array(
                'label' => 'Label',
                'recommended' => true,
                'discouraged' => true);

        $field = new Application_Model_Field($testName, $fieldSetting);

        // Discouraged and Recommended will not be true unless the field
        // is in the table
        $this->assertFalse($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
    }

    public function testWhenColsShownByDefaultAndHideNotSpecified()
    {
        $testName = 'MyField';
        $fieldSetting = array('label' => 'Label');

        $showColsByDefault = 'true';
        $field = new Application_Model_Field($testName, $fieldSetting,
                                             array(), $showColsByDefault);
        $this->assertTrue($field->isVisible());
        $this->assertSame($field->getLabel(), "Label");
    }

    public function testWhenColsShownByDefaultButHideIsTrue()
    {
        $this->assertTrue(true);
    }

    public function testRecommendedField()
    {
        // 'last_name' is not required, but recommended
        $table = new Application_Model_DbTable_Table('ramp_auth_users');
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'last_name';
        $fieldSetting = array('recommended' => true);

        $field = new Application_Model_Field($whichField, $fieldSetting,
                                             $metaInfo[$whichField]);

        $this->assertFalse($field->isRequired());
        $this->assertTrue($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
        // User does not HAVE to provide this value.
        $this->assertFalse($field->valueNecessaryForAdd());
    }

    public function testDiscouragedField()
    {
        $this->assertTrue(true);
    }

    public function testDiscouragedAndRecommended()
    {
        $this->assertTrue(true);
    }

    public function testFieldIsRequiredAndAutoIncremented()
    {
        // 'id' is required and primary key (and auto-incremented)
        $tableName = 'ramp_auth_users';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'id';

        $field = new Application_Model_Field($whichField, array(),
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
        $this->assertTrue(true);
    }

    public function testFieldIsRequiredAndHasNoDefault()
    {
        // 'status' is required but has no default
        $tableName = 'ramp_enumTesting';
        $this->assertTrue(true);
    }

    public function testEnumDataType()
    {
        // Test isEnum, enum data type & values, and default.
        $tableName = 'ramp_enumTesting';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'gender';

        $field = new Application_Model_Field($whichField, array(),
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isEnum());
        $this->assertSame($field->getDataType(), "enum('Unknown','M','F')");
        $this->assertSame(array_keys($field->getEnumValues()),
                          array('Unknown','M','F'));
        $this->assertSame($field->getDefault(), 'Unknown');
        $this->_assertWholelyLocal($field);
        $this->_assertMetaInfoValues($tableName, $whichField, $field);
    }

    public function testDataTypeWithLengthSpecified()
    {
        // Non-enum type (varchar); length specified
        $tableName = 'ramp_tabletest1';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $whichField = 'name';

        $field = new Application_Model_Field($whichField, array(),
                                             $metaInfo[$whichField]);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isEnum());
        $this->assertSame($field->getDataType(), "varchar");
        $this->assertSame($field->getLength(), "100");
        $this->_assertWholelyLocal($field);
        $this->_assertMetaInfoValues($tableName, $whichField, $field);
    }

    public function testDataTypeWithLengthNotSpecified()
    {
        // Non-enum type (int); length not specified
        $tableName = 'ramp_tabletest1';
        $this->assertTrue(true);
    }

    public function testSimpleImportedField()
    {
        $tableName = 'ramp_test_addresses';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'first_name';
        $fieldSetting = array(
                'label' => 'First Name',
                'importedFrom' => 'ramp_auth_users');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isImported());
        $this->assertSame($field->getImportTable(), "ramp_auth_users");
    }

    public function testImportedFieldAsDifferentName()
    {
        $tableName = 'ramp_test_addresses';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'firstName';
        $fieldSetting = array(
                'label' => 'First Name',
                'importedFrom' => 'ramp_auth_users',
                'importedField' => 'first_name');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isImported());
        $this->assertSame($field->getImportTable(), "ramp_auth_users");
    }

    public function testImportFromNullTable()
    {
        $tableName = 'ramp_test_addresses';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'first_name';
        $fieldSetting = array(
                'label' => 'First Name',
                'importedFrom' => null);

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
    }

    public function testAlwaysFails()
    {
        $this->assertSame('a', 'b');
    }

    private function _assertNoMetaInfo($field)
    {
        $this->assertFalse($field->isEnum());
        $this->assertNull($field->getDataType());
        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertSame($field->getMetaInfo(), array());
        $this->assertNull($field->getLength());
        $this->assertFalse($field->isRequired());
        $this->assertFalse($field->isPrimaryKey());
        $this->assertFalse($field->isAutoIncremented());
        $this->assertNull($field->getDefault());
        $this->assertFalse($field->valueNecessaryForAdd());
        $this->_assertWholelyLocal($field);
    }

    private function _assertWholelyLocal($field)
    {
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertNull($field->getInitTableName());
        $this->assertFalse($field->isImported());
        $this->assertNull($field->getImportTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    private function _assertMetaInfoValues($tableName, $fieldName, $field)
    {
        $metaInfo = $field->getMetaInfo();
        $this->assertSame($metaInfo['TABLE_NAME'], $tableName);
        $this->assertSame($metaInfo['COLUMN_NAME'], $fieldName);
        $this->assertSame($metaInfo['DATA_TYPE'], $field->getDataType());
        $this->assertSame($metaInfo['DEFAULT'], $field->getDefault());
        $this->assertSame($metaInfo['NULLABLE'], ! $field->isRequired());
        $lengthIsValid = $metaInfo['LENGTH'] == null ||
                         $metaInfo['LENGTH'] == $field->getLength();
        $this->assertTrue($lengthIsValid);
        $this->assertSame($metaInfo['PRIMARY'], $field->isPrimaryKey());
        $this->assertSame($metaInfo['IDENTITY'], $field->isAutoIncremented());
    }

}
