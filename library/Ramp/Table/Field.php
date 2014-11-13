<?php

/*
 * FYI: Meta Info contains: 
 *      [SCHEMA_NAME]
 *      [TABLE_NAME]
 *      [COLUMN_NAME]
 *      [COLUMN_POSITION]
 *      [DATA_TYPE]
 *      [DEFAULT]
 *      [NULLABLE]
 *      [LENGTH]
 *      [SCALE]
 *      [PRECISION]
 *      [UNSIGNED]
 *      [PRIMARY]
 *      [PRIMARY_POSITION]
 *      [IDENTITY]
 */

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

class Ramp_Table_Field
{

    // Constants representing field setting properties.
    const HIDE          = 'hide';
    const LABEL         = 'label';
    const FOOTNOTE      = 'footnote';
    const READ_ONLY     = 'readOnly';
    const RECOMMENDED   = 'recommended';
    const DISCOURAGED   = 'discouraged';
    const EXPR          = 'expression';
    const INIT_TBL      = 'initFrom';
    const INIT_FIELD    = 'initFromField';
    const IMPORTED      = 'importedFrom';
    const ALIAS         = 'importedField';
    const LEGAL_VALUES  = 'selectFrom';
    const LINK_TO_TBL   = 'selectUsing';
    const DISPLAY_IN_ROW = 'displayInRow';
    const SUPPRESS_IF_SAME   = 'suppressIfSame';

    // Constants representing db metadata properties.
    const NULLABLE      = 'NULLABLE';
    const PRIMARY       = 'PRIMARY';
    const DB_DEFAULT    = 'DEFAULT';
    const IDENTITY      = 'IDENTITY';
    const LENGTH        = 'LENGTH';

    // Constants to provide internal data structure.
    const VALID_VAL_TBL     = 'table';
    const VALID_VAL_FIELD   = 'field';

    /** @var string */
    protected $_name;           // field name

    /** @var string */
    protected $_label;          // field label

    /** @var string */
    protected $_footnote;       // field footnote

    /** @var bool */
    protected $_visible;        // Should the field be visible?

    /** @var bool */
    protected $_readOnly;       // Should the field be read-only?

    /** @var bool */
    protected $_recommended = false;  // suggestion that a value should be
                                      // provided (informational)

    /** @var bool */
    protected $_discouraged = false;  // suggestion that a value should be
                                      // left alone (informational)

    /** @var bool */
    protected $_inTable;        // Is the field in the database table?

    /** @var string */
    protected $_expression;     // expression

    /** @var string */
    protected $_initTable;      // name of setting to use to initialize field

    /** @var string */
    protected $_initField;      // name of field from which to initialize

    /** @var string */
    protected $_importTbl;      // name of table from which imported, or null

    /** @var string */
    protected $_importName;     // alias name for field being imported
                                // (name in the import table)

    /** @var string */
    protected $_legalValsSource; // table and field that contains legal values

    /** @var string */
    protected $_legalVals;       // legal values from an external table

    /** @var string */
    protected $_connectTbl;     // name of table to which this field is a link

    /** @var bool */
    protected $_suppressIfSame = false; // report display: don't repeat field
                                        // value unless diff. from prev. row

    /** @var bool */
    protected $_alwaysDisplayInRow = false;   // report display: never treat as
                                        // shared value in split views

    /** @var array */
    protected $_metaInfo;       // meta-information provided by database

    /**
     * Class constructor
     *
     * Creates an object that represents all the information known about 
     * a field.
     *
     * @param string $fieldName     the name of the field
     * @param array $settingInfo    field information from the table setting
     * @param array $metaInfo       field information from the database
     * @param bool $showColsByDefault true if the table setting 
     *                                specified showing columns by default
     */
    public function __construct($fieldName,
                                array $settingInfo = array(),
                                array $metaInfo = array(),
                                $showColsByDefault = false)
    {
        $this->_name = $fieldName;
        $this->_metaInfo = $metaInfo;

        $this->_init($settingInfo, $metaInfo, $showColsByDefault);
    }

