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

class Ramp_Table_SetTable
{
    // Constants representing table setting properties
    const TABLE_NAME            = "tableName";
    const TITLE                 = 'tableTitle';
    const DESCRIPTION           = 'tableDescription';  // unused?
    const TABLE_FOOTNOTE        = 'tableFootnote';
    const CONNECTED_TBL         = 'tableConnection';
    const SORT_ORDER            = 'tableSortOrder';
    const INIT_TBL_REF          = 'initTableRef';
    const EXTERNAL_TBL          = 'externalTableRef';
    const SHOW_COLS_BY_DEFAULT  = 'tableShowColsByDefault';
    const BLOCK_ENTRY           = 'blockEntry';
    const BLOCK_EDIT            = 'blockEdit';
    const FIELDS                = 'field';
    // const TABLE_QUERY_CONSTRAINT= 'tableQueryConstraint';

    // Sub-properties for table connections and block entry
    const CONNECTION            = 'connection';
    const ALIAS                 = 'aliasFor';
    const BLOCK_ENTRY_LABEL     = 'label';
    const BLOCK_FIELD           = 'field';
    const BLOCK_ENTRY_COUNT     = 'count';

    // Constants representing search values and search operators.
    const ANY_VAL         = '__any_search_value__';
    const CONTAINS        = Ramp_Form_Table_TableRecordEntry::CONTAINS;
    const LIKE            = Ramp_Form_Table_TableRecordEntry::LIKE;
    const DEFAULT_COMPARATOR
                = Ramp_Form_Table_TableRecordEntry::DEFAULT_COMPARATOR;

    // Constants representing search types
    const ANY                   = 'any';
    const ALL                   = 'all';

    // Status codes used for communicating whether the table has all 
    // recommended fields for a particular record.
    const BLANK     = 'blank';
    const PARTIAL   = 'partial';
    const GOOD      = 'sufficient';

    /** @var string */
    protected $_origSettings;       // settings read from gateway

    /** @var string */
    protected $_dbTableName;        // name of table in the actual database

    /** @var string */
    protected $_settingName;        // name of table setting

    /** @var Ramp_Table_Db_Table_Table */
    protected $_dbModel;              // model associated with db table

    /** @var string */
    protected $_tableQueryConstraint; // limits fields that can satisfy setting

    /** @var string */
    protected $_title;              // table title

    /** @var string */
    protected $_description;        // table description

    /** @var string */
    protected $_tableFootnote;      // table footnote

    /** @var string */
    protected $_sortOrder;          // sort order

    /** @var bool */
    protected $_showColsByDefault;  // show all fields unless hidden?

    /** @var array */
    protected $_inTable = array();  // all fields in local database table

    /** @var array */
    protected $_expressions = array();  // all 'fields' that are expressions

    /** @var array */
    protected $_sourceTables = array(); // table names (not aliases)

    /** @var array */
    protected $_allImportFields = array();  // fields from other tables

    /** @var array */
    protected $_importAliases = array();    // aliases for fields from other
                                            // tables, by table

    /** @var string */
    protected $_joinExpressions;    // join expressions for importing fields 

    /** @var array */
    protected $_linkFields = array(); // fields providing links to other tables

    /** @var array */
    protected $_visibleFields = array();    // visible fields

    /** @var array */
    protected $_keys = array();             // primary key(s) for table

    /** @var array */
    protected $_undefinedFieldNames = array();  // setting fields not in db

    /** @var array */
    protected $_defaults = array();         // field default values

    /** @var array */
    protected $_sourcesOfValidVals = array();  // tables that provide range
                                               // of valid values for some
                                               // fields

    /** @var array */
    protected $_fieldsInitFromElsewhere = array();  // fields initialized from
                                                    // other tables

    /** @var string */
    protected $_initTblRefs;        // initialization table references

    /** @var string */
    protected $_externalTblRefs;    // external table refs that look like fields

    /** @var string */
    protected $_blockEntryField;    // field used for block entry (if allowed)

    /** @var string */
    protected $_blockEditFields;    // fields used for block edit (if allowed)

    /** @var boolean */
    protected $_recordErrors;        // whether to record errors rather than
                                     // throw exceptions

    /** @var array */
    protected $_error_msgs;          // errors encountered

    /**
     * Returns a list of the valid sequence setting properties.
     */
    public static function validTableProps()
    {
        return array(self::TABLE_NAME, self::TITLE, self::DESCRIPTION,
                     self::TABLE_FOOTNOTE, self::SORT_ORDER,
                     self::CONNECTED_TBL,
                     self::INIT_TBL_REF, self::EXTERNAL_TBL,
                     self::SHOW_COLS_BY_DEFAULT, self::FIELDS,
                     self::BLOCK_ENTRY, self::BLOCK_EDIT
                    );
    }

    /**
     * Class constructor
     *
     * Creates an object that represents all the information known about 
     * a table, including information from the table setting (e.g., 
     * labels and descriptions) and from the database (e.g., which 
     * fields are required).
     *
     * @param string $settingName   the name of the table setting
     * @param TVSGateway $propGateway  gateway to table setting properties
     * @param boolean $recordErrors  record local errors rather than
     *                               throwing exceptions
     */
    public function __construct($settingName, $propGateway,
                                $recordErrors = false)
    {
        $this->_recordErrors = $recordErrors;
        $this->_error_msgs = array();

        // Get the table setting properties.
        $settingProps = $propGateway->getSettingProps($settingName);

        // Make sure that the setting properties include a table name
        // (min. requirement)
        if ( array_key_exists(self::TABLE_NAME, $settingProps) )
        {
            $this->_dbTableName = $settingProps[self::TABLE_NAME];
        }
        else
        {
            // Table name is not present (nor inherited)
            $errorMsg = "$settingName setting must include a key for '" .
                        self::TABLE_NAME .
                        "' that names the database table to use.";
            if ( $this->_recordErrors )
                { $this->_error_msgs[] = $errorMsg; return; }
            else
                { throw new Exception($errorMsg); }
        }
        $this->_settingName = $settingName;
        $this->_dbModel =
                    new Ramp_Table_DbTable_Table($this->_dbTableName);

        $this->_origSettings = ( $settingProps instanceof Zend_Config ) ?
                                   $settingProps->toArray() :
                                   $settingProps;
        $allColMetaInfo = array();
        try
        {
            $allColMetaInfo =
                     $this->_dbModel->info(Zend_Db_Table_Abstract::METADATA);
        }
        catch (Exception $e)
        {
            if ( $recordErrors )
            {
                $this->_error_msgs[] = "Table " . $this->_dbTableName .
                                       " does not exist in the database.";
            }
            else
                { throw new Exception($e->getMessage()); }
        }
        $this->_init($this->_origSettings, $allColMetaInfo);

    }

