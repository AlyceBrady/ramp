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
 * @package    Ramp_Forms
 * @copyright  Copyright (c) 2012-2014 Alyce Brady
 *             (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class Ramp_Form_Lock_FreeLock extends Zend_Form
{
    const LOCKS        = 'lockRecords';
    const LOCKED_TABLE = Ramp_Lock_DbTable_Locks::LOCK_TABLE;
    const LOCKED_FIELD = Ramp_Lock_DbTable_Locks::LOCKING_KEY;

    protected $_user;

    /**
     * Constructor
     *
     * @param string $user     the username
     */
    public function __construct($user)
    {
        $this->_user = $user;
        parent::__construct();
    }

    public function init()
    {
        $this->setName('freeLock');

        $lockTable = new Ramp_Lock_DbTable_Locks();
        $lockInfoArray = $lockTable->getLocksHeldBy($this->_user);

        $lockArray = array();
        foreach ( $lockInfoArray as $lockInfo )
        {
            $combinedName = $lockInfo[self::LOCKED_TABLE] . "." .
                            $lockInfo[self::LOCKED_FIELD];
            $lockArray[$combinedName] = $combinedName;
        }
        $lockList = new Zend_Form_Element_Select(self::LOCKS);
        $lockList->setMultiOptions($lockArray);
        $lockList->setLabel('Locked records')
                 ->setRequired(true);

        $this->addElements(array($lockList));
    }

}

