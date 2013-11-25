<?php
require_once 'TestConfiguration.php';

class Ramp_Activity_GatewayTest extends PHPUnit_Framework_TestCase
{
    const SIMPLE_TEST = TestSettings::SIMPLE_ACT_LIST;
    const MULT_LISTS = TestSettings::MULT_ACT_LISTS;
    const NON_FILE = TestSettings::NON_FILE;
    const INTERNAL_ACT_LIST = TestSettings::INTERNAL_ACT_LIST;
    const UNNAMED_ACT_LIST = TestSettings::UNNAMED_ACT_LIST;
    const MATCHING_ACT_LIST = TestSettings::MATCHING_ACT_LIST;
    const DUPL_ACT_LISTS = TestSettings::DUPL_ACT_LISTS;
    const DUPLICATED_LIST = TestSettings::DUPLICATED_LIST;

    const BAD_ACT_LISTS = TestSettings::BAD_ACT_LISTS;
    const BAD_AL_IN_GOOD_ALFILE = TestSettings::BAD_AL_IN_GOOD_ALFILE;
    const GOOD_AL_IN_BAD_ALFILE = TestSettings::GOOD_AL_IN_BAD_ALFILE;

    public function setUp()
    {
        // Reset database to known state
        // TestConfiguration::setupDatabase();
    }

    public function testGetActListFromValidFile()
    {
        // Test getting an activity list from a valid file.
        // Activities are fully defined in place.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::SIMPLE_TEST);
        $this->assertSame(7, count($actList));
    }

    public function testGetActListAlreadyReadIn()
    {
        // Read in activity list file with multiple activity lists, then 
        // get one that was already read in.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::MULT_LISTS);
        $actList2 = $gateway->getActivityList(self::INTERNAL_ACT_LIST);
        $this->assertSame(7, count($actList));
        $this->assertSame(4, count($actList2));
    }

    public function testReadFileWithMatchingActList()
    {
        // Test getting an activity list from a file that does not 
        // have a top-level list, but has a section matching the file name.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::MATCHING_ACT_LIST);
    }

    public function testGetActListFromNonExistentFile()
    {
        $this->setExpectedException('Exception', 'Missing activities file');
        // Test (not) getting an activity list from an invalid file.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::NON_FILE);
    }

    public function testReadFileWithNoMatchingActList()
    {
        $this->setExpectedException('Exception',
                                    'does not contain a top-level activity');
        // Test (not) getting an activity list from a file that does not 
        // have a top-level list, nor a section matching the file name.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::BAD_ACT_LISTS);
    }

    public function testGetBadActListFromFileWithMultLists()
    {
        $this->setExpectedException('Exception',
                                    'does not contain a section');
        // Test (not) getting an activity list from a file that has other
        // activity lists but not the one being requested.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::BAD_AL_IN_GOOD_ALFILE);
    }

    public function testGetGoodActListFromFileWithNoMatchingList()
    {
        // Test getting an activity list from a file that has it 
        // (although it doesn't have a list matching the file name).
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::GOOD_AL_IN_BAD_ALFILE);
        $this->assertSame(5, count($actList));
    }

    public function testReadFileWithDuplicateActLists()
    {
        // Test getting the main activity list from a file with 
        // duplicate sections.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::DUPL_ACT_LISTS);
        $this->assertSame(8, count($actList));
    }

    public function testGetDuplList()
    {
        // Test getting the duplicated list from a file with duplicate 
        // sections (the later section will override the earlier one).
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::DUPLICATED_LIST);
        $this->assertSame(7, count($actList));
    }

    public function testGetSingleUnnamedActivity()
    {
        // Test getting unnamed activity that is only contents of file.
        $gateway = new Ramp_Activity_Gateway();
        $actList = $gateway->getActivityList(self::UNNAMED_ACT_LIST);
        $this->assertSame(1, count($actList));
        $this->assertTrue($actList[0]->isSetting());
    }

    public function testGetNullTitleForActList()
    {
        // Test getting the title from an activity list that doesn't have one.
        $gateway = new Ramp_Activity_Gateway();
        $this->assertNull($gateway->getActivityListTitle(self::SIMPLE_TEST));
    }

    public function testGetNonNullTitleForActList()
    {
        // Test getting a valid title from an activity list.
        $gateway = new Ramp_Activity_Gateway();
        $title = $gateway->getActivityListTitle(self::INTERNAL_ACT_LIST);
        $this->assertNotNull($title);
    }

}
