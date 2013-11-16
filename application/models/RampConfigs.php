<?php

/**
 * RAMP: Records and Activity Management Program
 *
 * LICENSE
 *
 * This source file is subject to the BSD-2-Clause license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.cs.kzoo.edu/ramp/LICENSE.txt
 *
 * @category   Ramp
 * @package    Ramp_Model
 * @copyright  Copyright (c) 2013 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class Application_Model_RampConfigs
{
    const CONFIG_SETTINGS = "rampConfigSettings";

    const AUTH_TYPE       = "rampAuthenticationType";
    const INTERNAL_AUTH   = "internal";

    const MENU_LIST       = "roleBasedMenus";
    const DEFAULT_MENU    = "menuFilename";

    const ACTIVITIES_DIRECTORY = "rampActivitiesDirectory";
    const SETTINGS_DIRECTORY   = "rampSettingsDirectory";

    protected static $_singleton = null;

    protected $_configs;   // configuration properties read in

    protected $_activitiesDirectory;
    protected $_settingsDirectory;


    /**
     * Gets the singleton instance of the RampConfigs class.
     */
    public static function getInstance()
    {
        self::$_singleton = self::$_singleton ? :
                            new Application_Model_RampConfigs();
        return self::$_singleton;
    }

    /**
     * Class constructor
     *
     * Creates a singleton object representing the RAMP configuration 
     * properties.
     */
    protected function __construct()
    {
        $this->_configs =
            Zend_Registry::isRegistered(self::CONFIG_SETTINGS) ?
                Zend_Registry::get(self::CONFIG_SETTINGS) :
                array();
        $this->_activitiesDirectory =
            Zend_Registry::isRegistered(self::ACTIVITIES_DIRECTORY) ?
                Zend_Registry::get(self::ACTIVITIES_DIRECTORY) :
                null;
        $this->_settingsDirectory =
            Zend_Registry::isRegistered(self::SETTINGS_DIRECTORY) ?
                Zend_Registry::get(self::SETTINGS_DIRECTORY) :
                null;
    }

    /**
     * Checks whether the Ramp application is using internal authentication.
     *
     * UNDER CONSTRUCTION
    public function usingInternalAuthentication()
    {
    }
     */

    /**
     * Gets the appropriate menu for the given role (or the default menu 
     * if no role-specific menu has been defined for the given role).
     *
     * @param $role  the user's role
     */
    public function getMenu($role)
    {
        return ( isset($this->_configs[self::MENU_LIST]) &&
                 isset($this->_configs[self::MENU_LIST][$role]) )
                    ? $this->_configs[self::MENU_LIST][$role]
                    : $this->getDefaultMenu();
    }

    /**
     * Gets the default menu.
     */
    public function getDefaultMenu()
    {
        return isset($this->_configs[self::DEFAULT_MENU])
                    ? $this->_configs[self::DEFAULT_MENU]
                    : null;
    }

    /**
     * Gets the directory being used for activity specification files.
     * If there isn't one defined, use the settings directory.
     *
     * @return string   directory path
     */
    public function getActivitiesDirectory()
    {
        return $this->_activitiesDirectory ? : self::getSettingsDirectory();
    }

    /**
     * Gets the directory being used for table sequence/setting files.
     *
     * @return string   directory path
     */
    public function getSettingsDirectory()
    {
        // Get the settings directory from Zend_Registry.
        return $this->_settingsDirectory;
    }

}

