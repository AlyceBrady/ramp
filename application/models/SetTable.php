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
 * @version    $Id: Application_Model_SetTable.php 1 2012-07-12 alyce $
 *
 */

class Application_Model_SetTable
{
    // Constants representing field setting properties
    const SETTING_NAME          = 'settingName';
    const SCHEMA                = 'schema';
    const TABLE_NAME            = 'tableName';
    const TABLE_QUERY_CONSTRAINT= 'tableQueryConstraint';
    const TITLE                 = 'tableTitle';
    const DESCRIPTION           = 'tableDescription';
    const TABLE_FOOTNOTE        = 'tableFootnote';
    const SHOW_COLS_BY_DEFAULT  = 'tableShowColsByDefault';
    const FIELDS                = 'field';
    const CONNECTED_TBL         = 'tableConnection';
    const CONNECTION            = 'connection';
    const ALIAS                 = 'aliasFor';
    const INIT_TBL              = 'initTableRef';
    const EXTERNAL_TBL          = 'externalTableRef';

    // Constant representing an unspecified enum value for a search
    const ANY_VAL               = '__any_search_value__';

    // Constants representing search types
    const ANY                   = 'any';
    const ALL                   = 'all';
    const EXCLUDE               = 'exclude';

    // Sub-properties for initialization and external table references
    const TBL_REF_TITLE         = 'title';
    const TBL_REF_VIEWING_SEQUENCE = 'viewingSequence';

    // Status codes used for communicating whether the table has all 
    // recommended fields for a particular record.
    const UNKNOWN   = 'unknown';
    const BLANK     = 'blank';
    const PARTIAL   = 'partial';
    const GOOD      = 'sufficient';

    /** @var string */
    protected $_dbTableName;        // name of table in the actual database

    /** @var string */
    protected $_joinExpressions;    // join expressions to other tables

    /** @var string */
    protected $_initTblRefs;        // initialization table references

    /** @var string */
    protected $_externalTblRefs;    // external table refs that look like fields

    /** @var string */
    protected $_settingName;        // name of table setting

    /** @var Application_Model_Db_Table_Table */
    protected $_dbModel;              // model associated with db table

    /** @var string */
    protected $_tableQueryConstraint; // limits fields that can satisfy setting

    /** @var string */
    protected $_title;              // table title

    /** @var string */
    protected $_description;        // table description

    /** @var string */
    protected $_tableFootnote;      // table footnote

    /** @var bool */
    protected $_showColsByDefault;  // show all fields unless hidden?

    /** @var array */
    protected $_inTable = array();  // all fields in local database table

    /** @var array */
    protected $_allImportFields = array(); // fields from other table(s)

    /** @var array */
    protected $_importAliases = array();    // aliases for fields from other
                                            // table(s), by table

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
    protected $_externalInits = array();        // fields initialized from
                                                // external sources

    /** @var string */
    // protected $_singleRecordDisplayAction; // how to display single records

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
     * @return void
     */
    public function __construct($settingName, $propGateway)
    {
        // Get the table setting properties.
        $settingProps = $propGateway->getSettingProps($settingName);

        // Make sure that the setting properties include a table name
        // (min. requirement)
        if ( ! array_key_exists(self::TABLE_NAME, $settingProps) )
        {
            throw new Exception($settingName .
                                " setting must include a key for '" .
                                self::TABLE_NAME .
                                "' that names the database table to use.");
        }
        $this->_dbTableName = $settingProps[self::TABLE_NAME];
        $this->_settingName = $settingName;
        $this->_dbModel =
                    new Application_Model_DbTable_Table($this->_dbTableName);

        $settingsAsArray = ( $settingProps instanceof Zend_Config ) ?
                                   $settingProps->toArray() :
                                   $settingProps;
        $this->_init($settingsAsArray,
                    $this->_dbModel->info(Zend_Db_Table_Abstract::METADATA));

    }