    /**
     * Initializes field attributes from information provided by the 
     * table setting.
     *
     * @param array $settingInfo    field information from the table setting
     * @param array $metaInfo       field information from the database
     * @param bool $showColsByDefault true if the table setting 
     *                                specified showing columns by default
     * @return Ramp_Table_Field
     */
    protected function _init(array $settingInfo, array $metaInfo,
                         $showColsByDefault)
    {

        // Initialize attributes.  If no label is provided, use the 
        // column name from the database.  A column is assumed to be 
        // visible (not hidden) if a label is provided for it or if the 
        // table setting says to show all columns by default, although
        // even then a column can be explicitly marked as hidden.
        $this->_label = isset($settingInfo[self::LABEL]) ?
                            $settingInfo[self::LABEL] :
                            $this->_name;
        $this->_footnote = isset($settingInfo[self::FOOTNOTE]) ?
                            $settingInfo[self::FOOTNOTE] :
                            "";
        $this->_readOnly = isset($settingInfo[self::READ_ONLY]) &&
                            $settingInfo[self::READ_ONLY] == true;
        $explicitly_visible = isset($settingInfo[self::HIDE]) &&
                            $settingInfo[self::HIDE] == false;
        $assumed_visible = isset($settingInfo[self::LABEL]) ||
                            $showColsByDefault;
        $explicitly_hidden = isset($settingInfo[self::HIDE]) &&
                            $settingInfo[self::HIDE] == true;
        $this->_visible = $explicitly_visible ||
                          ($assumed_visible && ! $explicitly_hidden);
        $this->_recommended = isset($settingInfo[self::RECOMMENDED]) &&
                              $settingInfo[self::RECOMMENDED];
        $this->_discouraged = isset($settingInfo[self::DISCOURAGED]) &&
                              $settingInfo[self::DISCOURAGED];
        if ( isset($settingInfo[self::EXPR]) )
        {
            $this->_expression = '(' . $settingInfo[self::EXPR] . ')';
            $this->_readOnly = true;
        }
        else
            { $this->_expression = null; }
        $this->_initTable = isset($settingInfo[self::INIT_TBL]) ?
                            $settingInfo[self::INIT_TBL] :
                            null;
        $this->_initField = isset($settingInfo[self::INIT_FIELD]) ?
                            $settingInfo[self::INIT_FIELD] :
                            $this->_name;
        $this->_importTbl = isset($settingInfo[self::IMPORTED]) ?
                            $settingInfo[self::IMPORTED] :
                            null;
        $this->_importName = isset($settingInfo[self::ALIAS]) ?
                            $settingInfo[self::ALIAS] :
                            $this->_name;
        $this->_connectTbl = isset($settingInfo[self::LINK_TO_TBL]) ?
                            $settingInfo[self::LINK_TO_TBL] :
                            null;
        $this->_legalValsSource = isset($settingInfo[self::LEGAL_VALUES]) ?
                            $settingInfo[self::LEGAL_VALUES] :
                            null;
        $this->_suppressIfSame = isset($settingInfo[self::SUPPRESS_IF_SAME]) &&
                              $settingInfo[self::SUPPRESS_IF_SAME];
        $this->_alwaysDisplayInRow = isset($settingInfo[self::DISPLAY_IN_ROW])
                              && $settingInfo[self::DISPLAY_IN_ROW];
        $this->_inTable = ! empty($metaInfo);

        return $this;
    }

    /**
     * Sets the visibility of the field.
     *
     * @param boolean $visibility  true if field should be visible; 
     *                             false otherwise
     */
    public function setVisibility($visibility)
    {
        $this->_visible = $visibility;
    }

    /**
     * Gets the name of the field in the database.
     *
     * return string    field name
     */
    public function getDbFieldName()
    {
        return $this->_name;
    }

    /**
     * Returns true if the field is defined as an enumerated type in the 
     * database; false otherwise.
     *
     * @return 
     */
    public function isEnum()
    {
        return $this->_inTable &&
               substr($this->_metaInfo['DATA_TYPE'], 0, 4) == "enum";
    }

