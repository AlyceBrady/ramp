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
 * @version    $Id: AuthController.php 1 2012-07-12 alyce $
 * @version    $Id: AuthController.php 1 2012-11-28 Justin, Chris Cain, Alyce $
 *
 */
class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_forward('login');
    }

    /**
     * Prompts user for  username and password.
     * Modified from Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 134, and the Zend Manual's zend.form.quickstart.
     */ 
    public function loginAction()
    {
        // Instantiate the form that asks the user to log in.
        $form = new Application_Form_LoginForm();

        // Initialize the error message to be empty.
        $this->view->formResponse = '';

        // For security purposes, only proceed if this is a POST request.
        if ($this->getRequest()->isPost())
        {
            // Process the filled-out form that has been posted:
            // if the input values are valid, attempt to authenticate.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                if ( $this->_authenticate($formData) )
                {
                    $this->_helper->redirector('index', 'index');
                }
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
     * Sends unauthorized user to a "not authorized" page.
     * Justin Leatherwood, November 2012.
     */ 
    public function unauthorizedAction()
    {
        $this->view->errorMsg =
                'Sorry, you are not authorized for this action.';
    }

    /**
     * Adds users to authentication table (table of usernames, etc.).
     * Justin Leatherwood and/or Chris Cain, November 2012.
     */ 
    public function addUserAction()
    {
        $form = new Application_Form_UserEdit();
        $this->view->form = $form;

        if ($this->getRequest()->isPost())
        {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $username = $form->getValue('username');
                $password = $form->getValue('password');
                $role = $form->getValue('role');
                $first_name = $form->getValue('first_name');
                $last_name = $form->getValue('last_name');
                $email = $form->getValue('email'); 
                $user = new Application_Model_DbTable_Users();
                $user->addUser($username, $password, $first_name,
                               $last_name, $email, $role);
                $this->_helper->redirector('index', 'index');
            }
            else
            {
                $form->populate($formData);
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
     * Validates the username and password.  If valid, stores identity 
     * information (except for the password) to provide a persistent 
     * identity.
     * Modified from Zend Framework in Action by
     *      Allen, Lo, and Brown, 2009, p. 134
     *
     * @param   $formData   data returned from the login form
     * @return  boolean indicating whether authentication was successful
     */ 
    protected function _authenticate($formData)
    {
        // do the authentication
        $authAdapter = $this->_getAuthAdapter($formData);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ( $result->isValid() )
        {
            // Get everything except the password, and store it for a
            // persistent identity.
            $data = $authAdapter->getResultRowObject(null, 'password');
            $auth->getStorage()->write($data);
            return true;
        }
        else
        {
            $this->view->formResponse = 'Login failed';
            return false;
        }
    }

    /**
     * Sets up the authentication adapter.  (This particular adapter
     *      uses a database table.)
     * Modified from Zend Framework in Action by
     *      Allen, Lo, and Brown, 2009, p. 136
     *
     * @param   $formData   data returned from the login form
     * @return  the adapter
     */ 
    protected function _getAuthAdapter($formData)
    {
        $dbAdapter = Zend_Registry::get('db');

        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter->setTableName('ramp_auth_users')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password')
                ; // ->setCredentialTreatment('SHA1(?)');

        // get "salt" for better security
        $config = Zend_Registry::get('config');
/*
 * TODO: Fix this password stuff!
 * Doesn't work, presumably because the password wasn't salted when entered 
 * into the database ???  And, has a salt been provided in the 
 * configuration file ???
        $salt = $config->auth->salt;
        $password = $salt.$formData['password'];
*/
$password = $formData['password'];

        $authAdapter->setIdentity($formData['username']);
        $authAdapter->setCredential($password);

        return $authAdapter;
    }

}

