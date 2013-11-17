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
 * @version    $Id: ActivityController.php 1 2012-07-12 alyce $
 *
 */

class ActivityController extends Zend_Controller_Action
{
    // Valid controller types
    const DOC_CONTROLLER = Ramp_Controller_KeyParameters::DOC_CONTROLLER;
    const REP_CONTROLLER = Ramp_Controller_KeyParameters::REP_CONTROLLER;
    const TBL_CONTROLLER = Ramp_Controller_KeyParameters::TBL_CONTROLLER;

    // Keywords for sending parameters to controller/action combinations
    const AL_PARAM        = Ramp_Controller_KeyParameters::ACT_KEY_PARAM;
    const DOC_PARAM       = Ramp_Controller_KeyParameters::DOC_KEY_PARAM;
    const SETTING_PARAM   = Ramp_Controller_KeyParameters::SETTING_PARAM;

    protected $_actSpecName;

    /**
     * Initializes attributes for this object.
     */
    public function init()
    {
        // Get the activity specification associated with the name
        // passed as a parameter.
        $this->_actSpecName =
            Ramp_Controller_KeyParameters::getKeyParam($this->getRequest());
    }

    /**
     * Acts on an Activity specification or list.
     */
    public function indexAction()
    {
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_actSpecName);

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Make the activity list available to the View Renderer.
            $this->view->activityList = $actList;
            $this->view->activityTitle = 
                    $gateway->getActivityListTitle($this->_actSpecName);
        }
        else        // Callback based on a button action.
        {
            // The selected activity name is the only parameter that
            // was posted.
            $postParamNames = array_keys($this->getRequest()->getPost());
            $activity = $actList[$postParamNames[0]];
            $type = $activity->getType();

            switch ( $type )
            {
                case Application_Model_ActivitySpec::ACTIVITY_LIST_TYPE :
                    $this->_redirectToSource(null, 'index', self::AL_PARAM,
                                             $activity);
                    break;
                case Application_Model_ActivitySpec::SETTING_TYPE :
                    $this->_redirectToSource(self::TBL_CONTROLLER, 'index',
                                             self::SETTING_PARAM, $activity);
                    break;
                case Application_Model_ActivitySpec::REPORT_TYPE :
                    $this->_redirectToSource(self::REP_CONTROLLER, 'index',
                                             self::SETTING_PARAM, $activity);
                    break;
                case Application_Model_ActivitySpec::DOCUMENT_TYPE :
                    $this->_redirectToSource(self::DOC_CONTROLLER, 'index',
                                             self::DOC_PARAM, $activity);
                    break;
                case Application_Model_ActivitySpec::URL_TYPE :
                    $this->_redirect($activity->getUrl());
                    break;
                case Application_Model_ActivitySpec::CONTROLLER_ACTION_TYPE :
                    $this->_helper->redirector(
                            $activity->getAction(), $activity->getController(),
                            null, $activity->getParameters());
                    break;
            }
        }

    }

    /**
     * Redirects to a controller and activity based on the source provided.
     *
     */
    protected function _redirectToSource($controller, $action, $keyword,
                                         $activity)
    {
        $source = urlencode($activity->getSource());
        $this->_helper->redirector($action, $controller, null,
                                   array($keyword => $source));
    }

    /**
     * Returns true if the current request represents the initial 
     * display for the current action.  A return of false, therefore, 
     * indicates that the current request represents the callback
     * specifying an activity to follow.
     *
     */
    protected function _thisIsInitialDisplay()
    {
        return !  $this->getRequest()->isPost();
    }


}

