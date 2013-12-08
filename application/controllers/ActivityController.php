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
 *
 */

class ActivityController extends Zend_Controller_Action
{

    protected $_debugging = false;

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
        $gateway = new Ramp_Activity_Gateway();
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
                case Ramp_Activity_Specification::ACTIVITY_LIST_TYPE :
                case Ramp_Activity_Specification::SETTING_TYPE :
                case Ramp_Activity_Specification::REPORT_TYPE :
                case Ramp_Activity_Specification::DOCUMENT_TYPE :
                    $this->_redirectToSource($activity);
                    break;
                case Ramp_Activity_Specification::URL_TYPE :
                    $this->_redirect($activity->getUrl());
                    break;
                case Ramp_Activity_Specification::CONTROLLER_ACTION_TYPE :
                    $this->_helper->redirector(
                            $activity->getAction(), $activity->getController(),
                            null, $activity->getParameters());
                    break;
            }
        }

// $this->_debugging = true;
        $this->_debug();

    }

    /**
     * Adds debugging information to the  basic view renderer information.
     */
    protected function _debug()
    {
        if ( $this->_debugging )
        {
            $actList = $this->view->activityList;
            $errMsg = "<pre>DEBUGGING INFO:  Request params are: "
                        . print_r($this->getRequest()->getParams(), true);
            $errMsg .= "</pre><pre>Activity List:  "
                        . var_export($actList, true) .  "</pre>";
            $this->view->errMsg = $errMsg;
        }
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

    /**
     * Redirects to a controller and activity based on the source provided.
     *
     */
    protected function _redirectToSource($activity)
    {
        $controller = $activity->getController();
        $action = $activity->getAction();
        $keyword = $activity->getParamKeyword();
        $source = urlencode($activity->getSource());
        $this->_helper->redirector($action, $controller, null,
                                   array($keyword => $source));
    }


}

