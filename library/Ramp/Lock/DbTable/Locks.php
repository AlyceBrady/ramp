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
class Ramp_Lock_DbTable_Locks extends Zend_Db_Table_Abstract
{
    const TABLE_NAME    = 'ramp_lock_locks';
    const LOCK_TABLE    = 'lock_table';
    const LOCKING_KEY   = 'locking_key';
    const USER          = 'username';
    const LOCK_TIME     = 'lock_time';

    protected $_name=self::TABLE_NAME;

    /**
     * Gets a tuple containing the table & locking key field for locks 
     * for the given database table.
     */
    public function acquireLock($lockInfo)
    {
        $lock_table = $lockInfo[self::LOCK_TABLE];
        $lock_key = $lockInfo[self::LOCKING_KEY];

        // Lock the locks table to acquire the lock on $db_table.
        $this->getAdapter()->beginTransaction();

        // Get the lock if it exists.
        $where = array((self::LOCK_TABLE . ' = ?') => $lock_table,
                       (self::LOCKING_KEY . ' = ? ') => $lock_key);
        $lock = $this->fetchAll($where);
        if ( count($lock) != 0 )
        {
            // Lock exists, so return false;
            $this->getAdapter()->commit();
            return false;
        }

        // Lock does not already exist, so add it.
        $this->insert($lockInfo);

        $this->getAdapter()->commit();
        return true;
    }

    /**
     * Releases the lock associated with the information in $lockInfo.
     */
    public function releaseLock($lockInfo)
    {
        $lock_table = $lockInfo[self::LOCK_TABLE];
        $lock_key = $lockInfo[self::LOCKING_KEY];
        $user = $lockInfo[self::USER];

        // Lock the locks table to acquire the lock on $db_table.
        $this->getAdapter()->beginTransaction();

        // Release the lock.
        $where = array((self::LOCK_TABLE . ' = ?') => $lock_table,
                       (self::LOCKING_KEY . ' = ? ') => $lock_key,
                       (self::USER . ' = ? ') => $user);
        $numRows = $this->delete($where);

        $this->getAdapter()->commit();
        return;
    }

    /**
     * Gets an array of tuples containing the table & locking key field
     * for locks held by the given user.
     */
    public function getLocksHeldBy($user)
    {
        // Get the lock information, if it exists.
        $where = array((self::USER . ' = ?') => $user);
        return $this->fetchAll($where)->toArray();
    }

    /**
     * Releases any locks held by the given user.
     */
    public function releaseLocksHeldBy($user)
    {
        // Lock the locks table to acquire the lock on $db_table.
        $this->getAdapter()->beginTransaction();

        // Get the lock information, if it exists.
        $where = array((self::USER . ' = ?') => $user);
        $numRows = $this->delete($where);

        $this->getAdapter()->commit();
        return;
    }

    /**
     * Releases a lock administratively, regardless of who the user is.
     */
    public function freeLock($lock_table, $lock_key)
    {
        // Lock the locks table to acquire the lock on $db_table.
        $this->getAdapter()->beginTransaction();

        // Release the lock.
        $where = array((self::LOCK_TABLE . ' = ?') => $lock_table,
                       (self::LOCKING_KEY . ' = ? ') => $lock_key);
        $numRows = $this->delete($where);

        $this->getAdapter()->commit();
        return;
    }


}

