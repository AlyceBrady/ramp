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

class Ramp_Form_Auth_SetPasswordForm extends Ramp_Form_FormWithHiddenElements
{

    public function init()
    {

        $this->setName('setPassword');

        $userid = $this->_createHiddenElement('username', 'username');

        $new_password = new Zend_Form_Element_Password('new_password');
        $new_password->setLabel('Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->setLabel('Repeat Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $this->addElements(array($userid, $new_password, $confirm_password));
    }

}

