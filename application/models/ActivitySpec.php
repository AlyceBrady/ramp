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
 * @package    Ramp_Model
 * @copyright  Copyright (c) 2012 Alyce Brady (http://www.cs.kzoo.edu/~abrady)
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 * @version    $Id: Application_Model_ActivitySpec.php 1 2012-07-12 alyce $
 *
 */

/**
 * An Application_Model_ActivitySpec object models an activity 
 * specification.
 *
 */
class Application_Model_ActivitySpec
{

    // Valid properties
    const TYPE          = "type";
    const COMMENT       = "comment";
    const SOURCE        = "source";
    const TITLE         = "title";
    const DESCRIPTION   = "description";
    const HTML          = "html";
    const CONTROLLER    = "controller";
    const ACTION        = "action";
    const PARAMETER     = "parameter";
    const URL           = "url";

    // Valid specification types
    const SEPARATOR_TYPE            = "separator";
    const COMMENT_TYPE              = self::COMMENT;
    const ACTIVITY_LIST_TYPE        = "activityList";
    const SETTING_TYPE              = "setting";
    const SEQUENCE_TYPE             = "sequence";
    const REPORT_TYPE               = "report";
    const HTML_TYPE                 = self::HTML;
    const CONTROLLER_ACTION_TYPE    = "controllerAction";
    const URL_TYPE                  = self::URL;

    protected $_name;   // activity name (used for keyword lookup and
                        // error messages)

    protected $_type;   // type of activity being specified

    protected $_source; // setting, activity list, url, or other activity source

    protected $_title;  // brief title for activity

    protected $_description;  // summary description of activity
                              // (or comment/html body, in the case of
                              // comment/html "activities")

    protected $_controller;   // controller tied to controller/action activity

    protected $_action;       // action tied to controller/action activity

    protected $_parameter;    // parameter tied to controller/action activity

    protected $_validSpecTypes = array(
        self::COMMENT_TYPE, self::SEPARATOR_TYPE, self::ACTIVITY_LIST_TYPE,
        self::SETTING_TYPE, self::SEQUENCE_TYPE, self::REPORT_TYPE,
        self::HTML_TYPE, self::CONTROLLER_ACTION_TYPE, self::URL_TYPE);

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
            throw new Exception("Error: " . $this->_type . " is an " .
                                "invalid specification type; valid types are " .
                                $valTypes);
        }

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
            case self::HTML_TYPE:
                $this->_description =
                    $this->_confirmProperty(self::HTML, $specAsArray);
                break;
            case self::SETTING_TYPE:
            case self::REPORT_TYPE:
            case self::ACTIVITY_LIST_TYPE:
                $this->_title = $this->_confirmProperty(self::TITLE, $specAsArray);
                $this->_description =
                    $this->_confirmProperty(self::DESCRIPTION, $specAsArray);
                $this->_source = $this->_confirmProperty(self::SOURCE, $specAsArray);
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
            throw new Exception("Activity List Error: activity
                specification " . $this->_name . " has no " .
                $property . " property"
            );
        }

        if ( ! is_string($spec[$property]) )
        {
            throw new Exception("Activity List Error: the
                $property property value for activity
                specification " . $this->_name . " must be a string");
        }

        return $spec[$property];
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
     * Checks whether the "activity" is actually HTML text.
     *
     */
    public function isHTML()
    {
        return $this->_type == self::HTML_TYPE;
    }

    /**
     * Checks whether the "activity" is a URL.
     *
     */
    public function isControllerAction()
    {
        return $this->_type == self::CONTROLLER_ACTION_TYPE;
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
     * Gets the value specified with the html property.  Returns an 
     * empty string if no value was specified for this property.
     *
     */
    public function getHTML()
    {
        return $this->_description;
    }

    /**
     * Gets the value specified with the controller property.  Returns a
     * null if no value was specified for this property.
     *
     */
    public function getController()
    {
        return $this->_controller == "" ? null : $this->_controller;
    }

    /**
     * Gets the value specified with the action property.  Returns an 
     * empty string if no value was specified for this property.
     *
     */
    public function getAction()
    {
        return $this->_action == "" ? null : $this->_action;
    }

    /**
     * Gets the array specified with the parameter property.  Returns an 
     * empty string if no value was specified for this property.
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
            $params[$item[0]] = urlencode($item[1]);
            // $params[$item[0]] = urldecode($item[1]);
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

