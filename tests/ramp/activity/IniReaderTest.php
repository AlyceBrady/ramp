<?php
require_once 'TestSettings.php';

class Ramp_Activity_Config_IniReaderTest extends PHPUnit_Framework_TestCase
{
    // Files containing various test cases.
    const SIMPLE_TEST = TestSettings::SIMPLE_ACT_LIST;
    const MULT_LISTS = TestSettings::MULT_ACT_LISTS;
    const NON_FILE = TestSettings::NON_FILE;

    protected $_iniReader;

    public function setUp()
    {
        $this->_iniReader = new Ramp_Activity_Config_IniReader();
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

    public function testGetSimpleFilenameWhenActDirIsNull()
    {
        // ramp.activitiesDirectory in smart_regressiontesting section of 
        // application.ini is currently null.
        $filename = self::SIMPLE_TEST;
        $retrievedFilename =
            $this->_iniReader->getActivityListFilename($filename);
        $this->assertSame($filename, $retrievedFilename);
        return $retrievedFilename;
    }

    // TODO: How do we test the case where we're getting an activities 
    // directory that is defined in configuration file?

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

}
