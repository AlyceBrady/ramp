<?php
require_once 'TestConfiguration.php';

class models_BasicFieldWithNoDBAccessTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testSpecifyingOnlyFieldName()
    {
        $testName = 'MyField';

        $field = new Ramp_Table_Field($testName);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($testName, $field->getDbFieldName());
        $this->assertFalse($field->isReadOnly());
        $this->assertFalse($field->isVisible());
        $this->assertSame($testName, $field->getLabel());
        $this->assertSame("", $field->getFieldFootnote());
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

        $field = new Ramp_Table_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($testName, $field->getDbFieldName());
        $this->assertFalse($field->isReadOnly());
        $this->assertTrue($field->isVisible());
        $this->assertSame("Label", $field->getLabel());
        $this->assertSame("Footnote", $field->getFieldFootnote());
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

        $field = new Ramp_Table_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($testName, $field->getDbFieldName());
        $this->assertFalse($field->isReadOnly());
        $this->assertTrue($field->isVisible());
        $this->assertSame("Label", $field->getLabel());
        $this->assertSame("Footnote", $field->getFieldFootnote());
        // Recommended will not be true because the field is not in the table
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

        $field = new Ramp_Table_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertSame($testName, $field->getDbFieldName());
        $this->assertTrue($field->isReadOnly());
        $this->assertFalse($field->isVisible());
        $this->assertSame("Label", $field->getLabel());
        $this->assertSame("Footnote", $field->getFieldFootnote());
        $this->assertFalse($field->isRecommended());
        // Discouraged will not be true because the field is not in the table
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

        $field = new Ramp_Table_Field($testName, $fieldSetting);

        // Discouraged and Recommended will not be true because the field
        // is not in the table
        $this->assertFalse($field->isRecommended());
        $this->assertFalse($field->isDiscouraged());
        $this->_assertNoMetaInfo($field);
    }

    public function testWhenColsShownByDefaultAndHideNotSpecified()
    {
        // Assumed visible because columns shown by default (even though 
        // no label provide), and not explicitly hidden.
        $testName = 'MyField';

        $showColsByDefault = 'true';
        $field = new Ramp_Table_Field($testName, array(),
                                             array(), $showColsByDefault);
        $this->assertTrue($field->isVisible());
        $this->assertSame("MyField", $field->getLabel());
        $this->_assertNoMetaInfo($field);
    }

    public function testWhenColsShownByDefaultButHideIsTrue()
    {
        // Assumed visible because columns shown by default, but
        // explicitly hidden with 'hide' set to true.
        $testName = 'MyField';
        $fieldSetting = array(
                'label' => 'Label',
                'hide' => true);

        $showColsByDefault = 'true';
        $field = new Ramp_Table_Field($testName, $fieldSetting,
                                             array(), $showColsByDefault);
        $this->assertFalse($field->isVisible());
        $this->assertSame("Label", $field->getLabel());
        $this->_assertNoMetaInfo($field);
    }

    public function testAssumedHiddenButExplicitlyVisible()
    {
        // Assumed hidden because no label given and columns not shown 
        // by default, but explicitly visible because 'hide' is false.
        $testName = 'MyField';
        $fieldSetting = array('hide' => false);

        $field = new Ramp_Table_Field($testName, $fieldSetting);

        // Test all methods that don't have unmet preconditions.
        $this->assertTrue($field->isVisible());
        $this->_assertNoMetaInfo($field);
    }

    private function _assertNoMetaInfo($field)
    {
        $this->assertFalse($field->isEnum());
        $this->assertNull($field->getDataType());
        $this->assertFalse($field->isInTable());
        $this->assertFalse($field->isInDB());
        $this->assertSame(array(), $field->getMetaInfo());
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
        $this->assertFalse($field->validValsDefinedInExtTable());
        $this->assertFalse($field->isExternalTableLink());
    }

}