    /**
     * Initializes table attributes from information provided by the 
     * table setting and the database.
     *
     * @param array $settingInfo    information from the table setting
     * @param array $allColMetaInfo meta information from db for all fields
     * @return Ramp_Table_SetTable
     */
    protected function _init(array $settingInfo, array $allColMetaInfo)
    {

        // Initialize remaining table attributes.
        /*
        $this->_tableQueryConstraint =
                    isset($settingInfo[self::TABLE_QUERY_CONSTRAINT]) ?
                            $settingInfo[self::TABLE_QUERY_CONSTRAINT] :
                            "";
         */
        $this->_title = isset($settingInfo[self::TITLE]) ?
                            $settingInfo[self::TITLE] :
                            $this->_dbTableName;
        $this->_description = isset($settingInfo[self::DESCRIPTION]) ?
                            $settingInfo[self::DESCRIPTION] :
                            "";
        $this->_tableFootnote = isset($settingInfo[self::TABLE_FOOTNOTE]) ?
                            $settingInfo[self::TABLE_FOOTNOTE] :
                            "";
        $this->_sortOrder = isset($settingInfo[self::SORT_ORDER]) ?
                            $settingInfo[self::SORT_ORDER] :
                            "";
        $this->_showColsByDefault =
                    isset($settingInfo[self::SHOW_COLS_BY_DEFAULT]) ?
                            (bool) $settingInfo[self::SHOW_COLS_BY_DEFAULT] :
                            false;

        // Process table connections (join expressions).
        $connections = isset($settingInfo[self::CONNECTED_TBL]) ?
                            $settingInfo[self::CONNECTED_TBL] :
                            array();
        $this->_initConnections($connections);

        // Initialize references to initialization tables.
        $references = isset($settingInfo[self::INIT_TBL_REF]) ?
                            $settingInfo[self::INIT_TBL_REF] :
                            array();
        $this->_initTblRefs = $this->_initReferences($references);

        // Initialize references to external, related tables.
        $references = isset($settingInfo[self::EXTERNAL_TBL]) ?
                            $settingInfo[self::EXTERNAL_TBL] :
                            array();
        $this->_externalTblRefs = $this->_initReferences($references);

        // Initialize block entry information (if supported).
        $blockEntry = isset($settingInfo[self::BLOCK_ENTRY]) ?
                            $settingInfo[self::BLOCK_ENTRY] :
                            array();
        $this->_blockEntryField = $this->_initBlockEntry($blockEntry);

        // Initialize block edit information (if supported).
        $blockEdit = isset($settingInfo[self::BLOCK_EDIT]) ?
                            $settingInfo[self::BLOCK_EDIT] :
                            array();
        $this->_blockEditFields = $this->_initBlockEdit($blockEdit);

        // Create Field objects for all fields in database, providing 
        // table setting information when provided.  Add fields to 
        // appropriate field-information attributes.
        $allFieldSettings = array_key_exists(self::FIELDS, $settingInfo) ?
                            $settingInfo[self::FIELDS] :
                            array();
        $this->_initFields($allFieldSettings, $allColMetaInfo);

        return $this;
    }

    /**
     * Initializes table connection attributes from information provided by
     * the  table setting and the database.  Connections may be in one of two 
     * formats, a fully-qualified format:
     *   tableConnection.ExtTable.connection = "LocalTbl.field = ExtTable.field"
     * (useful when the ExtTable is an alias) or an abbreviated format:
     *   tableConnection.ExtTable = "LocalTbl.field = ExtTable.field"
     *
     * Users may optionally provide a table alias, as follows:
     *   tableConnection.ExtTable.aliasFor = "RealTableName"
     * In this case, the connection must be specified with the more 
     * verbose, fully-qualified format.
     *
     * @param array $allConnections connections to other tables
     * @return void
     */
    protected function _initConnections($allConnections)
    {
        $this->_sourceTables = array();
        $this->_joinExpressions = array();

        // Return immediately if there are no connections to process.
        if ( empty($allConnections) )
        {
            return;
        }

        // Put all connections in fully-qualified format.
        foreach ( $allConnections as $table => $connection )
        {
            $realTable = $table;            // $table might be real name
            if ( is_array($connection) )    // fully-qualified format
            {
                if ( ! isset($connection[self::CONNECTION]) )
                {
                    $errorMsg = "Table connection for " . $table .
                            " does not have the required format: \n     " .
                            self::CONNECTED_TBL . "." . $table .
                            ".connection =" .
                            "\"LocalTbl.localField = ExtTable.extField\"";
                    if ( $this->_recordErrors )
                        { $this->_error_msgs[] = $errorMsg; continue; }
                    else
                        { throw new Exception($errorMsg); }
                }
                $this->_joinExpressions[$table] = $connection;
                if ( isset($connection[self::ALIAS]) )
                {
                    // $table is alias for table named in connection info
                    $realTable = $connection[self::ALIAS];
                }
            }
            else        // abbreviated format
            {
                $this->_joinExpressions[$table][self::CONNECTION] = $connection;
            }
            $this->_importAliases[$table] = array();
            $this->_sourceTables[$table] = $realTable;
        }
    }

    /**
     * Initializes references to an external table.
     * @see Ramp_Table_ExternalTableReference
     *
     * @return array of Ramp_Table_ExternalTableReference objects
     * @throws Exception if reference information is badly formatted
     */
    protected function _initReferences($referenceInfo)
    {

        $refObjects = array();

        foreach ( $referenceInfo as $tableName => $refInfo )
        {
            try
            {
                $refObjects[$tableName] =
                    new Ramp_Table_ExternalTableReference($refInfo,
                                                        $this->_settingName);
            }
            catch (Exception $e)
            {
                if ( $this->_recordErrors )
                    { $this->_error_msgs[] = $e->getMessage(); }
                else
                    { throw new Exception($e->getMessage()); }
            }
        }

        return $refObjects;
    }

    /**
     * Initializes sub-properties for block entry.
     *
     * @return array of block entry sub-properties
     * @throws Exception if a necessary sub-property is missing
     */
    protected function _initBlockEntry($blockInfo)
    {
        // If block entry information was provided, check that the 
        // required sub-properties were also.
        if ( empty($blockInfo) )
        {
            return array();
        }
        if ( is_array($blockInfo) && isset($blockInfo[self::BLOCK_FIELD]) &&
             is_string($blockInfo[self::BLOCK_FIELD]) )
        {
            return $blockInfo;
        }
        else
        {
            throw new Exception("'" . self::BLOCK_ENTRY . "' property " .
                "does not have the required '" .  self::BLOCK_FIELD .
                "' sub-property.");
        }
    }

