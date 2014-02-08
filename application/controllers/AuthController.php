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
    const USERS_TABLE       = Ramp_Auth_DbTable_Users::TABLE_NAME;
    const USERNAME          = Ramp_Auth_DbTable_Users::USERNAME;
    const PASSWORD          = Ramp_Auth_DbTable_Users::PASSWORD;
    const ACTIVE            = Ramp_Auth_DbTable_Users::ACTIVE;
    const IS_ACTIVE         = Ramp_Auth_DbTable_Users::IS_ACTIVE;

    /* Form keywords related to initializing a password. */
    const NEW_PW            = 'new_password';
    const CONFIRMED_PW      = 'confirm_password';

    /* Keywords related to passing parameters to actions in this controller. */
    const DETAILS           = 'details';
    const CONTROLLER        = Ramp_Activity_Specification::CONTROLLER;
    const ACTION            = Ramp_Activity_Specification::ACTION;

    /* Button labels */
    const SUBMIT_BUTTON = 'submit';
    const LOGIN         = 'Login';
    const RESET_PW      = 'Reset Password';
    const SAVE          = 'Save';
    const DONE          = 'Done';
    const CANCEL        = 'Cancel';


    protected $_submittedButton;
    protected $_logger = null;

    public function init()
    {
        // Initialize action controller here

        $this->_submittedButton = $this->_getParam(self::SUBMIT_BUTTON, '');

        $registry = Ramp_RegistryFacade::getInstance();
        $logfilePath = $registry->getLogfilePath();
        if ( ! empty($logfilePath) )
        {
            $this->_logger = new Zend_Log();
            $this->_logger->addWriter(new Zend_Log_Writer_Stream($logfilePath));
            $this->_logger->addFilter(
                            new Zend_Log_Filter_Priority(Zend_Log::INFO));
        }
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
        // Is the user already logged in?  Go directly to destination.
        $auth = Zend_Auth::getInstance();
        if ( $auth->hasIdentity() )
        {
            $this->_goToAttemptedDestOrHome();
        }

        // Instantiate the form that asks the user to log in.
        $form = new Ramp_Form_Auth_LoginForm();

        // Initialize the error message to be empty.
        $this->view->formResponse = '';

        // For initial display, just render the form.  If this is the 
        // callback after the form has been filled out, process the form.
        if ( $this->_thisIsInitialDisplay() )
        {
            // Form will be rendered when function returns
        }
        elseif ( $this->_submittedButton == self::LOGIN )
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
                    // Release any stale locks previously held by this user.
                    $locksTable = new Ramp_Lock_DbTable_Locks();
                    $locksTable->releaseLocksHeldBy($username);

                    // If user was attempting to go somewhere, go there.
                    // Otherwise, go to Home page.
                    $this->_goToAttemptedDestOrHome();
                }
                else
                    { $this->view->formResponse = 'Login failed'; }
            }
        }
        else  // Cancel
        {
            $this->_redirect('/');  // Use this redirect when not logged in
        }

        // Render the view.
        $this->view->form = $form;
        $this->view->buttonList = array(self::LOGIN, self::CANCEL);
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
        $this->_redirect('/');  // Use this redirect when not logged in
    }

    /**
     * Informs users when they are trying to perform an action for which 
     * they are not authorized.
     * Justin Leatherwood, November 2012.
     */ 
    public function unauthorizedAction()
    {
        // See if specific information was passed as a parameter.
        $message = urldecode($this->_getParam(self::DETAILS, ""));

        $this->view->errorMsg =
                'Sorry, you are not authorized to perform this action';
        if ( $message != "" )
        {
            $this->view->errorMsg .= ' (' . $message . ')';
        }
        $this->view->errorMsg .= '.';
    }

    /**
     * Prompts user to initialize the password.
     *     (Internal authentication only!)
     */ 
    public function initPasswordAction()
    {
        // Get the username.
        $this->view->username = $username =
                        urldecode($this->_getParam(self::USERNAME));

        // Instantiate the form that prompts user for new password.
        $form = new Ramp_Form_Auth_SetPasswordForm();
        $form->populate(array(self::USERNAME => $username));
        $this->view->formResponse = "";

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            $this->view->msg = 'Password must be initialized.';
        }
        elseif ( $this->_submittedButton == self::SAVE )
        {
            // Process the filled-out form that has been posted.
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
        else  // Cancel
        {
            $this->_redirect('/');  // Use this redirect when not logged in
        }

        // Render the view.
        $this->view->form = $form;
        $this->view->buttonList = array(self::SAVE, self::CANCEL);
    }

    /**
     * Allows user to change their password.  (Internal authentication only!)
     */ 
    public function changePasswordAction()
    {
        // If user has been logged out below (incorrect password) and now wants
        // to login, go directly there.
        if ( $this->_submittedButton == self::LOGIN )
        {
            $this->_helper->redirector('login', 'auth');
        }

        // Get the logged in user's username.
        $auth = Zend_Auth::getInstance();
        if ( ! $auth->hasIdentity() )
        {
            $this->view->formResponse = "Must be logged in to change password.";
            return;
        }
        $this->view->username = $username = $auth->getIdentity()->username;

        // Instantiate the form that prompts user for new password.
        $form = new Ramp_Form_Auth_ChangePasswordForm();
        $form->populate(array(self::USERNAME => $username));
        $this->view->formResponse = "";

        // Specify the view that will be rendered when the function returns.
        $this->view->form = $form;
        $this->view->buttonList = array(self::SAVE, self::CANCEL);

        // Initial display, or ready to process filled-out form?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Form will be rendered when function returns
        }
        elseif ( $this->_submittedButton == self::SAVE )
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
                {
                    $this->view->formResponse =
                        'Old password is not correct.  ' .
                        'You have been logged out.';
                    $this->view->form = null;
                    $this->view->buttonList = array(self::LOGIN, self::CANCEL);
                }
            }
            else
            { $this->view->formResponse = 'Invalid input; please try again.'; }
        }
        else  // Cancel
        {
            $this->_helper->redirector('index', 'index'); // Still logged in
        }

    }

    // The remaining actions are administrative actions.

    /**
     * Resets a user's password.  (Internal authentication only!)
     */ 
    public function resetPasswordAction()
    {
        // Instantiate the form that asks whose password to reset.
        $form = new Ramp_Form_Auth_ResetPasswordForm();

        // Initialize the messages to be empty.
        $this->view->formResponse = '';
        $this->view->msg = '';

        // For initial display, just render the form.  If this is the 
        // callback after the form has been filled out, process the form.
        if ( $this->_thisIsInitialDisplay() )
        {
            // Form will be rendered when function returns
        }
        elseif ( $this->_submittedButton == self::RESET_PW )
        {
            // Process the filled-out form that has been posted:
            // if the input values are valid, reset the password.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $username = $formData[self::USERNAME];
                $userTable = new Ramp_Auth_DbTable_Users();
                if ( $userTable->resetPassword($username) )
                {
                    $this->view->msg = 'Password for ' . $username .
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
        $this->view->buttonList = array(self::RESET_PW, self::CANCEL);
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
        $userInfo = new Ramp_Auth_DbTable_Users();
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
        $authInfo = new Ramp_Auth_DbTable_Auths();
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
        $msgs = array();
        $msgs[] = "";
        $msgs[] = "<h4>Roles:</h4>" . var_export($acl->getRoles(), true);
        $msgs[] = "<h4>Resources:</h4>"
                    . var_export($acl->getResources(), true);
        // Too hard to return all rules, so just concentrate on rules 
        // from Registry and Database.
        $msgs[] = "<h4>Rules:</h4>"
                    . "<h5>"
                    . "<em>It's too hard to dig out and report on all rules "
                    . "(and derived rules), so these are just rules from "
                    . "Registry (application.ini) and Database.</em><br />"
                    . "</h5>"
                    . var_export($acl->getRules(), true);
        $this->view->messages = $msgs;
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
     * Logging adapted from Zend Auth Adapter Ldap reference manual page
     *      (Zend 1.11) as of January 2014.
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

        // Do the authentication.
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ( $result->isValid() )
        {
            // Get user information to save as persistent identity.
            $userTable = new Ramp_Auth_DbTable_Users();
            $userInfo = $userTable->getUserInfo($username);
            if ( $userInfo->{self::ACTIVE} == self::IS_ACTIVE )
            {
                // Save persistent identity info with session; set timeout.
                $this->_saveUserSessionInfo($auth, $userInfo);
                Ramp_Auth_SessionTimer::startSessionTimer();

                // Log messages or successful user login.
                // $this->_logLogin($username, $result);

                return true;
            }
            else
            {
                $auth->clearIdentity();
            }
        }

        // $this->_logLogin($username, $result);
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
        // Get authentication type (e.g., internal, ldap) and 
        // appropriate authentication adapter.
        $configs = Ramp_RegistryFacade::getInstance();
        if ( $configs->usingInternalAuthentication() )
        {
            return $this->_getInternalAuthAdapter($username, $password);
        }
        else
        {
            // For now, assume internal authentication using users table.
            throw new Exception("rampAuthenticationType must be 'internal' " .
                                "in application.ini.");

            // LDAP Authentication (including Active Directory, OpenLDAP, etc)
            return $this->_getLDAPAuthAdapter($username, $password);

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

        // If the username is not a valid, active username, getting the 
        // adapter fails.
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
     * Sets up an authentication adapter for LDAP authentication.
     *
     * Modified from Zend Auth Adapter Ldap reference manual page
     *      (Zend 1.11) as of January 2014.
     *
     * @param   $username   username returned from the login form
     * @param   $password   password returned from the login form
     * @return  the adapter
     */ 
    protected function _getLDAPAuthAdapter($username, $password)
    {
        $ldapOptions = Zend_Registry::get('ldap');

        return new Zend_Auth_Adapter_Ldap($ldapOptions, $username,
                                          $password);
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

        // Determine appropriate menu & initial activity for this user's role.
        $configs = Ramp_RegistryFacade::getInstance();
        $data->menuFilename = $configs->getMenu($userData->role);
        $data->initialActivity = $configs->getInitialActivity($userData->role);

        // Store user-specific information for session.
        $auth->getStorage()->write($data);
    }

    /**
     * Logs successful authentication or authentication error messages.
     *
     * Logging adapted from Zend Auth Adapter Ldap reference manual page
     *      (Zend 1.11) as of January 2014.
     * @param   $username    username returned from the login form
     * @param   $auth_result result from authentication
     */
    protected function _logLogin($username, $auth_result)
    {
        // Log successful user login or authentication error messages.
        if ( ! empty($this->_logger) )
        {
            $messages = $auth_result->getMessages();
            if ( empty($messages) )
            {
                // Success!
                $this->_logger->log("Login: $username", Zend_Log::INFO);
            }
            else
            {
                $this->_logger->log("Failed authentication: $username",
                                    Zend_Log::INFO);
                foreach ( $messages as $i => $message )
                {
                    $message = str_replace("\n", "\n  ", $message);
                    $this->_logger->log("  Failed auth: $i: $message",
                                        Zend_Log::INFO);
                }
            }
        }
    }

    /**
     * Goes to attempted destination (before detour to log in) or to 
     * home page if there is no attempted destination.
     */
    protected function _goToAttemptedDestOrHome()
    {
        // Was there an attempted destination?
        $controllerKeyword = '_' . self::CONTROLLER;
        $actionKeyword = '_' . self::ACTION;
        $dest_controller = $this->_getParam($controllerKeyword, "");
        $dest_action = $this->_getParam($actionKeyword, "");
        if ( !empty($dest_controller) && ! empty($dest_action) )
        {
            // Redirect to attempted destination.
            $dest_params = $this->getRequest()->getUserParams();
            unset($dest_params[$controllerKeyword]);
            unset($dest_params[$actionKeyword]);
            $this->_helper->redirector($dest_action, $dest_controller,
                                       null, $dest_params);
        }
        else
        {
            // Redirect to "home" (initial activity).
            $this->_helper->redirector('index', 'index');
        }
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
            $userTable = new Ramp_Auth_DbTable_Users();
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

