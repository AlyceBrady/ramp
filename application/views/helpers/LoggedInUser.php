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
        $auth = Zend_Auth::getInstance();
        $string = "";
        if ( $auth->hasIdentity() )
        {
            $logoutURL = $this->view->url(array('controller'=>'auth',
                                'action'=>'logout'));
            $user = $auth->getIdentity();
            $username = $this->view->escape($user->username);

            $string = 'Logged in as ' . $username .  ' |' .
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
