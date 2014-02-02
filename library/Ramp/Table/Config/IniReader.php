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
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

/**
 * A Ramp_Table_Config_IniReader object reads table
 * sequence/setting information from an external INI file.
 *
 */
class Ramp_Table_Config_IniReader
{

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
        $dir = self::getSettingsDirectory();
        $suffix = $this->_getSettingsSuffix();
        $settingsFile = $dir .  DIRECTORY_SEPARATOR .  $name . $suffix;
        if ( ! file_exists($settingsFile) )
        {
            throw new Exception("Missing settings file for '" . $name .
                "' (no '" . $settingsFile . "')");
        }

        // Read in the configuration information.
        return new Zend_Config_Ini($settingsFile);
    }

    /**
     * Gets the directory being used for table sequence/setting files.
     *
     * @return string   directory path
     *
     */
    public static function getSettingsDirectory()
    {
        // Get the settings directory from the Registry.
        $configs = Ramp_RegistryFacade::getInstance();
        return $configs->getSettingsDirectory();
    }

    /**
     * Gets the suffix being used for table sequence/setting files.
     * The default, if none is provided, is '.ini'.
     *
     * @return string   suffix
     *
     */
    protected static function _getSettingsSuffix()
    {
        // Get the suffix from the Registry.
        $configs = Ramp_RegistryFacade::getInstance();
        $suffix = $configs->getSettingsSuffix();

        $suffix = $suffix ? : '.ini';

        return $suffix;
    }

}

