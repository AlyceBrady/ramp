<?php

/*
 * TODO: Would be nice to have the test for internal vs external 
 * authentication confined to a single method in a single class, 
 * rather than spread across controllers/AuthController,
 * library/Ramp/Acl.php, and views/helpers/LoggedInUser.php.
 */ 

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
 * @version    $Id: AuthController.php 1 2012-07-12 alyce $
 * @version    $Id: AuthController.php 1 2012-11-28 Justin, Chris Cain, Alyce $
 * @version    $Id: AuthController.php 1 2013-11-3 Alyce $
 *
 */
class AuthController extends Zend_Controller_Action
{
    /* Keywords related to authentication types. */
    const AUTH_TYPE          = 'rampAuthenticationType';
    const INTERNAL_AUTH_TYPE = 'internal';

    /* Keywords related to accessing the Users table. */
    const USERS_TABLE       = Ramp_Acl::USERS_TABLE;
    const USERNAME          = 'username';
    const PASSWORD          = 'password';
    const IS_ACTIVE         = 'active = "TRUE"';

    /* Keywords related to initializing a password. */
    const NEW_PW            = 'new_password';
    const CONFIRMED_PW      = 'confirm_password';

    /* Keywords related to passing parameters to actions in this controller. */
    const DETAILS           = 'details';


    /**
     * Indicates whether authentication is internal or external.
     */
    public static function usingInternalAuthentication()
    {
        $authType = Zend_Registry::get(self::AUTH_TYPE);
        return $authType === self::INTERNAL_AUTH_TYPE;
    }

    public function init()
    {
        // Initialize action controller here
    }

    public function indexAction()
    {
        $this->_forward('login');
    }

    /**
     * Prompts user for username and password.
     * Modified from Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 134, and the Zend Manual's zend.form.quickstart.
     */ 
    public function loginAction()
    {
        // Instantiate the form that asks the user to log in.
        $form = new Application_Form_LoginForm();

        // Initialize the error message to be empty.
        $this->view->formResponse = '';

        // For initial display, just render the form.  If this is the 
        // callback after the form has been filled out, process the form.
        if ( ! $this->_thisIsInitialDisplay() )
        {
            // Process the filled-out form that has been posted:
            // if the input values are valid, attempt to authenticate.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $username = $formData[self::USERNAME];
                $password = $formData[self::PASSWORD];
                if ( $this->_authenticate($username, $password) )
                {
                    // Logged in; go to Home page.
                    $this->_helper->redirector('index', 'index');

                    // TODO: Would be nice if it went instead to the 
                    // page the user was originally trying to get to.
                }
                else
                    { $this->view->formResponse = 'Login failed'; }
            }
        }

