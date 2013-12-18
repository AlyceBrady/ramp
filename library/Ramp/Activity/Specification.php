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
 * @package    Ramp_Activity
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */

/**
 * A Ramp_Activity_Specification object models an activity 
 * specification.
 *
 */
class Ramp_Activity_Specification
{

    // Valid properties
    const TYPE          = "type";
    const INACTIVE      = "inactive";
    const COMMENT       = "comment";
    const SOURCE        = "source";
    const TITLE         = "title";
    const DESCRIPTION   = "description";
    const CONTROLLER    = "controller";
    const ACTION        = "action";
    const PARAMETER     = "parameter";
    const URL           = "url";

    // Valid specification types
    const SEPARATOR_TYPE            = "separator";
    const COMMENT_TYPE              = "comment";
    const ACTIVITY_LIST_TYPE        = "activityList";
    const SETTING_TYPE              = "setting";
    const SEQUENCE_TYPE             = "sequence";
    const REPORT_TYPE               = "report";
    const CONTROLLER_ACTION_TYPE    = "controllerAction";
    const DOCUMENT_TYPE             = "document";
    const URL_TYPE                  = "url";

    // Common controller/action types
    const ACT_CONTROLLER = Ramp_Controller_KeyParameters::ACT_CONTROLLER;
    const DOC_CONTROLLER = Ramp_Controller_KeyParameters::DOC_CONTROLLER;
    const REP_CONTROLLER = Ramp_Controller_KeyParameters::REP_CONTROLLER;
    const TBL_CONTROLLER = Ramp_Controller_KeyParameters::TBL_CONTROLLER;
    const DEFAULT_ACTION = "index";

    // Keywords for sending parameters to controller/action combinations
    const AL_PARAM        = Ramp_Controller_KeyParameters::ACT_KEY_PARAM;
    const DOC_PARAM       = Ramp_Controller_KeyParameters::DOC_KEY_PARAM;
    const SETTING_PARAM   = Ramp_Controller_KeyParameters::SETTING_PARAM;

    protected $_name;   // activity name (used for keyword lookup and
                        // error messages)

    protected $_type;   // type of activity being specified

    protected $_inactive; // whether activity has been inactivated

    protected $_title;  // brief title for activity

    protected $_description;  // summary description of activity
                              // (or comment/html body, in the case of
                              // comment/html "activities")

    protected $_source; // setting, activity list, url, or other activity source

    protected $_controller;   // controller tied to controller/action activity

    protected $_action;       // action tied to controller/action activity

    protected $_parameter;    // parameter tied to controller/action activity

    protected $_validSpecTypes = array(
        self::COMMENT_TYPE, self::SEPARATOR_TYPE, self::ACTIVITY_LIST_TYPE,
        self::SETTING_TYPE, self::SEQUENCE_TYPE, self::REPORT_TYPE,
        self::CONTROLLER_ACTION_TYPE, self::DOCUMENT_TYPE, self::URL_TYPE);

