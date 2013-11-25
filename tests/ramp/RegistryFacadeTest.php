<?php
/*
 * NOTE: TODO: Many more functions have been added for which test cases
 * * have not yet been written.
 */
require_once 'TestSettings.php';

class Ramp_RegistryFacadeTest extends PHPUnit_Framework_TestCase
{
    // Files containing various test cases.
    const SIMPLE_TEST = TestSettings::SIMPLE_ACT_LIST;
    const MULT_LISTS = TestSettings::MULT_ACT_LISTS;
    const NON_FILE = TestSettings::NON_FILE;

    const BASIC_SETTING = TestSettings::BASIC_SETTINGS_FILE;

    protected $_configs;

    public function setUp()
    {
        $this->_configs = Ramp_RegistryFacade::getInstance();
    }

    // How do we run test that timeout works correctly?  (Have access 
    // for some time, then lose it?)  I've tested this manually with 
    // zero and non-zero timeouts.

    public function testSessionTimeoutNotDefined()
    {
        // TODO: How do we force reading in of ini file with no timeout?
        // Or have test ini with no timeout by default and various 
        // timeouts for various environments?  But how do we run tests 
        // against different environments?
        // $filename = self::SIMPLE_TEST;
        $timeout = $this->_configs->getSessionTimeout();
        // $this->assertNull($timeout);
        return $timeout;
    }

    public function testSessionTimeoutIsZero()
    {
        // TODO: How do we force reading in of ini file with timeout of 0?
        $timeout = $this->_configs->getSessionTimeout();
        // $this->assertSame("0", $timeout);
        return $timeout;
    }

    public function testSessionTimeoutIsNotZero()
    {
        // TODO: How do we force reading in of ini file with timeout
        // that is not 0?
        // $timeout = $this->_configs->getSessionTimeout();
        // $this->assertSame(0, $timeout);
        // return $timeout;
    }

    public function testMenuDirectoryNotDefined()
    {
        // TODO: How do we force reading in of ini file with no menu 
        // directory?
    }

    public function testDefaultMenuDefinedWFullPath()
    {
        // getDefaultMenu() && _buildMenuFilename (full path of existing file)
        // TODO: How do we force reading in of ini file with a default 
        // menu whose full path is defined?
    }

    public function testDefaultMenuWRelPath()
    {
        // getDefaultMenu() && _buildMenuFilename (relative path, good file)
        // TODO: How do we force reading in of ini file with a default 
        // menu whose path is relative to the provided menu directory?
    }

    public function testDefaultMenuWRelPathButNoMenuDir()
    {
        // getDefaultMenu() && _buildMenuFilename (relative path, no menu dir)
        // TODO: How do we force reading in of ini file with a default 
        // menu whose path is relative (but no menu directory defined)?
    }

    public function testDefaultMenuWRelPathButNoFile()
    {
        // getDefaultMenu() && _buildMenuFilename (good menu dir, but
        // menuFilename is relative path of nonexistent file)
        // TODO: How do we force reading in of ini file with a default 
        // menu whose path is relative but menu file does not exist?
    }

    public function testGoodRoleBasedMenu()
    {
        // getMenu() && _buildMenuFilename
        // TODO: How do we force reading in of ini file with a good
        // role-based menu?
    }

    public function testBadRoleBasedMenu()
    {
        // getMenu() && _buildMenuFilename
        // TODO: How do we force reading in of ini file with a bad
        // role-based menu?
        // Returns default menu
    }

    public function testNonexistentRoleBasedMenu()
    {
        // getMenu()
        // TODO: How do we force reading in of ini file with no
        // role-based menu for a given role? (or none at all?)
        // Returns default menu
    }

    public function testGetNonexistentActDir()
    {
        // TODO: How do we force reading in of ini file with no
        // activities directory defined?
        // getActivitiesDirectory returns settings directory
    }

    public function testGetActDir()
    {
        // TODO: How do we force reading in of ini file with an
        // activities directory defined?
        // getActivitiesDirectory returns activities directory
    }

}
