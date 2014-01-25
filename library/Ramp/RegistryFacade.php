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
 * @package    Ramp
 * @copyright  Copyright (c) 2013 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class Ramp_RegistryFacade
{
    const CONFIG_SETTINGS   = "rampConfigSettings";

    const LOGFILE_PATH      = "logPath";

    const AUTH_TYPE         = "authenticationType";
    const INTERNAL_AUTH     = "internal";

    const SESSION_TIMEOUT   = "sessionTimeout";

    const ACL_ROLES         = "aclNonGuestRole";
    const ACL_RESOURCES     = "aclResources";
    const ACL_RULES         = "aclRules";

    const BASE_MENU_DIR     = "menuDirectory";
    const MENU_LIST         = "roleBasedMenus";
    const DEFAULT_MENU      = "menuFilename";

    const ACTIVITIES_ROOT   = "activitiesDirectory";
    const SETTINGS_ROOT     = "settingsDirectory";
    const SETTINGS_SUFFIX   = "settingsSuffix";
    const DOCUMENT_ROOT     = "documentRoot";
    const INIT_ACT_LIST     = "roleBasedInitActivities";
    const DEF_INIT_ACT      = "initialActivity";

    const TITLE             = "title";
    const SUBTITLE          = "subtitle";
    const SHORT_NAME        = "applicationShortName";
    const FOOTER            = "footer";
    const ICON              = "icon";
    const CSS               = "css";

    protected static $_singleton = null;

    protected $_configs;   // configuration properties read in



    // STATIC FUNCTIONS

    /**
     * Gets the singleton instance of the RegistryFacade class.
     */
    public static function getInstance()
    {
        self::$_singleton = self::$_singleton ? :
                            new Ramp_RegistryFacade();
        return self::$_singleton;
    }


    // CONSTRUCTOR AND INSTANCE FUNCTIONS

    /**
     * Class constructor
     *
     * Creates a singleton object representing the RAMP configuration 
     * properties in the Zend_Registry.
     */
    protected function __construct()
    {
        $this->_configs =
            Zend_Registry::isRegistered(self::CONFIG_SETTINGS) ?
                Zend_Registry::get(self::CONFIG_SETTINGS) :
                array();
    }

    /**
     * Gets the logfile path (if any).
     */
    public function getLogfilePath()
    {
        return isset($this->_configs[self::LOGFILE_PATH])
            ? $this->_configs[self::LOGFILE_PATH] : null;
    }

    /**
     * Gets the authentication type.  (If no authentication type has 
     * been set in the application.ini file, the default is to use 
     * internal authentication.)
     */
    public function getAuthenticationType()
    {
        return isset($this->_configs[self::AUTH_TYPE])
            ? $this->_configs[self::AUTH_TYPE] : self::INTERNAL_AUTH;
    }

    /**
     * Checks whether the Ramp application is using internal authentication.
     */
    public function usingInternalAuthentication()
    {
        return $this->getAuthenticationType() == self::INTERNAL_AUTH;
    }

    /**
     * Gets the session timeout value.
     */
    public function getSessionTimeout()
    {
        return isset($this->_configs[self::SESSION_TIMEOUT])
            ? $this->_configs[self::SESSION_TIMEOUT] : 0;
    }

    /**
     * Gets the authorization roles used in Access Control Lists (ACLs).
     */
    public function getAclRoles()
    {
        return isset($this->_configs[self::ACL_ROLES])
            ? $this->_configs[self::ACL_ROLES] : null;
    }

    /**
     * Gets the resources whose use is authorized with Access Control
     * Lists (ACLs).
     */
    public function getAclResources()
    {
        return isset($this->_configs[self::ACL_RESOURCES])
            ? $this->_configs[self::ACL_RESOURCES] : null;
    }

    /**
     * Gets the rules that implement the RAMP Access Control Lists (ACLs).
     */
    public function getAclRules()
    {
        return isset($this->_configs[self::ACL_RULES])
            ? $this->_configs[self::ACL_RULES] : array();
    }

    /**
     * Gets an array containing settings that affect the look and feel 
     * of the application.  The array keys are:
     *      'title'
     *      'subtitle'
     *      'shortName'
     *      'footer'
     *      'icon'
     *      'rampStyleSheet'
     */
    public function getLookAndFeel()
    {
        $lookAndFeel = array();
        $lookAndFeel['title'] = isset($this->_configs[self::TITLE])
            ? $this->_configs[self::TITLE] : null;
        $lookAndFeel['subtitle'] = isset($this->_configs[self::SUBTITLE])
            ? $this->_configs[self::SUBTITLE] : null;
        $lookAndFeel['shortName'] = isset($this->_configs[self::SHORT_NAME])
            ? $this->_configs[self::SHORT_NAME] : null;
        $lookAndFeel['footer'] = isset($this->_configs[self::FOOTER])
            ? $this->_configs[self::FOOTER] : null;
        $lookAndFeel['icon'] = isset($this->_configs[self::ICON])
            ? $this->_configs[self::ICON] : null;
        $lookAndFeel['rampStyleSheet'] = isset($this->_configs[self::CSS])
            ? $this->_configs[self::CSS] : null;
        return $lookAndFeel;
    }

    /**
     * Gets the base directory for menus.
     * If there isn't one defined, uses the settings directory.
     */
    public function getMenuDirectory()
    {
        $path = $this->_configs[self::BASE_MENU_DIR];
        return empty($path) ? self::getSettingsDirectory() : $path;
    }

    /**
     * Gets the default menu.
     */
    public function getDefaultMenu()
    {
        if ( ! empty($this->_configs[self::DEFAULT_MENU]) )
        {
            $defaultMenu = $this->_configs[self::DEFAULT_MENU];
            return $this->_buildFilename($defaultMenu,
                                         $this->getMenuDirectory());
        }
        return null;
    }

    /**
     * Gets the appropriate menu for the given role (or the default menu 
     * if no role-specific menu has been defined for the given role).
     *
     * @param $role  the user's role
     */
    public function getMenu($role)
    {
        if ( ! empty($this->_configs[self::MENU_LIST]) &&
             ! empty($this->_configs[self::MENU_LIST][$role]) )
        {
            $menu = $this->_configs[self::MENU_LIST][$role];
            $menu = $this->_buildFilename($menu, $this->getMenuDirectory());
            if ( $menu != null )
            {
                return $menu;
            }
        }
        return $this->getDefaultMenu();
    }

    /**
     * Gets the base directory being used for activity specification
     * files.  If there isn't one defined, uses the settings directory.
     *
     * @return string   directory path
     */
    public function getActivitiesDirectory()
    {
        $path = isset($this->_configs[self::ACTIVITIES_ROOT])
            ? $this->_configs[self::ACTIVITIES_ROOT] : null;;
        return empty($path) ? self::getSettingsDirectory() : $path;
    }

    /**
     * Gets the base directory being used for table sequence/setting files.
     *
     * @return string   directory path
     */
    public function getSettingsDirectory()
    {
        // Get the settings directory from Zend_Registry.
        $path = isset($this->_configs[self::SETTINGS_ROOT])
            ? $this->_configs[self::SETTINGS_ROOT] : null;;

        // If no directory specified, come up with a default instead.
        if ( empty($path) )
        {
            $baseDir = Zend_Controller_Front::getInstance()->getBaseUrl();
            $path = $baseDir . DIRECTORY_SEPARATOR . 'settings';
        }

        return $path;
    }

    /**
     * Gets the suffix being used for table sequence/setting files.
     */
    public function getSettingsSuffix()
    {
        return isset($this->_configs[self::SETTINGS_SUFFIX])
            ? $this->_configs[self::SETTINGS_SUFFIX] : null;
    }

    /**
     * Gets the base directory being used for document files.
     * If there isn't one defined, uses the settings directory.
     *
     * @return string   directory path
     */
    public function getDocumentRoot()
    {
        $path = $this->_configs[self::DOCUMENT_ROOT];
        return empty($path) ? self::getSettingsDirectory() : $path;
    }

    /**
     * Gets the default initial activity.
     */
    public function getDefaultInitialActivity()
    {
        return isset($this->_configs[self::DEF_INIT_ACT])
            ? $this->_configs[self::DEF_INIT_ACT] : null;
    }

    /**
     * Gets the appropriate initial activity for the given role (or the
     * default initial activity if no role-specific activity has
     * been defined for the given role).
     *
     * @param $role  the user's role
     */
    public function getInitialActivity($role)
    {
        if ( ! empty($this->_configs[self::INIT_ACT_LIST]) &&
             ! empty($this->_configs[self::INIT_ACT_LIST][$role]) )
        {
            $act = $this->_configs[self::INIT_ACT_LIST][$role];
            if ( $act != null )
            {
                return $act;
            }
        }
        return $this->getDefaultInitialActivity();
    }

    /**
     * Returns the given activity name if that file exists, an extended 
     * version of the activity name (built up from the base activity
     * directory and the given file name) if that file exists, or null.
     *
     * @param $filename      the file name (absolute or relative)
     * @param $directory     the directory in which to look if the 
     *                          filename is relative
     */
    protected function _buildFilename($filename, $directory)
    {
        if ( file_exists($filename) )
        {
            return $filename;
        }
        if ( $directory != null )
        {
            $extendedFilename = $directory .  DIRECTORY_SEPARATOR .  $filename;
            if ( file_exists($extendedFilename) )
            {
                return $extendedFilename;
            }
        }
        return null;
    }

}