        // Render the view.
        $this->view->form = $form;
    }

    /**
     * Logs the user out.
     * From Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 138.
     */ 
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_redirect('/');
    }

    /**
     * Informs users when they are trying to perform an action for which 
     * they are not authorized.
     * Justin Leatherwood, November 2012.
     */ 
    public function unauthorizedAction()
    {
        // See if specific information was passed as a parameter.
        $resource = urldecode($this->_getParam(self::DETAILS, ""));
        $resourceName = $this->_formatUnauthResourceName($resource);

        $this->view->errorMsg =
                'Sorry, you are not authorized to perform this action';
        if ( $resourceName != "" )
        {
            $this->view->errorMsg .= ' (' . $resourceName . ')';
        }
        $this->view->errorMsg .= '.';
    }

    /**
     * Prompts user to initialize the password (for internal authentication
     * only).
     */ 
    public function initPasswordAction()
    {
        // Get the username.
        $this->view->username = $username =
                        urldecode($this->_getParam(self::USERNAME));

        // Instantiate the form that prompts user for new password.
        $form = new Application_Form_SetPasswordForm();
        $form->populate(array(self::USERNAME => $username));
        $this->view->formResponse = "";

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            $this->view->formResponse = 'Password has never been set.';
        }
        else    // Process the filled-out form that has been posted.
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                // Process the password change; log in using new password.
                $password = $this->_processPasswordChange($form, $username);
                if ( $password != null )
                {
                    if ( $this->_authenticate($username, $password) )
                    {
                        // Go to Home page.
                        // TODO: Would be nice if it went to the page the
                        // user was originally trying to get to instead.
                        $this->_helper->redirector('index', 'index');
                    }
                    else
                    {
                        $this->view->formResponse =
                            "Not able to login with new password.";
                    }
                }
            }
            else
            {
                $this->view->formResponse = 'Invalid input; please try again.';
            }
        }

        // Render the view.
        $this->view->form = $form;
    }

    /**
     * Allows user to change their password.
     */ 
    public function changePasswordAction()
    {
        // Get the logged in user's username.
        $auth = Zend_Auth::getInstance();
        if ( ! $auth->hasIdentity() )
        {
            $this->view->formResponse = "Must be logged in to change password.";
            return;
        }
        $this->view->username = $username = $auth->getIdentity()->username;

        // Instantiate the form that prompts user for new password.
        $form = new Application_Form_ChangePasswordForm();
        $form->populate(array(self::USERNAME => $username));
        $this->view->formResponse = "";

        // Initial display, or ready to process filled-out form?
        if ( ! $this->_thisIsInitialDisplay() )
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                // Authenticate the user using the old password, then 
                // process the password change.
                $password = $formData[self::PASSWORD];
                if ( $this->_authenticate($username, $password) )
                {
                    $new_pw = $this->_processPasswordChange($form, $username);
                    if ( $new_pw != null )
                    {
                        // Password changed; go to Home page.
                        $this->_helper->redirector('index', 'index');
                    }
                }
                else
                { $this->view->formResponse = 'Old password is not correct'; }
            }
            else
            { $this->view->formResponse = 'Invalid input; please try again.'; }
        }

        // Render the view.
        $this->view->form = $form;
    }

    /**
     * Resets a user's password.
     * Modified from Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 134, and the Zend Manual's zend.form.quickstart.
     */ 
    public function resetPasswordAction()
    {
        // Instantiate the form that asks whose password to reset
        $form = new Application_Form_ResetPasswordForm();

        // Initialize the error message to be empty.
        $this->view->formResponse = '';

        // For initial display, just render the form.  If this is the 
        // callback after the form has been filled out, process the form.
        if ( ! $this->_thisIsInitialDisplay() )
        {
            // Process the filled-out form that has been posted:
            // if the input values are valid, reset the password.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $username = $formData[self::USERNAME];
                $userTable = new Application_Model_DbTable_Users();
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
            }
        }

        // Render the view.
        $this->view->form = $form;
    }

    /**
     * Validates that all roles in the Users Table have been defined.
     */ 
    public function validateRolesAction()
    {
        $this->view->invalid = array();

        // Get the access control list containing roles and resources.
        $acl = new Ramp_Acl();

        // Get the Access Control List containing defined roles and 
        // resources.
        $userInfo = new Application_Model_DbTable_Users();
        $roles = $userInfo->getRoles();

        // Check the access rules to validate whether each role and 
        // resource exist in the access control list.
        foreach ( $roles as $role )
        {
            if ( ! $acl->hasRole($role) )
            {
                $this->view->invalid['role'] = $role;
            }
        }
    }

    /**
     * Validates that all roles and resources in Access Control List
     * rules have been defined.
     */ 
    public function validateAclRulesAction()
    {
        $this->view->invalid = array();

        // Get the access control list containing roles and resources.
        $acl = new Ramp_Acl();

        // Get the Access Control List containing defined roles and 
        // resources.
        $authInfo = new Application_Model_DbTable_Auths();
        $accessRules = $authInfo->getAccessRules();

        // Check the access rules to validate whether each role and 
        // resource exist in the access control list.
        foreach ( $accessRules as $ruleNumber => $rule )
        {
            foreach ( $rule as $role => $resource )
            {
                if ( ! $acl->hasRole($role) )
                {
                    $this->view->invalid[$ruleNumber]['role'] = $role;
                }
                if ( ! $acl->has($resource) )
                {
                    $this->view->invalid[$ruleNumber]['resource'] = $resource;
                }
            }
        }
    }

    /**
     * Returns true if the current request represents the initial 
     * display for the current action.  A return of false, therefore, 
     * indicates that the current request represents the callback with 
     * fields filled in.
     */
    protected function _thisIsInitialDisplay()
    {
        return !  $this->getRequest()->isPost();
    }

    /**
     * Validates the username and password.  If valid, stores identity 
     * information (except for the password) to provide a persistent 
     * identity.
     * Modified from Zend Framework in Action by
     *      Allen, Lo, and Brown, 2009, p. 134
     *
     * @param   $username   username returned from the login form
     * @param   $password   password returned from the login form
     * @return  boolean indicating whether authentication was successful
     */ 
    protected function _authenticate($username, $password)
    {
        // Get authentication type (e.g., internal, LDAP, etc) and 
        // appropriate authentication adapter.
        if ( self::usingInternalAuthentication() )
        {
            $authAdapter = $this->_getInternalAuthAdapter($username, $password);
            if ( $authAdapter == null )
                { return false; }
        }
        else
        {
            // TODO: Handle other types of authentication, such as LDAP.
            // For now, assume internal authentication using users table.
            throw new Exception("rampAuthenticationType must be 'internal' " .
                                "in application.ini.");
        }

        // Add check for user being active.
        $select = $authAdapter->getDbSelect();
        $select->where('active = "TRUE"');

        // do the authentication
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ( $result->isValid() )
        {
            // Get everything except the password, and store it for a
            // persistent identity.
            $data = $authAdapter->getResultRowObject(null, self::PASSWORD);
            $auth->getStorage()->write($data);
            return true;
        }

        return false;
    }

    /**
     * Sets up an authentication adapter for internal authentication 
     * (authentication using a RAMP database table).  The
     * Ramp_Auth_Adapter_DbTable adapter handles encryption with a
     * salt derived from the credential in the database.
     *
     * Modified from Zend Framework in Action by
     *      Allen, Lo, and Brown, 2009, p. 136
     *
     * @param   $username   username returned from the login form
     * @param   $password   password returned from the login form
     * @return  the adapter
     */ 
    protected function _getInternalAuthAdapter($username, $password)
    {
        $dbAdapter = Zend_Registry::get('db');

        // Create an adapter that uses the identity and credential columns 
        // and the credential treatment defined above.
        $authAdapter = new Ramp_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName(self::USERS_TABLE)
                ->setIdentityColumn(self::USERNAME)
                ->setCredentialColumn(self::PASSWORD);

        // Adjust the adapter to authenticate the actual username and 
        // password provided by the user.
        $authAdapter->setIdentity($username);
        $authAdapter->setCredential($password);  //encrypts it also

        // If the username is not a valid, active username, login fails.
        if ( $authAdapter->notAValidIdentity() )
        {
            return null;
        }

        // If there was no credential in the database, force the user to 
        // set one.
        if ( $authAdapter->needsPassword() )
        {
            $this->_helper->redirector('init-password', 'auth', null,
                        array(self::USERNAME => urlencode($username)));
        }

        return $authAdapter;
    }

    /**
     * Format resource name from full resource specification.
     *
     * @param $resourceSpec  full resource specification
     */
    protected function _formatUnauthResourceName($resourceSpec)
    {
        // Get the various components of a full resource spec.  If it 
        // doesn't have the expected components, return full spec.
        $components = explode(Ramp_Acl::DELIM, $resourceSpec);
        if ( count($components) != 3 )
        {
            return 'Resource: ' . $resourceSpec;
        }

        // Return the resource type plus either the second or third
        // component, depending on the resource type.
        if ( $components[0] == Ramp_Controller_KeyParameters::ACT_CONTROLLER )
        {
            return 'activities in ' . $components[2] . ' directory';
        }
        if ( $components[0] == Ramp_Controller_KeyParameters::DOC_CONTROLLER )
        {
            return $components[2] . ' document';
        }
        if ( $components[0] == Ramp_Controller_KeyParameters::TBL_CONTROLLER ||
             $components[0] == Ramp_Controller_KeyParameters::REP_CONTROLLER )
        {
            return $components[0] . ' in setting ' . $components[2];
        }
        return 'Resource: ' . $resourceSpec;
    }

    /**
     * Processes a password change request by confirming that the new 
     * password and confirmed password are equivalent and then setting 
     * an encrypted version of the password in the database.  The 
     * encryption is performed by the setPassword method in the class 
     * modeling the Users table.
     *
     * @param $form      the form containing the new and confirmed passwords
     * @param $username  the username whose password is changing
     */
    protected function _processPasswordChange($form, $username)
    {
        // Check that the new password was correctly entered twice.
        $new_password = $form->getValue(self::NEW_PW);
        $confirmed_password = $form->getValue(self::CONFIRMED_PW);
        if ( $new_password == $confirmed_password )
        {
            // Encrypt and save the new password.
            $userTable = new Application_Model_DbTable_Users();
            $userTable->setPassword($username, $new_password);
            return $new_password;
        }
        else
        {
            $this->view->formResponse =
                'Passwords do not match; please try again.';
            return null;
        }

    }

}

