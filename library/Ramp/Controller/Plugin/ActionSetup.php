<?php

class Ramp_Controller_Plugin_ActionSetup extends Zend_Controller_Plugin_Abstract
{

    /**
     * Take care of any items to do before any dispatch loop starts:
     *      Add items to the ActionStack, to enable multiple actions in 
     *              each dispatch.
     * From Zend Framework in Action by Allen, Lo, and Brown,
     *      2009, p. 73.
     *
     * @param Zend_Controller_Request_Abstract $request  the user request 
     * that this dispatch loop is addressing
     */ 
    public function dispatchLoopStartup(
        Zend_Controller_Request_Abstract $request)
    {
        // Get the front controller's ActionStack (or create it if it 
        // doesn't already exist).
        $front = Zend_Controller_Front::getInstance();
        if ( ! $front->hasPlugin('Zend_Controller_Plugin_ActionStack') )
        {
            $actionStack = new Zend_Controller_Plugin_ActionStack();
            $front->registerPlugin($actionStack, 97);
        }
        else
        {
            $actionStack =
                $front->getPlugin('Zend_Controller_Plugin_ActionStack');
        }

        // Add the menu action to the ActionStack, with a clone of the 
        // current request.
        $menuAction = clone($request);
        $menuAction->setActionName('menu')->setControllerName('index');
        $actionStack->pushStack($menuAction);
    }

}
