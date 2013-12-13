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
class LockController extends Zend_Controller_Action
{

    public function init()
    {
        // Initialize action controller here
    }

    public function indexAction()
    {
        $this->_forward('unavailable-lock');
    }

    /**
     * Controls the editing action for a single, editable record on a page.
     *
     * Precondition: this action should only be invoked when the 
     * parameters provided uniquely identify a single record.
     */
    public function unavailableLockAction()
    {
        // User should have been passed as a parameter.
        $user =
            urldecode($this->_getParam(Application_Model_DbTable_Locks::USER,
                                       ""));
        $byWhom = empty($user) ? "" : " by $user";
        $this->view->errorMsg = "This record is already locked$byWhom.";
    }

    /**
     * Prompt the user for information about the lock to release.
     */ 
    public function freeLockAction()
    {
        // Instantiate the form that asks what lock to release.
        $form = new Application_Form_FreeLockForm();

        // Initialize the error message to be empty.
        $this->view->formResponse = '';

        // For initial display, just render the form.  If this is the 
        // callback after the form has been filled out, process the form.
        if ( ! $this->getRequest()->isPost() )
        {
            // Render the view.
            $this->view->form = $form;
        }
        else
        {
            // Process the filled-out form that has been posted:
            // if the input values are valid, release the lock.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $lockTable = new Application_Model_DbTable_Locks();
                $lockTable->freeLock($formData);
                $this->view->formResponse = 'Lock should now ' .
                    'be released.';
            }
            else
            {
                // Render the view.
                $this->view->form = $form;
            }
        }

    }

}

