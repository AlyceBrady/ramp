<?php

/**
 * RAMP: General Records and Activity Management Infrastructure
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Ramp
 * @package    Ramp_Controller
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Config.php 23775 2011-03-01 17:25:24Z ralph $
 */
class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
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
        $authAdapter->setTableName('users')
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

?>