    /**
     * Initializes sub-properties for block edit properties.
     *
     * @return array of block edit sub-properties
     * @throws Exception if a necessary sub-property is missing
     */
    protected function _initBlockEdit($blockInfo)
    {
        // If block edit information was provided, check that one or 
        // more fields were also.
        if ( empty($blockInfo) )
        {
            return array();
        }
        if ( is_array($blockInfo) && isset($blockInfo[self::BLOCK_FIELD]) &&
             ( is_array($blockInfo[self::BLOCK_FIELD]) ||
               is_string($blockInfo[self::BLOCK_FIELD]) ) )
        {
            if ( is_array($blockInfo[self::BLOCK_FIELD]) )
            {
                $fields = array();
                foreach ( $blockInfo[self::BLOCK_FIELD] as $key => $value )
                {
                    $fields[$value] = $value;
                }
                return $fields;
            }
            else
            {
                return array($blockInfo => $blockInfo);
            }
        }
        else
        {
            throw new Exception("'" . self::BLOCK_EDIT . "' property " .
                "does not have the correct, required '" .  self::BLOCK_FIELD .
                "' sub-property.");
        }
    }

    /**
     * Initializes field attributes from information provided by the 
     * table setting and the database.
     *
     * @param array $allFieldSettings information from the table setting
     * @param array $allColMetaInfo   meta information from db for all fields
     * @return void
     */
    protected function _initFields($allFieldSettings, $allColMetaInfo)
    {
        // Loop through all fields in database and/or table setting...
        $allColNames = array_unique(array_merge(array_keys($allFieldSettings),
                                                array_keys($allColMetaInfo)));
        foreach ( $allColNames as $colName )
        {
            // Get the table setting and meta information for each 
            // field, and create a Field object for it.
            $fieldSettings = isset($allFieldSettings[$colName]) ?
                                $allFieldSettings[$colName] : array();
            $metaInfo = isset($allColMetaInfo[$colName]) ?
                                $allColMetaInfo[$colName] : array();
            $field = new Ramp_Table_Field($colName, $fieldSettings,
                                          $metaInfo, $this->_showColsByDefault);

            // Add field (or its table) to appropriate field lists.
            if ( $field->isinDb() || $field->isExpression() )
                { $this->_categorizeField($colName, $field); }
            else
                { $this->_undefinedFieldNames[] = $colName; }
            if ( $field->validValsDefinedInExtTable() )
            {
                $this->_sourcesOfValidVals[] = $field->getSourceOfValidVals();
            }
        }
    }

    /**
     * Adds field to appropriate lists.
     *
     * @param $name     name of field object to categorize
     * @param $field    the object itself
     */
    protected function _categorizeField($name, $field)
    {
        if ( $field->isInTable() )
            { $this->_inTable[$name] = $field; }
        else if ( $field->isExpression() )
        {
            $this->_expressions[$name] = $field;
        }
        else // if ( $field->isVisible() )  // imported, visible fields
        {
            $table = $field->getImportTable();
            if ( ! isset($this->_importAliases[$table]) )
            {
                $errorMsg = "Cannot import '$name' from " .
                            "'$table' table; there is no '" .
                            self::CONNECTED_TBL .
                            "' clause for '$table.'";
                if ( $this->_recordErrors )
                    { $this->_error_msgs[] = $errorMsg; return; }
                else
                    { throw new Exception($errorMsg); }
            }
            $this->_importAliases[$table][$name] = $field->resolveAlias();
            $this->_allImportFields[$name] = $field;
        }
        /*
        else
        {
            // Ignore invisible imported fields.
            return;
        }
         */

        if ( $field->isExternalTableLink() )
        {
            $this->_linkFields[$name] = $field;
        }

        if ( $field->isVisible() )
            { $this->_visibleFields[$name] = $field; }
        if ( $field->isPrimaryKey() )
            { $this->_keys[$name] = $field; }
        $fieldDefault = $field->getDefault();
        if ( null !== $fieldDefault )
            { $this->_defaults[$name] = $fieldDefault; }
        if ( $field->initFromAnotherTable() )
            { $this->_fieldsInitFromElsewhere[$name] = $field; }

    }

    /**
     * Creates another set table that constitutes a "visual subset" of 
     * this setting with only the single given field visible; all other
     * fields are hidden.  If the given field was previously hidden, it 
     * is made visible in the "visual subset" setting.
     *
     * @param string  $fieldToKeep   the name of the field to keep visible
     */
    public function createSingleFieldSubset($fieldToKeep)
    {
        $subset = clone $this;

        // Hide every field except the given one.
        foreach ( $this->getVisibleFields() as $fieldName => $field )
        {
            // Make visible or invisible, depending on whether this is 
            // the field to keep (or make) visible.
            if ( $fieldName != $fieldToKeep )
            {
                $subset->_setVisibilityOfField($fieldName, false);
            }
        }
        $subset->_setVisibilityOfField($fieldToKeep, true);

        return $subset;
    }

    /**
     * Creates another set table that constitutes a "visual subset" of 
     * this setting; all attributes are the same except that all except
     * the given fields are hidden.
     *
     * @param array  $fieldsToNotHide   the fields to keep visible
     */
    public function createSubsetWithOnly($fieldsToNotHide)
    {
        $subset = clone $this;

        // Hide every field except the given ones.
        foreach ( $this->getVisibleFields() as $fieldName => $field )
        {
            // Make visible or invisible, depending on whether this is a
            // field to keep (or make) visible.
            if ( isset($fieldsToNotHide[$fieldName]) )
            {
                $subset->_setVisibilityOfField($fieldName, true);
            }
            else
            {
                $subset->_setVisibilityOfField($fieldName, false);
            }
        }

        return $subset;
    }

    /**
     * Creates another set table that constitutes a "visual subset" of 
     * this setting; all attributes are the same except that more of the 
     * fields are hidden.
     *
     * @param array  $fieldsToHide   the fields to hide
     */
    public function createSubsetWithout($fieldsToHide)
    {
        $subset = clone $this;

        // Hide each provided field.
        foreach ( $fieldsToHide as $fieldName )
        {
            $subset->_setVisibilityOfField($fieldName, false);  // Hide
        }

        return $subset;
    }

    /**
     * Marks the field with the given name as visible or hidden (in multiple 
     * ways) in this set table.
     */
    protected function _setVisibilityOfField($fieldName, $visible)
    {
        // Make a clone of the field with the correct visibility, and 
        // put the clone in the inTable, expressions, allImportFields, and/or 
        // keys lists, as appropriate.
        if ( isset($this->_inTable[$fieldName]) )
        {
            $clone = clone $this->_inTable[$fieldName];
            $this->_inTable[$fieldName] = $clone;
        }
        else if ( isset($this->_expressions[$fieldName]) )
        {
            $clone = clone $this->_expressions[$fieldName];
            $this->_expressions[$fieldName] = $clone;
        }
        else if ( isset($this->_allImportFields[$fieldName]) )
        {
            $clone = clone $this->_allImportFields[$fieldName];
            $this->_allImportFields[$fieldName] = $clone;
        }
        else
            { return; }

        if ( isset($this->_keys[$fieldName]) )
        {
            $this->_keys[$fieldName] = $clone;
        }

        $clone->setVisibility($visible);

        // Add or remove the field from the list of visible fields.
        if ( $visible && ! isset($this->_visibleFields[$fieldName]) )
        {
            $this->_visibleFields[$fieldName] = $clone;
        }
        else if ( ! $visible && isset($this->_visibleFields[$fieldName]) )
        {
            unset($this->_visibleFields[$fieldName]);
        }

        return $this;
    }

