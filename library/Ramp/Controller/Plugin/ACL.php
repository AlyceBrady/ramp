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
 * @copyright  Copyright (c) 2012 Justin Leatherwood
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 * @version    $Id: ACL.php 1 2012-11-13 Justin Leatherwood $
 *
 * This class was added based on a tutorial found at:
 * http://www.ens.ro/2012/03/20/zend-authentication-and-authorization-tutorial-with-zend_auth-and-zend_acl/
 *
 */
class Ramp_Controller_Plugin_ACL extends Zend_Controller_Plugin_Abstract
{
    const DEFAULT_ROLE = 'guest';
 
    /**
     * Take care of any items to do before the actual dispatch:
     *      Check that the user is authorized to do this action.
     * Based on a tutorial found at:
     *    http://www.ens.ro/2012/03/20/
     *        zend-authentication-and-authorization-tutorial-with-
     *        zend_auth-and-zend_acl/
     *
     * @param Zend_Controller_Request_Abstract $request  the user request 
     *      that this dispatch loop is addressing
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Get the current session.
        $mysession = new Zend_Session_Namespace('mysession');

        // Create a new Zend ACL object.
        $acl = new Ramp_Acl();

        // Determine what resource is being requested.
        $requestedResource = $this->_getResource($request, $acl);

        // Store Zend redirector for future use.
        $zendRedirector = $this->_getRedirector();

        // Check if there is an authenticated user.
        $auth = Zend_Auth::getInstance();
        if ( $auth->hasIdentity() && is_object($auth->getIdentity()) )
        {
            // Get the identity of the user.
            $user = $auth->getIdentity();

            // Check the user role against the request controller and action.
            if ( $acl->isAllowed($user->role, $requestedResource) )
            {
                // Allowed!
                return;
            }
            else
            {
                // Not allowed -- set the url accordingly.
                $mysession->destination_url = $request->getPathInfo();
                return $zendRedirector->setGotoUrl('auth/unauthorized');
            }
        } 

        // If this is not an authenticated user, check the default role
        // against the request controller/action.
        else if ( ! $acl->isAllowed(self::DEFAULT_ROLE, $requestedResource) )
        {
            // Set the url accordingly.
            $mysession->destination_url = $request->getPathInfo();
            return $zendRedirector->setGotoUrl('auth/login');
        }
    }

    /**
     * Determine what resource is being requested.
     *
     * @param Zend_Controller_Request_Abstract $request  the user request 
     *      that this dispatch loop is addressing
     * @param Zend_Acl $acl  the Access Control List
     */
    protected function _getResource(Zend_Controller_Request_Abstract $request,
                                    Zend_Acl $acl)
    {
        // Construct the requestedResource from the controller, action,
        // and  possibly the activity or table.
        $controllerName = $request->getControllerName();
        $requestedResource = $controllerName . '::'
                              . $request->getActionName();

        if ( $controllerName == 'activity' )
        {
            $activityListName = urldecode($request->getUserParam('activity'));
            $requestedResource .= "::" . dirname($activityListName);
        }
        else if ( $controllerName == 'table' || $controllerName == 'report' )
        {
            $seqName = urldecode($request->getUserParam('_setting'));
            $tblViewingSeq =
              Application_Model_TVSFactory::getSequenceOrSetting($seqName);
            $setTable = $tblViewingSeq->getSetTableForViewing();
            $requestedResource .= "::" . $setTable->getDbTableName();
        }

        // Check that the requested resource is a defined resource.
        if ( ! $acl->has($requestedResource) )
        {
            // Accessing an undefined resource is an unauthorized access.
            $zendRedirector = $this->_getRedirector();
            $mysession->destination_url = $request->getPathInfo();
            return $zendRedirector->setGotoUrl('auth/unauthorized');
        }

        return $requestedResource;
    }

    protected function _getRedirector()
    {
        return
            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    }

}
