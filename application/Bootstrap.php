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
            // Register the directory that stores the application menu.
            $rampConfigSettings = $configOptions['ramp'];
            $path = $rampConfigSettings['menuFilename'];
            if ( ! empty($path) )
            {
                Zend_Registry::set('rampMenuFilename', $path);
            }

            // Register the directory that stores table settings.
            $rampConfigSettings = $configOptions['ramp'];
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

