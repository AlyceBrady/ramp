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

class Ramp_Form_Auth_UserTableEntry extends Ramp_Form_Table_TableRecordEntry
{
    const USERS_TABLE = Ramp_Auth_DbTable_Users::TABLE_NAME;
    const ROLE        = Ramp_Auth_DbTable_Users::ROLE_COL;

    /**
     * Constructor
     *
     * @param Ramp_Form_Table_TableRecordEntry $setTable the table setting
     * @param Ramp_Table_SetTable $setTable the table setting
     * @param string $formType     specifies type of form (VIEW, ADD, 
     *                                  EDIT, or SEARCH)
     * @param string $makeSmall    make buttons smaller
     * @param bool   $formSuffix   a suffix to make form name unique on page
     *                             e.g., a row number
     */
    public function __construct(Ramp_Table_SetTable $setTable,
                                $formType = self::VIEW, $makeSmall = false,
                                $formSuffix = null)
    {
        parent::__construct($setTable, $formType, $makeSmall, $formSuffix);
    }

    /**
     * Determines whether the field's range of values is defined externally.
     */
    protected function _valRangeDefinedExternally($field)
    {
        if ( $this->_setTable->getDbTableName() == self::USERS_TABLE &&
             $field->getDbFieldName() == self::ROLE )
        {
            return true;
        }
        return parent::_valRangeDefinedExternally($field);
    }

    /**
     * Gets the field's externally-defined range of values.
     */
    protected function _getValueRange($field)
    {
        if ( $this->_setTable->getDbTableName() == self::USERS_TABLE &&
             $field->getDbFieldName() == self::ROLE )
        {
            $acl = new Ramp_Acl();
            $roles = array();
            foreach ( $acl->getRoles() as $role )
            {
                $roles[$role] = $role;
            }
            return $roles;
        }

        return parent::_getValueRange($field);
    }

}

