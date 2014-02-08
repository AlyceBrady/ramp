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

class Ramp_Table_TableViewSequence
{

    /* sequence properties referenced in config files */
    const SEQUENCE                  = "sequence";
    const INIT_ACTION               = "initAction";
    const MAIN_SETTING              = "setting";
    const EDIT_SETTING              = "editSetting";
    const ADD_SETTING               = "addSetting";
    const DEL_SETTING               = "deleteSetting";
    const SEARCH_SPEC_SETTING       = "searchSpecSetting";
    const SEARCH_RES_SETTING        = "searchResultsSetting";
    const TABULAR_SETTING           = "tabularSetting";
    const SPLIT_VIEW_SETTING        = "splitViewSetting";

    const DISPLAY_ALL_FORMAT        = "displayAllFormat";

    /* values used in sequence and table setting config files */
    const SEARCH                    = "search";
    const DISPLAY_ALL               = "displayAll";
    const DEFAULT_START             = self::SEARCH;

    /* table actions associated with sequence properties */
    const TBL_INDEX         = Ramp_Acl::TBL_INDEX;
    const TBL_SEARCH        = Ramp_Acl::TBL_SEARCH;
    const VIEW_LIST_RESULTS = Ramp_Acl::VIEW_LIST_RESULTS;
    const VIEW_TABLE_FORMAT = Ramp_Acl::VIEW_TABLE_FORMAT;
    const VIEW_SPLIT_FORMAT = Ramp_Acl::VIEW_SPLIT_FORMAT;
    const VIEW_RECORD       = Ramp_Acl::VIEW_RECORD;
    const EDIT_RECORD       = Ramp_Acl::EDIT_RECORD;
    const ADD_RECORD        = Ramp_Acl::ADD_RECORD;
    const BLOCK_ADD         = Ramp_Acl::BLOCK_ADD;
    const BLOCK_EDIT        = Ramp_Acl::BLOCK_EDIT;
    const DELETE_RECORD     = Ramp_Acl::DELETE_RECORD;

    /** @var string */
    protected $_tableName;           // shared table name (if any)

    /** @var string */
    protected $_initialAction;       // start with a display or a search?

    /** @var array */
    protected $_settingNames;        // names of settings referenced in seq.

    /** @var string */
    protected $_settings;            // settings read in from gateway

    /** @var Ramp_Table_TVSGateway */
    protected $_propertyGateway;     // gateway for getting setting properties

    /** @var boolean */
    protected $_recordErrors;        // whether to record errors rather than
                                     // throw exceptions

    /** @var array */
    protected $_error_msgs;          // errors encountered

    /** @var array */
    protected static $_validSeqSettingProps =
        array(self::MAIN_SETTING, self::EDIT_SETTING,
                     self::ADD_SETTING, self::DEL_SETTING, 
                     self::SEARCH_SPEC_SETTING, self::SEARCH_RES_SETTING,
                     self::TABULAR_SETTING, self::SPLIT_VIEW_SETTING);

    /**
     * Checks syntax for the sequence of table views in the given
     * filename.
     */
    public static function checkSyntax($filename)
    {
        $msgs[] = "==> Checking syntax for $filename...";
        $sequence = new Ramp_Table_TableViewSequence($filename, true);

        // Run through all the unique settings and try to get a SetTable 
        // for each.
        $settings = $sequence->_getUniqueSettingNames();
        foreach ( $settings as $name => $property )
        {
            $msgs[] = "<hr />";
            $msgs[] = "==> Checking syntax for getting Set Table $name...";
            $sequence->_clearErrorMsgs();
            $setTable = $sequence->_getSetTable($property);
            $setTable->summarizeSyntaxChecking();
            $msgs = array_merge($msgs, $sequence->_getErrorMsgs(),
                                $setTable->getErrorMsgs());
        }
        return $msgs;
    }

    /**
     * Class constructor
     *
     * Creates an object that represents a sequence for viewing pages 
     * that specifies what table settings and data formats will be used 
     * when the user moves from action to action, such as from
     * viewing multiple records to searching, to viewing a single record,
     * to adding new records, and to editing or deleting records.
     *
     * If the main setting is not provided, it is set from the edit 
     * setting, the add setting, the search specification setting,
     * the search results setting, the tabular setting, or the delete 
     * confirmation setting (in that order).  If only one of the 
     * modifying settings (add and edit) is provided, the other is set 
     * from it.  If neither modifying setting is provided, or if any of 
     * the other settings are missing, they are set from the main 
     * setting.  If no setting sequence was specified but
     * the imported properties included a table setting, assume
     * that that is the table setting that should be used for
     * all types of display.
     *
     * @param string  $name     the name associated with this sequence
     * @param boolean $recordErrors  record local errors rather than
     *                               throwing exceptions
     * @throws Exception if TVSGateway encounters an error (even if 
     *                   $recordErrors is true) or if a local error is
     *                   encountered (if $recordErrors is false)
     */
    public function __construct($name, $recordErrors = false)
    {
        $this->_recordErrors = $recordErrors;
        $this->_error_msgs = array();

        // Create a gateway to the raw table viewing properties imported 
        // from an external file.
        $this->_propertyGateway = new Ramp_Table_TVSGateway($name);

        // Get sequence and setting information from the gateway.
        $this->_tableName = $this->_propertyGateway->getTopLevelTableName();
        $sequence = $this->_propertyGateway->getSequenceProps();
        $settingsReadIn = $this->_propertyGateway->getTableSettingNames();
        $this->_initSettingsUsedInSequence($name, $sequence, $settingsReadIn);

        // Initialize values that affect TableController actions.
        $this->_initActionsUsedInSequence($sequence);
    }

