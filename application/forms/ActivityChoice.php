<?php

class Application_Form_ActivityChoice extends Zend_Form
{

    public function init()
    {
        $this->setName('activityChoice');

        $table = new Zend_Form_Element_Text('activityName');
        $table->setLabel('Activity Name')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id','submitbutton');

        $this->addElements(array($table, $submit));
    }

}

