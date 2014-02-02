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
 * @package    Ramp_Model
 * @copyright  Copyright (c) 2013 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

class Ramp_Auth_SessionTimer
{

    protected static $_singleton = null;


    // STATIC FUNCTIONS

    /**
     * Gets the singleton instance of the SessionTimer class.
     */
    public static function getInstance()
    {
        self::$_singleton = self::$_singleton ? :
                            new Ramp_Auth_SessionTimer();
        return self::$_singleton;
    }

    /**
     * Starts the session timer. (Provides an interface to the
     * expiration of the authenticated session namespace).
     */
    public static function startSessionTimer()
    {
        $configs = Ramp_RegistryFacade::getInstance();
        $timeout = $configs->getSessionTimeout();
        if ( $timeout > 0 )
        {
            $sessionInfo = Zend_Auth::getInstance()->getStorage();
            $ns = new Zend_Session_Namespace($sessionInfo->getNamespace());
            $ns->setExpirationSeconds($timeout);
        }
    }


    // CONSTRUCTOR AND INSTANCE FUNCTIONS

    /**
     * Class constructor
     *
     * Creates a singleton object representing the session timer (an 
     * interface to the expiration of the authenticated session 
     * namespace).
     */
    protected function __construct()
    {
    }

}

