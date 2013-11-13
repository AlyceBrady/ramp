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

    /* Key parameter names */
    const ACT_KEY_PARAM         = 'activity';  
    const DOC_KEY_PARAM        = 'document';
    const SETTING_PARAM         = '_setting';

    const DELIM = '::';              // Delimiter separating resource sections
    const ACTIVITY_PREFIX = 'activity::index'; // Start of Activity resources
    const DOCUMENT_PREFIX = 'document::index'; // Start of Activity resources

    // STATIC (CLASS) FUNCTIONS

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
        if ( $controller == self::ACT_CONTROLLER )
        {
            $keyParam = $request->getUserParam(self::ACT_KEY_PARAM);
        }
        else if ( $controller == self::DOC_CONTROLLER )
        {
            $keyParam = $request->getUserParam(self::DOC_KEY_PARAM, '');
            $keyParam = $keyParam ? :
                           $request->getUserParam(self::ACT_KEY_PARAM, '');
        }
        else if ( $controller == self::TBL_CONTROLLER ||
                  $controller == self::REP_CONTROLLER )
        {
            $keyParam = $request->getUserParam(self::SETTING_PARAM);
        }

        return urldecode($keyParam);
    }

}