    /**
     * Gets the name of the underlying table.
     */
    public function getDbTableName()
    {
        return $this->_dbTableName;
    }

    /**
     * Gets the names of dependent tables for which VIEW authorization
     * is needed to do work with the current table setting.  These are:
     *   - tables that provide the source of possible values for a field
     *   - the tables from which one is importing data
     *   - tables from which one is initializing data (only for ADD 
     *     operations, so initTableReference and initFrom should only be 
     *     for those settings)
     *  External tables settings, which are just links, do not need to 
     *  be included.  Even the "complete, incomplete, empty" designation 
     *  is essentially meta-information and does not require VIEW 
     *  authorization.
     */
    public function getDependentTables()
    {
        $validValTbls = $this->_sourcesOfValidVals;
        $importTbls = array_values($this->_sourceTables);
        $initTbls  = array_keys($this->_initTblRefs);
        $tables = array_unique(array_merge($validValTbls, $importTbls,
                                           $initTbls));
        return $tables;
    }

    /**
     * Gets all data types represented in this table setting (for 
     * development and debugging purposes).
     */
    public function getAllDataTypes()
    {
        $set = array();
        foreach ( $this->_inTable as $fieldName => $fieldObj )
            if ( ! array_key_exists($fieldName, $set) )
                $set[$fieldName] = $fieldObj->getDataType();
        return $set;
    }

    /**
     * Gets the name of this table setting.
     *
     * return string    table setting name
     */
    public function getSettingName()
    {
        return $this->_settingName;
    }

    /**
     * Gets the title of the table from the table setting.  If no
     * title was provided, returns the actual database name.
     *
     * return string    table title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Gets the table description from the table setting.  If no 
     * description was provided, returns an empty string.
     *
     * return string    table description
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Gets the table footnote from the table setting.  If no 
     * footnote was provided, returns an empty string.
     *
     * return string    table footnote
     */
    public function getTableFootnote()
    {
        return $this->_tableFootnote;
    }

    /**
     * Gets the sort order from the table setting.  If no 
     * sort order was provided, returns an empty string.
     *
     * return string    sort order
     */
    public function getSortOrder()
    {
        return $this->_sortOrder;
    }

    /**
     * Gets the names of any fields that were specified in the table 
     * setting but do not exist in the database.
     *
     * @return array    array of field names
     */
    public function getUndefinedFieldNames()
    {
        return $this->_undefinedFieldNames;
    }

    /**
     * Gets field information (labels, footnotes, meta information 
     * from the database, etc.) for all fields defined in the database.
     * Fields with no table setting information will be marked as 
     * hidden (the isVisible method returns false) unless the table 
     * setting specifies to show all fields by default.  In that case,
     * the field name from the database is returned as the label.
     *
     * @return array    array of Ramp_Table_Field objects
     */
    public function getFields()
    {
        return $this->_inTable + $this->_expressions + $this->_allImportFields;
    }

    /**
     * Gets the local fields that are links to other tables, as
     * fieldname => table pairs.  For example, an employee record might 
     * include an employer id field that is a link to a table of 
     * employer information.
     */
    public function getTableLinkFields()
    {
        return $this->_linkFields;
    }

    /**
     * Gets field information (labels, footnotes, meta information 
     * from the database, etc.) for all fields included in the table 
     * setting that are not specified as hidden.
     * If the table setting specifies to show all fields by default, 
     * then even fields that were not included in the table setting 
     * will be included.  In that case, the field names from the 
     * database will be used as field labels/headings.
     *
     * @return array    array of Ramp_Table_Field objects
     */
    public function getVisibleFields()
    {
        return $this->_visibleFields;
    }

    /**
     * Gets field information (labels, footnotes, meta information 
     * from the database, etc.) for the primary key(s) in the table,
     * whether visible or not.
     *
     * @return array    array of Ramp_Table_Field objects
     */
    public function getPrimaryKeys()
    {
        return $this->_keys;
    }

    /**
     * Gets field information for all "local" (non-imported) fields
     * (see getLocalRelevantFields for details), and, for all "fields"
     * that actually represent expressions, replace the field names
     * with the expression.
     *
     * @return array    array of Ramp_Table_Field objects
     */
    public function getLocalSelectObjects()
    {
        $result = array();
        $localFieldsAndExprs = $this->getLocalRelevantFields();
        foreach ( $localFieldsAndExprs as $fieldName => $field )
        {
            if ( $field->isExpression() )
            {
                $result[$fieldName] = new Zend_Db_Expr($field->getExpression());
            }
            else
            {
                $result[] = $fieldName;
            }
        }
        return $result;
    }

    /**
     * Gets field information (labels, footnotes, meta information 
     * from the database, etc.) for all "local" fields (those not
     * imported from another table) that are relevant to a table or 
     * record display, including primary keys and other, non-hidden 
     * fields.
     * If the table setting specifies to show all fields by default, 
     * then even fields that were not included in the table setting 
     * will be included.  In that case, the field names from the 
     * database will be used as field labels/headings.
     *
     * @return array    array of Ramp_Table_Field objects
     */
    public function getLocalRelevantFields()
    {
        return array_diff_key($this->getRelevantFields(),
                              $this->_allImportFields);
    }

    /**
     * Gets field information (labels, footnotes, meta information 
     * from the database, etc.) for all fields relevant to a table or 
     * record display, including visible fields, primary keys, and
     * fields for which the setting provides external references.
     * If the table setting specifies to show all fields by default, 
     * then even fields that were not included in the table setting 
     * will be included.  In that case, the field names from the 
     * database will be used as field labels/headings.
     *
     * @return array    array of Ramp_Table_Field objects
     */
    public function getRelevantFields()
    {
        $extTblConnections = array();
        $extTblFields = array();
        if ( ! empty($this->_externalTblRefs) )
        {
            foreach ( $this->_externalTblRefs as $name => $ref )
            {
                $extTblConnections = array_merge($extTblConnections, 
                                    $ref->getConnectionExpressions());
            }
            foreach ( $extTblConnections as $ext => $local )
            {
                $localFieldObj = $this->getFieldObject($local);
                if ( $localFieldObj == null )
                {
                    throw new Exception("Error: $local is not a field " .
                        "in this setting (" . $this->_settingName . ").");
                }
                $extTblFields[$local] = $localFieldObj;
            }
        }
        return $this->_visibleFields + $this->getPrimaryKeys() + $extTblFields;
    }

    /**
     * Gets an array of all field defaults provided for this table in
     * fieldName => default format.  Returns an empty array if there
     * are no fields with defaults.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

    /**
     * Gets an array of all fields in this table that should be 
     * initialized from external sources.  Returns an empty array if 
     * there are no such fields.
     *
     * @return array array of Field objects
     */
    public function getExternallyInitFields()
    {
        return $this->_fieldsInitFromElsewhere;
    }