    /**
     * Returns the set of valid values for this enumerated type.
     *
     * Precondition: isEnum() is true
     *
     * @return 
     */
    public function getEnumValues()
    {
        $options = array();

        // Trim away "enum(" and the trailing ")".
        $valueString = $this->_metaInfo['DATA_TYPE'];
        $valueString = substr($valueString, 5, strlen($valueString) - 6);
        $optionNames = explode(",", $valueString);

        // Trim quotes off the values and add to options array.
        foreach ( $optionNames as $optionName )
        {
            $optionName = substr($optionName, 1, strlen($optionName) - 2);
            $options[$optionName] = $optionName;
        }
        return $options;
    }

    /**
     * Returns the data type for this field, if the field is in the 
     * "local" table.  If the field is imported, returns String as the
     * best default for Text input boxes.
     *
     * @return 
     */
    public function getDataType()
    {
        return ( $this->_inTable ) ?  $this->_metaInfo['DATA_TYPE'] : null;
    }

    /**
     * Is this field present in the database table?
     *
     * @return bool
     */
    public function isInTable()
    {
        return $this->_inTable;
    }

    /**
     * Is this an expression rather than a field?
     *
     * @return bool
     */
    public function isExpression()
    {
        return $this->_expression !== null;
    }

    /**
     * Get the expression from which this 'field' should be set.
     *
     * @return string  the expression to evaluate
     */
    public function getExpression()
    {
        return $this->_expression;
    }

    /**
     * Should this field be initialized from another database table?
     *
     * @return bool
     */
    public function initFromAnotherTable()
    {
        return $this->_inTable && $this->_initTable !== null;
    }

    /**
     * Get the name of the database table from which this field
     * should be initialized.
     *
     * @return string  name of database table
     */
    public function getInitTableName()
    {
        return $this->_initTable;
    }

    /**
     * Get the database field from which this field should be initialized.
     *
     * Precondition: $this->initFromAnotherTable()
     *
     * @return string  name of field in other table
     */
    public function getInitField()
    {
        return $this->_initField;
    }

    /**
     * Is this field imported from another database table?
     *
     * @return bool
     */
    public function isImported()
    {
        return $this->_importTbl !== null;
    }

    /**
     * Get the database table from which this field is imported.
     *
     * @return string  name of database table (null if !$this->isImported)
     */
    public function getImportTable()
    {
        return $this->_importTbl;
    }

    /**
     * Gets the name to use when importing this field from another 
     * table.
     *
     * Precondition: $this->isImported()
     *
     * @return string  name of field in other table
     */
    public function resolveAlias()
    {
        return $this->_importName;
    }

    /**
     * Is this field in the database, whether in the current table or
     * imported from another table?
     *
     * @return bool
     */
    public function isInDB()
    {
        return $this->_inTable || $this->_importTbl;
    }

    /**
     * Are the valid values for this field defined in another table?
     *
     * @return bool
     */
    public function validValsDefinedInExtTable()
    {
        return ! empty($this->_legalValsSource);
    }

    /**
     * Returns the table that is the source of valid values for this field.
     *
     * Precondition: $this->validValsDefinedInExtTable()
     *
     * @return string  tableName
     */
    public function getSourceOfValidVals()
    {
        return Ramp_Table_DbTable_ValidValuesSource::
                                        getTableIn($this->_legalValsSource);
    }

    /**
     * Returns the set of valid values for this field, obtained from
     * an external table.
     *
     * Precondition: $this->validValsDefinedInExtTable()
     *
     * @return array   list of valid values
     */
    public function getValidVals()
    {
        return Ramp_Table_DbTable_ValidValuesSource::
                                getValidVals($this->_legalValsSource);
    }

    /**
     * Is this a local field that links to information in another
     * database table?
     *
     * @return bool
     */
    public function isExternalTableLink()
    {
        return $this->_connectTbl !== null;
    }

