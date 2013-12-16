<?php

class TestSettings
{
    private static $_config = null;

    // Files containing various test cases.
    const NON_FILE = 'nonExistentFile';

    // Activity List test cases...
    const SIMPLE_ACT_LIST = 'actListTests/simpleTest.act';
    const MULT_ACT_LISTS = 'actListTests/multipleLists.act';
    const INTERNAL_ACT_LIST = 'actListTests/multipleLists.act/actList2';
    const UNNAMED_ACT_LIST = 'actListTests/noList.act';
    const MATCHING_ACT_LIST = 'actListTests/mainActListInSection.act';
    const DUPL_ACT_LISTS = 'actListTests/duplLists.act';
    const DUPLICATED_LIST = 'actListTests/duplLists.act/actList2';
    const BAD_ACT_LISTS = 'actListTests/badMultipleLists.act';
    const BAD_AL_IN_GOOD_ALFILE = 'actListTests/multipleLists.act/actList1';
    const GOOD_AL_IN_BAD_ALFILE =
                            'actListTests/badMultipleLists.act/actList2';

    // Table Setting test cases...
    const BASIC_SETTINGS_FILE = 'BasicTableSetting';
    const BASIC_2_SETTINGS_FILE = 'BasicVariantTableSetting';
    const MULT_SETTINGS_FILE = 'MultipleValidSettings';
    const FILE_SHOWING_COLS_BY_DEFAULT = 'ShowColsByDefault';
    const FILE_WITH_EXTERNAL_INIT = 'settingTests/InitTesting';
    const FILE_WITH_EXTERNAL_FILES = 'settingTests/MultExtRefTypes';
    const EXT_REF_TARGET = 'settingTests/Users';
    const NO_TABLE_PROPS_SETTINGS_FILE = 'sectionWNoTableProps';
    const NO_TABLE_SETTINGS_FILE = 'noDbTable';
    const NO_TABLE_SETTINGS_FILE2 = 'noDbTableInSubSetting';
    const BAD_MULT_SETTINGS_FILE = 'multSettingsError';
    const BAD_TOP_LEVEL_PROP = 'badTopLevelProperty';
    const FILE_W_EXTRA_SEQUENCE = 'extSettingsWithAddSequence';
    const FILE_W_INVAL_MULT_SEQ = 'multSeqError';
    const MISLEADING_SEQ_SEC = 'misleadingSeqSection';
    const MISLEADING_SEQ_PROP = 'misleadingSeqProperty';

    // Sequence test cases...
    const EMPTY_SETTING = 'sequenceTests/noNothing';
    const NO_SEQ = 'sequenceTests/noExplicitOrImplicitSequence';
    const MAIN_ONLY = 'sequenceTests/mainSeqOnly';
    const ADD_AND_SEARCH_RES = 'sequenceTests/addAndSearchResSeqs';
    const ADD_AND_EDIT = 'sequenceTests/editAndAddSeqs';
    const SEARCH_RES_ONLY = 'sequenceTests/searchResSeqOnly';
    const SEARCH_SPEC_ONLY = 'sequenceTests/searchSpecSeqOnly';
    const TABULAR_ONLY = 'sequenceTests/tabularSeqOnly';
    const SPLIT_VIEW_ONLY = 'sequenceTests/splitViewOnly';

    private static $_basicTableSetting;
    private static $_variantBasicTableSetting;
    private static $_multSettingsTopLevel;
    private static $_sequenceSettings;
    private static $_fullSettingSet;
    private static $_settingNames;
    private static $_dataSourceTables;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if ( self::$_config === null )
        {
            self::init();
            self::$_config = new TestSettings();
        }

        return self::$_config;
    }

    protected static function init()
    {
        self::$_basicTableSetting = array(
            'tableName' => 'albums',
            'tableTitle' => 'Albums',
            'tableDescription' => 'A table of albums and artists',
            'tableFootnote' => 'A footnote about this table...',
            'field' => array(
                'id' => array (
                    'label' => 'id',
                    'hide' => '1'),
                'artist' => array ('label' => 'Artist'),
                'title' => array('label' => 'Album Title'))
            );

        self::$_variantBasicTableSetting = array(
            'tableName' => 'albums_variant',
            'tableTitle' => 'Albums',
            'tableDescription' => 'A variant table of albums and artists',
            'tableShowColsByDefault' => '0',
            'field' => array(
                'id' => array (
                    'label' => 'id',
                    'hide' => ''),
                'artist' => array (
                    'label' => 'Artist',
                    'footnote' => 'Extra field information',
                    'hide' => '0'),
                'title' => array('label' => 'Album Title')
            )
        );

        self::$_multSettingsTopLevel = array(
            'tableName' => 'ramp_test_addresses'
        );

        self::$_sequenceSettings = array(
            'initAction' => 'displayAll',
            'setting' => 'DetailedView',
            'addSetting' => 'AddView',
            'editSetting' => 'ModifyingView',
            'deleteSetting' => 'ModifyingView',
            'searchSpecSetting' => 'DetailedView',
            'searchResultsSetting' => self::BASIC_SETTINGS_FILE,
            'tabularSetting' => self::BASIC_SETTINGS_FILE,
            'splitViewSetting' => self::BASIC_SETTINGS_FILE,
        );

        self::$_fullSettingSet = array(
            'initAction' => 'displayAll',
            'setting' => 'DetailedView',
            'addSetting' => 'AddView',
            'editSetting' => 'ModifyingView',
            'searchSpecSetting' => 'DetailedView',
            'searchResultsSetting' => self::BASIC_SETTINGS_FILE,
            'tabularSetting' => self::BASIC_SETTINGS_FILE,
        );

        self::$_settingNames = array('SharedProperties',
                                     'DetailedView', 'ModifyingView',
                                     'AddView', 'TableSetting3',
                                     self::MULT_SETTINGS_FILE);

        self::$_dataSourceTables = array('Terms', 'Person', 'ModuleOfferings',
                                      'Modules');

    }

    function getBasicSetting()
    {
        return self::$_basicTableSetting;
    }

    function getVariantBasicSetting()
    {
        return self::$_variantBasicTableSetting;
    }

    function getSettingsInMultSettingsFile()
    {
        return self::$_settingNames;
    }

    function getTopLevelSettingsInMultSettingsFile()
    {
        return self::$_multSettingsTopLevel;
    }

    function getSeqSettingsInMultSettingsFile()
    {
        return self::$_sequenceSettings;
    }

    function getAllSettingsInMultSettingsFile()
    {
        return self::$_fullSettingSet;
    }

    function getSearchResultsSettingTableName()
    {
        return self::$_basicTableSetting['tableName'];
    }

    function getDataSourceTables()
    {
        return self::$_dataSourceTables;
    }

}
