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
    const DISPLAY_EXCEPTIONS = 'displayExceptions';
    const VIEW = 'record-view';

    protected $_displayExceptionDetails = 0;

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
        $frontController = Zend_Controller_Front::getInstance();
        $this->_displayExceptionDetails =
            $frontController->getParam(self::DISPLAY_EXCEPTIONS);

        // Create a new Zend ACL object.
        $acl = new Ramp_Acl();

        // Determine what resources are being requested.
        $requestedResources = $this->_getResources($request, $acl);

        // Store Zend redirector for use in errors.
        $zendRedirector = $this->_getRedirector();

        // Check whether the current user is authorized for the 
        // requested resources.
        foreach ( $requestedResources as $reqResource )
        {
            if ( ! $acl->authorizesCurrentUser($reqResource) )
            {
                /* 
                 * This was not working very well, but something similar 
                 * might be useful for saving most recent search params 
                 * or most recent key from certain "key" tables.
                 *
                // Save the attempted destination.
                $mysession = new Zend_Session_Namespace('Ramp_actionAttempt');
                $mysession->destination_url = $request->getPathInfo();
                 */

                $auth = Zend_Auth::getInstance();
                if ( ! $auth->hasIdentity() ||
                     ! is_object($auth->getIdentity()) )
                {
                    // Not an authenticated user -- need to log in.
                    $this->_redirectToLogin($request);
                    return $zendRedirector->setGotoUrl('auth/login');
                }
                else
                {
                    // Not authorized -- inform user.
                    $msg = $this->_formatUnauthResourceName($reqResource);
                    $this->_reportUnauthorized($msg);
                }
            }
        }

        // Authorized!  Reset session timer and return.
        Ramp_Auth_SessionTimer::startSessionTimer();
        return;

    }

    /**
     * Determine what resources are being requested.
     *
     * @param Zend_Controller_Request_Abstract $request  the user request 
     *      that this dispatch loop is addressing
     * @param Zend_Acl $acl  the Access Control List
     */
    protected function _getResources(Zend_Controller_Request_Abstract $request,
                                    Zend_Acl $acl)
    {
        $error_details = "";

        // Start with controller and action.
        // Treat reports as (special types of) tables for authorization.
        $resources = array();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ( $controller == Ramp_Controller_KeyParameters::REP_CONTROLLER )
        {
            $controller = Ramp_Controller_KeyParameters::TBL_CONTROLLER;
        }
        $prefix = $controller . Ramp_Acl::DELIM . $action;

        // Add activity, document, or table/report details.
        $param = Ramp_Controller_KeyParameters::getKeyParam($request);
        if ( $controller == Ramp_Controller_KeyParameters::ACT_CONTROLLER
            || $controller == Ramp_Controller_KeyParameters::DOC_CONTROLLER )
        {
            $resources[] = $prefix . Ramp_Acl::DELIM . dirname($param);
        }
        else if ( $this->_isSettingController($controller) )
        {
            try
            {
                $tblViewingSeq =
                    Ramp_Table_TVSFactory::getSequenceOrSetting($param);
                $setTable = $tblViewingSeq->getSetTableForAction($action);
                $mainTable = $setTable->getDbTableName();
                $resources[] = $prefix . Ramp_Acl::DELIM . $mainTable;
                $tables = $setTable->getDependentTables();
                foreach ( $tables as $table )
                {
                    $prefix2 = $controller . Ramp_Acl::DELIM . self::VIEW;
                    $resources[] = $prefix2 . Ramp_Acl::DELIM . $table;
                }
            }
            catch (Exception $e)
            {
                $resourceIsSetting = true;
                $badResource = $prefix . Ramp_Acl::DELIM . $param;
                $error_details = $this->_displayExceptionDetails
                            ? $e->getMessage()
                            : $this->_formatUnauthResourceName($badResource,
                                $resourceIsSetting);
                $this->_reportUnauthorized($error_details);

                // Should be unnecessary, but ...  (Exception handling 
                // is being handled VERY WIERDLY by Zend.)
                throw new Exception($error_details);
            }
        }
        else
        {
            // Default resource is just controller & action.
            $resources[] = $prefix;
        }

        // Check that the requested resource is a defined resource.
        foreach ( $resources as $resource )
        {
            if ( ! $acl->has($resource) )
            {
                // Accessing an undefined resource is an unauthorized access.
                $error_details = $this->_displayExceptionDetails
                            ? $resource . " is not a defined resource"
                            : $this->_formatUnauthResourceName($resource);
                $this->_reportUnauthorized($error_details);
            }
        }

        return $resources;
    }

    /**
     * Determines whether the given controller is one that works with
     * table settings.
     */
    protected function _isSettingController($controller)
    {
        return
            Ramp_Controller_KeyParameters::isASettingController($controller);
    }

    /**
     * Formats resource name from full resource specification.
     *
     * @param $resourceSpec  full resource specification
     * @param $isSetting     for tables/reports, this the setting or table?
     */
    protected function _formatUnauthResourceName($resourceSpec,
                                                 $isSetting = false)
    {
        // Get the various components of a full resource spec.  If it 
        // doesn't have the expected components, return full spec.
        $components = explode(Ramp_Acl::DELIM, $resourceSpec);
        if ( count($components) != 3 )
        {
            return 'Resource: ' . $resourceSpec;
        }

        // Return the resource type plus either the second or third
        // component, depending on the resource type.
        if ( $components[0] == Ramp_Controller_KeyParameters::ACT_CONTROLLER )
        {
            return 'activities in ' . $components[2] . ' directory';
        }
        if ( $components[0] == Ramp_Controller_KeyParameters::DOC_CONTROLLER )
        {
            return $components[2] . ' document';
        }
        if ( $this->_isSettingController($components[0]) )
        {
            if ( $isSetting )
            {
                return $components[0] . ' in setting ' . $components[2];
            }
        }
        return 'Resource: ' . $resourceSpec;
    }

    /**
     * Redirect to login screen.
     *
     * @param  request  original request
     */
    protected function _redirectToLogin($request)
    {
        $controller_attempt = '_' . Ramp_Activity_Specification::CONTROLLER;
        $action_attempt = '_' . Ramp_Activity_Specification::ACTION;
        $params = array($controller_attempt => $request->getControllerName(),
                        $action_attempt => $request->getActionName());
        $params = $params + $request->getParams(); 
        $zendRedirector = $this->_getRedirector();
        $zendRedirector->setGotoSimple('login', 'auth', null, $params);
    }

    /**
     * Report unauthorized attempt to use resource.
     *
     * @param  resource  undefined resource or user is unauthorized to use it
     */
    protected function _reportUnauthorized($msg)
    {
        $params = array('details' => urlencode($msg));
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
