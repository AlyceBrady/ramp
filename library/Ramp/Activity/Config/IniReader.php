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
 * @package    Ramp_Activity
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

/**
 * A Ramp_Activity_Config_INIReader object reads activity
 * specifications information from an external INI file.
 *
 */
class Ramp_Activity_Config_IniReader
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
        // Given name might be the filename or might be the name of an 
        // activity with the file.  Determine the filename.
        $name2 = dirname($name);
        $dir = self::_getActivitiesDirectory();
        $fullPath1 = $dir .  DIRECTORY_SEPARATOR .  $name;
        $fullPath2 = $dir .  DIRECTORY_SEPARATOR .  $name2;
        if ( is_file($fullPath1) )
            { return $name; }
        elseif ( is_file($fullPath2) )
            { return $name2; }
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
        $dir = self::_getActivitiesDirectory();
        $fullPath1 = $dir .  DIRECTORY_SEPARATOR .  $name;
        $fullPath2 = dirname($fullPath1);
        $activityName = basename($fullPath1);

        if ( is_file($fullPath1) )
            { return new Zend_Config_Ini($fullPath1); }
        elseif ( is_file($fullPath2) )
            { return new Zend_Config_Ini($fullPath2); }
        else
        {
            throw new Exception("Missing activities file  (no " .
                "'$fullPath1' file or '$fullPath2' file that " .
                "might have an internal '$activityName' in it)");
        }
    }

    /**
     * Gets the directory being used for activity specification files.
     *
     * @return string   directory path
     *
     */
    protected static function _getActivitiesDirectory()
    {
        // Get the activities directory from the Registry.
        $configs = Ramp_RegistryFacade::getInstance();
        return $configs->getActivitiesDirectory();
    }

}

