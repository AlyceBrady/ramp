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
 * @copyright  Copyright (c) 2012 Alyce Brady
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */
class Ramp_Table_DbTable_Table extends Zend_Db_Table_Abstract
{
    /**
     * Simple Constructor.
     *
     * @param  string name table name
     */
    public function __construct($name)
    {
        $config = array();
        $config[parent::NAME] = $name;
        parent::__construct($config);
    }

    /**
     * Constructor.  [currently not used, so commented out]
     *
     * Supported params for $config include:
     * - db              = user-supplied instance of database connector,
     *                     or key name of registry instance.
     * - primary         = string or array of primary key(s).
     * - rowClass        = row class name.
     * - rowsetClass     = rowset class name.
     * - referenceMap    = array structure to declare relationship
     *                     to parent tables.
     * - dependentTables = array of child tables.
     * - metadataCache   = cache for information from adapter describeTable().
     *
     * @param  string name table name
     * @param  mixed $config Array of user-specified config options, or just the Db Adapter.
    public function __construct($name, $config = array())
    {
        // We want to be able to add $name to $config, but if $config is 
        // just the DB Adapter, we have to make it into an array first.  
        // (This is duplicating some code from the parent constructor,
        // but it is, unfortunately, unavoidable.)
        //
        // From parent constructor:
        // Allow a scalar argument to be the Adapter object or Registry key.
        //
        if (!is_array($config)) {
            $config = array(parent::ADAPTER => $config);
        }

        $config[parent::NAME] = $name;
        parent::__construct($config);
    }
     */

    /**
     * setOptions()
     *
     * @param array $options
     * @return Zend_Db_Table_Abstract
     */
    public function setOptions(Array $options)
    {
/*
 * Not necessary unless we add more option types
        foreach ($options as $key => $value)
        {
            switch ($key) {
                case self::DEFINITION:
                    $this->setDefinition($value);
                    break;
                case self::DEFINITION_CONFIG_NAME:
                    $this->setDefinitionConfigName($value);
                    break;
                case self::NAME:
                    $this->_name = (string) $value;
                    break;
                default:
                    // ignore unrecognized configuration directive
                    break;
            }
        }
 */

        // Let parent class handle other recognized configuration directives.
        return  parent::setOptions($options);
    }

    public function getName()
    {
        return $this->_name;
    }

}

