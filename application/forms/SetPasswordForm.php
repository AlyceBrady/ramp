<?php

class Application_Form_SetPasswordForm extends Zend_Form
{

    public function init()
    {

        $this->setName('setPassword');

        $userid = new Zend_Form_Element_Hidden('username');

        $new_password = new Zend_Form_Element_Password('new_password');
        $new_password->setLabel('Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $confirm_password = new Zend_Form_Element_Password('confirm_password');
        $confirm_password->setLabel('Repeat Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Save')
                ->setAttrib('setPassword','submitbutton');

        $this->addElements(array($userid, $new_password, $confirm_password,
                                 $submit));
    }

}

