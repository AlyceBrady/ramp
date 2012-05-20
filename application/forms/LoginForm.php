<?php

class Application_Form_LoginForm extends Zend_Form
{

    public function init()
    {
        $this->setName('login');

        $userid = new Zend_Form_Element_Text('username');
        $userid->setLabel('Username')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $password = new Zend_Form_Element_Password('password');
        $password->setLabel('Password')
                ->setRequired(true)
                ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Login')
                ->setAttrib('login','submitbutton');

        $this->addElements(array($userid, $password, $submit));
    }

}

