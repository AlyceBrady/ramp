<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

class models_FieldWithExtConnTest extends PHPUnit_Framework_TestCase
{
    protected $_settingTests;

    public function setUp()
    {
        $this->_settingTests = TestSettings::getInstance();
    }

    public function testSimpleImportedField()
    {
        $tableName = 'ramp_test_addresses';
        $fieldName = 'first_name';
        $fieldSetting = array(
                'label' => 'First Name',
                'importedFrom' => 'ramp_auth_users');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

    // See FieldWithExtConnAndDBAccessTest.php for testSimpleInitField() 
    // and testInitFieldAsDifferentName().

    public function testInitFromNullTable()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'initFrom' => null);

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = array_keys($field->getValidVals());
        $this->assertSame(12, count($validVals));
        $this->assertSame('2008-09 Sem 1', $validVals[0]);
        $this->assertFalse($field->isExternalTableLink());
    }

    public function testSelectFromInvalidFieldInValidTable()
    {
        $this->setExpectedException('Exception',
                        'should be a valid table name and field name');
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'ramp_valsTableTesting.nonField');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = array_keys($field->getValidVals());
    }

    public function testSelectFromInvalidTableSetting()
    {
        $this->setExpectedException('Exception',
                        'should be a valid table name and field name');
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'someTable.someField');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertTrue($field->validValsDefinedInExtTable());
        $validVals = array_keys($field->getValidVals());
    }

    public function testSelectFromTableFieldWithInvalidFormat()
    {
        $this->setExpectedException('Exception',
                    'does not have the required tableName.fieldName format');
        $tableName = 'ramp_initTesting';
        $fieldName = 'term';
        $fieldSetting = array(
                'label' => 'Term',
                'selectFrom' => 'invalid.Table.Field.Format');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertTrue($field->isExternalTableLink());
        $this->assertSame($settingFile, $field->getLinkedTableSetting());
        $setting = $this->_settingTests->getBasicSetting();
        $this->assertSame($setting['tableTitle'],
                          $field->getLinkedTableTitle());
    }

    public function testSelectUsingInvalidTableSetting()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        $tableName = 'ramp_initTesting';
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'selectUsing' => 'otherTable');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertTrue($field->isExternalTableLink());
        $this->assertSame("otherTable", $field->getLinkedTableSetting());
        $this->assertSame("otherTableTitle",
                          $field->getLinkedTableTitle());
    }

    public function testSelectUsingNullTableSetting()
    {
        $tableName = 'ramp_initTesting';
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'selectUsing' => null);

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
                                             array());

        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertFalse($field->isImported());
        $this->assertFalse($field->initFromAnotherTable());
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

}
