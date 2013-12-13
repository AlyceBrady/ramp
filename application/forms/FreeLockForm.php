<?php

class Application_Form_FreeLockForm extends Zend_Form
{
    const LOCKED_TABLE = Application_Model_DbTable_Locks::LOCK_TABLE;
    const LOCKED_FIELD = Application_Model_DbTable_Locks::LOCKING_KEY;

    public function init()
    {
        $this->setName('freeLock');

        $lockedTable = new Zend_Form_Element_Text(self::LOCKED_TABLE);
        $lockedTable->setLabel('Table with locked record')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim');

        $lockedField = new Zend_Form_Element_Text(self::LOCKED_FIELD);
        $lockedField->setLabel('Key associated with locked record')
                    ->setRequired(true)
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Free Lock')
                ->setAttrib('freeLock','submitbutton');

        $this->addElements(array($lockedTable, $lockedField, $submit));
    }

}