    /**
     * Gets the initialization reference information for a particular
     * table name.  Returns an ExternalTableReference object or null
     * if the given table name has not been defined as an initialization
     * reference.
     *
     * @return ExternalTableReference  object representing matching info
     *
     */
    public function getInitRefInfo($tableName)
    {
        return isset($this->_initTblRefs[$tableName])
                            ? $this->_initTblRefs[$tableName]
                            : null;
    }

    /**
     * Gets the list of references to external tables related to this 
     * table ("See Also" type tables that provide more detail about some
     * field in the current table).  Returns an empty array if there are 
     * no such references.
     *
     * @return array   list of ExternalTableReference objects
     */
    public function getExtTableReferences()
    {
        return $this->_externalTblRefs;
    }

    /**
     * Determines whether this set table supports block record 
     * insertion.
     *
     * @return bool   true if block data entry is supported
     */
    public function supportsBlockEntry()
    {
        return ! empty($this->_blockEntryField);
    }

    /**
     * Gets the field for which block entry is supported.
     *
     * Precondition:  supportsBlockEntry() returns true
     *
     * @return string   field name
     */
    public function getBlockEntryField()
    {
        return $this->_blockEntryField[self::BLOCK_FIELD];
    }

    /**
     * Gets the label describing the field for which block entry is 
     * supported.
     *
     * Precondition:  supportsBlockEntry() returns true
     *
     * @return string   the label
     */
    public function getBlockEntryLabel()
    {
        return isset($this->_blockEntryField[self::BLOCK_ENTRY_LABEL])
                ? $this->_blockEntryField[self::BLOCK_ENTRY_LABEL]
                : "Fields";
    }

    /**
     * Gets the number of entry fields to include on the page (default 
     * is 10, if this is not provided).
     *
     * Precondition:  supportsBlockEntry() returns true
     *
     * @return int   the count
     */
    public function getBlockEntryCount()
    {
        // Coerce string value to integer; default is 10.
        return isset($this->_blockEntryField[self::BLOCK_ENTRY_COUNT])
                ? +($this->_blockEntryField[self::BLOCK_ENTRY_COUNT])
                : 10;
    }

    /**
     * Determines whether this set table supports block editing.
     *
     * @return bool   true if block editing is supported
     */
    public function supportsBlockEdit()
    {
        return ! empty($this->_blockEditFields);
    }

    /**
     * Gets the fields for which block editing is supported.
     *
     * Precondition:  supportsBlockEdit() returns true
     *
     * @return array   field names
     */
    public function getBlockEditFields()
    {
        return $this->_blockEditFields;
    }

    /**
     * Gets the Field object for the given field name (useful when 
     * processing user input based on field names).  Returns null if the 
     * given field name does not correspond to a field object.
     *
     * @param string $fieldName   name of the field
     * @return Field object
     */
    public function getFieldObject($fieldName)
    {
        if ( isset($this->_inTable[$fieldName]) )
            { return $this->_inTable[$fieldName]; }
        elseif ( isset($this->_expressions[$fieldName]) )
            { return $this->_expressions[$fieldName]; }
        elseif ( isset($this->_allImportFields[$fieldName]) )
            { return $this->_allImportFields[$fieldName]; }
        else
        {
            return null;
        }
    }

    /**
     * Gets the row(s) with values matching the data provided
     * in the parameter.
     *
     * @param array $data        Column-value pairs identifying entry to find
     * @param array $comparators Column-comp pairs identifying comparators
     *                           to use
     * @param bool $searchType   ANY or ALL
     * @return array             Column-value data for the found rows
     */
    public function getTableEntries(array $data = array(),
                                    $comparators = array(),
                                    $searchType = self::ALL)
    {
        // Remove column-value pairs with null values (unless field has 
        // a unary comparator and so is meaningful even without a value).
        foreach ( $data as $key => $value )
        {
            if ( $this->_fieldWithUnaryComparator($key, $comparators) )
            {
                continue;  // column is significant; check next column
            }
            if ( $value === null || $value == "" || $value == self::ANY_VAL )
            {
                unset($data[$key]);
            }
        }
// throw new Exception("after pruning null: " . print_r($data, true));

        // Construct SQL query.
        $db = $this->_dbModel->getAdapter();
        $select = $db->select()
                        ->from($this->_dbTableName,
                               $this->getLocalSelectObjects());
        if ( ! empty($this->_sortOrder) )
        {
            $select->order(array_map("trim", explode(',', $this->_sortOrder)));
        }
        foreach ( $this->_joinExpressions as $localName => $expression )
        {
            // Joined table and/or fields might have aliases; resolve these.
            // Then test that the table is in the database.
            $realName = isset($expression[self::ALIAS])
                              ? $expression[self::ALIAS]
                              : $localName;
            $table = isset($expression[self::ALIAS])
                              ? array($localName => $realName)
                              : $localName;
            try
            {
                $tempModel =
                    new Ramp_Table_DbTable_Table($realName);
                $cols = $tempModel->info(Zend_Db_Table_Abstract::COLS);
            }
            catch (Exception $e)
            {
                throw new Exception("Error: $realName is not a table in " .
                    "this database.");
            }

            // Add Join information to the query.
            $fields = $this->_importAliases[$localName];
            foreach ( $fields as $importedField )
            {
                if ( isset($this->_visible[$importedField]) &&
                     ! in_array($importedField, $cols) )
                {
                    throw new Exception("Error: $importedField is not a " .
                        "valid field in table $realName.");
                }
            }
            $select->joinLeft($table, $expression[self::CONNECTION], $fields);
        }

        // Construct SQL WHERE clause based on data provided.
        $whereMethod = ($searchType == self::ANY) ? "orwhere" : "where";
        foreach ( $data as $fieldName => $value )
        {
            // Skip expressions.
            if ( isset($this->_expressions[$fieldName]) )
                { continue; }

            // Create "condition with placeholder" => $vaue pair.
            $fieldObj = $this->getFieldObject($fieldName);
            if ( $fieldObj == null )
            {
                throw new Exception("Error: $fieldName is not a field in " .
                    "this setting (" . $this->_settingName . ").");
            }
            $realFieldName = $fieldObj->isImported() ?
                                $fieldObj->getImportTable() :
                                $this->_dbTableName;
            $realFieldName .= "." . $fieldObj->resolveAlias();

            $op = ( empty($comparators) || empty($comparators[$fieldName]) )
                        ? self::DEFAULT_COMPARATOR : $comparators[$fieldName];
            if ( $op == self::CONTAINS )
            {
                $op = self::LIKE;
                $value = "%$value%";
            }

            // Differentiate between comparisons with 0 or 1 arguments
            if ( $this->_isUnaryComparator($op) )
            {
                $select = $select->$whereMethod("$realFieldName " . $op);
            }
            else
            {
                $select = $select->$whereMethod("$realFieldName " . $op .
                                                " ?", $value);
            }

        }

// throw new Exception("query: " . print_r($select->__toString(), true));

        // Execute the query.
        try
        {
            $rows = $db->query($select)->fetchAll();
// throw new Exception("fetched data: " . print_r($rows, true));
            return $rows;
        }
        catch (Exception $e)
        {
            throw new Exception("Error: Invalid data request using table " .
                "setting " . $this->_settingName . "."
                . "<br/><br/>SQL Query was:<br/>" . $select->__toString()
            );
        }
    }

