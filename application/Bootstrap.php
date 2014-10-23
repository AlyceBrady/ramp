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
 * @package    Ramp
 * @copyright  Copyright (c) 2013 Alyce Brady
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    const ACT_CONTROLLER = Ramp_Controller_KeyParameters::ACT_CONTROLLER;

    const ACT_DEFAULT_ACTION = 'index';

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
     * Registers RAMP configuration settings.
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
     * Registers the ACL (Access Control List) plugin to check for
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

        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Ramp_Controller_Plugin_ACL(), 1);
        return $frontController;
    }

    /**
     * Initializes navigation
     *
     * Ashton Galloway, March 2013
     * updated by Alyce Brady, November 2013
     */
    protected function _initNavigation()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // Determine what menu to use.
        $menu = new Zend_Navigation();
        $menuFilename = $this->_determineMenu();
        $initActivity = $this->_determineInitActivity();
        $actKeyParam = Ramp_Controller_KeyParameters::
                                    getKeyParamKeyword(self::ACT_CONTROLLER);
        if ( ! empty($menuFilename) )
        {
            // Step through the menu entries in the menu.
            $menuEntries = new Zend_Config_Ini($menuFilename);
            foreach ( $menuEntries as $entry )
            {
                $uri = "/";
                $children = array();

                // Menu item's action is usually specified with url info.
                if ( empty($entry->url) )
                {
                    // No url info; use the initial activity as the action.
                    $controller = self::ACT_CONTROLLER;
                    $action = self::ACT_DEFAULT_ACTION;
                    $activity = $initActivity;
                    $uri = $this->_build_uri($controller, $action,
                                urlencode($activity), $actKeyParam);
                    $children = $this->_readActivityListFile($activity);
                }
                else
                {
                    // Use the url controller/action info provided.
                    $controller = $entry->url->controller;
                    $action = $entry->url->action;

                    // Build up rest of info from other properties.
                    $otherInfo = array();
                    foreach ( $entry->url as $key => $val )
                    {
                        // Add property info, but only if new.
                        if ( $key != 'controller' && $key != 'action' )
                        {
                            $otherInfo[$key] = urlencode($val);
                        }

                        // If this is an activity property, store its value.
                        if ( $key == $actKeyParam )
                        {
                            $children = $this->_readActivityListFile($val);
                        }
                    }
                    $uri = $this->_build_uri($controller, $action, $otherInfo);
                }

                $menu->addPage(new Zend_Config(array(
                    'label' => $entry->title,
                    'uri' => $uri,
                    'pages' => $children
                )));
            }
        }

        $view->navigation($menu);
    }

    /**
     * Determines the menu to use, which is role-dependent if this is an 
     * authenticated user whose role has its own menu or the defined 
     * default menu otherwise.
     */
    protected function _determineMenu()
    {
        // Is this an authenticated user whose role dictates a specific menu?
        $auth = Zend_Auth::getInstance();
        if ( $auth->hasIdentity() && is_object($auth->getIdentity()) )
        {
            $user = $auth->getIdentity();
            return $user->menuFilename;
        }

        // No, so return the default menu.
        $configs = Ramp_RegistryFacade::getInstance();
        return $configs->getDefaultMenu();
    }

    /**
     * Determines the initial activity to use, which is role-dependent
     * if this is an authenticated user whose role has its own
     * initial activity or the defined default menu otherwise.
     */
    protected function _determineInitActivity()
    {
        // Is this an auth. user whose role dictates a specific activity?
        $auth = Zend_Auth::getInstance();
        if ( $auth->hasIdentity() && is_object($auth->getIdentity()) )
        {
            $user = $auth->getIdentity();
            return $user->initialActivity;
        }

        // No, so return the default initial activity.
        $configs = Ramp_RegistryFacade::getInstance();
        return $configs->getDefaultInitialActivity();
    }

    /**
     * Builds a URI (relative to the base URI) out of the
     * controller/action/keyword/param (or 
     * controller/action/param_key_val_pairs).
     */
    protected function _build_uri($controller, $action, $params,
                                  $keyword = null )
    {
        $uri = $controller .  DIRECTORY_SEPARATOR . $action;
        if ( is_array($params) )
        {
            foreach ( $params as $key => $val )
            {
                $uri .= DIRECTORY_SEPARATOR . $key .
                        DIRECTORY_SEPARATOR . urlencode($val);
            }
        }
        else if ( ! empty($keyword) )
        {
            $uri .= DIRECTORY_SEPARATOR . $keyword .
                    DIRECTORY_SEPARATOR . urlencode($params);
        }
        return $uri;
    }

    /**
     * Reads the activity list file containing menu sub-items.
     *
     * Ashton Galloway, March 2013
     * updated by Alyce Brady, November 2013
     *
     * @param $filename  the name of the activity list file
     */
    protected function _readActivityListFile($filename)
    {
        // if $filename is not set (or is empty), there is nothing to do.
        if ( empty($filename) )
        {
            return array();
        }

        // Read the activity list 
        $gateway = new Ramp_Activity_Gateway();
        $activityList = $gateway->getActivityList($filename);

        // Build up the menu sub-items.
        $pages = array();
        foreach ( $activityList as $activity )
        {
            // URI for menu destination depends on activity type.
            if ( $activity->isSeparator() || $activity->isComment() )
            {
                continue;
            }
            else if ( $activity->isControllerAction() )
            {
                $uri = $this->_build_uri($activity->getController(),
                        $activity->getAction(), $activity->getParameters());
            }
            else if ( $activity->isUrl() )
            {
                $uri = $activity->getSource();
            }
            else
            {
                $keyword = $activity->getParamKeyword();
                $source = urlencode($activity->getSource());
                $uri = $this->_build_uri($activity->getController(),
                        $activity->getAction(), $source, $keyword);
            }
            array_push($pages, array(
                            'label' => $activity->getTitle(),
                            'uri' => $uri
                        ));
        }
        return $pages;
    }

}

