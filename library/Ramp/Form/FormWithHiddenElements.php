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

class Ramp_Form_FormWithHiddenElements extends Zend_Form
{

    /**
     * Creates a hidden field element.
     */
    protected function _createHiddenElement($name, $label)
    {
        $fieldElement = new Zend_Form_Element_Hidden($name);
        $fieldElement->setLabel($label)
                     ->setAttrib('class', 'hidden');

        // Add a ViewHelper decorator with the alias "Elem", a decorator 
        // for the label, and a decorator for errors.  These decorators 
        // hide an element, its label, and errors.
        $hiddenFieldDecParams =
                    array('separator'=>'', 'tag'=>'div', 'class'=>'hidden');
        $fieldElement->addDecorator(array('Elem' => 'ViewHelper'),
                               $hiddenFieldDecParams);
        $fieldElement->addDecorator('Label', $hiddenFieldDecParams);
        $fieldElement->addDecorator('Errors', $hiddenFieldDecParams);

        return $fieldElement;
    }

}

