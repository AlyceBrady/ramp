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

class Ramp_Form_Auth_ResetPasswordForm extends Zend_Form
{

    public function init()
    {
        $this->setName('resetPassword');

        $userid = new Zend_Form_Element_Text('username');
        $userid->setLabel('Username')
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim');

        $this->addElements(array($userid));
    }

}

