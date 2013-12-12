<?php

class Application_Model_DbTable_Locks extends Zend_Db_Table_Abstract
{
    const LOCK_TABLE    = 'lock_table';
    const LOCKING_KEY   = 'locking_key';
    const USER          = 'username';
    const LOCK_TIME     = 'lock_time';

    protected $_name='ramp_lock_locks';

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


}

