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
class AuthController extends Zend_Controller_Action
{

    /* Keywords related to accessing the Users table and related form. */
    const USERS_TABLE       = Ramp_Acl::USERS_TABLE;
    const USERNAME          = Application_Model_DbTable_Users::USERNAME;
    const PASSWORD          = Application_Model_DbTable_Users::PASSWORD;

    /* Form keywords related to initializing a password. */
    const NEW_PW            = 'new_password';
    const CONFIRMED_PW      = 'confirm_password';

    /* Keywords related to passing parameters to actions in this controller. */
    const DETAILS           = 'details';


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
                    // If user was attempting to go somewhere, go there.
                    // Otherwise, go to Home page.
                    $this->_goToAttemptedDestOrHome();
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
                        // If user was attempting to go somewhere, go there.
                        // Otherwise, go to Home page.
                        $this->_goToAttemptedDestOrHome();
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
     * View roles, resources, and resources that have been defined for
     * Access Control Lists for debugging purposes.
     */ 
    public function viewAclInfoAction()
    {
        $acl = new Ramp_Acl();
        $msg = "";
        $msg .= "<h4>Roles:</h4>" . var_export($acl->getRoles(), true);
        $msg .= "<h4>Resources:</h4>"
                    . var_export($acl->getResources(), true);
        // Too hard to return all rules, so just concentrate on rules 
        // from Registry and Database.
        $msg .= "<h4>Rules:</h4>"
                    . "<h5>"
                    . "<em>It's too hard to dig out and report on all rules "
                    . "(and derived rules), so these are just rules from "
                    . "Registry (application.ini) and Database.</em><br />"
                    . "</h5>"
                    . var_export($acl->getRules(), true);
        $msg .= "</pre>";
        $this->view->msg = $msg;
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
        $authAdapter = $this->_getAuthAdapter($username, $password);
        if ( $authAdapter == null )
            { return false; }

        // Add check for user being active.
        $select = $authAdapter->getDbSelect();
        $select->where('active = "TRUE"');

        // do the authentication
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ( $result->isValid() )
        {
            // Get everything except the password, and save it as
            // persistent identity.  Apply other identity-related 
            // customizations.
            $data = $authAdapter->getResultRowObject(null, self::PASSWORD);
            $this->_saveUserSessionInfo($auth, $data);

            // Set session timeout.
            Application_Model_SessionTimer::startSessionTimer();
            return true;
        }

        return false;
    }

    /**
     * Gets the authentication adapter (connection to internal or 
     * external authentication mechanism).
     *
     * @param   $username   username returned from the login form
     * @param   $password   password returned from the login form
     * @return  appropriate authentication adapter
     */
    protected function _getAuthAdapter($username, $password)
    {
        // Get authentication type (e.g., internal, LDAP, etc) and 
        // appropriate authentication adapter.
        $configs = Ramp_RegistryFacade::getInstance();
        if ( $configs->usingInternalAuthentication() )
        {
            return $this->_getInternalAuthAdapter($username, $password);
        }
        else
        {
            // TODO: Handle other types of authentication, such as LDAP.
            // For now, assume internal authentication using users table.
            throw new Exception("rampAuthenticationType must be 'internal' " .
                                "in application.ini.");
        }
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
     * Saves identity-related user session information.
     *
     * @param @auth       authentication object
     * @param $userData   user data from Ramp authentication (username, 
     *                        role, active, and possibly others)
     */
    protected function _saveUserSessionInfo($auth, $userData)
    {
        // Store user-specific information for session.
        $data = $userData;

        // Determine appropriate menu for this user's role.
        $configs = Ramp_RegistryFacade::getInstance();
        $data->menuFilename = $configs->getMenu($userData->role);

        // Store user-specific information for session.
        $auth->getStorage()->write($data);
    }

    /**
     * Goes to attempted destination (before detour to log in) or to 
     * home page if there is no attempted destination.
     */
    protected function _goToAttemptedDestOrHome()
    {
        $mysession = new Zend_Session_Namespace('Ramp_actionAttempt');
        if ( isset($mysession->destination_url) )
        {
            $destinationAttempt = $mysession->destination_url;
            unset($mysession->destination_url);
            $this->_redirect($destinationAttempt);
        }
        else
        {
            $this->_helper->redirector('index', 'index');
        }
    }

    /**
     * Formats resource name from full resource specification.
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

