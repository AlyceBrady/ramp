<?php

class Application_Model_DbTable_Auths extends Zend_Db_Table_Abstract
{
    protected $_name='ramp_auth_auths';
    protected $_rowClass = 'Application_Model_DbTable_AccessRule';

    protected $_activityAccessRules = null;
    protected $_tableAccessRules = null;

    /**
     * Get all activity and table resources defined in the database.
     */
    public function getResources()
    {
        // Get all the activity and table resources.
        $actResources = $this->getActivityResources();
        $tableResources = $this->getTableResources();

        return array_merge($actResources, $tableResources);
    }

    /**
     * Get all Activity-related Resources.
     *
     */
    public function getActivityResources()
    {
        // Get all the activity access rules (if not already retrieved).
        if ( empty($this->_activityAccessRules) )
        {
            $delim = Ramp_Acl::DELIM;
            $this->_activityAccessRules = $this->getActivityAccessRules();
        }

        // Dig the resource information out from inside the rules.
        return $this->_getRawResources($this->_activityAccessRules);
    }

    /**
     * Get all Table-related Resources.
     *
     */
    public function getTableResources()
    {
        // Get all the table access rules (if not already retrieved).
        if ( empty($this->_tableAccessRules) )
        {
            $this->_tableAccessRules = $this->getTableAccessRules();
        }

        // Dig the resource information out from inside the rules.
        return $this->_getRawResources($this->_tableAccessRules);
    }

    /**
     * Get all activity and table access rules defined in the database.
     */
    public function getAccessRules()
    {
        // Get all the activity access rules (if not already retrieved).
        if ( empty($this->_activityAccessRules) )
        {
            $this->_activityAccessRules = $this->getActivityAccessRules();
        }

        // Get all the table access rules (if not already retrieved).
        if ( empty($this->_tableAccessRules) )
        {
            $this->_tableAccessRules = $this->getTableAccessRules();
        }

        return array_merge($this->_activityAccessRules,
                            $this->_tableAccessRules);
    }

    /**
     * Get all Access Control List rules related to the Activity resource
     * type.
     *
     */
    public function getActivityAccessRules()
    {
        // If the rules have already been retrieved, just return them.
        if ( ! empty($this->_activityAccessRules) )
        {
            return $this->_activityAccessRules;
        }

        $rules = array();

        // Get the Activity access rules from the database.
        $where = array('resource_type = ?' => 'Activity');
        $rawActivityAccessRules = $this->fetchAll($where);

        // Build the full set of access rules from the rules in the database.
        foreach ( $rawActivityAccessRules as $rule )
        {
            if ( ! isset($rule->role) ||
                 ! isset($rule->resource_name) )
            {
                throw new Exception("Access control list table contains " .
                            "an invalid rule (rule " .  $rule->id . ").");
            }

            // Build up the resource name.
            $prefix = Ramp_Acl::ACTIVITY_PREFIX;
            $delim = Ramp_Acl::DELIM;
            $rules[] = array($rule->role =>
                             $prefix . $delim . $rule->resource_name);
        }

        $this->_activityAccessRules = $rules;
        return $rules;
    }

    /**
     * Get all Access Control List rules related to the Table resource
     * type.
     *
     */
    public function getTableAccessRules()
    {
        // If the rules have already been retrieved, just return them.
        if ( ! empty($this->_tableAccessRules) )
        {
            return $this->_tableAccessRules;
        }

        $rules = array();
        $actionCategories = Ramp_Acl::createCategoryConverter();

        // Get the Table access rules from the database.
        $where = array('resource_type = ?' => 'Table');
        $rawTableAccessRules = $this->fetchAll($where);

        // Build the full set of access rules from the rules in the database.
        foreach ( $rawTableAccessRules as $rule )
        {
            // An access rule "action" in the database may actually refer
            // to a category of related actions.
            if ( ! isset($rule->role) ||
                 ! isset($rule->resource_name) ||
                 ! isset($rule->action) ||
                 ! isset($actionCategories[$rule->action]) )
            {
                throw new Exception("Access control list table contains " .
                            "an invalid rule (rule " .  $rule->id . ").");
            }
            $relatedActions = $actionCategories[$rule->action];
            foreach ( $relatedActions as $action ) 
            {
                // Build up the resource name.
                $prefix = Ramp_Acl::TABLE_PREFIX;
                $delim = Ramp_Acl::DELIM;
                $rules[] = array($rule->role =>
                                 $prefix . $delim . $action
                                         . $delim . $rule->resource_name);
            }
        }

        $this->_tableAccessRules = $rules;
        return $rules;
    }

    /**
     * Get all specified raw Resources.
     *
     * @param $accessRules rules containing resource information
     */
    protected function _getRawResources($accessRules)
    {
        // Dig the resource information out from inside the rules.
        $resources = array();
        foreach ( $accessRules as $rule )
        {
            // Each rule is an array of 1 element; add its value to $resources.
            $resources[] = array_shift($rule);
        }
        return array_unique($resources);
    }

}

