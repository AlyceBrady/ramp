<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

class models_TableViewSequenceTest extends PHPUnit_Framework_TestCase
{
    const NON_FILE = TestSettings::NON_FILE;
    const NO_SETTING = TestSettings::NO_SETTING;
    const BASIC_FILE = TestSettings::BASIC_SETTINGS_FILE;
    const MULT_SETTINGS = TestSettings::MULT_SETTINGS_FILE;
    const NO_SEQ_TBL_NAME = TestSettings::FILE_SHOWING_COLS_BY_DEFAULT;
    const MAIN_ONLY = TestSettings::MAIN_ONLY ;
    const ADD_AND_SEARCH_RES = TestSettings::ADD_AND_SEARCH_RES ;
    const ADD_AND_EDIT = TestSettings::ADD_AND_EDIT ;
    const SEARCH_RES_ONLY = TestSettings::SEARCH_RES_ONLY ;
    const SEARCH_SPEC_ONLY = TestSettings::SEARCH_SPEC_ONLY;
    const REFERENCE_ONLY = TestSettings::REFERENCE_ONLY ;

    protected $_settingTests;
    protected $_main;
    protected $_add;
    protected $_edit;
    protected $_searchSpec;
    protected $_searchRes;
    protected $_reference;

    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();

        $this->_settingTests = TestSettings::getInstance();
        $multSettings =
                    $this->_settingTests->getSeqSettingsInMultSettingsFile();
        $this->_main = $multSettings['setting'];
        $this->_add = $multSettings['addSetting'];
        $this->_edit = $multSettings['editSetting'];
        $this->_searchSpec = $multSettings['searchSpecSetting'];
        $this->_searchRes = $multSettings['searchResultsSetting'];
        $this->_reference = $multSettings['referenceSetting'];
    }

    public function testInvalidSettingSequenceFile()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        // Test constructing a sequence from an invalid file.
        $sequence = new Application_Model_TableViewSequence(self::NON_FILE);
    }

    public function testNoExplicitOrImpliedSequence()
    {
        $this->setExpectedException('Exception',
                                    'not recognized as a sequence');
        $sequence = new Application_Model_TableViewSequence(self::NO_SETTING);
    }

    public function testSimpleImpliedSequence()
    {
        $sequence = new Application_Model_TableViewSequence(self::BASIC_FILE);

        $expectedSetting = $this->_settingTests->getBasicSetting();
        $this->assertSame($expectedSetting['tableName'],
                          $sequence->getSeqLevelTableName());

        $setting = $sequence->getSetTableForViewing();
        $this->assertSame(self::BASIC_FILE, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getReferenceSetTable());
    }

    public function testSeqWithAllSettingsExplicitlySpecified()
    {
        $sequence =
            new Application_Model_TableViewSequence(self::MULT_SETTINGS);

        $multSettingsTopLevel =
                $this->_settingTests->getTopLevelSettingsInMultSettingsFile();
        $this->assertSame($multSettingsTopLevel['tableName'],
                          $sequence->getSeqLevelTableName());

        $setting = $sequence->getSetTableForViewing();
        $this->assertSame($this->_main, $setting->getSettingName());
        $setting = $sequence->getSetTableForModifying();
        $this->assertSame($this->_edit, $setting->getSettingName());
        $setting = $sequence->getSetTableForAdding();
        $this->assertSame($this->_add, $setting->getSettingName());
        $setting = $sequence->getSetTableForSearching();
        $this->assertSame($this->_searchSpec, $setting->getSettingName());
        $setting = $sequence->getSetTableForSearchResults();
        $this->assertSame($this->_searchRes, $setting->getSettingName());
        $setting = $sequence->getReferenceSetTable();
        $this->assertSame($this->_reference, $setting->getSettingName());
    }

    public function testSeqWithNoTopLevelTableName()
    {
        $sequence =
            new Application_Model_TableViewSequence(self::NO_SEQ_TBL_NAME);
        $this->assertNull($sequence->getSeqLevelTableName());
    }

    public function testSeqWithOnlySearchResultSettingSpecified()
    {
        // All should use SearchResult setting
        $sequence =
            new Application_Model_TableViewSequence(self::SEARCH_RES_ONLY);

        $setting = $sequence->getSetTableForSearchResults();
        $this->assertSame($this->_searchRes, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getReferenceSetTable());
    }

    public function testSeqWithOnlySearchSpecSettingSpecified()
    {
        // All should use SearchSpec, including SearchResult setting
        $sequence =
            new Application_Model_TableViewSequence(self::SEARCH_SPEC_ONLY);

        $setting = $sequence->getSetTableForSearching();
        $this->assertSame($this->_searchSpec, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getReferenceSetTable());
    }

    public function testSeqWithAddAndSearchResultSpecified()
    {
        // SearchResult should be as specified;
        // all others should use Add setting
        $sequence =
            new Application_Model_TableViewSequence(self::ADD_AND_SEARCH_RES);

        $setting = $sequence->getSetTableForSearchResults();
        $this->assertSame($this->_searchRes, $setting->getSettingName());

        $setting = $sequence->getSetTableForAdding();
        $this->assertSame($this->_add, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getReferenceSetTable());
    }

    public function testSeqWithEditAndAddSpecified()
    {
        // Add and Reference settings should be the same (Add);
        // all others should use Edit setting
        $sequence = new Application_Model_TableViewSequence(self::ADD_AND_EDIT);

        $setting = $sequence->getSetTableForAdding();
        $this->assertSame($this->_add, $setting->getSettingName());
        $this->assertEquals($setting, $sequence->getReferenceSetTable());

        $setting = $sequence->getSetTableForModifying();
        $this->assertSame($this->_edit, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
    }

    public function testSeqWithReferenceSpecified()
    {
        // All should use Reference setting
        $sequence =
                new Application_Model_TableViewSequence(self::REFERENCE_ONLY);

        $setting = $sequence->getReferenceSetTable();
        $this->assertSame($this->_reference, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
    }

    public function testSeqWithMainSettingSpecified()
    {
        // All should use Main setting
        $sequence = new Application_Model_TableViewSequence(self::MAIN_ONLY);

        $setting = $sequence->getSetTableForViewing();
        $this->assertSame($this->_main, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getReferenceSetTable());
    }

    public function testInitialActionIsSearch()
    {
        $sequence =
            new Application_Model_TableViewSequence(self::SEARCH_SPEC_ONLY);
        $this->assertSame('search', $sequence->getInitialAction());
    }

    public function testInitialActionIsDisplayAll()
    {
        $sequence =
            new Application_Model_TableViewSequence(self::MULT_SETTINGS);
        $this->assertSame('list-view', $sequence->getInitialAction());
    }

    public function testGetSetTableWhenNameIsNull()
    {
        $this->setExpectedException('Exception',
                                'set table for empty setting property name');
        $sequence = new Application_Model_TableViewSequence(self::BASIC_FILE);
        $setTable = $sequence->getSetTable(null);
    }

    public function testGetSetTableWhenNameIsEmpty()
    {
        $this->setExpectedException('Exception',
                                'set table for empty setting property name');
        $sequence = new Application_Model_TableViewSequence(self::BASIC_FILE);
        $setTable = $sequence->getSetTable("");
    }

    public function testGetSetTableForUnknownSettingName()
    {
        $this->setExpectedException('Exception', 'unknown setting property');
        $sequence = new Application_Model_TableViewSequence(self::BASIC_FILE);
        $setTable = $sequence->getSetTable('invalidSetting');
    }

    public function testGetSetTableWhenAlreadyFound()
    {
        $sequence =
            new Application_Model_TableViewSequence(self::SEARCH_SPEC_ONLY);
        $setTable = $sequence->getSetTable('setting');
        $setTable = $sequence->getSetTable('setting');
    }

}
