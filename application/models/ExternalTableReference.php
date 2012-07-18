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
 * @version    $Id: Application_Model_ExternalTableReference.php 1 2012-07-12 alyce $
 *
 */

class Application_Model_ExternalTableReference
{

    // Constants representing external table reference properties.
    const TITLE         = 'title';
    const VIEW_SEQ      = 'viewingSequence';
    const LOCAL         = 'localField';
    const EXTERNAL      = 'externalField';

    // Setting used to determine status.
    const SETTING   = Application_Model_TableViewSequence::MAIN_SETTING;

    /** @var string */
    protected $_refFromSetting;  // setting containing this external reference

    /** @var string */
    protected $_title;       // table title

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
     * @param string $localTable  name of this "local" table in database
     * @param array $refInfo  array containing the reference information
     *                        (MUST contain viewing sequence, at minimum)
     * @param string $setting name of setting containing this external ref
     *
     */
    public function __construct($localTable, $refInfo = array(), $setting = "")
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

        // See if there is a title.
        if ( isset($refInfo[self::TITLE]) )
        {
            $this->_title = $refInfo[self::TITLE];
            unset($refInfo[self::TITLE]);
        }
        else
            { $this->_viewingSeq; }

        // Look for one or more field-matching connections.
        $this->_connections = array();
        if ( $this->_isOKConnection($refInfo) )
        {
            $this->_connections[$refInfo[self::LOCAL]] =
                                                $refInfo[self::EXTERNAL];
            unset($refInfo[self::LOCAL]);
            unset($refInfo[self::EXTERNAL]);
        }
        foreach ( $refInfo as $subPropName => $subProp )
        {
            if ( count($subProp) == 2 && $this->_isOKConnection($subProp) )
            {
                $this->_connections[$subProp[self::LOCAL]] =
                                                $subProp[self::EXTERNAL];
            }
            else
            {
                throw new Exception("External reference error: " .
                    $subProp . " is not part of a valid external " .
                    "reference connection.");
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
        return Application_Model_TVSFactory::getSequenceOrSetting(
                                                        $this->_viewingSeq);
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
    public function findConnectionFields(array $data)
    {
        $translatedData = array();
        $connectionExprs = $this->getConnectionExpressions();
        foreach ( $connectionExprs as $local => $external )
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

