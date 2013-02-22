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
 * @version    $Id: Application_Model_TVSGateway.php 1 2012-07-12 alyce $
 *
 */

/**
 * An Application_Model_TVSGateway object gets table sequence and table
 * viewing properties (table settings) from an external source.
 *
 */
class Application_Model_TVSGateway
{
    const SEQUENCE                  = "sequence";
    const SETTINGS                  = "settings";
    const RAW                       = "raw";

    /** @var array */
    protected $_sequence;   // sequence properties

    /** @var array */
    protected $_listOfSettings;   // array of name => settings lists

    /**
     * Class constructor
     *
     * Creates an object that gets table sequence and table viewing 
     * properties (table settings) from an external file.
     *
     * @param string $name     the name associated with this sequence
     * @return void
     */
    public function __construct($name)
    {
        $properties = $this->_importProperties($name);
        $this->_sequence = $properties[self::SEQUENCE];
        $this->_listOfSettings = $properties[self::SETTINGS];
    }

    /**
     * Gets the array of sequence properties defined in the property
     * file associated with the settings name provided to this object's 
     * constructor.  Returns an empty array if no sequence properties 
     * were defined in that file.
     *
     */
    public function getSequenceProps()
    {
        // Return sequence properties.
        return $this->_sequence;
    }

    /**
     * Gets an array of names of table settings that have been read in 
     * so far.  For example, if called immediately after this object is 
     * constructed (but before the first call to getSettingsProps), 
     * this function would return the names of table settings that were 
     * defined (i.e., whose properties were specified) in the original 
     * setting file read.  Returns an empty array if no table settings 
     * have been read in.
     *
     */
    public function getTableSettingNames()
    {
        // Return sequence properties.
        return array_keys($this->_listOfSettings);
    }

    /**
     * Gets the array of table setting properties associated with a 
     * setting property file (either the one provided to this object's 
     * constructor or one passed in as a parameter).
     *
     * @param $name     name of the Settings whose properties we want
     *
     */
    public function getSettingProps($name)
    {
        // Check whether table setting has already been read in.
        if ( ! isset($this->_listOfSettings[$name]) )
        {
            // If not, import it and add to the overall list of settings.
            $importedProperties = $this->_importProperties($name);
            if ( ! empty($importedProperties[self::SEQUENCE]) )
            {
                throw new Exception("Error: too many lists of sequence " .
                    "properties found.");
            }
            $this->_listOfSettings[$name] =
                                $importedProperties[self::SETTINGS][$name];
        }

        return $this->_listOfSettings[$name];
    }

    /**
     * Imports table viewing properties from an INI file, separating out
     * sequence information from various table settings.  Returns an 
     * array with two entries, one containing a set of sequence properties
     * and  one containing an array of sets table setting properties.
     *
     * @param string $name    name of a setting file (no suffix)
     * @return array          contains sequence and setting properties
     * @throws Exception error reading table setting information from a file
     *
     */
    protected function _importProperties($name)
    {
        // Import all table viewing properties (sequence information 
        // and/or table settings) that are provided in the external source 
        // associated with $name.
        $reader = new Application_Model_RampIniReader();
        $importedProps = $reader->importSettings($name)->toArray();

        // Get sequence and setting information from the imported properties.
        $sequence = $this->_getSequenceProps($name, $importedProps);
        $settings = $this->_getSettingsProps($name, $importedProps);
        $properties[self::SEQUENCE] = $sequence;
        $properties[self::SETTINGS] = $settings;

        return $properties;
    }

    /**
     * Gets the sequence properties (if any) from the given properties
     * definitions.  Properties could be at the top level or in a section,
     * so need to check both levels.  Properties at the top level will 
     * be associated with $name; those in a section will be associated 
     * with the section name.
     * NOTE: this function modifies the $propDefs parameter, removing 
     * the found sequence properties from it.
     *
     * @param string $name  name associated with these property definitions
     * @param array $propDefs  property definitions (modified by function!)
     * @return array   array of the sequence information (if any)
     * @throws Exception if multiple sequence specifications found
     *
     */
    protected function _getSequenceProps($name, &$propDefs)
    {

        // Look for sequence properties at top level, i.e., SEQUENCE
	// appears as key, but does not appear again as a key below
	// that.  Also look for sequence properties nested in a
	// section.  Ensure that only one set of sequence properties
	// was provided.
        $allSequenceDefs = array();
        if ( $this->_hasSequenceSpec($propDefs) )
        {
            $allSequenceDefs[] = $propDefs[self::SEQUENCE];
            unset($propDefs[self::SEQUENCE]);
        }
        foreach ( $propDefs as $key => $value )
        {
            if ( $this->_hasSequenceSpec($value) )
            {
                $allSequenceDefs[] = $value[self::SEQUENCE];
                unset($propDefs[$key][self::SEQUENCE]);
            }
        }
        if ( count($allSequenceDefs) > 1 )
        {
            throw new Exception("Error: duplicate or conflicting sequence " .
                "properties found.");
        }

        // Return the one set of sequence definitions (at index 0).
        return empty($allSequenceDefs) ? array() : $allSequenceDefs[0];
    }