    /**
     * Constructs an ActivitySpec object using the information provided 
     * in array form in $specAsArray.
     *
     * @param string $name the name associated with this specification
     * @param array $specAsArray    property array representing activity
     *
     * Precondition:  If the "name" property is provided in the spec, it 
     *                should match the $name passed in as a parameter.
     *
     */
    public function __construct($name, $specAsArray)
    {
        // Set the name to the activity name passed in as a parameter 
        // unless it is numeric, indicating an unnamed activity.  If it 
        // is numeric, use the source property, if there is one, or the 
        // numeric "name" as a last resort.  Setting the name up front
        // allows use of the name in error messages, if necessary.
        $this->_name = ( ! is_numeric($name) ) ? $name :
                    ( array_key_exists(self::SOURCE, $specAsArray) ?
                            $specAsArray[self::SOURCE] : $name );

        // Make sure that the specification contains a valid type (minimum 
        // requirement).
        $this->_type = $this->_confirmProperty(self::TYPE, $specAsArray);
        if ( ! in_array($this->_type, $this->_validSpecTypes) )
        {
            $valTypes = "";
            $sep = "";
            foreach ( $this->_validSpecTypes as $valType )
            {
                $valTypes .= $sep . $valType;
                $sep = ", ";
            }
            throw new Exception("Error: '" . $this->_type . "' is an " .
                                "invalid specification type; valid types are " .
                                $valTypes);
        }

        // By default, assume that activities have not been inactivated.
        $this->_inactive = isset($specAsArray[self::INACTIVE]) &&
                                    $specAsArray[self::INACTIVE] == true
                        ? true : false;

        // "Sequence" is a synonym for "setting" -- normalize to 1 term.
        $this->_type = ($this->_type == self::SEQUENCE_TYPE) ?
                        self::SETTING_TYPE : $this->_type;

        // Verify that the specification includes the other properties 
        // expected for its type.
        switch ( $this->_type )
        {
            case self::COMMENT_TYPE:
                $this->_description =
                    $this->_confirmProperty(self::COMMENT, $specAsArray);
                break;
            case self::ACTIVITY_LIST_TYPE:
            case self::SETTING_TYPE:
            case self::REPORT_TYPE:
            case self::DOCUMENT_TYPE:
                $this->_title = $this->_confirmProperty(self::TITLE,
                                                        $specAsArray);
                $this->_description =
                    $this->_confirmProperty(self::DESCRIPTION, $specAsArray);
                $this->_source = $this->_confirmProperty(self::SOURCE,
                                                         $specAsArray);
                $this->_setDefaultController();
                $this->_action = self::DEFAULT_ACTION;
                break;
            case self::CONTROLLER_ACTION_TYPE:
                $this->_title =
                    $this->_confirmProperty(self::TITLE, $specAsArray);
                $this->_description =
                    $this->_confirmProperty(self::DESCRIPTION, $specAsArray);
                $this->_controller =
                    $this->_confirmProperty(self::CONTROLLER, $specAsArray);
                $this->_action =
                    $this->_confirmProperty(self::ACTION, $specAsArray);
                $this->_parameter =
                    $this->_confirmProperty(self::PARAMETER, $specAsArray);
                break;
            case self::URL_TYPE:
                $this->_title = $this->_confirmProperty(self::TITLE,
                                                        $specAsArray);
                $this->_description =
                    $this->_confirmProperty(self::DESCRIPTION, $specAsArray);
                $this->_source = $this->_confirmProperty(self::URL,
                                                         $specAsArray);
                break;
            default:
                // ignore extraneous properties
        }

    }

    /**
     * Confirms that the $spec activity specification includes the
     * given property and that the property value is a string.  If
     * either condition is not met, an exception is thrown.
     *
     * @param string $property  the property to check for
     * @param array  $spec      the specification in which to check
     *
     */
    protected function _confirmProperty($property, $spec)
    {
        if ( ! array_key_exists($property, $spec) )
        {
            throw new Exception("Activity List Error: activity " .
                "specification '" . $this->_name . "' has no " .
                $property . " property"
            );
        }

        if ( ! is_string($spec[$property]) )
        {
            throw new Exception("Activity List Error: the '" .
                "$property' property value for activity " .
                "specification '" . $this->_name . "' must be a string");
        }

        return $spec[$property];
    }

    /**
     * Sets the default controller associated with this activity.
     */
    protected function _setDefaultController()
    {
        switch ( $this->_type )
        {
            case self::ACTIVITY_LIST_TYPE:
                $this->_controller = self::ACT_CONTROLLER;
                break;
            case self::SETTING_TYPE:
                $this->_controller = self::TBL_CONTROLLER;
                break;
            case self::REPORT_TYPE:
                $this->_controller = self::REP_CONTROLLER;
                break;
            case self::DOCUMENT_TYPE:
                $this->_controller = self::DOC_CONTROLLER;
                break;
        }
    }

    /**
     * Checks whether the "activity" is actually a separator in an 
     * activity list.
     *
     */
    public function isSeparator()
    {
        return $this->_type == self::SEPARATOR_TYPE;
    }

    /**
     * Checks whether the "activity" is actually a comment in an 
     * activity list.
     *
     */
    public function isComment()
    {
        return $this->_type == self::COMMENT_TYPE;
    }

    /**
     * Checks whether the activity is a nested activity list.
     *
     */
    public function isActivityList()
    {
        return $this->_type == self::ACTIVITY_LIST_TYPE;
    }

