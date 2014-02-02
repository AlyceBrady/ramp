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

class Ramp_Table_TVSFactory
{

    /** @var array */
    protected static $_singleton = null;

    /** @var array */
    protected $_tableViewingSequences;   // sequences/settings read in

    /**
     * Gets the singleton instance of the TVSFactory class.
     *
     */
    public static function getInstance()
    {
        self::$_singleton = self::$_singleton ? : new Ramp_Table_TVSFactory();
        return self::$_singleton;
    }

    /**
     * Gets the named sequence or setting (creating a new one if 
     * necessary).
     *
     * @param string $name  sequence or setting name
     */
    public static function getSequenceOrSetting($name)
    {
        $factory = self::getInstance();
        return $factory->_getSequenceOrSetting($name);
    }

    /**
     * Class constructor
     *
     * Creates a singleton object that creates TableViewingSequence
     * objects as necessary.
     *
     */
    protected function __construct()
    {
        $this->_tableViewingSequences = array();
    }

    /**
     * Gets the named sequence or setting (creating a new one if 
     * necessary).
     *
     * @param string $name  sequence or setting name
     */
    protected function _getSequenceOrSetting($name)
    {
        // Get the sequence (or setting).
        if ( isset($this->_tableViewingSequences[$name]) )
        {
            $sequence = $this->_tableViewingSequences[$name];
        }
        else
        {
            // Sequence not in registry; construct and register it.
            $sequence = new Ramp_Table_TableViewSequence($name);
            $this->_tableViewingSequences[$name] = $sequence;
        }

        // Return the sequence.
        return $sequence;
    }

}

