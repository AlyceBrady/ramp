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
 * @copyright  Copyright (c) 2013 Alyce Brady
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */
class Ramp_Table_DbTable_ValidValuesSource extends Zend_Db_Table_Abstract
{
    protected $_validValues = array();

    /**
     * Returns the table embedded in the "table.field" format, or
     * an empty string if the input string is empty.
     *
     * @param  string  source  "table.field" in that format
     *
     * @return string  tableName
     * @throws Exception        if $source does not have the proper format
     */
    public static function getTableIn($source)
    {
        return self::_getTableName($source);
    }

    /**
     * Gets the set of valid values from the given source in 
     * "table.field" format.
     *
     * @param  string  source  "table.field" in that format
     *
     * @return string  tableName
     * @throws Exception        if $source does not have the proper format
     */
    public static function getValidVals($source)
    {
        $valValsSource =
            new Ramp_Table_DbTable_ValidValuesSource($source);
        return $valValsSource->getValidValues($source);
    }

    /**
     * Simple Constructor.
     *
     * @param  string name            table or table.field name
     */
    public function __construct($name)
    {
        $name = self::_getTableName($name);
        $config = array();
        $config[parent::NAME] = $name;
        parent::__construct($config);
    }

    /**
     * Get the valid values from the specified field.
     *
     * @param string   name  a field (or table.field) in this table
     *
     * @return array of valid values with the values as both keys and 
     *         values (e.g., array('valVal' => 'valVal')).
     */
    public function getValidValues($name)
    {
        // See if the valid values need to be retrieved or have been already.
        if ( empty($this->_validValues) )
        {
            // Get the valid values from the database.
            $fieldName = self::_getFieldName($name);
            try {
                $select = $this->select();
                $select = $select->from($this->_name, $fieldName);
                $rows = $this->fetchAll($select);
                foreach ( $rows as $row)
                {
                    $this->_validValues[$row[$fieldName]] = $row[$fieldName];
                }
            }
            catch (Exception $e)
            {
                throw new Exception("Error: '" . $this->_name . "' and '" .
                    $fieldName .
                    "' should be a valid table name and field name.");
            }
        }

        return $this->_validValues;
    } 

    /**
     * Returns the table name (either $name or the table component of 
     * $name if it is in "table.field" format).
     */
    protected static function _getTableName($name)
    {
        $components = explode('.', $name);
        if ( count($components) > 2 )
        {
            throw new Exception("Error: " . $name .
                " does not have the required tableName.fieldName format.");
        }
        return $components[0];
    }

    /**
     * Returns the field name (either $name or the field component of 
     * $name if it is in "table.field" format).
     */
    protected static function _getFieldName($name)
    {
        $components = explode('.', $name);
        switch ( count($components) )
        {
            case 1: return $components[0];
            case 2: return $components[1];
            default:
                throw new Exception("Error: " . $name .
                    " does not have the required tableName.fieldName format.");
        }
    }

}

