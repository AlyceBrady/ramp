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

/*
 * This class deliberately uses the same element names as the login form 
 * for the username and old password, and the same element names as the 
 * set password form for the new and confirmed passwords.
 */
class Ramp_Form_Auth_ChangePasswordForm
    extends Ramp_Form_FormWithHiddenElements
{

    public function init()
    {

        $this->setName('changePassword');

        $userid = $this->_createHiddenElement('username', 'username');

        $old_password = new Zend_Form_Element_Password('password');
        $old_password->setLabel('Old Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $new_password = new Zend_Form_Element_Password('new_password');
        $new_password->setLabel('New Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->setLabel('Repeat New Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $this->addElements(array($userid, $old_password,
                                 $new_password, $confirm_password));
    }

}

