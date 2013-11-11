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
 * @version    $Id: IndexController.php 1 2012-07-12 alyce $
 *
 */

class IndexController extends Zend_Controller_Action
{
    const SETTING_NAME = 'setting';     // TODO: should use one in TableController!
    const AL_NAME = 'activity';     // TODO: should use one in ActivityController!
    // Define constants that define basic configuration variables.
    const CONFIG_SETTINGS = "rampConfigSettings";
    const TITLE = "title";
    const SUBTITLE = "subtitle";
    const MENU_FILENAME = "menuFilename";
    const INITIAL_ACTIVITY = "initialActivity";
    const STYLE_SHEET = "css";
    const TAB_TITLE = "applicationShortName";
    const ICON = "icon";

    // Define instance variables that hold basic configuration variables.
    protected $_menuFilename = null;
    protected $_initialActivity = null;

    public function init()
    {
        /* Initialize action controller here */

        /* Doesn't work!!!
        // Make sure that TableController has been loaded, so that this 
        // controller can use its constants for inter-controller 
        // communication.
        if ( ! class_exists('TableController') )
        {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass('TableController');
        }
         */

        // Get various applications variables from Zend_Registry.
        if ( Zend_Registry::isRegistered(self::CONFIG_SETTINGS) )
        {
            $configSettings = Zend_Registry::get(self::CONFIG_SETTINGS);

            // Get the menu filename from Zend_Registry.
            $this->_menuFilename = isset($configSettings[self::MENU_FILENAME]) ?
                    $configSettings[self::MENU_FILENAME] : null;

            // Get the initial activity from Zend_Registry.
            $this->_initialActivity =
                isset($configSettings[self::INITIAL_ACTIVITY]) ?
                    $configSettings[self::INITIAL_ACTIVITY] : null;

            // Get appropriate title, subtitle, tab title, and icon
            // information from Zend_Registry.
            $tabTitle = isset($configSettings[self::TAB_TITLE]) ?
                            $configSettings[self::TAB_TITLE] : null;
            if ( ! empty($tabTitle) )
            {
                $this->view->headTitle($tabTitle)->setSeparator(' - ');
            }
            $this->view->icon = isset($configSettings[self::ICON]) ?
                            $configSettings[self::ICON] : null;
            $this->view->pageTitle = isset($configSettings[self::TITLE]) ?
                            $configSettings[self::TITLE] : null;
            $this->view->pageSubTitle = isset($configSettings[self::SUBTITLE]) ?
                            $configSettings[self::SUBTITLE] : null;

            // Get the appropriate cascading stylesheet from Zend_Registry.
            $stylesheet =
                isset($configSettings[self::STYLE_SHEET]) ?
                    $configSettings[self::STYLE_SHEET] : null;
            if ( ! empty($stylesheet) )
            {
                $this->view->headLink()->prependStylesheet($stylesheet);
            }

        }

// $this->_getAuthDebuggingInfo();

    }

    public function indexAction()
    {
        // Redirect to the appropriate initial activity for this 
        // application and environment, if one has been specified
        // (see configs/application.ini).  Otherwise ask the user to 
        // choose an initial activity.
        if ( $this->_initialActivity != null )
        {
            $activityListName = urlencode($this->_initialActivity);
            $params = array(self::AL_NAME => $activityListName);

            $this->_helper->redirector('index', 'activity', null, $params);
        }
        else
        {
            $this->_forward('choose-activity-list');
        }
    }

    /**
     * Show the user a form from which they should choose a
     * table/table setting.
     * DEPRECATED?  Certainly no longer used as initial page, as it was 
     * in the very first version of RAMP.
     */
    public function chooseTableAction()
    {
        // Instantiate the form that asks the user which table setting to 
        // retrieve.
        $form = new Application_Form_TableChoice();
        $form->submit->setLabel('Retrieve');

        // Specify the view to render.
        $this->view->form = $form;

        if ($this->getRequest()->isPost())
        {
            // Process the filled-out form that has been posted:
            // if the changes are valid, view the appropriate table.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $settingName = $form->getValue('settingName');
                $settingName = urlencode($settingName);
                $params = array(self::SETTING_NAME => $settingName);

                $this->_helper->redirector('index', 'table', null, $params);
            }
            else
            {
                $form->populate($formData);
            }
        }
    }

    /**
     * Show the user a form from which they should choose an
     * activity to perform.
     */
    public function chooseActivityListAction()
    {
        // Instantiate the form that asks the user which activity list to 
        // retrieve.
        $form = new Application_Form_ActivityChoice();
        $form->submit->setLabel('Retrieve');

        // Specify the view to render.
        $this->view->form = $form;

        if ($this->getRequest()->isPost())
        {
            // Process the filled-out form that has been posted:
            // if the changes are valid, view the appropriate activity list.
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData))
            {
                $activityListName = $form->getValue('activityName');
                $activityListName = urlencode($activityListName);
                $params = array(self::AL_NAME => $activityListName);

                $this->_helper->redirector('index', 'activity', null, $params);
            }
            else
            {
                $form->populate($formData);
            }
        }
    }

    /**
     * Provides the main menu.
     * From Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 74.
     */ 
    public function menuAction()
    {
        // TODO: Convert this to use Zend_Navigation

        // Assigns the menu to the view & changes the response placeholder.
        $this->view->menu = $this->_readMenu();
        $this->_helper->viewRenderer->setResponseSegment('menu');
    }

    /**
     * Reads in the menu.
     *
     * @return string   filename
     *
     */
    protected function _readMenu()
    {
        $menu =  new Zend_Config_Ini($this->_menuFilename);
        return $menu;
    }


    protected function _getAuthDebuggingInfo()
    {
        $roles = Zend_Registry::get('roles');
        $resources = Zend_Registry::get('resources');
        $rules = Zend_Registry::get('rules');
        $this->view->authDebugging .= "<blockquote><b>Roles</b>: " . print_r($roles, true) . "</blockquote>";
        $this->view->authDebugging .= "<blockquote><b>Resources</b>: " . print_r($resources, true) . "</blockquote>";
        $this->view->authDebugging .= "<blockquote><b>Rules</b>: " . print_r($rules, true) . "</blockquote>";
    }

}