    /**
     * Gets the row with the information provided in the parameter.
     *
     * Precondition: this action should only be invoked when the 
     * information in the parameter uniquely identifies a single record.
     *
     * @param array $data     Column-value pairs identifying entry to find
     * @return array          The column-value data for the found row
     * @throws Exception if $data does not uniquely identify one table row
     */
    public function getTableEntry(array $data)
    {
        $rows = $this->getTableEntries($data);

        if ( count($rows) == 0 )
        {
            throw new Exception(
                'Could not find row in "' . $this->_dbTableName
                . '" table with field values ' . print_r($data, true));
        }
        elseif ( count($rows) > 1 )
        {
            throw new Exception("Error: information provided does not "
                . 'uniquely identify a single row in the "'
                . $this->_dbTableName . '" table. Values provided were: '
                .  print_r($data, true));
        }

        return $rows[0];
    }

    /**
     * Determines the status of the row indicated by the information 
     * provided in the parameter.  If there is no data corresponding to 
     * the $keys parameter, this function returns BLANK.
     * Otherwise, it returns PARTIAL or GOOD, depending on whether or 
     * not all recommended fields have been filled in.
     *
     * @param array $keys     Column-value pairs identifying entry to find
     */
    public function getStatusOfRecord(array $keys)
    {
        $rows = $this->getTableEntries($keys);

        if ( count($rows) == 0 )
        {
            return self::BLANK;
        }

        // Go through the recommended fields of each found record looking
        // for any that are not filled in.
        $allRecommendedFieldsProvided = true;
        foreach ( $rows as $record )
        {
            foreach ( $this->_inTable as $fieldName => $fieldObj )
            {
                if ( $fieldObj->isRecommended() &&
                     ( $record[$fieldName] == NULL ) )
                {
                    $allRecommendedFieldsProvided = false;
                }
            }
        }

        return $allRecommendedFieldsProvided ? self::GOOD : self::PARTIAL;
    }

    /**
     * Gets primary key information for the row identified by the fields 
     * provided in the parameter.
     *
     * Precondition: this action should only be invoked when the 
     * information in the parameter uniquely identifies a single record.
     *
     * @param array $searchFields  Column-value pairs
     * @return array       Column-value pairs of non-primary key information
     * @throws Exception if multiple rows match $searchFields
     */
    public function getKeyInfo(array $searchFields)
    {
        $foundData = $this->getTableEntry($searchFields);
        return $this->filterPrimaryKeyInfo($foundData, true);
    }

    /**
     * Gets "cloneable" fields, i.e., fields from this table that are 
     * not part of the primary key, for the row identified by the
     * fields provided in the parameter.
     *
     * Precondition: this action should only be invoked when the 
     * information in the parameter uniquely identifies a single record.
     *
     * @param array $searchFields  Column-value pairs
     * @return array       Column-value pairs of non-primary key information
     * @throws Exception if multiple rows match $searchFields
     */
    public function getCloneableFields(array $searchFields)
    {
        $foundData = $this->getTableEntry($searchFields);
        $foundData = $this->removeImportsAndExpressions($foundData);
        return $this->filterPrimaryKeyInfo($foundData, false);
    }

    /**
     * Filters out imported fields, returning a version of the parameter 
     * array that contains only fields in the "local" table.
     *
     * @param array $data  Column-value pairs of local and/or imported fields
     * @return array       Column-value pairs of local fields
     */
    public function removeImportsAndExpressions(array $data)
    {
        $retArray = array_diff_key($data, $this->_allImportFields);
        return array_diff_key($retArray, $this->_expressions);
    }

    /**
     * Adds a new table entry with the given data to the table.
     *
     * @param array $data   Column-value pairs.
     * @return mixed        The primary key of the row inserted.
     */
    public function addTableEntry(array $data)
    {
        // Check that user is not trying to insert data with a duplicate key.
        // TODO: Could this use Zend_Validate_Db_NoRecordExists instead?
        $pkInfo = $this->filterPrimaryKeyInfo($data, true);
        if ( ! empty($pkInfo) )
        {
            $rows = $this->getTableEntries($pkInfo);
            if ( count($rows) != 0 )
            {
                throw new Exception("Error: trying to insert a record "
                    . 'with a duplicate key into the "'
                    . $this->_dbTableName . '" table. Values provided were: '
                    .  print_r($data, true));
            }
        }

        $filtered = $this->removeImportsAndExpressions($data);
        return $this->_dbModel->insert($filtered);
    }

    /**
     * Updates the row with the primary key information provided
     * in the parameter.
     *
     * @param array $data     Column-value pairs identifying entry to update
     * @return int            The number of rows updated.
     * @throws Exception      If $data does not uniquely identify one row
     */
    public function updateTableEntry(array $data)
    {
        // Construct SQL WHERE clause that specifies entry being updated.
        $data = $this->_normalizeNulls($data);
        $where = $this->_constructUniquelyIdentifyingWhere($data);

        // Update the table.  (Will update 0 rows if no changes made.)
        $filtered = $this->removeImportsAndExpressions($data);
        $count = $this->_dbModel->update($filtered, $where);
        if ( $count > 1 )
        {
            throw new Exception("Error: Updated $count rows!");
        }
        return $count;
    }

    /**
     * Deletes the row with the primary key information provided
     * in the parameter.  If the array passed in also contains data
     * associated  with fields that are not primary keys, that
     * information is ignored.
     *
     * @param array $data     Column-value pairs identifying entry to update
     * @return int            The number of rows deleted.
     * @throws Exception      If $data does not uniquely identify one row
     */
    public function deleteTableEntry($data)
    {

        // Construct SQL WHERE clause that specifies entry being deleted.
        $where = $this->_constructUniquelyIdentifyingWhere($data);

        // Delete the entry.
        $count =  $this->_dbModel->delete($where);
        if ( $count != 1 )
        {
            throw new Exception("Error: Deleted $count rows!");
        }
        return $count;
    }

    /**
     * Gets primary (or non-primary) key information embedded in the
     * given data.  Whether the function returns the primary key 
     * information or the non-primary key information depends on the 
     * $primary parameter.
     *
     * @param array $data       Column-value pairs
     * @param boolean $primary  true if primary key information wanted
     * @return array            subset of $data containing only primary (or 
     *                          only non-primary) key info
     */
    public function filterPrimaryKeyInfo(array $data, $primary = true)
    {
        $pkInfo = array();
        $npkInfo = array();
        foreach ( $data as $fieldName => $value )
        {
            // Identify primary keys
            if ( array_key_exists($fieldName, $this->_keys) )
                { $pkInfo[$fieldName] = $value; }
            else
                { $npkInfo[$fieldName] = $value; }
        }

        return $primary ? $pkInfo : $npkInfo;
    }

