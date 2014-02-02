<?php
require_once 'TestConfiguration.php';
require_once 'TestSettings.php';

class models_TableViewSequenceTest extends PHPUnit_Framework_TestCase
{
    const NON_FILE = TestSettings::NON_FILE;
    const EMPTY_SEQ_SETTING = TestSettings::EMPTY_SETTING;
    const NO_SEQ = TestSettings::NO_SEQ;
    const BASIC_FILE = TestSettings::BASIC_SETTINGS_FILE;
    const MULT_SETTINGS = TestSettings::MULT_SETTINGS_FILE;
    const NO_SEQ_TBL_NAME = TestSettings::FILE_SHOWING_COLS_BY_DEFAULT;
    const MAIN_ONLY = TestSettings::MAIN_ONLY ;
    const ADD_AND_SEARCH_RES = TestSettings::ADD_AND_SEARCH_RES ;
    const ADD_AND_EDIT = TestSettings::ADD_AND_EDIT ;
    const SEARCH_RES_ONLY = TestSettings::SEARCH_RES_ONLY ;
    const SEARCH_SPEC_ONLY = TestSettings::SEARCH_SPEC_ONLY;
    const TABULAR_ONLY = TestSettings::TABULAR_ONLY;
    const SPLIT_VIEW_ONLY = TestSettings::TABULAR_ONLY;

    protected $_settingTests;
    protected $_main;
    protected $_add;
    protected $_edit;
    protected $_delete;
    protected $_searchSpec;
    protected $_searchRes;

    public function setUp()
    {
        // Reset database to known state
        TestConfiguration::setupDatabase();

        // Get various expected results
        $this->_settingTests = TestSettings::getInstance();
        $multSettings =
                    $this->_settingTests->getAllSettingsInMultSettingsFile();
        $this->_main = $multSettings['setting'];
        $this->_add = $multSettings['addSetting'];
        $this->_delete = $multSettings['deleteSetting'];
        $this->_edit = $multSettings['editSetting'];
        $this->_searchSpec = $multSettings['searchSpecSetting'];
        $this->_searchRes = $multSettings['searchResultsSetting'];
        $this->_tabular = $multSettings['tabularSetting'];
    }

    public function testInvalidSettingSequenceFile()
    {
        // Test constructing a sequence from an invalid file.
        $this->setExpectedException('Exception', 'Missing settings file');
        $sequence = new Ramp_Table_TableViewSequence(self::NON_FILE);
    }

    // _initSettingsUsedInSequence: no sequence, no setting
    public function testNoExplicitOrImpliedSequence()
    {
        $this->setExpectedException('Exception',
                                    'sequence or one table setting');
        $sequence =
            new Ramp_Table_TableViewSequence(self::EMPTY_SEQ_SETTING);
    }

    // _initSettingsUsedInSequence: misleading sequence prop, not setting
    public function testSequencePropertyHasNothingToDoWithSequence()
    {
        // No sequence or table setting (though one invalid property 
        //      called "sequence").
        //  NOTE: Not an error.  (Property is ignored.)
        $filename = TestSettings::MISLEADING_SEQ_PROP;
        $sequence = new Ramp_Table_TableViewSequence($filename);
    }

    // _initSettingsUsedInSequence: sequence is empty array; mult tables read
    public function testNoExplicitBadImpliedSequence()
    {
        $this->setExpectedException('Exception',
                                    'sequence or one table setting');
        $sequence = new Ramp_Table_TableViewSequence(self::NO_SEQ);
    }

    // _initSettingsUsedInSequence: misleading seq section; mult tables read
    public function testSequenceSectionHasNothingToDoWithSequence()
    {
        // No sequence, multiple table settings (one of which is 
        //      misleadingly called "sequence").
        $this->setExpectedException('Exception',
                                    'sequence or one table setting');
        $filename = TestSettings::MISLEADING_SEQ_SEC;
        $sequence = new Ramp_Table_TableViewSequence($filename);
    }

