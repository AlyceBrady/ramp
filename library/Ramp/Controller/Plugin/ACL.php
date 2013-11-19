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
        // Create a new Zend ACL object.
        $acl = new Ramp_Acl();

        // Determine what resource is being requested.
        $requestedResource = $this->_getResource($request, $acl);

        // Store Zend redirector for use in errors.
        $zendRedirector = $this->_getRedirector();

        // Check whether the current user is authorized for the 
        // requested resource.
        if ( $acl->authorizesCurrentUser($requestedResource) )
        {
            // Authorized!  Reset session timer and return.
            Application_Model_SessionTimer::startSessionTimer();
            return;
        }
        else
        {
            // Save the attempted destination.
            $mysession = new Zend_Session_Namespace('Ramp_actionAttempt');
            $mysession->destination_url = $request->getPathInfo();

            $auth = Zend_Auth::getInstance();
            if ( ! $auth->hasIdentity() || ! is_object($auth->getIdentity()) )
            {
                // Not an authenticated user -- need to log in.
                return $zendRedirector->setGotoUrl('auth/login');
            }
            else
            {
                // Not authorized -- inform user.
                $this->_reportUnauthorized($requestedResource);
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
        // Start with controller and action.
        $controller = $request->getControllerName();
        $resource = $controller . Ramp_Acl::DELIM . $request->getActionName();

        // Add activity, document, or table/report details.
        $param = Ramp_Controller_KeyParameters::getKeyParam($request);
        if ( $controller == Ramp_Controller_KeyParameters::ACT_CONTROLLER )
        {
            $resource .= Ramp_Acl::DELIM . dirname($param);
        }
        else if ( $controller == Ramp_Controller_KeyParameters::TBL_CONTROLLER
               || $controller == Ramp_Controller_KeyParameters::REP_CONTROLLER )
        {
            try
            {
                $tblViewingSeq =
                    Application_Model_TVSFactory::getSequenceOrSetting($param);
                $setTable = $tblViewingSeq->getSetTableForViewing();
                $resource .= Ramp_Acl::DELIM . $setTable->getDbTableName();
            }
            catch (Exception $e)
            {
                $resource .= Ramp_Acl::DELIM . $param;
                $this->_reportUnauthorized($resource);
            }
        }
        else if ( $controller == Ramp_Controller_KeyParameters::DOC_CONTROLLER )
        {
            $resource .= Ramp_Acl::DELIM . $param;
        }

        // Check that the requested resource is a defined resource.
        if ( ! $acl->has($resource) )
        {
            // Accessing an undefined resource is an unauthorized access.
            $this->_reportUnauthorized($resource);
        }

        return $resource;
    }

    /**
     * Report unauthorized attempt to use resource.
     *
     * @param  resource  undefined resource or user is unauthorized to use it
     */
    protected function _reportUnauthorized($resource)
    {
        $params = array('details' => urlencode($resource));
        $zendRedirector = $this->_getRedirector();
        $zendRedirector->setGotoSimple('unauthorized', 'auth', null, $params);
    }

    /**
     * Get Zend redirector for situations where user is not authorized 
     * to do their requested action.
     */
    protected function _getRedirector()
    {
        return Zend_Controller_Action_HelperBroker::
                                            getStaticHelper('redirector');
    }

}
