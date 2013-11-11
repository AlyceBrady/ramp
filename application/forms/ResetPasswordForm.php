<?php

class Application_Form_ResetPasswordForm extends Zend_Form
{

    public function init()
    {
        $this->setName('resetPassword');

        $userid = new Zend_Form_Element_Text('username');
        $userid->setLabel('Username')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Reset Password')
                ->setAttrib('resetPassword','submitbutton');

        $this->addElements(array($userid, $submit));
    }

}

