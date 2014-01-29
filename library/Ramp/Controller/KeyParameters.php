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
 * @copyright  Copyright (c) 2013 Alyce Brady
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */
class Ramp_Controller_KeyParameters
{
    /* Controller names */
    const ACT_CONTROLLER = 'activity';
    const DOC_CONTROLLER = 'document';
    const REP_CONTROLLER = 'report';
    const TBL_CONTROLLER = 'table';
    const ADMIN_CONTROLLER = 'admin-table';

    const DEFAULT_CONTROLLER = self::TBL_CONTROLLER;

    /* Key parameter names */
    const ACT_KEY_PARAM        = 'activity';  
    const DOC_KEY_PARAM        = 'document';
    const SETTING_PARAM        = '_setting';

    // STATIC (CLASS) VARIABLES

    protected static $_settingControllerTypes = array(
            self::TBL_CONTROLLER,
            self::REP_CONTROLLER, self::ADMIN_CONTROLLER,
        );

    // STATIC (CLASS) FUNCTIONS

    public static function isASettingController($controllerName)
    {
        return in_array($controllerName, self::$_settingControllerTypes);
    }

    /**
     * Gets the key parameter (activity, document, setting, etc) 
     * in the given request.
     *
     * @param $request  the Controller Action request
     */
    public static function
        getKeyParam(Zend_Controller_Request_Abstract $request)
    {
        $controller = $request->getControllerName();
        $keyParam = "";
        $keyword = self::getKeyParamKeyword($controller);
        $keyParam = $request->getUserParam($keyword, '');
        if ( $controller == self::DOC_CONTROLLER )
        {
            $keyParam = $keyParam ? :
                           $request->getUserParam(self::ACT_KEY_PARAM, '');
        }

        return urldecode($keyParam);
    }

    /**
     * Gets the key parameter keyword for the given controller type.
     *
     * @param controller  the given controller type ('activity', 
     *                    'table', etc.)
     */
    public static function getKeyParamKeyword($controller)
    {
        if ( $controller == self::ACT_CONTROLLER )
        {
            return self::ACT_KEY_PARAM;
        }
        else if ( $controller == self::DOC_CONTROLLER )
        {
            return self::DOC_KEY_PARAM;
        }
        else if ( self::isASettingController($controller) )
        {
            return self::SETTING_PARAM;
        }

        return "";
    }

}
