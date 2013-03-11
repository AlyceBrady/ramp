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
        $this->_iniReader = new Application_Model_RampIniReader();
    }

    public function testImportActSpecsFromFile()
    {
        $filename = self::SIMPLE_TEST;
        $importedSpecs =
                $this->_iniReader->importActivitySpecs($filename)->toArray();
        $this->assertSame(1, count($importedSpecs));
        $this->assertSame(7, count($importedSpecs['activity']));
        return $importedSpecs;
    }

    public function testImportActSpecsFromFileWithGivenActList()
    {
        $filename = self::MULT_LISTS;
        $actListName = $filename . '/actList2';
        $importedSpecs =
            $this->_iniReader->importActivitySpecs($actListName)->toArray();
        $this->assertSame(4, count($importedSpecs));
        $this->assertSame(7, count($importedSpecs['activity']));
        $this->assertSame(2, count($importedSpecs['actList2']));
        $this->assertSame(4, count($importedSpecs['actList2']['activity']));
        return $importedSpecs;
    }

    public function testImportActSpecsFromNullFilename()
    {
        $this->setExpectedException('Exception', 'Missing activities file');
        $importedSpecs = $this->_iniReader->importActivitySpecs(null);
        return $importedSpecs;
    }

    public function testImportActSpecsFromBadFilename()
    {
        $this->setExpectedException('Exception', 'Missing activities file');
        $filename = self::NON_FILE;
        $importedSpecs = $this->_iniReader->importActivitySpecs($filename);
        return $importedSpecs;
    }

    public function testGetSimpleFilename()
    {
        $filename = self::SIMPLE_TEST;
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($filename);
        $this->assertSame($filename, $retrievedFilename);
        return $retrievedFilename;
    }

    public function testActListInFilename()
    {
        $filename = self::MULT_LISTS;
        $actListName = $filename . '/actList2';
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($actListName);
        $this->assertSame($filename, $retrievedFilename);
        return $retrievedFilename;
    }

    public function testBadActListFilename()
    {
        $filename = self::NON_FILE;
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($filename);
        $this->assertNull($retrievedFilename);
        return $retrievedFilename;
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
