<?php

class Application_Model_DbTable_LockRelations extends Zend_Db_Table_Abstract
{
    const DB_TABLE = 'db_table';
    const LOCK_TABLE = 'lock_table';
    const LOCKING_KEY_NAME = 'locking_key_name';

    protected $_name='ramp_lock_relations';

    /**
     * Gets a tuple containing the table & locking key field for locks 
     * for the given database table.
     */
    public function getLockInfo($db_table)
    {
        // Get the Lock Table Relation for the given DB table.
        $where = array((self::DB_TABLE . ' = ?') => $db_table);
        $lock_relations = $this->fetchAll($where);

        if ( count($lock_relations) != 1 )
        {
            throw new Exception("There should be exactly one entry in " .
                        "the lock relations table for $db_table.");
        }

        $lock_relation = $lock_relations[0];
        $lockInfo = array();
        $lockInfo[self::LOCK_TABLE] = $lock_relation[self::LOCK_TABLE];
        $lockInfo[self::LOCKING_KEY_NAME] =
                        $lock_relation[self::LOCKING_KEY_NAME];
        return $lockInfo;
    }
}

