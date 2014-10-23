<?php

/**
 * RAMP: Records and Activity Management Program
 *
 * This class is a slightly modified version of the
 * Zend_Controller_Plugin_ErrorHandler class in the Zend Framework.
 * That class has the following license:
 *
 * Zend Framework License:
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
 * Derived from code in:
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage Plugins
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class ErrorController extends Zend_Controller_Action
{

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        
        if ( ! $errors || ! $errors instanceof ArrayObject )
        {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';

                // Following line added by A. Brady, 26 Feb, 2012
                $this->view->explanation .= $errors->exception->getMessage();
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog())
        {
            $log->log($this->view->message, $priority, $errors->exception);
            $log->log('Request Parameters', $priority, $errors->request->getParams());
        }
        
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true)
        {
            $this->view->exception = $errors->exception;
        }
        
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        // if ( ! isset($bootstrap) || !$bootstrap->hasResource('Log') )
        if ( ! $bootstrap->hasResource('Log') )
        {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