    /**
     * Initializes table attributes from information provided by the 
     * table setting and the database.
     *
     * @param array $settingInfo    information from the table setting
     * @param array $allColMetaInfo meta information from db for all fields
     * @return Application_Model_SetTable
     */
    protected function _init(array $settingInfo, array $allColMetaInfo)
    {

        // Initialize remaining table attributes.
        $this->_tableQueryConstraint =
                    isset($settingInfo[self::TABLE_QUERY_CONSTRAINT]) ?
                            $settingInfo[self::TABLE_QUERY_CONSTRAINT] :
                            "";
        $this->_title = isset($settingInfo[self::TITLE]) ?
                            $settingInfo[self::TITLE] :
                            $this->_dbTableName;
        $this->_description = isset($settingInfo[self::DESCRIPTION]) ?
                            $settingInfo[self::DESCRIPTION] :
                            "";
        $this->_tableFootnote = isset($settingInfo[self::TABLE_FOOTNOTE]) ?
                            $settingInfo[self::TABLE_FOOTNOTE] :
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
        $references = isset($settingInfo[self::INIT_TBL]) ?
                            $settingInfo[self::INIT_TBL] :
                            array();
        $this->_initTblRefs = $this->_initReferences($references);

        // Initialize references to external, related tables.
        $references = isset($settingInfo[self::EXTERNAL_TBL]) ?
                            $settingInfo[self::EXTERNAL_TBL] :
                            array();
        $this->_externalTblRefs = $this->_initReferences($references);

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
     * or an abbreviated format:
     *   tableConnection.ExtTable = "LocalTbl.field = ExtTable.field"
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
        // Verify that allConnections is an array.
        if ( ! is_array($allConnections) )
        {
            throw new Exception("Table connection (" .
                self::CONNECTED_TBL . " = \"" . $allConnections .
                "\") does not specify table; correct format is " .
                self::CONNECTED_TBL .
                ".ExtTable = \"LocalTbl.field = ExtTable.field\"");
        }

        // Put all connections in fully-qualified format.
        $this->_joinExpressions = array();
        foreach ( $allConnections as $table => $connection )
        {
            if ( is_array($connection) )    // fully-qualified format
            {
                if ( ! isset($connection[self::CONNECTION]) )
                {
                    throw new Exception("Cannot create table connection " .
                        "for " . $table . " without a " .
                        self::CONNECTION . " property.");
                }
                $this->_joinExpressions[$table] = $connection;
            }
            else        // abbreviated format
            {
                $this->_joinExpressions[$table][self::CONNECTION] = $connection;
            }
            $this->_importAliases[$table] = array();
        }
    }

    /**
     * Initializes references to an external table.
     * @see Application_Model_ExternalTableReference
     *
     * @return array of Application_Model_ExternalTableReference objects
     * @throws Exception if reference information is badly formatted
     */
    protected function _initReferences($referenceInfo)
    {
        $refObjects = array();
        if ( ! is_array($referenceInfo) )
        {
            throw new Exception("Initialization or external table " .
                "reference requires sub-properties.");
        }
        foreach ( $referenceInfo as $tableName => $refInfo )
        {
            $refObjects[$tableName] =
                new Application_Model_ExternalTableReference(
                        $this->_dbTableName, $refInfo, $this->_settingName);
        }
        return $refObjects;
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
            $field = new Application_Model_Field($colName, 
                                                 $fieldSettings,
                                                 $metaInfo,
                                                 $this->_showColsByDefault);

            // Add field to appropriate field lists.
            if ( $field->isinDb() )
                { $this->_categorizeField($colName, $field); }
            else
                { $this->_undefinedFieldNames[] = $colName; }
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
        else  // imported
        {
            $table = $field->getImportTable();
            if ( ! isset($this->_importAliases[$table]) )
            {
                throw new Exception("Cannot import '$name' from " .
                    "'$table' table; there is no '" .
                    self::CONNECTED_TBL .
                    "' clause for '$table.'");
            }
            $this->_importAliases[$table][$name] = $field->resolveAlias();
            $this->_allImportFields[$name] = $field;
        }

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
            { $this->_externalInits[$name] = $field; }

    }

    /**
     * Gets the name of the underlying table.
     */
    public function getDbTableName()
    {
        return $this->_dbTableName;
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
     * from the database, etc.) for all field defined in the database.
     * Fields with no table setting information will be marked as 
     * hidden (the isVisible method returns false) unless the table 
     * setting specifies to show all fields by default.  In that case,
     * the field name from the database is returned as the label.
     *
     * @return array    array of Application_Model_Field objects
     */
    public function getFields()
    {
        return $this->_inTable + $this->_allImportFields;
    }

    /**
     * Gets the fields that are links to other tables, as
     * fieldname => table pairs.
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
     * @return array    array of Application_Model_Field objects
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
     * @return array    array of Application_Model_Field objects
     */
    public function getPrimaryKeys()
    {
        return $this->_keys;
    }

