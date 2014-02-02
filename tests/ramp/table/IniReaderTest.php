<?php
require_once 'TestSettings.php';

class models_RampIniReaderTest extends PHPUnit_Framework_TestCase
{
    // Files containing various test cases.
    const SIMPLE_TEST = TestSettings::SIMPLE_ACT_LIST;
    const MULT_LISTS = TestSettings::MULT_ACT_LISTS;
    const NON_FILE = TestSettings::NON_FILE;

    const BASIC_SETTING = TestSettings::BASIC_SETTINGS_FILE;

    protected $_iniReader;

    public function setUp()
    {
        $this->_iniReader = new Ramp_Table_Config_IniReader();
    }

    public function testImportSettingsFromFile()
    {
        $filename = self::BASIC_SETTING;
        $importedProps =
                $this->_iniReader->importSettings($filename)->toArray();
        $this->assertSame(5, count($importedProps));
        $this->assertSame('albums', $importedProps['tableName']);
        $this->assertSame(3, count($importedProps['field']));
        return $importedProps;
    }

    public function testImportSettingsFromNullFilename()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        $importedProps = $this->_iniReader->importSettings(null);
        return $importedProps;
    }

    public function testImportSettingsFromBadFilename()
    {
        $this->setExpectedException('Exception', 'Missing settings file');
        $filename = self::NON_FILE;
        $importedProps = $this->_iniReader->importSettings($filename);
        return $importedProps;
    }

}
