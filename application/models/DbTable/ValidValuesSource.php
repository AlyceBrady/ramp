<?php

class Application_Model_DbTable_ValidValuesSource extends Zend_Db_Table_Abstract
{
    protected $_validValues = array();

    /**
     * Simple Constructor.
     *
     * @param  string name   table name
     */
    public function __construct($name)
    {
        $config = array();
        $config[parent::NAME] = $name;
        parent::__construct($config);
    }

    /**
     * Get the valid values from the specified field.
     *
     * @param string fieldName  a field in this table
     *
     */
    public function getValidValues($fieldName)
    {
        // See if the valid values need to be retrieved or have been already.
        if ( empty($this->_validValues) )
        {
            // Get the valid values from the database.
            $select = $this->select();
            $select = $select->from($this->_name, $fieldName);
            $rows = $this->fetchAll($select);
            foreach ( $rows as $row)
            {
                $this->_validValues[] = $row[$fieldName];
            }
        }

        return $this->_validValues;
    }

}

