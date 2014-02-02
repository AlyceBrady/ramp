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

class Ramp_Form_Lock_ChooseLockUser extends Zend_Form
{
    const LOCK_TABLE = Ramp_Lock_DbTable_Locks::TABLE_NAME;
    const USER       = Ramp_Lock_DbTable_Locks::USER;

    public function init()
    {
        $this->setName('chooseLockUser');

        $vvSource =
            new Ramp_Table_DbTable_ValidValuesSource(self::LOCK_TABLE);
        $users = $vvSource->getValidValues(self::USER);

        $userDD = new Zend_Form_Element_Select(self::USER);
        $userDD->setMultiOptions($users);
        $userDD->setLabel('Users with locked records:')
               ->setRequired(true);

        $this->addElements(array($userDD));
    }

}

