<?php
require_once 'TestConfiguration.php';

class models_RampIniReaderTest extends PHPUnit_Framework_TestCase
{
    protected $_iniReader;

    public function setUp()
    {
        $this->_iniReader = new Application_Model_RampIniReader();
    }

    public function testImportActSpecsFromFile()
    {
        $filename = 'tests/activityTesting/simpleTest.act';
        $importedSpecs =
                $this->_iniReader->importActivitySpecs($filename)->toArray();
        $this->assertSame(1, count($importedSpecs));
        $this->assertSame(7, count($importedSpecs['activity']));
        return $importedSpecs;
    }

    public function testImportActSpecsFromFileWithGivenActList()
    {
        $filename = 'tests/activityTesting/multipleLists.act';
        $actListName = $filename . '/actList2';
        $importedSpecs =
            $this->_iniReader->importActivitySpecs($actListName)->toArray();
        $this->assertSame(4, count($importedSpecs));
        $this->assertSame(7, count($importedSpecs['activity']));
        $this->assertSame(2, count($importedSpecs['actList2']));
        $this->assertSame(4, count($importedSpecs['actList2']['activity']));
        return $importedSpecs;
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Missing activities file"
     */
    public function testImportActSpecsFromNullFilename()
    {
        $importedSpecs = $this->_iniReader->importActivitySpecs(null);
        return $importedSpecs;
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Missing activities file"
     */
    public function testImportActSpecsFromBadFilename($filename)
    {
        $filename = 'nonExistentFile';
        $importedSpecs = $this->_iniReader->importActivitySpecs($filename);
        return $importedSpecs;
    }

    public function testGetSimpleFilename()
    {
        $filename = 'tests/activityTesting/simpleTest.act';
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($filename);
        $this->assertSame($filename, $retrievedFilename);
        return $retrievedFilename;
    }

    public function testActListInFilename()
    {
        $filename = 'tests/activityTesting/multipleLists.act';
        $actListName = $filename . '/actList2';
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($actListName);
        $this->assertSame($filename, $retrievedFilename);
        return $retrievedFilename;
    }

    public function testBadActListFilename()
    {
        $filename = 'nonExistentFile';
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($filename);
        $this->assertNull($retrievedFilename);
        return $retrievedFilename;
    }

    public function testImportSettingsFromFile()
    {
        $filename = 'tests/settingTesting/BasicTableSetting';
        $importedProps =
                $this->_iniReader->importSettings($filename)->toArray();
        $this->assertSame(5, count($importedProps));
        $this->assertSame('albums', $importedProps['tableName']);
        $this->assertSame(3, count($importedProps['field']));
        return $importedProps;
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Missing settings file"
     */
    public function testImportSettingsFromNullFilename()
    {
        $importedProps = $this->_iniReader->importSettings(null);
        return $importedProps;
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Missing settings file"
     */
    public function testImportSettingsFromBadFilename()
    {
        $filename = 'nonExistentSetting';
        $importedProps = $this->_iniReader->importSettings($filename);
        return $importedProps;
    }

}
