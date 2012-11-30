<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initRegistryWithDB()
    {
        $this->bootstrap('db');
        $db = $this->getResource('db');
        Zend_Registry::set('db', $db);
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }

    protected function _initRamp()
    {
        $configOptions = $this->getOptions();
        if ( isset($configOptions['ramp']) )
        {

            // Register the variables that may vary from Ramp 
            // application to application, or even among different 
            // environments within an application (e.g., production vs. 
            // test vs. development environments).
            $rampConfigSettings = $configOptions['ramp'];
            Zend_Registry::set('rampConfigSettings', $rampConfigSettings);

            // Register the directory that stores table settings.
            $path = $rampConfigSettings['settingsDirectory'];
            if ( ! empty($path) )
            {
                Zend_Registry::set('rampSettingsDirectory', $path);
            }

        }

	// Register the (currently empty) associated array of
	// read-in settings and setting information.
        $settings = array();
        Zend_Registry::set('rampTableViewingSequences', $settings);
    }

}

