<?php

/*
 * This class deliberately uses the same element names as the login form 
 * for the username and old password, and the same element names as the 
 * set password form for the new and confirmed passwords.
 */
class Application_Form_ChangePasswordForm extends Zend_Form
{

    public function init()
    {
        $hiddenFieldDecParams =
                    array('separator'=>'', 'tag'=>'div', 'class'=>'hidden');
        $hiddenFieldDecorators = array(
            array(array('Elem' => 'ViewHelper'), $hiddenFieldDecParams),
            array('Label', $hiddenFieldDecParams),
            array('Errors', $hiddenFieldDecParams),
        );

        $this->setName('changePassword');

        $userid = new Zend_Form_Element_Text('username');
        $userid->setLabel('Username')
                ->setAttrib('class', 'hidden')
                ->setDecorators($hiddenFieldDecorators);

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

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save')
                ->setAttrib('changePassword','submitbutton');

        $this->addElements(array($userid, $old_password,
                                 $new_password, $confirm_password, $submit));
    }

}

