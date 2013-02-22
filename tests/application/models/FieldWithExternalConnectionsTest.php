<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

class models_FieldWithExternalConnectionsTest extends PHPUnit_Framework_TestCase
{
    protected $_settingTests;

    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
        $this->_settingTests = TestSettings::getInstance();
    }

    public function testSimpleImportedField()
    {
        $tableName = 'ramp_test_addresses';
        $fieldName = 'first_name';
        $fieldSetting = array(
                'label' => 'First Name',
                'importedFrom' => 'ramp_auth_users');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertTrue($field->isImported());
        $this->assertSame("ramp_auth_users", $field->getImportTable());
        $this->assertSame("first_name", $field->resolveAlias());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testImportedFieldAsDifferentName()
    {
        $tableName = 'ramp_test_addresses';
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
        $this->assertSame("ramp_auth_users", $field->getImportTable());
        $this->assertSame("first_name", $field->resolveAlias());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testImportFromNullTable()
    {
        $tableName = 'ramp_test_addresses';
        $fieldName = 'first_name';
        $fieldSetting = array(
                'label' => 'First Name',
                'importedFrom' => null);

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSimpleInitField()
    {
        $tableName = 'ramp_initTesting';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'initFrom' => 'ramp_auth_users');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             $metaInfo);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertTrue($field->initFromAnotherTable());
        $this->assertSame("ramp_auth_users", $field->getInitTableName());
        $this->assertSame("fname", $field->getInitField());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testInitFieldAsDifferentName()
    {
        $tableName = 'ramp_initTesting';
        $table = new Application_Model_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'initFrom' => 'ramp_auth_users',
                'initFromField' => 'first_name');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             $metaInfo);

        $this->assertTrue($field->isInTable());
        $this->assertTrue($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertTrue($field->initFromAnotherTable());
        $this->assertSame("ramp_auth_users", $field->getInitTableName());
        $this->assertSame("first_name", $field->getInitField());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testInitFromNullTable()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'initFrom' => null);

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSelectFromValidTableField()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'ramp_valsTableTesting.term');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = $field->getValidVals();
        $this->assertSame(12, count($validVals));
        $this->assertSame('2008-09 Sem 1', $validVals[0]);
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSelectFromInvalidFieldInValidTable()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'ramp_valsTableTesting.nonField');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = $field->getValidVals();
        $this->assertSame(12, count($validVals));
        $this->assertSame('2008-09 Sem 1', $validVals[0]);
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSelectFromInvalidTableSetting()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'someTable.someField');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = $field->getValidVals();
        $this->assertFalse($field->isExternalTableLink());
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "does not have the required tableName.fieldName format"
     */
    public function testSelectFromTableFieldWithInvalidFormat()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'invalidTableFieldFormat');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = $field->getValidVals();
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSelectFromNullTableAndField()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => null);

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSelectUsingValidTableSetting()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'artist';
        $settingFile = TestSettings::BASIC_SETTINGS_FILE;
        $fieldSetting = array(
                'label' => 'Artist',
                'selectUsing' => $settingFile);

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertTrue($field->isExternalTableLink());
        $this->assertSame($settingFile, $field->getLinkedTableSetting());
        $setting = $this->_settingTests->getBasicSetting();
        $this->assertSame($setting['tableTitle'], $field->getLinkedTable());
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Missing settings file"
     */
    public function testSelectUsingInvalidTableSetting()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'selectUsing' => 'otherTable');

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertTrue($field->isExternalTableLink());
        $this->assertSame("otherTable", $field->getLinkedTableSetting());
        $this->assertSame("otherTableTitle", $field->getLinkedTable());
    }

    public function testSelectUsingNullTableSetting()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'selectUsing' => null);

        $field = new Application_Model_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testCompletelyWritten()
    {
        $this->assertTrue(false);
    }

}