    /**
     * Checks whether the activity is a table-viewing/modifying one.
     *
     */
    public function isSetting()
    {
        return $this->_type == self::SETTING_TYPE;
    }

    /**
     * Checks whether the activity is a report-generating one.
     *
     */
    public function isReport()
    {
        return $this->_type == self::REPORT_TYPE;
    }

    /**
     * Checks whether the "activity" is a controller/action combination.
     *
     */
    public function isControllerAction()
    {
        return $this->_type == self::CONTROLLER_ACTION_TYPE;
    }

    /**
     * Checks whether the "activity" is a document containing text (might be 
     * formatted text using HTML or Markdown).
     *
     */
    public function isDocument()
    {
        return $this->_type == self::DOCUMENT_TYPE;
    }

    /**
     * Checks whether the "activity" is a URL.
     *
     */
    public function isUrl()
    {
        return $this->_type == self::URL_TYPE;
    }

    /**
     * Checks whether the activity has been marked as inactive.
     *
     */
    public function isInactive()
    {
        return $this->_inactive;
    }

    /**
     * Gets the value specified with the type property.
     *
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Gets the value specified with the comment property.  Returns an 
     * empty string if no value was specified for this property.
     *
     */
    public function getComment()
    {
        return $this->_description;
    }

    /**
     * Gets the value specified with the source property.  Returns an 
     * empty string if no value was specified for this property.
     *
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Sets the value specified with the source property.  Used,
     * for example, to change an activity list source name to a fully 
     * qualified name, including the file in which the list occurs.
     *
     */
    public function setSource($newSourceName)
    {
        $this->_source = $newSourceName;
    }

    /**
     * Gets the value specified with the title property.  Returns an 
     * empty string if no value was specified for this property.
     *
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Gets the value specified with the description property.  Returns 
     * an empty string if no value was specified for this property.
     *
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Returns the controller associated with this activity.  If
     * the activity type is controller/action, returns the value
     * specified with the controller property.  If the activity is an 
     * activity list, table setting, report, or document, returns the 
     * appropriate controller. Returns an empty string for activities
     * like separators and comments that do not have any controller 
     * associated with them.
     *
     */
    public function getController()
    {
        return empty($this->_controller) ? "" : $this->_controller;
    }

    /**
     * Returns the action associated with this activity.  If
     * the activity type is controller/action, returns the value
     * specified with the action property.  If the activity is an 
     * activity list, table setting, report, or document, returns the 
     * appropriate action. Returns an empty string for activities
     * like separators and comments that do not have any action 
     * associated with them.
     */
    public function getAction()
    {
        return empty($this->_action) ? "" : $this->_action;
    }

    /**
     * Returns the parameter keyword associated with this activity
     * if an activity list, table setting, report, or document; returns
     * an empty string for other activities.
     */
    public function getParamKeyword()
    {
        switch ( $this->_type )
        {
            case self::ACTIVITY_LIST_TYPE:
                return self::AL_PARAM;
            case self::SETTING_TYPE:
            case self::REPORT_TYPE:
                return self::SETTING_PARAM;
            case self::DOCUMENT_TYPE:
                return self::DOC_PARAM;
        }
    }

    /**
     * Gets the array specified with the parameter property.  Returns an 
     * empty array if no value was specified for this property.
     *
     */
    public function getParameters()
    {
        if ( $this->_parameter == "" )
        {
            return array();
        }
        $paramAssignments = explode('&', $this->_parameter);
        $params = array();
        foreach ( $paramAssignments as $paramAssignment )
        {
            $item = explode('=', $paramAssignment);
            if ( count($item) != 2 )
            {
                throw new Exception("Error: '" . $this->getType() .
                    "' activity with title '" . $this->getTitle() .
                    "' has a badly-formatted parameter property.");
            }
            $params[trim($item[0])] = urlencode(trim($item[1]));
// throw new Exception("params: " . print_r($params, true));
        } 
        return $params;
    }

    /**
     * Gets the value specified with the url property.  Returns an 
     * empty string if no value was specified for this property.
     *
     */
    public function getUrl()
    {
        return $this->_source == "" ? null : $this->_source;
    }

}

