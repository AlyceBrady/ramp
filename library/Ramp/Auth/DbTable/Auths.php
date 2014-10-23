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
 * @package    Ramp
 * @copyright  Copyright (c) 2013 Alyce Brady
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 */
class Ramp_Auth_DbTable_Auths extends Zend_Db_Table_Abstract
{
    const DELIM          = Ramp_Acl::DELIM;
    const DEFAULT_ACTION = Ramp_ACL::DEFAULT_ACTION;

    protected $_name='ramp_auth_auths';
    protected $_rowClass = 'Ramp_Auth_DbTable_AccessRule';

    protected $_resources = null;
    protected $_accessRules = null;

    /**
     * Get all Resources.
     *
     */
    public function getResources()
    {
        // If the resources have already been retrieved, just return them.
        if ( ! empty($this->_resources) )
        {
            return $this->_resources;
        }

        // Get all the access rules (if not already retrieved).
        if ( empty($this->_accessRules) )
        {
            $this->_accessRules = $this->getAccessRules();
        }

        // Dig the resource information out from inside the rules.
        $this->_resources = $this->_getResourcesFrom($this->_accessRules);
        return $this->_resources;
    }

    /**
     * Get all Access Control List rules defined in the database.
     */
    public function getAccessRules()
    {
        // If the rules have already been retrieved, just return them.
        if ( ! empty($this->_accessRules) )
        {
            return $this->_accessRules;
        }

        // Get the access rules from the database.
        $rawAccessRules = $this->fetchAll();

        // Build the full set of access rules from the rules in the database.
        $this->_accessRules = array();
        $actionCategories = Ramp_Acl::getActionCategoryXL();
        foreach ( $rawAccessRules as $rule )
        {
            // All rules must include a role, resource type, and resource name.
            if ( ! isset($rule->role) ||
                 ! isset($rule->resource_type) ||
                 ! isset($rule->resource_name) )
            {
                throw new Exception("Access control list table contains " .
                            "an invalid rule (rule " .  $rule->id . ").");
            }
            $controller = strtolower($rule->resource_type);
            if ( $this->_isASettingController($controller) )
            {
                // Rules for setting-using controllers need an action also.
                if ( ! isset($rule->action) ||
                     ! isset($actionCategories[$rule->action]) )
                {
                    $ruleDesc = $rule->role . "::" . $rule->resource_type .
                                "::" . $rule->resource_name;
                    throw new Exception("Access control list table contains " .
                                        "a missing or invalid action (rule " .
                                        $rule->id . ": $ruleDesc).");
                }

                // That action may represent a group of related actions.
                $relatedActions = $actionCategories[$rule->action];
                foreach ( $relatedActions as $action ) 
                {
                    // Build up the resource name.
                    $rules[] = $this->_buildRule($rule->role, $controller,
                                             $action, $rule->resource_name);
                }
            }
            else
            {
                $rules[] = $this->_buildRule($rule->role, $controller,
                                 self::DEFAULT_ACTION, $rule->resource_name);
            }

        }

        $this->_accessRules = $rules;
        return $rules;
    }

    /**
     * Determines whether the given controller is one that works with
     * table settings.
     */
    protected function _isASettingController($controller)
    {
        return
            Ramp_Controller_KeyParameters::isASettingController($controller);
    }

    /**
     * Builds the access rule up out of its constituent parts.
     */
    protected function _buildRule($role, $controller, $action, $resource)
    {
        $delim = self::DELIM;
        $fullResource = $controller . $delim .  $action . $delim .  $resource;
        return array($role => $fullResource);
    }

    /**
     * Gets Resources embedded in the given access rules.
     *
     * @param $accessRules rules containing resource information
     */
    protected function _getResourcesFrom($accessRules)
    {
        // Dig the resource information out from inside the rules.
        $resources = array();
        foreach ( $accessRules as $rule )
        {
            // Each rule has a role as the first element; the resource 
            // is made up of the other elements.
            $resources[] = array_shift($rule);
        }
        return array_unique($resources);
    }

}

