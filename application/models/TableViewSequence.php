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
 * @version    $Id: Application_Model_TableViewSequence.php 1 2012-07-12 alyce $
 *
 */

class Application_Model_TableViewSequence
{

    /* sequence properties referenced in config files */
    const SEQUENCE                  = "sequence";
    const INIT_ACTION               = "initAction";
    const MAIN_SETTING              = "setting";
    const EDIT_SETTING              = "editSetting";
    const ADD_SETTING               = "addSetting";
    const SEARCH_SPEC_SETTING       = "searchSpecSetting";
    const SEARCH_RES_SETTING        = "searchResultsSetting";
    const REFERENCE_SETTING         = "referenceSetting";

    const DISPLAY_ALL_FORMAT        = "displayAllFormat";

    /* values used in sequence and table setting config files */
    const SEARCH                    = "search";
    const DISPLAY_ALL               = "displayAll";
    const DEFAULT_START             = self::DISPLAY_ALL;

    /** @var array */
    protected $_validSequenceProps;   // valid sequence properties

    /** @var string */
    protected $_initialAction;        // start with a display or a search?

    /** @var array */
    protected $_settingsArray;       // raw setting configuration info

    /** @var string */
    protected $_settingNames;        // names of settings

    /** @var string */
    protected $_settings;            // settings for dealing with records

    /** @var Application_Model_TVSGateway */
    protected $_propertyGateway;      // gateway for getting setting properties

    /**
     * Class constructor
     *
     * Creates an object that represents a sequence for viewing pages 
     * that specifies what table settings and data formats will be used 
     * when the user moves from action to action, such as from
     * viewing multiple records to searching, to viewing a single record,
     * to adding new records, and to editing or deleting records.
     *
     * @param string $name     the name associated with this sequence
     * @return void
     */
    public function __construct($name)
    {
        // Create a gateway to the raw table viewing properties imported 
        // from an external file.
        $this->_propertyGateway = new Application_Model_TVSGateway($name);

        // Get sequence and setting information from the gateway.
        $sequence = $this->_propertyGateway->getSequenceProps();
        $settingsReadIn = $this->_propertyGateway->getTableSettingNames();
        $this->_initSettingsUsedInSequence($name, $sequence, $settingsReadIn);

        // Initialize values that affect TableController actions.
        $this->_initActionsUsedInSequence($sequence);
    }

    /**
     * Initializes the table setting names for displaying records 
     * and for displaying search results.
     * If only one setting was provided there, set the other
     * setting to be the same.
     * If the main setting is not provided, set it from the edit 
     * setting, the add setting, the search specification setting,
     * or the search results setting (in that order).
     * If any of the other four are not provided, use the main setting.
     * If no setting sequence was specified but
     * the imported properties included a table setting, assume
     * that that is the table setting that should be used for
     * all types of display.
     *
     * @param $name     the name associated with this sequence
     * @param $sequence sequence-related properties from property gateway
     * @param $settingsReadIn table settings properties from property gateway
     *
     */
    protected function _initSettingsUsedInSequence($name, $sequence,
                                                   $settingsReadIn)
    {
        $main = $this->_getKeyVal($sequence, self::MAIN_SETTING);
        $edit = $this->_getKeyVal($sequence, self::EDIT_SETTING);
        $add = $this->_getKeyVal($sequence, self::ADD_SETTING);
        $search = $this->_getKeyVal($sequence, self::SEARCH_SPEC_SETTING);
        $searchRes = $this->_getKeyVal($sequence, self::SEARCH_RES_SETTING);
        $reference = $this->_getKeyVal($sequence, self::REFERENCE_SETTING);

	// If no table settings were specified but a single
	// table setting was defined in the property source, use
	// that setting in all cases.
        if ( ! ( $main || $edit || $add ||
                 $search || $searchRes || $reference ) )
        {
            if ( count($settingsReadIn) == 1 )
            {
                $main = $settingsReadIn[0];
            }
            else
            {
                throw new Exception("Must specify " .
                    "at least one table setting in $name");
            }
        }

        // We have at least one setting name now.  If we don't have 
        // all, set the missing ones from the ones that were provided.
        $this->_settingNames[self::MAIN_SETTING] = $main ? :
                                                   $reference ? :
                                                   $edit ? :
                                                   $add ? :
                                                   $search ? :
                                                   $searchRes;
        $edit = $this->_settingNames[self::EDIT_SETTING] = $edit ? : $main;
        $add = $this->_settingNames[self::ADD_SETTING] = $add ? : $main;
        $search = $this->_settingNames[self::SEARCH_SPEC_SETTING] =
                        $search ? : $main;
        $searchRes = $this->_settingNames[self::SEARCH_RES_SETTING] =
                        $searchRes ? : $main;
        $reference = $this->_settingNames[self::REFERENCE_SETTING] =
                        $reference ? : $add;
        $this->_settings[self::MAIN_SETTING] = 
                $this->_settings[self::REFERENCE_SETTING] = 
                $this->_settings[self::EDIT_SETTING] = 
                $this->_settings[self::ADD_SETTING] = 
                $this->_settings[self::SEARCH_SPEC_SETTING] = 
                $this->_settings[self::SEARCH_RES_SETTING] =  null;
    }

