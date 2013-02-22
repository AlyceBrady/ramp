<?php
require_once 'TestConfiguration.php';

class models_ActivityGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $_simpleTest = 'tests/activityTesting/simpleTest.act';
    protected $_multLists = 'tests/activityTesting/multipleLists.act';
    protected $_internalActList =
                        'tests/activityTesting/multipleLists.act/actList2';
    protected $_singleUnnamedAct = 'tests/activityTesting/noList.act';
    protected $_matchingActList =
                        'tests/activityTesting/mainActListInSection.act';
    protected $_fileWithDuplLists = 'tests/activityTesting/duplLists.act';
    protected $_duplList = 'tests/activityTesting/duplLists.act/actList2';

    protected $_nonExistentFile = 'nonExistentActListFile.act';
    protected $_noMatchingActList =
                        'tests/activityTesting/badMultipleLists.act';
    protected $_badListInGoodFile =
                        'tests/activityTesting/multipleLists.act/actList1';
    protected $_goodListInBadFile =
                        'tests/activityTesting/badMultipleLists.act/actList2';

    public function setUp()
    {
        // Reset database to known state
        // TestConfiguration::setupDatabase();
    }

    public function testGetActListFromValidFile()
    {
        // Test getting an activity list from a valid file.
        // Activities are fully defined in place.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_simpleTest);
        $this->assertSame(7, count($actList));
    }

    public function testGetActListAlreadyReadIn()
    {
        // Read in activity list file with multiple activity lists, then 
        // get one that was already read in.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_multLists);
        $actList2 = $gateway->getActivityList($this->_internalActList);
        $this->assertSame(7, count($actList));
        $this->assertSame(4, count($actList2));
    }

    public function testReadFileWithMatchingActList()
    {
        // Test getting an activity list from a file that does not 
        // have a top-level list, but has a section matching the file name.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_matchingActList);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "Missing activities file"
     */
    public function testGetActListFromNonExistentFile()
    {
        // Test (not) getting an activity list from an invalid file.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_nonExistentFile);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "does not contain a top-level activity"
     */
    public function testReadFileWithNoMatchingActList()
    {
        // Test (not) getting an activity list from a file that does not 
        // have a top-level list, nor a section matching the file name.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_noMatchingActList);
    }

    /**
     * @expectedException           Exception
     * @expectedExceptionMessage    "does not contain a section"
     */
    public function testGetBadActListFromFileWithMultLists()
    {
        // Test (not) getting an activity list from a file that has other
        // activity lists but not the one being requested.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_badListInGoodFile);
    }

    public function testGetGoodActListFromFileWithNoMatchingList()
    {
        // Test getting an activity list from a file that has it 
        // (although it doesn't have a list matching the file name).
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_goodListInBadFile);
        $this->assertSame(5, count($actList));
    }

    public function testReadFileWithDuplicateActLists()
    {
        // Test getting the main activity list from a file with 
        // duplicate sections.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_fileWithDuplLists);
        $this->assertSame(8, count($actList));
    }

    public function testGetDuplList()
    {
        // Test getting the duplicated list from a file with duplicate 
        // sections (the later section will override the earlier one).
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_duplList);
        $this->assertSame(7, count($actList));
    }

    public function testGetSingleUnnamedActivity()
    {
        // Test getting unnamed activity that is only contents of file.
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_singleUnnamedAct);
        $this->assertSame(1, count($actList));
        $this->assertTrue($actList[0]->isSetting());
    }

    public function testGetNullTitleForActList()
    {
        // Test getting the title from an activity list that doesn't have one.
        $gateway = new Application_Model_ActivityGateway();
        $this->assertNull($gateway->getActivityListTitle($this->_simpleTest));
    }

    public function testGetNonNullTitleForActList()
    {
        // Test getting a valid title from an activity list.
        $gateway = new Application_Model_ActivityGateway();
        $title = $gateway->getActivityListTitle($this->_internalActList);
        $this->assertNotNull($title);
    }

}
