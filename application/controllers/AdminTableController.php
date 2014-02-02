<?php

require_once("TableController.php");

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
 * @package    Ramp_Controller
 * @copyright  Copyright (c) 2012-2014 Alyce Brady
 *             (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class AdminTableController extends TableController
{

    /**
     * Creates a form with the given parameters.  (Abstracted into a 
     * method so that it can be redefined in subclasses.)
     *
     * @param Ramp_Table_SetTable $setTable     the table setting
     * @param string $formType     specifies type of form (VIEW, ADD, 
     *                                  EDIT, or SEARCH)
     * @param string $makeSmall    make buttons smaller
     * @param bool   $formSuffix   a suffix to make form name unique on page
     *                             e.g., a row number
     */
    protected function _getForm(Ramp_Table_SetTable $setTable,
                                $formType = self::VIEW, $makeSmall = false,
                                $formSuffix = null)
    {
        return new Ramp_Form_Auth_UserTableEntry($setTable, $formType,
                                                 $makeSmall, $formSuffix);
    }

}