    /**
     * Gets field information (labels, footnotes, meta information 
     * from the database, etc.) for all fields relevant to a table or 
     * record display, including primary keys and other, non-hidden 
     * fields.
     * If the table setting specifies to show all fields by default, 
     * then even fields that were not included in the table setting 
     * will be included.  In that case, the field names from the 
     * database will be used as field labels/headings.
     *
     * @return array    array of Application_Model_Field objects
     */
    public function getRelevantFields()
    {
        return $this->_visibleFields + $this->getPrimaryKeys();
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
     * @return array    array of Application_Model_Field objects
     */
    public function getLocalRelevantFields()
    {
        return array_diff_key($this->_visibleFields + $this->getPrimaryKeys(),
                              $this->_allImportFields);
    }

    /**
     * Gets an array of all defaults provided for this table.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->_defaults;
    }

    /**
     * Gets an array of all fields in this table that should be 
     * initialized from external sources.
     *
     * @return array
     */
    public function getExternallyInitFields()
    {
        return $this->_externalInits;
    }

    /**
     * Gets the initialization reference information for a particular
     * table name.
     *
     */
    public function getInitRefInfo($tableName)
    {
        return $this->_initTblRefs[$tableName];
    }

    /**
     * Gets the list of references to external tables related to this 
     * table (tables with mapping relationships to this table).
     *
     */
    public function getExtTableReferences()
    {
        return $this->_externalTblRefs;
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
     * @param array $data      Column-value pairs identifying entry to find
     * @param bool $searchType ANY, ALL, or EXCLUDE
     * @return array           Column-value data for the found rows
     */
    public function getTableEntries(array $data = array(),
                                    $searchType = self::ALL)
    {
        // Remove column-value pairs with null values.
        foreach ( $data as $key => $value )
        {
            if ( $value === null || $value == "" || $value == self::ANY_VAL )
            {
                unset($data[$key]);
            }
        }

        // Construct SQL query.
        $db = $this->_dbModel->getAdapter();
        $select = $db->select()
                        ->from($this->_dbTableName,
                               array_keys($this->getLocalRelevantFields()));
        foreach ( $this->_joinExpressions as $localName => $expression )
        {
            // Joined table and/or fields might have aliases; resolve these.
            $table = isset($expression[self::ALIAS]) ?
                           array($localName => $expression[self::ALIAS]) :
                           $localName;
            $fields = $this->_importAliases[$localName];
            $select->joinLeft($table, $expression[self::CONNECTION], $fields);
        }

        // Construct SQL WHERE clause based on data provided.
        /* TODO: Support constraints on a query.
            if ( $this->_tableQueryConstraint != "" )
            {
                // TODO: Quote the components of this constraint.
                // $select = $select->where($this->_tableQueryConstraint);
            }
         */
        $method = ($searchType == self::ANY) ? "orwhere" : "where";
        $op = ($searchType == self::EXCLUDE) ? "<>" : "=";
        // $delim =  "";
        // $whereClause = "";
        foreach ( $data as $fieldName => $value )
        {
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
            $select = $select->$method("$realFieldName " . $op . " ?", $value);
            /*  // Need something like the following to support table query 
                // constraints.
                $whereClause .= $delim . $realFieldName . " = " .
                                            $db->quoteIdentifier($value);
                $delim =  $any ? " OR " : " AND ";
            */
        }
        /* For table query constraints:  $select = $select->where($whereClause); */

        // Execute the query.
// throw new Exception("query: " . print_r($select->__toString(), true));
        $rows = $db->query($select)->fetchAll();
        return $rows;
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
     * the $keys parameter, or if it does not uniquely identify a single 
     * row, then this function returns BLANK or UNKNOWN, respectively.
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
        return $this->filterPrimaryKeyInfo($foundData, false);
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

        return $this->_dbModel->insert($this->_removeImports($data));
    }

    /**
     * Updates the row(s) with the primary key information provided
     * in the parameter.
     *
     * @param array $data     Column-value pairs identifying entry to update
     * @return int            The number of rows updated.
     */
    public function updateTableEntry(array $data)
    {
        // Construct SQL WHERE clause that specifies entry being updated.
        $where = $this->_constructUniquelyIdentifyingWhere($data);

        // Update the table.  (Will update 0 rows if no changes made.)
        $count = $this->_dbModel->update($this->_removeImports($data), $where);
        if ( $count > 1 )
        {
            throw new Exception("Error: Updated $count rows!");
        }
        return $count;
    }

    /**
     * Deletes the row(s) with the primary key information provided
     * in the parameter.  If the array passed in also contains data
     * associated  with fields that are not primary keys, that
     * information is ignored.
     *
     * @param array $data     Column-value pairs identifying entry to update
     * @return int            The number of rows deleted.
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
            if ( in_array($fieldName, array_keys($this->_keys)) )
                { $pkInfo[$fieldName] = $value; }
            else
                { $npkInfo[$fieldName] = $value; }
        }

        return $primary ? $pkInfo : $npkInfo;
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
     * Filters out imported fields, returning a version of the parameter 
     * array that contains only fields in the "local" table.
     *
     * @param array $data  Column-value pairs of local and/or imported fields
     * @return array       Column-value pairs of local fields
     */
    protected function _removeImports(array $data)
    {
        return array_diff_key($data, $this->_allImportFields);
    }

}

