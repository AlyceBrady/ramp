<?php

class Zend_View_Helper_LoggedInUser
{
    protected $view;

    function setView($view)
    {
        $this->view = $view;
    }

    /**
     * Logs the user out.
     * From Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 137.
     */
    function loggedInUser()
    {
        $configs = Ramp_RegistryFacade::getInstance();

        $auth = Zend_Auth::getInstance();
        $string = "";
        if ( $auth->hasIdentity() )
        {
            $changepwURL = $this->view->url(array('controller'=>'auth',
                                'action'=>'change-password'));
            $logoutURL = $this->view->url(array('controller'=>'auth',
                                'action'=>'logout'));
            $user = $auth->getIdentity();
            $username = $this->view->escape($user->username);
            $changePW = "";
            if ( $configs->usingInternalAuthentication() )
            {
                $changePW = '<a href=' . $changepwURL .
                                    '>Change password</a>' . ' |';
            }

            $string = 'Logged in as ' . $username .  ' |' .  $changePW .
                '<a href=' . $logoutURL . '>Log out</a>';
        }
        else
        {
            $loginURL = $this->view->url(array('controller'=>'auth',
                'action'=>'login'));

            $string = '<a href=' . $loginURL . '>Log in</a>';
        }

        return $string;
    }

}