    /**
     * Determines whether a field is significant, despite not having a 
     * value,  because it is tied to a unary comparator which does not 
     * need a value.
     */
    protected function _fieldWithUnaryComparator($field, $comparators)
    {
        return isset($comparators[$field]) &&
               $this->_isUnaryComparator($comparators[$field]);
    }

    /**
     * Determines whether a comparator is a unary comparator, which
     * does not need a value.
     */
    protected function _isUnaryComparator($comparator)
    {
        return in_array($comparator,
                        Ramp_Form_Table_TableRecordEntry::unaryComparators());
    }

    /**
     * Constructs a WHERE array based on primary key information
     * provided in the parameter to identify one row to modify.
     * If the array passed in also contains data associated 
     * with fields that are not primary keys, that information is 
     * ignored.
     *
     * @param array $data    Column-value pairs identifying entry to update
     * @return array         WHERE clause in array format
     * @throws Exception     If any keys not provided
     */
    protected function _constructUniquelyIdentifyingWhere(array $data)
    {
        // Construct SQL WHERE clause that specifies which entry is being 
        // updated based on the primary key column-value pairs.
        $filteredData = $this->filterPrimaryKeyInfo($data, true);
        if ( count($filteredData) != count($this->_keys) )
        {
            throw new Exception("Error: not all keys were provided " .
                "for an action that should affect only one table entry. " .
                "Keys provided were: " .
                print_r($filteredData, true) .
                "");
        }

        $where = array();
        foreach ( $filteredData as $fieldName => $value )
        {
            // Create "condition with placeholder" => $value pair.
            $cond = $this->_dbTableName . ".$fieldName = ?";
            $where[$cond] = $value;
        }
        return $where;
    }

    /**
     * Normalizes fields with nulls and empty strings to use SQL NULL keyword.
     */
    protected function _normalizeNulls(array $data)
    {
        foreach ( $data as $fieldName => $value )
        {
            if ( $value == '' )
            {
                $data[$fieldName] = NULL;
            }
        }
        return $data;
    }

    /**
     * Clears the error messages accumulated so far while
     * doing syntax checking for this setting.
     */
    public function clearErrorMsgs()
    {
        $this->_error_msgs = array();
    }

    /**
     * Gets the error messages accumulated when doing syntax checking.
     */
    public function getErrorMsgs()
    {
        return $this->_error_msgs;
    }

    /**
     * Checks the syntax of a table setting and its connections to
     * other tables.
     */
    public function summarizeSyntaxChecking()
    {
        $this->_recordErrors = true;
        $present = "<i class='icon-ok' title='present'> </i>";
        $absent = "<i class='icon-remove' title='present'> </i>";

        // Check that the table for this setting is a valid table.
        $localCols = $this->_testTable($this->_dbTableName, true);

        $this->_error_msgs[] = "title: " .
            ($this->_title ? $this->_title : $absent);
        $this->_error_msgs[] = "description: " .
            ($this->_description ? $this->_description : $absent);
        $this->_error_msgs[] = "tableFootnote: " .
            ($this->_tableFootnote ? $this->_tableFootnote : $absent);
        $this->_error_msgs[] = "sort order: " .
            ($this->_sortOrder ? $this->_sortOrder : $absent);
        $this->_error_msgs[] = "showColsByDefault: " .
            ($this->_showColsByDefault ? "true" : "false");

        // Check block entry information.
        $msg = "blockEntry";
        $blockEntryField = $this->_blockEntryField;
        if ( empty($blockEntryField) )
            { $msg .= ": not allowed"; }
        else
        {
            $fieldName = $blockEntryField[self::BLOCK_FIELD];
            $msg .= "." . self::BLOCK_FIELD . ":  $fieldName ";
            $msg .= in_array($fieldName, array_keys($this->_inTable))
                        ? $present
                        : "is not a local field";
        }
        $this->_error_msgs[] = $msg;
        if ( count($blockEntryField) > 2 ||
             ( count($blockEntryField) == 2 &&
             ! isset($blockEntryField[self::BLOCK_ENTRY_LABEL]) ) )
        {
            $this->_error_msgs[] = self::BLOCK_ENTRY . " has one or more " .
                "undefined sub-properties.";
        }

        // Check block edit information.
        $msg = "blockEdit";
        $blockEditField = $this->_blockEditField;
        if ( empty($blockEditField) )
            { $msg .= ": not allowed"; }
        else
        {
            $fieldName = $blockEditField[self::BLOCK_FIELD];
            $msg .= "." . self::BLOCK_FIELD . ":  $fieldName ";
            $msg .= in_array($fieldName, array_keys($this->_inTable))
                        ? $present
                        : "is not a local field";
        }
        /*
        $this->_error_msgs[] = $msg;
        if ( count($blockEditField) > 2 ||
             ( count($blockEditField) == 2 &&
             ! isset($blockEditField[self::BLOCK_ENTRY_LABEL]) ) )
        {
            $this->_error_msgs[] = self::BLOCK_ENTRY . " has one or more " .
                "undefined sub-properties.";
        }
         */

        // Check for extraneous settings.
        $this->_checkExtraneousSettings();

        // Check for invalid field names.
        $this->_error_msgs[] = "==> Checking for invalid fields... ";
        $prevNumErrorMsgs = count($this->_error_msgs);
        $this->_testFields($this->_undefinedFieldNames, $localCols,
                           $this->_dbTableName, "Local fields");
        foreach ( $this->_inTable as $fieldName => $field )
        {
            if ( $field->valueNecessaryForAdd() &&
                 ! isset($this->_visibleFields[$fieldName]) )
            {
                $this->_error_msgs[] = "$fieldName is required for ADD " .
                    "but not visible (OK if this setting not used for ADD).";
            }
        }
        if ( $prevNumErrorMsgs == count($this->_error_msgs) )
        {
            $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
        }

        // Check table connections, importedFrom clauses.
        if ( ! empty($this->_joinExpressions) )
        {
            foreach ( $this->_joinExpressions as $tableName => $expression )
            {
                $this->_error_msgs[] = "==> Checking table connections " .
                                       "for $tableName...";
                $prevNumErrorMsgs = count($this->_error_msgs);
                $cols = null;
                $prevErrMsgCount = count($this->_error_msgs);

                // Get table's real name (not alias).
                $realName = isset($expression[self::ALIAS])
                                  ? $expression[self::ALIAS] : $tableName;
                if ( $realName != $tableName )
                {
                    $this->_error_msgs[] =
                                    "$tableName is an alias for $realName.";
                }

                // Test if it is a valid table in the database.
                $cols = $this->_testTable($realName, true);
                if ( $prevNumErrorMsgs == count($this->_error_msgs) )
                {
                    $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
                }

                $this->_error_msgs[] = "==> Checking importFrom references... ";
                $prevNumErrorMsgs = count($this->_error_msgs);
                if ( empty($this->_importAliases[$tableName]) )
                {
                    $this->_error_msgs[] = "Table connection for $tableName " .
                        "has no imported fields.";
                }
                else
                {
                    // Test if field imports are valid.
                    $fieldAliases = $this->_importAliases[$tableName];
                    $aliasLocalNames = array_keys($fieldAliases);
                    $localFieldNames = array_keys($this->_visibleFields); 
                    $this->_testFields($aliasLocalNames, $localFieldNames,
                                       $this->_settingName . " setting",
                                       "Importing");
                    $this->_testFields($fieldAliases, $cols,
                                       $realName, "Importing");
                }
                if ( $prevNumErrorMsgs == count($this->_error_msgs) )
                {
                    $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
                }
            }
        }

        // Check initialize-from information.
        $this->_error_msgs[] =
                        "==> Checking initialization table references... ";
        $prevNumErrorMsgs = count($this->_error_msgs);
        if ( ! empty($this->_initTblRefs) )
        {
            $locFields = $this->_testExternalRefs($this->_initTblRefs,
                                                  "initTableRef");
            // Are the fields used for the init reference search required?
            foreach ( $locFields as $searchField )
            {
                $field = $this->getFieldObject($searchField);
                if ( $field == null )
                {
                    throw new Exception("Error: $searchField is not a field " .
                        "in this setting (" . $this->_settingName . ").");
                }
                if ( ! $field->valueNecessaryForAdd() )
                {
                    $this->_error_msgs[] = "$searchField is required for " .
                        "initialization of other fields but has not been " .
                        "marked 'required'.  (OK if this setting not used " .
                        "for ADD).";
                }
            }
        }
        if ( $prevNumErrorMsgs == count($this->_error_msgs) )
        {
            $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
        }
        $this->_error_msgs[] = "==> Checking initFrom references... ";
        $prevNumErrorMsgs = count($this->_error_msgs);
        foreach ( $this->_fieldsInitFromElsewhere as $fieldName => $field )
        {
            // Determine table name & field.  Are they valid?
            $sourceTblName = $field->getInitTableName();
            $sourceFieldName = $field->getInitField();
            $cols = $this->_testTable($sourceTblName, true);
            $this->_testFields(array($sourceFieldName), $cols, $sourceTblName,
                                     "Initialization");
            // Is there a relevant initTableReference property for the field?
            $initRef = $this->getInitRefInfo($sourceTblName);
            if ( $initRef == null )
            {
                $this->_error_msgs[] =
                    "There is no 'initTableRef` information provided " .
                    "for $sourceFieldName" . ".initFrom = $sourceTblName";
            }
        }
        if ( $prevNumErrorMsgs == count($this->_error_msgs) )
        {
            $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
        }

        // Check external references.
        $this->_error_msgs[] = "==> Checking external table references... ";
        $prevNumErrorMsgs = count($this->_error_msgs);
        if ( ! empty($this->_fieldsInitFromElsewhere) )
        {
            $this->_testExternalRefs($this->_externalTblRefs,
                                     "externalTableRef");
        }
        if ( $prevNumErrorMsgs == count($this->_error_msgs) )
        {
            $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
        }

        // Check selectFrom properties.
        $this->_error_msgs[] = "==> Checking selectFrom properties... ";
        $prevNumErrorMsgs = count($this->_error_msgs);
        $allFields = $this->_keys + $this->_visibleFields;
        foreach ( $allFields as $field )
        {
            if ( $field->validValsDefinedInExtTable() )
            {
                try
                    { $field->getValidVals(); }
                catch (Exception $e)
                {
                    $this->_error_msgs[] = "selectFrom: " .
                                           $e->getMessage();
                }
            }
        }
        if ( $prevNumErrorMsgs == count($this->_error_msgs) )
        {
            $this->_error_msgs[] = "&nbsp;&nbsp;&nbsp;&nbsp;$present";
        }

    }

