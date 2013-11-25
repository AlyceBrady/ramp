<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initRegistryWithDB()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        Zend_Registry::set('db', $db);
        try
        {
            $dbAdapter = Zend_Registry::get('db');
            $connection = $dbAdapter->getConnection();
        }
        catch (Exception $e)
        {
            $configInfo = $db->getConfig();
            throw new Exception("Error: Cannot access database '".
                $configInfo['dbname'] .  "' using user '" .
                $configInfo['username'] . "'@'" .
                $configInfo['host'] . "'.");
        }
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    /**
     * Register RAMP configuration settings.
     */
    protected function _initRamp()
    {
        $configOptions = $this->getOptions();
        if ( isset($configOptions['ramp']) )
        {
            // Read the configuration settings that may vary from Ramp 
            // application to application, or even among different 
            // environments within an application (e.g., production vs. 
            // test vs. development environments).
            $rampConfigSettings = $configOptions['ramp'];
            Zend_Registry::set('rampConfigSettings', $rampConfigSettings);
        }

	// Register the (currently empty) associated array of
	// read-in settings and setting information.
        $settings = array();
        Zend_Registry::set('rampTableViewingSequences', $settings);
    }

    /**
     * Register the ACL (Access Control List) plugin to check for
     * authorization to perform various actions.
     *
     * Based on a tutorial found at:
     *    http://www.ens.ro/2012/03/20/
     *        zend-authentication-and-authorization-tutorial-with-
     *        zend_auth-and-zend_acl/
     * -- Justin Leatherwood, 13 November 2012
     */
    protected function _initACL()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Ramp_');

        $objFront = Zend_Controller_Front::getInstance();
        $objFront->registerPlugin(new Ramp_Controller_Plugin_ACL(), 1);
        return $objFront;
    }

}