    /**
     * Get the title of the database table to which this field is a link.
     *
     * Precondition: $this->isExternalTableLink()
     *
     * @return string  name of database table
     */
    public function getLinkedTableTitle()
    {
        $extTableSetting = Ramp_Table_TVSFactory::getSequenceOrSetting(
                                    $this->_connectTbl);
        return $extTableSetting->getSetTableForSearching()->getTitle();
    }

    /**
     * Get the database table setting to which this field is a link.
     *
     * Precondition: $this->isExternalTableLink()
     *
     * @return string  name of database table
     */
    public function getLinkedTableSetting()
    {
        return $this->_connectTbl;
    }

    /**
     * Should this field always be read-only, according to the table
     * setting?
     *
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * Should this field be visible, according to the table setting?
     * Fields are visible by default when a label is provided in the 
     * table setting (unless explicitly marked as hidden), and also when 
     * marked as visible (hidden is false) in the setting, even if no 
     * label is provided.
     *
     * @return bool
     */
    public function isVisible()
    {
        return $this->_visible;
    }

    /**
     * Gets the label or column heading for this field from the table 
     * setting.  If no label was provided, returns the name from the database.
     *
     * return string    field label
     */
    public function getLabel()
    {
        return $this->_label;
    }

    /**
     * Gets the field footnote (if any) from the table setting.  If no 
     * footnote was provided, returns an empty string.
     *
     * return string    table footnote
     */
    public function getFieldFootnote()
    {
        return $this->_footnote;
    }

    /**
     * Returns the meta information for this field.
     *
     * @return 
     */
    public function getMetaInfo()
    {
        return $this->_metaInfo;
    }

    /**
     * Returns the length of this field (only meaningful for some data 
     * types, such as varchar).
     *
     * TODO: CHECK THIS!  The length in _metaInfo appears to be a string 
     * value, not an int, but forms/TableRecordEntry expects an int!
     *
     * @return int
     */
    public function getLength()
    {
        return ( $this->_inTable && $this->_metaInfo[self::LENGTH] ) ?
                     $this->_metaInfo[self::LENGTH] : NULL;
    }

    /**
     * Is this field required by the database?
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_inTable && ! $this->_metaInfo[self::NULLABLE];
    }

    /**
     * Is providing data for this field recommended (even if the field 
     * is not required by the database)?
     *
     * @return bool
     */
    public function isRecommended()
    {
        return $this->_inTable && $this->_recommended;
    }

    /**
     * Is providing or modifying data for this field discouraged (usually
     * because it is best set automatically by defaults, triggers, etc.)?
     *
     * @return bool
     */
    public function isDiscouraged()
    {
        return $this->_inTable && $this->_discouraged;
    }

    /**
     * Is this field a primary key?
     *
     * @return bool
     */
    public function isPrimaryKey()
    {
        return $this->_inTable && $this->_metaInfo[self::PRIMARY];
    }

    /**
     * Is this field auto-incrementable?
     *
     * @return bool
     */
    public function isAutoIncremented()
    {
        return $this->_inTable && $this->_metaInfo[self::IDENTITY];
    }

    /**
     * Gets the default provided in the database for this field (if 
     * any).
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->_inTable ? $this->_metaInfo[self::DB_DEFAULT] : null;
    }

    /**
     * Can entries be added to the table even if this field is not
     * provided?
     *
     * @return bool
     */
    public function valueNecessaryForAdd()
    {
        return ( $this->isPrimaryKey() || $this->isRequired() )
               && $this->getDefault() == null
               && ! $this->isAutoIncremented()
               && ! $this->initFromAnotherTable();
    }

    /**
     * In reports, should this field be suppressed if its value is the
     * same as in the previous row?
     *
     * @return bool
     */
    public function suppressIfSame()
    {
        return $this->_suppressIfSame;
    }

    /**
     * In reports, should this field always be repeated, even if its 
     * value is the same as that in all other rows?  (Relevant to split 
     * views.)
     *
     * @return bool
     */
    public function alwaysDisplayInRow()
    {
        return $this->_alwaysDisplayInRow;
    }

}