    /**
     * Tests that the given table name represents an actual table in the 
     * database.  Returns the columns for that table if so.  Throws an 
     * exception or adds an error message to the _error_msgs instance
     * variable and returns an empty array if the table was invalid.
     */
    protected function _testTable($tableName, $recordErrors = false)
    {
        $cols = array();
        try
        {
            $tempModel = new Ramp_Table_DbTable_Table($tableName);
            $cols = $tempModel->info(Zend_Db_Table_Abstract::COLS);
        }
        catch (Exception $e)
        {
            // Could be used by getTableEntries in future, so allow the 
            // possibility of throwing an exception.
            if ( $recordErrors )
            {
                $tableName = $tableName ? : "[no name]";
                $this->_error_msgs[] = "Error: $tableName is not a table " .
                    "in this database.";
            }
            else
                { throw new Exception($e->getMessage()); }
        }

        return $cols;
    }

    /**
     * Tests that all the fields in the given array of fields are
     * represented in the array of actual table columns.
     */
    protected function _testFields($fieldNames, $tableCols,
                                   $tableName, $prefix)
    {
        foreach ( $fieldNames as $fieldToCheck )
        {
            if ( ! in_array($fieldToCheck, $tableCols) )
            {
                $this->_error_msgs[] = "$prefix: $fieldToCheck is " .
                    "not a valid visible field in $tableName.";
            }
        }
    }

    /**
     * Tests a set of external references (for initializing data from or 
     * for establishing links).
     */
    protected function _testExternalRefs($references, $type)
    {
        $allLocalConnFields = array();
        foreach ( $references as $keyword => $refInfo )
        {
            try
            {
                $sequence = $refInfo->getViewingSeq();
                $setting = $sequence->getSetTableForAdding();
            }
            catch (Exception $e)
            {
                $seqName = $refInfo->getViewingSeqName();
                $this->_error_msgs[] = "Error: $seqName is not a valid " .
                    "table setting/sequence file.";
                return;
            }
            $tableName = $setting->getDbTableName();
            $cols = $this->_testTable($tableName, true);
            $connections = $refInfo->getConnectionExpressions();
            $localConnectionFields = array_keys($connections);
            $allLocalConnFields = array_merge($allLocalConnFields,
                                              $localConnectionFields);
            $allLocalFieldNames = array_keys($this->_visibleFields); 
            $this->_testFields($localConnectionFields, $allLocalFieldNames,
                               $this->_settingName . " setting", $type);
            $this->_testFields($connections, $cols, $tableName, $type);
        }
        return $allLocalConnFields;
    }

    protected function _checkExtraneousSettings()
    {
        $validProps = $this->validTableProps();
        foreach ( $this->_origSettings as $propName => $prop )
        {
            if ( ! in_array($propName, $validProps) )
            {
                $this->_error_msgs[] = "$propName is an unused " .
                                       "setting property.";
            }
        }
    }

}

