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
 * @package    Ramp_Controller
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class TableSyntaxController extends Zend_Controller_Action
{
    const FILENAME = Application_Form_GetSettingName::SETTING_NAME;

    /**
     * Initializes the attributes for this object as well as some
     * values commonly used by the associated view scripts.
     */
    public function init()
    {
        // Initialize action controller here
    }

    /**
     * Checks the syntax of a table setting/sequence file chosen by the 
     * user.
     */
    public function indexAction()
    {
        // Instantiate the form that asks for a sequence setting file.
        $form = new Application_Form_GetSettingName();
        $this->view->form = $form;

        // Initialize the error message to be empty.
        $this->view->formMessages = array();

        // For initial display, just render the form.  If this is the 
        // callback after the form has been filled out, process the form.
        if ( $this->getRequest()->isPost() )
        {
            // Get the filename from the filled-out form.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $filename = $formData[self::FILENAME];

                $this->view->form = null;
                $this->view->formMessages =
                    Application_Model_TableViewSequence::checkSyntax($filename);

$this->view->formMessages[] = "";
$this->view->formMessages[] = "Should have a button to check another file.";
                /*
                if ( $userTable->resetPassword($username) )
                {
                    $this->view->formResponse = 'Password for ' . $username .
                        ' has been reset to the default password.';
                }
                else
                {
                    $this->view->formResponse = "Password for " . $username .
                        " was not reset ('" . $username . "' is not a valid" .
                        " user or the password was already the default).";
                }
                $form->populate(array(self::USERNAME => ''));
                 */
            }
            else
            {
                $this->view->formMessages[] = "Invalid input";
            }
        }

    }

}

