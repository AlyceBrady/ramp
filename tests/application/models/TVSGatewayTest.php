<?php

require_once 'TestSettings.php';

class models_TVSGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $_settingTests;

    public function setUp()
    {
        $this->_settingTests = TestSettings::getInstance();
    }

    public function testConstructSingleSettingFromValidFile()
    {
        // Test constructing sequence of single setting at top level
        // (not in a section)
        // and getting setting props for same name as passed to constructor.
        $filename = TestSettings::BASIC_SETTINGS_FILE;
        $gateway = new Application_Model_TVSGateway($filename);
        $this->assertSame(array(), $gateway->getSequenceProps());
        $this->assertSame(array($filename), $gateway->getTableSettingNames());
        $this->assertSame($this->_settingTests->getBasicSetting(),
                          $gateway->getSettingProps($filename));
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "no database table name provided"
     */
    public function testSettingWithNoTableName()
    {
        // Test constructing a table setting with no table name.
        $filename = TestSettings::NO_TABLE_SETTINGS_FILE;
        $gateway = new Application_Model_TVSGateway($filename);
    }

    public function testGetSettingPropsAlreadyReadIn()
    {
        // Test constructing a sequence with multiple settings (sequence
        // defined in a section) and 
        // getting a setting defined in an internal section.
        $filename = TestSettings::MULT_SETTINGS_FILE;
        $settingNames = $this->_settingTests->getSettingsInMultSettingsFile();
        $sequenceSettings =
                $this->_settingTests->getSeqSettingsInMultSettingsFile();
        $multSettingsTopLevel =
                $this->_settingTests->getTopLevelSettingsInMultSettingsFile();
        $gateway = new Application_Model_TVSGateway($filename);
        $this->assertSame($sequenceSettings, $gateway->getSequenceProps());
        $this->assertSame($settingNames, $gateway->getTableSettingNames());
        $this->assertSame($multSettingsTopLevel,
                          $gateway->getSettingProps($filename));
        $this->assertSame($this->_settingTests->getVariantBasicSetting(),
                          $gateway->getSettingProps($settingNames[2]));
    }

    public function testGetSettingPropsNotYetReadIn()
    {
        // Test constructing a sequence with multiple settings and 
        // getting an externally-defined setting.
        $filename = TestSettings::MULT_SETTINGS_FILE;
        $gateway = new Application_Model_TVSGateway($filename);
        $sequenceSettings =
                $this->_settingTests->getSeqSettingsInMultSettingsFile();
        $externalSetting = $sequenceSettings['searchResultsSetting'];
        $tableName = $this->_settingTests->getSearchResultsSettingTableName();
        $propsReadIn = $gateway->getSettingProps($externalSetting);
        $this->assertSame($tableName, $propsReadIn['tableName']);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "too many lists of sequence properties"
     */
    public function testGetExtSettingFromFileWithSequence()
    {
        // Test constructing a sequence that gets a setting from a file 
        // that also defines a sequence.
        $filename = TestSettings::FILE_W_EXTRA_SEQUENCE;
        $gateway = new Application_Model_TVSGateway($filename);
        $otherFilename = TestSettings::MULT_SETTINGS_FILE;
        $propsReadIn = $gateway->getSettingProps($otherFilename);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "duplicate or conflicting sequence"
     */
    public function testSequenceSettingIsAnotherSequence()
    {
        // Test a file with sequence information defined at both the top 
        // level and in a section with a name other than 'sequence'.
        // (If the section name is 'sequence', the INI reader will just 
        // override the top-level information with the information read
        // in later.)
        $filename = TestSettings::FILE_W_INVAL_MULT_SEQ;
        $gateway = new Application_Model_TVSGateway($filename);
    }

}
