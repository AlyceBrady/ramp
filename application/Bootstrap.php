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

            // Register the Authentication Type.
            if ( ! empty($rampConfigSettings['authenticationType']) )
            {
                $authType = $rampConfigSettings['authenticationType'];
                Zend_Registry::set('rampAuthenticationType', $authType);
            }
            unset($rampConfigSettings['authenticationType']);

            // Register the Default Password.
            if ( ! empty($rampConfigSettings['defaultPassword']) )
            {
                $default_pw = $rampConfigSettings['defaultPassword'];
                Zend_Registry::set('rampDefaultPassword', $default_pw);
            }
            unset($rampConfigSettings['defaultPassword']);

            // Register the Access Control List roles.
            if ( ! empty($rampConfigSettings['aclNonGuestRole']) )
            {
                $aclRoles = $rampConfigSettings['aclNonGuestRole'];
                Zend_Registry::set('rampAclRoles', $aclRoles);
            }
            unset($rampConfigSettings['aclNonGuestRole']);

            // Register Access Control List resources.
            if ( !  empty($rampConfigSettings['aclResources']))
            {
                $dirs = $rampConfigSettings['aclResources'];
                Zend_Registry::set('rampAclResources', $dirs);
            }
            unset($rampConfigSettings['aclResources']);

            // Register Access Control List rules.
            if ( !  empty($rampConfigSettings['aclRules']))
            {
                $dirs = $rampConfigSettings['aclRules'];
                Zend_Registry::set('rampAclRules', $dirs);
            }
            unset($rampConfigSettings['aclRules']);

            // Register the documentation root directory.
            if ( ! empty($rampConfigSettings['documentRoot']) )
            {
                $path = $rampConfigSettings['documentRoot'];
                Zend_Registry::set('rampDocumentRoot', $path);
            }
            unset($rampConfigSettings['documentRoot']);

            // Register the documentation root directory.
            if ( ! empty($rampConfigSettings['documentRoot']) )
            {
                $path = $rampConfigSettings['documentRoot'];
                Zend_Registry::set('rampDocumentRoot', $path);
            }
            unset($rampConfigSettings['documentRoot']);

            // Register the root directory for activities.
            if ( ! empty($rampConfigSettings['activitiesDirectory']) )
            {
                $path = $rampConfigSettings['activitiesDirectory'];
                Zend_Registry::set('rampActivitiesDirectory', $path);
            }
            unset($rampConfigSettings['activitiesDirectory']);

            // Register the root directory for table settings.
            if ( ! empty($rampConfigSettings['settingsDirectory']) )
            {
                $path = $rampConfigSettings['settingsDirectory'];
                Zend_Registry::set('rampSettingsDirectory', $path);
            }
            unset($rampConfigSettings['settingsDirectory']);

            // Register the rest of the configuration settings as a group.
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

