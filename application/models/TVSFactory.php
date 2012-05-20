<?php

/**
 * RAMP: Records and Activity Management Program
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Ramp
 * @package    Ramp_Model
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Config.php 23775 2011-03-01 17:25:24Z ralph $
 */

class Application_Model_TVSFactory
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
        self::$_singleton = self::$_singleton ? :
                            new Application_Model_TVSFactory();
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
            { $sequence = $this->_tableViewingSequences[$name]; }
        else
        {
            // Sequence not in registry; construct and register it.
            $sequence = new Application_Model_TableViewSequence($name);
            $this->_tableViewingSequences[$name] = $sequence;
        }

        // Return the sequence.
        return $sequence;
    }

}