    // _initSettingsUsedInSequence: sequence is empty array; one table read
    public function testSimpleImpliedSequence()
    {
        $sequence = new Ramp_Table_TableViewSequence(self::BASIC_FILE);

        $expectedSetting = $this->_settingTests->getBasicSetting();
        $this->assertSame($expectedSetting['tableName'],
                          $sequence->getSeqLevelTableName());

        $setting = $sequence->getSetTableForViewing();
        $this->assertSame(self::BASIC_FILE, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForDeleting());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithAllSettingsExplicitlySpecified()
    {
        $sequence =
            new Ramp_Table_TableViewSequence(self::MULT_SETTINGS);

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
    }

    public function testSeqWithNoTopLevelTableName()
    {
        $sequence =
            new Ramp_Table_TableViewSequence(self::NO_SEQ_TBL_NAME);
        $this->assertNull($sequence->getSeqLevelTableName());
    }

    public function testSeqWithOnlySearchResultSettingSpecified()
    {
        // All should use SearchResult setting
        $sequence =
            new Ramp_Table_TableViewSequence(self::SEARCH_RES_ONLY);

        $setting = $sequence->getSetTableForSearchResults();
        $this->assertSame($this->_searchRes, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithOnlySearchSpecSettingSpecified()
    {
        // All should use SearchSpec, including SearchResult setting
        $sequence =
            new Ramp_Table_TableViewSequence(self::SEARCH_SPEC_ONLY);

        $setting = $sequence->getSetTableForSearching();
        $this->assertSame($this->_searchSpec, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithOnlyTabularSettingSpecified()
    {
        // All should use Tabular.
        $sequence =
            new Ramp_Table_TableViewSequence(self::TABULAR_ONLY);

        $setting = $sequence->getSetTableForTabularView();
        $this->assertSame($this->_tabular, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithOnlySplitViewSettingSpecified()
    {
        // All should use Split View.
        $sequence =
            new Ramp_Table_TableViewSequence(self::SPLIT_VIEW_ONLY);

        $setting = $sequence->getSetTableForSplitView();
        $this->assertSame($this->_tabular, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithAddAndSearchResultSpecified()
    {
        // SearchResult should be as specified;
        // all others should use Add setting
        $sequence =
            new Ramp_Table_TableViewSequence(self::ADD_AND_SEARCH_RES);

        $setting = $sequence->getSetTableForSearchResults();
        $this->assertSame($this->_searchRes, $setting->getSettingName());

        $setting = $sequence->getSetTableForAdding();
        $this->assertSame($this->_add, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithEditAndAddSpecified()
    {
        // All settings except Add should use Edit setting.
        $sequence = new Ramp_Table_TableViewSequence(self::ADD_AND_EDIT);

        $setting = $sequence->getSetTableForAdding();
        $this->assertSame($this->_add, $setting->getSettingName());

        $setting = $sequence->getSetTableForModifying();
        $this->assertSame($this->_edit, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForViewing());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testSeqWithMainSettingSpecified()
    {
        // All should use Main setting
        $sequence = new Ramp_Table_TableViewSequence(self::MAIN_ONLY);

        $setting = $sequence->getSetTableForViewing();
        $this->assertSame($this->_main, $setting->getSettingName());

        $this->assertEquals($setting, $sequence->getSetTableForModifying());
        $this->assertEquals($setting, $sequence->getSetTableForAdding());
        $this->assertEquals($setting, $sequence->getSetTableForSearching());
        $this->assertEquals($setting, $sequence->getSetTableForSearchResults());
        $this->assertEquals($setting, $sequence->getSetTableForTabularView());
        $this->assertEquals($setting, $sequence->getSetTableForSplitView());
    }

    public function testInitialActionIsSearch()
    {
        $sequence =
            new Ramp_Table_TableViewSequence(self::SEARCH_SPEC_ONLY);
        $this->assertSame('search', $sequence->getInitialAction());
    }

    public function testInitialActionIsDisplayAll()
    {
        $sequence =
            new Ramp_Table_TableViewSequence(self::MULT_SETTINGS);
        $this->assertSame('list-view', $sequence->getInitialAction());
    }

    /*
    public function testGetSetTableWhenNameIsNull()
    {
        $this->setExpectedException('Exception',
                                'set table for empty setting property:');
        $sequence = new Ramp_Table_TableViewSequence(self::BASIC_FILE);
        $setTable = $sequence->_getSetTable(null);
    }

    public function testGetSetTableWhenNameIsEmpty()
    {
        $this->setExpectedException('Exception',
                                'set table for empty setting property:');
        $sequence = new Ramp_Table_TableViewSequence(self::BASIC_FILE);
        $setTable = $sequence->_getSetTable("");
    }

    public function testGetSetTableForUnknownSettingName()
    {
        $this->setExpectedException('Exception', 'unknown setting property');
        $sequence = new Ramp_Table_TableViewSequence(self::BASIC_FILE);
        $setTable = $sequence->_getSetTable('invalidSetting');
    }
     */

    public function testGetSetTableWhenAlreadyFound()
    {
        $sequence =
            new Ramp_Table_TableViewSequence(self::SEARCH_SPEC_ONLY);
        $setTable = $sequence->getSetTableForViewing();
    }

}
