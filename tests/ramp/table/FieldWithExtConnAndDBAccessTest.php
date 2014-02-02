<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

class models_FieldWithExtConnAndDBAccessTest extends PHPUnit_Framework_TestCase
{
    protected $_settingTests;

    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();
        $this->_settingTests = TestSettings::getInstance();
    }

    public function testSimpleInitField()
    {
        $tableName = 'ramp_initTesting';
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'initFrom' => 'ramp_auth_users');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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
        $table = new Ramp_Table_DbTable_Table($tableName);
        $metaInfo = $table->info(Zend_Db_Table_Abstract::METADATA);
        $fieldName = 'fname';
        $fieldSetting = array(
                'label' => 'First Name',
                'initFrom' => 'ramp_auth_users',
                'initFromField' => 'first_name');

        $field = new Ramp_Table_Field($fieldName, $fieldSetting,
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

}
