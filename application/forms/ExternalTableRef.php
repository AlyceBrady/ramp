<?php

class Application_Form_ExternalTableRef extends Zend_Form
{

    protected $_externalRefInfo; // each ref should have a title,
                                 // a sequence/setting, and a connecting 
                                 // expression (e.g., t1.id = t2.id)

    /**
     * Constructor
     *
     * @param array $externalRefs  array of external refs; each is an 
     *                             Application_Model_ExternalTableReference
     */
    public function __construct(array $externalRefs)
    {
        $this->_externalRefInfo = $externalRefs;
        parent::__construct();
    }

    public function init()
    {
        $this->setName('externalReferences');

        foreach ( $this->_externalRefInfo as $table => $refObj )
        {
            $elt = new Zend_Form_Element_Text($refObj->getTitle());
            // $elt = 
        }
        $table = new Zend_Form_Element_Text('settingName');
        $table->setLabel('Setting Name')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id','submitbutton');

        $this->addElements(array($table, $submit));
    }

}

