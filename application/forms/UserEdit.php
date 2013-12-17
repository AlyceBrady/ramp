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

class Application_Form_UserEdit extends Zend_Form
{

    public function init()
    {
        $this->setName('userEdit');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addFilter('Int');

        $username = new Zend_Form_Element_Text('username');
        $username->setLabel('Username')
                 ->setRequired(true)
                 ->addFilter('StripTags')
                 ->addFilter('StringTrim')
                 ->addValidator('NotEmpty');
        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                 ->setRequired(true)
                 ->addValidator('NotEmpty');
        $first_name = new Zend_Form_Element_Text('first_name');
        $first_name->setLabel('First Name')
                   ->setRequired(true)
                   ->addFilter('StripTags')
                   ->addFilter('StringTrim')
                   ->addValidator('NotEmpty');
        $last_name = new Zend_Form_Element_Text('last_name');
        $last_name->setLabel('Last Name')
                  ->setRequired(true)
                  ->addFilter('StripTags')
                  ->addFilter('StringTrim')
                  ->addValidator('NotEmpty');
        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email Address*')
              ->setRequired(false)
              ->addFilter('StripTags')
              ->addFilter('StringTrim')
              ->addValidator('NotEmpty');
        $role = new Zend_Form_Element_Select('role');
        $role_list = array('nonadmin'=>'nonadmin', 'admin' =>'admin',
                           'guest'=>'guest');
        $role->setLabel('Select User Role')
             ->addMultiOptions($role_list)
             ->setValue($role_list["nonadmin"])
             ->setRequired(true)
             ->addDecorators(array(array('Label'),
                                    array('HtmlTag', array('tag' => 'td'))));            

        $submit = new Zend_Form_Element_Submit('Submit');
        $submit->setAttrib('id', 'submitbutton');

        $this->addElements(array($id, $username, $password, $role,
                                 $first_name, $last_name, $email, $submit));
    }

}
