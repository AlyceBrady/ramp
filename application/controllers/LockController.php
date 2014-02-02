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
 * @copyright  Copyright (c) 2013 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */
class LockController extends Zend_Controller_Action
{
    const SUBMIT_BUTTON = 'submit';
    const FIND_LOCKS    = 'Find Locks';
    const RELEASE_LOCK  = 'Free Lock';
    const DONE          = 'Done';
    const CANCEL        = 'Cancel';
    const USER          = Ramp_Lock_DbTable_Locks::USER;
    const LOCKS         = Ramp_Form_Lock_FreeLock::LOCKS;

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
            urldecode($this->_getParam(Ramp_Lock_DbTable_Locks::USER,
                                       ""));
        $byWhom = empty($user) ? "" : " by $user";
        $this->view->errorMsg = "This record is already locked$byWhom.";
    }

    /**
     * Prompt the user for information about the lock to release.
     */ 
    public function freeLockAction()
    {
        // Determine what action to take next.
        $submittedButton = $this->_getParam(self::SUBMIT_BUTTON);

        // Initialize the information message to be empty.
        $this->view->msg = '';

        // This action proceeds in two phases.  In the first phase, the 
        // administrator chooses the user who holds the lock that needs 
        // to be freed.  In the second phase, the administrator frees a 
        // particular lock held by that user.

        if ( ! $this->getRequest()->isPost() )
        {
            // Start Phase 1: Choose the user holding the errant lock(s).
            $form = new Ramp_Form_Lock_ChooseLockUser();

            // Render the correct form.
            $this->view->form = $form;
            $this->view->buttonList = array(self::FIND_LOCKS, self::CANCEL);
        }
        elseif ( $submittedButton == self::FIND_LOCKS )
        {
            // Process Phase 1:  Get the chosen user.
            $form = new Ramp_Form_Lock_ChooseLockUser();
            $formData = $this->getRequest()->getPost();
            $user = $formData[self::USER];

            // Start Phase 2: Choose the lock to release.
            $form = new Ramp_Form_Lock_FreeLock($user);
            $this->view->buttonList = array(self::RELEASE_LOCK, self::CANCEL);

            // Render the correct form.
            $this->view->form = $form;
        }
        elseif ( $submittedButton == self::RELEASE_LOCK )
        {
            // Process Phase 2: Release the chosen lock.
            $formData = $this->getRequest()->getPost();
            $lockInfo = $formData[self::LOCKS];
            $components = explode('.', $lockInfo);
            $lockTable = new Ramp_Lock_DbTable_Locks();
            $lockTable->freeLock($components[0], $components[1]);
            $this->view->msg = 'Lock should now be released.';
            $this->view->buttonList = array(self::DONE);
        }
        else  // Done or Cancel
        {
            $this->_helper->redirector('index', 'index');
        }
    }

}

