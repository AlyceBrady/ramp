<?php

class TestSettings
{
    private static $_config = null;

    const BASIC_SETTINGS_FILE = 'tests/settingTesting/BasicTableSetting';
    const BASIC_2_SETTINGS_FILE =
                            'tests/settingTesting/BasicVariantTableSetting';
    const MULT_SETTINGS_FILE = 'tests/settingTesting/MultipleValidSettings';
    const NO_TABLE_SETTINGS_FILE = 'tests/settingTesting/noDbTable';
    const FILE_W_EXTRA_SEQUENCE =
                            'tests/settingTesting/extSettingsWithAddSequence';
    const FILE_W_INVAL_MULT_SEQ = 'tests/settingTesting/multSeqError';

    private static $_basicTableSetting;
    private static $_variantBasicTableSetting;
    private static $_multSettingsTopLevel;
    private static $_sequenceSettings;
    private static $_settingNames;

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
            'tableName' => 'ramp_test_addresses',
            'sequence' => array()
        );

        self::$_sequenceSettings = array(
            'initAction' => 'displayAll',
            'setting' => 'DetailedView',
            'addSetting' => 'ModifyingView',
            'editSetting' => 'ModifyingView',
            'searchResultsSetting' => self::BASIC_SETTINGS_FILE
        );

        self::$_settingNames = array('DetailedView', 'ModifyingView',
                                     'TableSetting3', self::MULT_SETTINGS_FILE);
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

    function getSearchResultsSettingTableName()
    {
        return self::$_basicTableSetting['tableName'];
    }

}
