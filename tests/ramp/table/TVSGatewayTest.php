<?php

require_once 'TestSettings.php';

class models_TVSGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $_settingTests;

    public function setUp()
    {
        $this->_settingTests = TestSettings::getInstance();
    }

    // constr -> _importProps
    public function testInvalidSettingSequenceFile()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        // Test constructing a table setting from an invalid file.
        // Error actually comes from Ramp_Ini_Reader.
        $filename = TestSettings::NON_FILE;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // constr -> _importProps -> _getKeyVal
    // constr -> _importProps -> _getSequenceProps
    // constr -> _importProps -> _getSettingProps
    // constr -> _importProps -> _isSettingSpec
    // getSettingProps
    public function testConstructSingleSettingFromValidFile()
    {
        // _getKeyVal: Top-level table name exists
        // _getSequenceProps: No explicit sequence settings
        // _hasSequenceSpec: top-level propDefs is array without seq 
        //      keyword (TF); tableName prop is not an array (F)
        // _getSettingProps: No sections; valid setting props at top level
        // _isSettingSpec: several properties are not arrays; remaining
        //      top-level property (field) is not a full setting;
        //      top-level array has valid setting properties
        // getSettingProps: setting name is same as constructor (there 
        //      are no sections with settings
        $filename = TestSettings::BASIC_SETTINGS_FILE;
        $gateway = new Ramp_Table_TVSGateway($filename);
        $expectedSetting = $this->_settingTests->getBasicSetting();
        $this->assertSame($expectedSetting['tableName'],
                          $gateway->getTopLevelTableName());
        $this->assertSame(array(), $gateway->getSequenceProps());
        $this->assertSame(array($filename), $gateway->getTableSettingNames());
        $this->assertSame($expectedSetting,
                          $gateway->getSettingProps($filename));
    }

    // constr -> _importProps -> _getKeyVal
    // constr -> _importProps -> _getSequenceProps
    // constr -> _importProps -> _getSettingProps
    public function testTVSFileWithNoTopLevelTableName()
    {
        // _getKeyVal: No top-level table name
        // _getSequenceProps: Explicit top-level sequence settings
        // _getSettingProps: Several sections with valid setting props
        $filename = TestSettings::FILE_SHOWING_COLS_BY_DEFAULT;
        $gateway = new Ramp_Table_TVSGateway($filename);
        $expectedSetting = $this->_settingTests->getBasicSetting();
        $this->assertNull($gateway->getTopLevelTableName());
    } 

    // constr -> _importProps -> _getSequenceProps
    // constr -> _importProps -> _getSequenceProps -> _hasSequenceSpec
    // getSettingProps
    public function testGetSettingPropsAlreadyReadIn()
    {
        // Test constructing a sequence with multiple settings and
        // getting a setting defined in an internal section.
        // _getSequenceProps: No sequence props at top level; explicit
        //      sequence defined in a section
        // _hasSequenceSpec: top-level propDefs is array with array seq 
        //      keyword that has embedded sequence keyword(TTTF); propDefs 
        //      inside sequence section is array with array seq keyword
        //      without embedded sequence (TTTT; returns true); other section
        //      propDefs are arrays without sequence keyword (TF); 
        //      tableName prop is not an array (F)
        // getSettingProps: get setting already read in
        $filename = TestSettings::MULT_SETTINGS_FILE;
        $settingNames = $this->_settingTests->getSettingsInMultSettingsFile();
        $sequenceSettings =
                $this->_settingTests->getSeqSettingsInMultSettingsFile();
        $multSettingsTopLevel =
                $this->_settingTests->getTopLevelSettingsInMultSettingsFile();
        $gateway = new Ramp_Table_TVSGateway($filename);
        $this->assertSame($multSettingsTopLevel['tableName'],
                          $gateway->getTopLevelTableName());
        $this->assertSame($sequenceSettings, $gateway->getSequenceProps());
        $this->assertSame($settingNames, $gateway->getTableSettingNames());
        $this->assertSame($multSettingsTopLevel,
                          $gateway->getSettingProps($filename));
        $this->assertSame($this->_settingTests->getVariantBasicSetting(),
                          $gateway->getSettingProps($settingNames[4]));
    }

    // constr -> _importProps -> _getSequenceProps
    // constr -> _importProps -> _getSequenceProps -> _hasSequenceSpec
    public function testSequencePropertyHasNothingToDoWithSequence()
    {
        // _getSequenceProps: No sequence props at top level, nor in any
        //      section
        // _hasSequenceSpec: top-level propDefs is array with non-array
        //      sequence keyword (TTF)
        //  NOTE: Not an error.  (Property is ignored.)
        $filename = TestSettings::MISLEADING_SEQ_PROP;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // constr -> _importProps -> _getSequenceProps
    // constr -> _importProps -> _getSequenceProps -> _hasSequenceSpec
    public function testSequenceSectionHasNothingToDoWithSequence()
    {
        // _getSequenceProps: No sequence props at top level, nor in any
        //      section
        // _hasSequenceSpec: top-level propDefs is array with array seq
        //      keyword without embedded sequence keyword (but is 
        //      nothing to do with sequences)
        //  NOTE: Not an error here.  Section treated as a table setting.
        $filename = TestSettings::MISLEADING_SEQ_SEC;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // constr -> _importProps -> _getSequenceProps
    public function testSequenceSettingIsAnotherSequence()
    {
        $this->setExpectedException('Exception',
                                    'duplicate or conflicting sequence');
        // _getSequenceProps: Test a file with sequence information defined
        // at both the top 
        // level and in a section with a name other than 'sequence'.
        // (If the section name is 'sequence', the INI reader will just 
        // override the top-level information with the information read
        // in later.)
        $filename = TestSettings::FILE_W_INVAL_MULT_SEQ;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // constr -> _importProps -> _getSettingProps
    public function testSettingNameIsDuplicated()
    {
        // _getSettingProps: section name & top-level name are the same
        $this->setExpectedException('Exception',
                                    'two sets of table settings properties');
        $filename = TestSettings::BAD_MULT_SETTINGS_FILE;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // constr -> _importProps -> _getSettingProps
    public function testSettingWithNoTableProperties()
    {
        // _getSettingProps: has bad section (no valid table properties)
        $this->setExpectedException('Exception',
                                    'no table setting properties');
        $filename = TestSettings::NO_TABLE_PROPS_SETTINGS_FILE;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // constr -> _importProps -> _getSettingProps
    public function testBadTopLevelProperty()
    {
        // _getSettingProps: has bad top-level property
        $this->setExpectedException('Exception',
                                    'sequence or setting property');
        $filename = TestSettings::BAD_TOP_LEVEL_PROP;
        $gateway = new Ramp_Table_TVSGateway($filename);
    }

    // getSettingProps
    public function testGetSettingPropsNotYetReadIn()
    {
        // getSettingProps:
        // Test constructing a sequence with multiple settings and 
        // getting an externally-defined setting.
        $filename = TestSettings::MULT_SETTINGS_FILE;
        $gateway = new Ramp_Table_TVSGateway($filename);
        $sequenceSettings =
                $this->_settingTests->getSeqSettingsInMultSettingsFile();
        $externalSetting = $sequenceSettings['searchResultsSetting'];
        $tableName = $this->_settingTests->getSearchResultsSettingTableName();
        $propsReadIn = $gateway->getSettingProps($externalSetting);
        $this->assertSame($tableName, $propsReadIn['tableName']);
    }

    // getSettingProps
    public function testGetExtSettingFromFileWithSequence()
    {
        // getSettingProps:
        // Test constructing a sequence that gets a setting from a file 
        // that also defines a sequence.
        $this->setExpectedException('Exception',
                                    'too many lists of sequence properties');
        $filename = TestSettings::FILE_W_EXTRA_SEQUENCE;
        $gateway = new Ramp_Table_TVSGateway($filename);
        $otherFilename = TestSettings::MULT_SETTINGS_FILE;
        $propsReadIn = $gateway->getSettingProps($otherFilename);
    }

}
