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

        // Check whether the current user is authorized for the 
        // requested resource.
        if ( $acl->authorizesCurrentUser($requestedResource) )
        {
            return;     // Authorized!
        }
        else
        {
            $auth = Zend_Auth::getInstance();
            $mysession->destination_url = $request->getPathInfo();
            if ( ! $auth->hasIdentity() || ! is_object($auth->getIdentity()) )
            {
                // Not an authenticated user -- set the url accordingly.
                return $zendRedirector->setGotoUrl('auth/login');
            }
            else
            {
                // Not allowed -- set the url accordingly.
    throw new Exception("I'm here");
                $params = array('details' => $requestedResource);
                $zendRedirector = $this->_getRedirector();
                return $zendRedirector->setGotoSimple('unauthorized', 'auth',
                                                      null, $params);
            }
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
        // and possibly the activity or table.
        $controllerName = $request->getControllerName();
        $requestedResource = $controllerName . Ramp_Acl::DELIM
                              . $request->getActionName();
        $keyParam = Ramp_Controller_KeyParameters::getKeyParam($request);

        if ( $controllerName == 'activity' )
        {
            $requestedResource .= Ramp_Acl::DELIM . dirname($keyParam);
        }
        else if ( $controllerName == 'table' || $controllerName == 'report' )
        {
            $tblViewingSeq =
                Application_Model_TVSFactory::getSequenceOrSetting($keyParam);
            $setTable = $tblViewingSeq->getSetTableForViewing();
            $requestedResource .= Ramp_Acl::DELIM . $setTable->getDbTableName();
        }
        else if ( $controllerName == 'document' )
        {
            $requestedResource .= Ramp_Acl::DELIM . $keyParam;
        }

        // Check that the requested resource is a defined resource.
        if ( ! $acl->has($requestedResource) )
        {
            // Accessing an undefined resource is an unauthorized access.
            $params = array('details' => $requestedResource);
            $zendRedirector = $this->_getRedirector();
            return $zendRedirector->setGotoSimple('unauthorized', 'auth',
                                                  null, $params);
        }

        return $requestedResource;
    }

    protected function _getRedirector()
    {
        return
            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
    }

}
