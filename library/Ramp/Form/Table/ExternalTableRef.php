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

class Ramp_Form_Table_ExternalTableRef extends Zend_Form
{

    protected $_externalRefInfo; // each ref should have a title,
                                 // a sequence/setting, and a connecting 
                                 // expression (e.g., t1.id = t2.id)

    /**
     * Constructor
     *
     * @param array $externalRefs  array of external refs; each is an 
     *                             Ramp_Table_ExternalTableReference
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