    /**
     * Gets table settings properties (if any) from the given properties
     * definitions.  Properties could be at the top level or in a section,
     * so need to check both levels.  Properties at the top level will 
     * be associated with $name; those in a section will be associated 
     * with the section name.
     * NOTE: this function modifies the $propDefs parameter, removing 
     * the found settings properties from it.
     *
     * @param string $name  name associated with these property definitions
     * @param array $propDefs  property definitions (modified by function!)
     * @return array   array of the zero, one, or more table settings
     * @throws Exception if multiple sequence specifications found
     *
     */
    protected function _getSettingsProps($name, &$propDefs)
    {

        // Start by looking for sections containing settings properties.  
        // As they're found, move them from propDefs to a new 
        // allSettingDefs array.
        $allSettingDefs = array();
        foreach ( $propDefs as $key => $value )
        {
            if ( $this->_isSettingSpec($value) )
            {
                $allSettingDefs[$key] = $value;
                unset($propDefs[$key]);
            }
        }

        // If there are any property defintions left unaccounted for, 
        // see if they constitute properties for a top-level setting.
        // If not, produce an error message.
        if ( $this->_isSettingSpec($propDefs) )
        {
            // Top-level setting; save in an array associated with 
            // $name.  (Check that $name doesn't match a section name.
            // Can't have two sets of table settings with the same name.)
            if ( isset($allSettingDefs[$name]) )
            {
                throw new Exception("There are two sets of table settings " .
                    "properties associated with the name $key, one at the " .
                    "top level and one in a section.");
            }
            $allSettingDefs[$name] = $propDefs;
        }
        else
        {
            foreach ( $propDefs as $key => $value )
            {
                throw new Exception("Section $key not recognized as a " .
                    "sequence (no \"sequence\" properties) nor as a " .
                    "setting (no database table name provided).");
            }
        }

        return $allSettingDefs;
    }

    /**
     * Gets the value in $theArray associated with $key.  Returns null 
     * if $key is not in $theArray (or if it is, but its value is null).
     *
     */
    protected function _getKeyVal($theArray, $key)
    {
        return isset($theArray[$key]) ? $theArray[$key] : null;
    }

    /**
     * Looks for a set of sequence properties at this level, i.e.,
     * SEQUENCE appears as a key, but does not appear again as a
     * key below that.
     *
     * Examples: 
     *    $propDefs = array(SEQUENCE => array(... no SEQUENCE key ...), ...)
     *          returns true
     *    $propDefs = array(SEQUENCE => array(SEQUENCE=>value, ...), ...)
     *          returns false, regardless of the type of value
     *    $propDefs = array(... no SEQUENCE key ...)
     *          returns false
     *
     * @param array $propDefs   array of property definitions
     * @return bool
     *
     */
    protected function _hasSequenceSpec($propDefs)
    {
        return is_array($propDefs) &&
               array_key_exists(self::SEQUENCE, $propDefs) &&
               is_array($propDefs[self::SEQUENCE]) &&
               ! array_key_exists(self::SEQUENCE, $propDefs[self::SEQUENCE]);
    }

    /**
     * Checks whether the given array parameter contains table settings 
     * property definitions.  Since every table setting must include a 
     * specification for the name of an associated database table, we just
     * look for that.
     *
     * @return bool
     *
     */
    protected function _isSettingSpec($propDefs)
    {
        $dbNameIndex = Application_Model_SetTable::TABLE_NAME;
        return is_array($propDefs) &&
               array_key_exists($dbNameIndex, $propDefs);
    }

}

