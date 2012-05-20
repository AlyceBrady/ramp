<?php

/**
 * RAMP: Records and Activity Management Program
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Ramp
 * @package    Ramp_Controller
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Config.php 23775 2011-03-01 17:25:24Z ralph $
 *
 */

class ActivityController extends Zend_Controller_Action
{
    const SETTING_KEYWORD = '_setting';  // TODO: should use one in TableController!
    const AL_KEYWORD = 'activity';

    protected $_actListName;

    public function init()
    {
        // Get the activity list associated with the name passed as a 
        // parameter.
        $rawActivityName = $this->_getParam(self::AL_KEYWORD);
        $this->_activityListName = urldecode($rawActivityName);
    }

    public function indexAction()
    {
        $gateway = new Application_Model_ActivityGateway();
        $actList = $gateway->getActivityList($this->_activityListName);

        // Is this the initial display or a callback from a button action?
        if ( $this->_thisIsInitialDisplay() )
        {
            // Make the activity list available to the View Renderer.
            $this->view->activityList = $actList;
            $this->view->activityTitle = 
                    $gateway->getActivityListTitle($this->_activityListName);
        }
        else        // Callback based on a button action.
        {
            // The selected activity name is the only parameter being 
            // posted.
            $postParamNames = array_keys($this->getRequest()->getPost());
            $activity = $actList[$postParamNames[0]];
            $type = $activity->getType();
            $source = $activity->getSource();

            if ( $type == Application_Model_ActivitySpec::SETTING_TYPE )
            {
                $source = urlencode($source);
                $this->_helper->redirector('index', 'table', null,
                    array(self::SETTING_KEYWORD => $source));
            }

            elseif ( $type ==
                        Application_Model_ActivitySpec::ACTIVITY_LIST_TYPE )
            {
                $source = urlencode($source);
                $this->_helper->redirector('index', null, null,
                    array(self::AL_KEYWORD => $source));
            }
        }
    }

    /**
     * Returns true if the current request represents the initial 
     * display for the current action.  A return of false, therefore, 
     * indicates that the current request represents the callback with 
     * fields to add, modify, or search filled in.
     *
     */
    protected function _thisIsInitialDisplay()
    {
        return !  $this->getRequest()->isPost();
    }


}