    /**
     * Initializes the table setting names for displaying records 
     * and for displaying search results.
     *
     * @param $name     the name associated with this sequence
     * @param $sequence sequence-related properties from property gateway
     * @param $settingsReadIn table settings properties from property gateway
     *
     */
    protected function _initSettingsUsedInSequence($name, $sequence,
                                                   $settingsReadIn)
    {
        // Get settings that were provided, set variables to null otherwise.
        $main = $this->_getKeyVal($sequence, self::MAIN_SETTING);
        $edit = $this->_getKeyVal($sequence, self::EDIT_SETTING);
        $add = $this->_getKeyVal($sequence, self::ADD_SETTING);
        $delete = $this->_getKeyVal($sequence, self::DEL_SETTING);
        $search = $this->_getKeyVal($sequence, self::SEARCH_SPEC_SETTING);
        $searchRes = $this->_getKeyVal($sequence, self::SEARCH_RES_SETTING);
        $tabular = $this->_getKeyVal($sequence, self::TABULAR_SETTING);
        $splitView = $this->_getKeyVal($sequence, self::SPLIT_VIEW_SETTING);


	// If no sequence table settings were specified but a single
	// table setting was defined in the property source, use
	// that setting in all cases.
        if ( ! ( $main || $edit || $add || $delete || $search ||
                 $searchRes || $tabular || $splitView ) )
        {
            if ( count($settingsReadIn) == 1 )
            {
                $main = $settingsReadIn[0];
            }
            else
            {
                $errorMsg = "Must specify a sequence or " .
                            "one table setting in $name";
                if ( $this->_recordErrors )
                    { $this->_error_msgs[] = $errorMsg; }
                else
                    { throw new Exception($errorMsg); }
            }
        }

        // We have at least one setting name now.  If we don't have 
        // all, set the missing ones from the ones that were provided.
        $this->_settingNames = array();
        $main = $this->_settingNames[self::MAIN_SETTING] = $main ? :
                                                           ($edit ? :
                                                           ($add ? :
                                                           ($search ? :
                                                           ($searchRes ? :
                                                           ($tabular ? :
                                                           ($splitView ? :
                                                           $delete 
                                                           ))))));
        $edit = $this->_settingNames[self::EDIT_SETTING] =
                        $edit ? : ( $add ? : $main );
        $this->_settingNames[self::ADD_SETTING] = $add ? : $edit;
        $this->_settingNames[self::DEL_SETTING] = $delete ? : $main;
        $this->_settingNames[self::SEARCH_SPEC_SETTING] = $search ? : $main;
        $this->_settingNames[self::SEARCH_RES_SETTING] = $searchRes ? : $main;
        $this->_settingNames[self::TABULAR_SETTING] = $tabular ? : $main;
        $this->_settingNames[self::SPLIT_VIEW_SETTING] = $splitView ? : $main;

        // No settings have actually been retrieved from gateway yet.
        $this->_settings = array();
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
     * Gets the table name associated with this sequence, or null if 
     * one has not been specified.
     *
     * @return string   name of the table associated with this sequence
     *
     */
    public function getSeqLevelTableName()
    {
        return $this->_tableName;
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
     * Gets the specified table setting for the given table action.
     *
     * @param $actionName   name of table action from TableController
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForAction($actionName)
    {
        switch ($actionName)
        {
            case self::TBL_INDEX:
                    return $this->getSetTableForSearching(); break;
            case self::TBL_SEARCH:
                    return $this->getSetTableForSearching(); break;
            case self::VIEW_LIST_RESULTS:
                    return $this->getSetTableForSearchResults(); break;
            case self::VIEW_TABLE_FORMAT:
                    return $this->getSetTableForTabularView(); break;
            case self::VIEW_SPLIT_FORMAT:
                    return $this->getSetTableForSplitView(); break;
            case self::VIEW_RECORD:
                    return $this->getSetTableForViewing(); break;
            case self::EDIT_RECORD:
                    return $this->getSetTableForModifying(); break;
            case self::BLOCK_EDIT:
                    return $this->getSetTableForModifying(); break;
            case self::ADD_RECORD:
                    return $this->getSetTableForAdding(); break;
            case self::BLOCK_ADD:
                    return $this->getSetTableForAdding(); break;
            case self::DELETE_RECORD:
                    return $this->getSetTableForDeleting(); break;
            default:
                $errorMsg = "Error: trying to get set table " .
                    "for unknown table action: " . $actionName . ".";
                if ( $recordErrors )
                    { $this->_error_msgs[] = $errorMsg; }
                else
                    { throw new Exception($errorMsg); }
        }
    }

    /**
     * Gets the table setting for displaying table records.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForViewing()
    {
        return $this->_getSetTable(self::MAIN_SETTING);
    }

    /**
     * Gets the table setting for modifying table records.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForModifying()
    {
        return $this->_getSetTable(self::EDIT_SETTING);
    }

    /**
     * Gets the table setting for adding table records.  This is a good 
     * choice of table setting for determining the "completeness" status
     * of individual records (whether a record's recommended fields are
     * completely filled in, partially filled in, or not provided at all).
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForAdding()
    {
        return $this->_getSetTable(self::ADD_SETTING);
    }

    /**
     * Gets the table setting for deleting table records.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForDeleting()
    {
        return $this->_getSetTable(self::DEL_SETTING);
    }

    /**
     * Gets the table setting for searching for table records.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForSearching()
    {
        return $this->_getSetTable(self::SEARCH_SPEC_SETTING);
    }

    /**
     * Gets the table setting for displaying multiple search 
     * results.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForSearchResults()
    {
        return $this->_getSetTable(self::SEARCH_RES_SETTING);
    }

    /**
     * Gets the table setting for displaying multiple search 
     * results in tabular format.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForTabularView()
    {
        return $this->_getSetTable(self::TABULAR_SETTING);
    }

    /**
     * Gets the table setting for displaying a split view of
     * multiple search results.
     *
     * @return Ramp_Table_SetTable
     */
    public function getSetTableForSplitView()
    {
        return $this->_getSetTable(self::SPLIT_VIEW_SETTING);
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

        $errorMsg = "Error: $valueProvided is not a valid value for " .
                        "$property.  Must be one of (" . 
                        implode(", ", $validValues) . ").";
        if ( $this->_recordErrors )
            { $this->_error_msgs[] = $errorMsg; }
        else
            { throw new Exception($errorMsg); }
    }

    /**
     * Gets the value in $theArray associated with $key.  Returns null 
     * if $key is not in $theArray (or if it is, but its value is empty 
     * or null).
     *
     */
    protected function _getKeyVal($theArray, $key)
    {
        return isset($theArray[$key]) && ! empty($theArray[$key])
                    ? $theArray[$key] : null;
    }

    /**
     * Returns a set of setting names defined in this sequence, each 
     * with a property that uses that setting.  For example, if a 
     * sequence includes several setting properties that resolve to a 
     * 'View' setting and several others that resolve to a 'Modify'
     * setting,  this method might return:
     *    'View' => 'setting'
     *    'Modify' => 'addSetting'
     */
    protected function _getUniqueSettingNames()
    {
        $uniqueNames = array();
        foreach ( $this->_settingNames as $property => $settingName )
        {
            if ( ! isset($uniqueNames[$settingName]) )
            {
                $uniqueNames[$settingName] = $property;
            }
        }
        return $uniqueNames;
    }

    /**
     * Clears the error messages accumulated so far while
     * doing syntax checking for this sequence.
     */
    protected function _clearErrorMsgs()
    {
        $this->_error_msgs = array();
    }

    /**
     * Gets the error messages accumulated when doing syntax checking.
     */
    protected function _getErrorMsgs()
    {
        return $this->_error_msgs;
    }

    /**
     * Gets the specified table setting for displaying, modifying, or 
     * searching for table records.
     *
     * @param $property   name of desired setting property (setting, 
     *                      addSetting, etc.)
     *
     * @return Ramp_Table_SetTable
     */
    protected function _getSetTable($property)
    {
        if ( empty($property) )
        {
            throw new Exception("Error: trying to get set table " .
                "for empty setting property name.");
        }
        if ( ! in_array($property, self::$_validSeqSettingProps) )
        {
            throw new Exception("Error: trying to get set table for " .
                "unknown setting property: " . $property . ".");
        }
        if ( ! isset($this->_settings[$property]) )
        {
            $name = $this->_settingNames[$property];
            $this->_settings[$property] =
                    new Ramp_Table_SetTable($name, $this->_propertyGateway);
        }
        return $this->_settings[$property];
    }

}

