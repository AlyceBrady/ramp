<?php

/**
 * RAMP: Records and Activity Management Program
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Ramp
 * @package    Ramp_Model
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Config.php 23775 2011-03-01 17:25:24Z ralph $
 */

/**
 * An Application_Model_RampINIReader object reads activity
 * specifications or table sequence/setting information from an external 
 * INI file.
 *
 */
class Application_Model_RampIniReader
{

    /**
     * Retrieves the activity list filename from an internal name that 
     * could represent an activity list file or an activity list within 
     * a file, i.e., a name that could have either of the two following 
     * formats:
     *      filename
     *      filename/activityListName
     *
     * @param  string $name name that is one of the formats above
     * @return string       the filename
     */
    public function getActivityListFilename($name)
    {
        // Determine the filename, based on system path and $name.
        $altName = dirname($name);
        $dir = $this->_getActivitiesDirectory();
        $actFile1 = $dir .  DIRECTORY_SEPARATOR .  $name;
        $actFile2 = $dir .  DIRECTORY_SEPARATOR .  $altName;
        if ( is_file($actFile1) )
            { return $name; }
        elseif ( is_file($actFile2) )
            { return $altName; }
        else
            { return null; }
    }

    /**
     * Imports activity specifications from an INI file.
     *
     * @param $name         internal name of an activity list file or 
     *                      activity list within a file
     *                      e.g., actList.act, actList.ini, al.act/actList
     * @return Zend_Config
     * @throws Exception error reading activity information from a file
     *
     */
    public function importActivitySpecs($name)
    {
        // Determine the file name, based on system path and $name.
        $dir = $this->_getActivitiesDirectory();
        $activitiesFile = $dir .  DIRECTORY_SEPARATOR .  $name;
        $altFilename = dirname($activitiesFile);
        $altActivityName = basename($activitiesFile);
        if ( is_file($activitiesFile) )
            { return new Zend_Config_Ini($activitiesFile); }
        elseif ( is_file($altFilename) )
            { return new Zend_Config_Ini($altFilename); }
        else
        {
            throw new Exception("Missing activities file  (no " .
                "'$activitiesFile' file or '$altFilename' file that " .
                "might have an internal '$altActivityName' in it)");
        }
    }

    /**
     * Imports table viewing properties from an INI file.
     *
     * @param string $name    name of the current setting file (no suffix)
     * @return Zend_Config
     * @throws Exception error reading table setting information from a file
     *
     */
    public function importSettings($name)
    {
        // Determine the file name, based on system path and $name.
        $dir = $this->_getSettingsDirectory();
        $suffix = $this->_getSettingsSuffix();
        $settingsFile = $dir .  DIRECTORY_SEPARATOR .  $name . $suffix;
        if ( ! file_exists($settingsFile) )
        {
            throw new Exception('Missing settings file for ' . $name .
                ' (no "' . $settingsFile . '")');
        }

        // Read in the configuration information.
        return new Zend_Config_Ini($settingsFile);
    }

    /**
     * Gets the directory being used for activity specification files.
     *
     * @return string   directory path
     *
     */
    protected function _getActivitiesDirectory()
    {
        // Get the activities directory from Zend_Registry.  If none is 
        // found, use the settings directory.
        $path = 
            Zend_Registry::isRegistered('rampActivitiesDirectory') ?
                Zend_Registry::get('rampActivitiesDirectory') :
                null;

        $path = $path ? : $this->_getSettingsDirectory();

        return $path;
    }

    /**
     * Gets the directory being used for table sequence/setting files.
     *
     * @return string   directory path
     *
     */
    protected function _getSettingsDirectory()
    {
        // Get the settings directory from Zend_Registry.
        $path = 
            Zend_Registry::isRegistered('rampSettingsDirectory') ?
                Zend_Registry::get('rampSettingsDirectory') :
                null;

        if ( empty($path) )
        {
            // No directory specified; come up with a default instead.
            $baseDir = $this->_getBaseDirectory();
            $path = $baseDir . DIRECTORY_SEPARATOR . 'settings';
        }

        return $path;
    }

    /**
     * Gets the suffix being used for table sequence/setting files.
     *
     * @return string   suffix
     *
     */
    protected function _getSettingsSuffix()
    {
        // Get the suffix from Zend_Registry.
        $suffix = 
            Zend_Registry::isRegistered('rampSettingsSuffix') ?
                Zend_Registry::get('rampSettingsSuffix') :
                null;

        $suffix = $suffix ? : '.ini';

        return $suffix;
    }

    /**
     * Gets the path where settings should be stored.  Uses code from 
     * Zend_Controller_Action::initView() -- if only the Zend 
     * programmers had broken this out into a protected function!
     *
     * @return the base directory for this application module
     *
     */
    protected function _getBaseDirectory()
    {
        $front = Zend_Controller_Front::getInstance();
        $module  = $front->getRequest()->getModuleName();
        $dirs    = $front->getControllerDirectory();
        if (empty($module) || !isset($dirs[$module])) {
            $module = $front->getDispatcher()->getDefaultModule();
        }
        $baseDir = dirname($dirs[$module]);

        return $baseDir;
    }

}