    /**
     * Determines the initial action for this sequence.
     *
     * @param $sequence sequence-related properties from property gateway
     *
     */
    protected function _initActionsUsedInSequence($sequence)
    {
        $validInitActions =
                    array(self::SEARCH=>'search',
                          self::DISPLAY_ALL=>'list-view');
        $initSpec = $this->_setControllerAttrib(
                            self::INIT_ACTION, $sequence,
                            self::DEFAULT_START,
                            array_keys($validInitActions));
        $this->_initialAction = $validInitActions[$initSpec];

    }

    /**
     * Gets the initial action associated with this sequence.
     *
     * @return string   name of the initial action to take for this sequence
     *
     */
    public function getInitialAction()
    {
        return $this->_initialAction;
    }

    /**
     * Gets the specified table setting for displaying, modifying, or 
     * searching for table records.
     *
     * @return Application_Model_SetTable
     *
     */
    public function getSetTable($setting)
    {
        if ( $this->_settings[$setting] == null )
        {
            $name = $this->_settingNames[$setting];
            $this->_settings[$setting] =
                    new Application_Model_SetTable($name,
                                                   $this->_propertyGateway);
        }
        return $this->_settings[$setting];
    }

    /**
     * Gets the specified table setting for displaying table records.
     *
     * @return Application_Model_SetTable
     */
    public function getSetTableForViewing()
    {
        return $this->getSetTable(self::MAIN_SETTING);
    }

    /**
     * Gets the specified table setting for modifying table records.
     *
     * @return Application_Model_SetTable
     */
    public function getSetTableForModifying()
    {
        return $this->getSetTable(self::EDIT_SETTING);
    }

    /**
     * Gets the specified table setting for adding table records.
     *
     * @return Application_Model_SetTable
     */
    public function getSetTableForAdding()
    {
        return $this->getSetTable(self::ADD_SETTING);
    }

    /**
     * Gets the specified table setting for searching for table records.
     *
     * @return Application_Model_SetTable
     */
    public function getSetTableForSearching()
    {
        return $this->getSetTable(self::SEARCH_SPEC_SETTING);
    }

    /**
     * Gets the specified table setting for displaying multiple search 
     * results.
     *
     * @return Application_Model_SetTable
     */
    public function getSetTableForSearchResults()
    {
        return $this->getSetTable(self::SEARCH_RES_SETTING);
    }

    /**
     * Gets the specified table setting for resolving external references.
     *
     * @return Application_Model_SetTable
     */
    public function getReferenceSetTable()
    {
        return $this->getSetTable(self::REFERENCE_SETTING);
    }

    /**
     * Sets the controller attribute specified for the given property 
     * with the appropriate value from the property specifications.  If 
     * the property was not defined, the attribute should be set to the 
     * given default value.  Throws an exception if the attribute was
     * specified with an invalid value.
     *
     */
    protected function _setControllerAttrib($property,
                                            $propertySpecs,
                                            $defaultValue,
                                            $validValues)
    {
        $valueProvided = isset($propertySpecs[$property]) ?
                            $propertySpecs[$property] : $defaultValue;

        if ( in_array($valueProvided, $validValues) )
            return $valueProvided;

        throw new Exception("Error: $valueProvided is not a valid " .
            "value for $property.  Must be one of (" .
            implode(", ", $validValues) . ").");
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

}

