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

class Ramp_Table_ExternalTableReference
{

    // Constants representing external table reference properties.
    const TITLE         = 'title';
    const CONTROLLER    = 'controller';
    const VIEW_SEQ      = 'viewingSequence';
    const LOCAL         = 'localField';
    const EXTERNAL      = 'externalField';

    const DEFAULT_CONTROLLER
                        = Ramp_Controller_KeyParameters::DEFAULT_CONTROLLER;

    // Setting used to determine status.
    const SETTING   = Ramp_Table_TableViewSequence::MAIN_SETTING;

    /** @var string */
    protected $_refFromSetting;  // setting containing this external reference

    /** @var string */
    protected $_title;       // table title

    /** @var string */
    protected $_controller;  // controller for this table (default: 'table')

    /** @var string */
    protected $_viewingSeq;  // name of table viewing sequence or setting

    /** @array string */
    protected $_connections; // array of localField/externalField matches

    /**
     * Class constructor
     *
     * Creates an object representing a named external table reference,
     * with a table setting and (hopefully) field-matching information
     * (e.g., 'localField' => studentID, 'externalField' => id).
     *
     * @param array $refInfo  array containing the reference information
     *                        (MUST contain viewing sequence, at minimum)
     * @param string $setting name of setting containing this external ref
     *
     */
    public function __construct($refInfo = array(), $setting = "")
    {
        $this->_refFromSetting = $setting;

        // Make sure that the reference contains a viewing sequence
        // name (minimum requirement).
        if ( ! is_array($refInfo) || ! isset($refInfo[self::VIEW_SEQ]) )
        {
            $ident = is_array($refInfo) && isset($refInfo[self::TITLE]) ?
                        $refInfo[self::TITLE] : print_r($refInfo, true);
            throw new Exception("Cannot create external reference for " .
                $ident . " without a " .
                self::VIEW_SEQ . " property.");
        }
        $this->_viewingSeq = $refInfo[self::VIEW_SEQ];
        unset($refInfo[self::VIEW_SEQ]);

        // See if a controller has been specified.
        if ( isset($refInfo[self::CONTROLLER]) )
        {
            $this->_controller = $refInfo[self::CONTROLLER];
            unset($refInfo[self::CONTROLLER]);
        }
        else
        {
            $this->_controller = self::DEFAULT_CONTROLLER;
        }

        // See if there is a title.
        if ( isset($refInfo[self::TITLE]) )
        {
            $this->_title = $refInfo[self::TITLE];
            unset($refInfo[self::TITLE]);
        }
        else
        {
            $this->_title = $this->_viewingSeq;
        }

        // Look for one or more field-matching connections.
        $this->_connections = array();
        if ( $this->_isOKConnection($refInfo) )
        {
            $this->_connections[$refInfo[self::EXTERNAL]] =
                                                $refInfo[self::LOCAL];
            unset($refInfo[self::LOCAL]);
            unset($refInfo[self::EXTERNAL]);
        }
        foreach ( $refInfo as $subPropName => $subProp )
        {
            if ( count($subProp) == 2 && $this->_isOKConnection($subProp) )
            {
                $this->_connections[$subProp[self::EXTERNAL]] =
                                                $subProp[self::LOCAL];
            }
            else
            {
                throw new Exception("External reference error: " .
                    $subPropName . " is not a valid " .
                    "external reference connector.");
            }
        }
    }
    
    /**
     * Checks to see whether the given array constitutes a valid 
     * field-matching connection to another table.
     */
    protected function _isOKConnection($connection)
    {
        return isset($connection[self::LOCAL]) &&
                   is_string($connection[self::LOCAL]) &&
               isset($connection[self::EXTERNAL]) &&
                   is_string($connection[self::EXTERNAL]);
    }

    /**
     * Gets the name of the controller to use to access this
     * external table reference.
     *
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Gets the name of the viewing sequence or setting for this
     * external table reference.
     *
     */
    public function getViewingSeqName()
    {
        return $this->_viewingSeq;
    }

    /**
     * Gets the viewing sequence object for this external table reference.
     *
     */
    public function getViewingSeq()
    {
        return Ramp_Table_TVSFactory::getSequenceOrSetting($this->_viewingSeq);
    }

    /**
     * Gets the title for this external table reference.
     *
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Gets the field-matching connection expression(s) for this
     * external table reference.  Returns an empty array if no
     * connection information was provided.
     *
     */
    public function getConnectionExpressions()
    {
        return $this->_connections;
    }

    /**
     * Translates the fieldname/value pairs in the given data to
     * the associated field names for this external reference.
     *
     * @param array $data       fieldname-value pairs from current table
     * @return array            fieldname-value pairs with same values
     *                          but field names from external table
     */
    public function xlFieldValuePairs(array $data)
    {
        $translatedData = array();
        $connectionExprs = $this->getConnectionExpressions();
        foreach ( $connectionExprs as $external => $local )
        {
            if ( ! isset($data[$local]) )
            {
                throw new Exception("Error: $local field is used in " .
                    "initialization or external reference, but is not " .
                    "a visible field or primary key in " .
                    $this->_refFromSetting . " setting");
            }
            $translatedData[$external] = $data[$local];
        }

        return $translatedData;
    }

}

