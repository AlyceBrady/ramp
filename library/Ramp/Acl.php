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
 * This class was added based on a tutorial found at
 * http://www.ens.ro/2012/03/20/zend-authentication-and-authorization-tutorial-with-zend_auth-and-zend_acl/
 * and on Zend Framework in Action by Allen, Lo, and Brown, 2009, pp. 140-146.
 *
 */
class Ramp_Acl extends Zend_Acl
{
    // Tables with essential authorization information.
    const USERS_TABLE = 'ramp_auth_users';              // source of roles
    const AUTHORIZATIONS_TABLE = 'ramp_auth_auths';     // source of auths

    // Delimiter between resource components.
    const DELIM = '::';

    // Default role for users who are not logged in.
    const DEFAULT_ROLE = 'guest';

    // Default action for controllers.
    const DEFAULT_ACTION = 'index';

    // Table actions.
    const TBL_INDEX         = 'index';
    const TBL_SEARCH        = 'search';
    const VIEW_LIST_RESULTS = 'list-view';
    const VIEW_TABLE_FORMAT = 'table-view';
    const VIEW_SPLIT_FORMAT = 'split-view';
    const VIEW_RECORD       = 'record-view';
    const EDIT_RECORD       = 'record-edit';
    const ADD_RECORD        = 'add';
    const BLOCK_ADD         = 'block-add';
    const BLOCK_EDIT        = 'block-edit';
    const DELETE_RECORD     = 'delete';
    // const ENTER_BLOCK_DATA  = 'block-entry';

    // ACL categories for Table actions.
    const VIEW = 'View';
    const ADD = 'AddRecords';
    const MOD = 'ModifyRecords';
    const DEL = 'DeleteRecords';
    const ALL = 'All';
    const ALL_BUT_DEL = 'AllButDelete';

    protected $_authInfo;
    protected $_tableCategories;
    protected $_tableAccessRules;

    // STATIC (CLASS) FUNCTIONS

    /**
     * Create an associative array of table action categories and the
     * list of actions associated with each category.
     * (Static (Class) function.)
     */
    public static function getActionCategoryXL()
    {
	// Since the table access rules in the database are by
	// category rather than by individual actions, need to
        // create a "category translator" that associates categories
        // with lists of actions.  The 'index' action is part of the
        // VIEW category because it always forwards to either a search
        // or view action.
        $viewActions = array(self::TBL_INDEX, self::TBL_SEARCH,
                             self::VIEW_LIST_RESULTS, self::VIEW_TABLE_FORMAT,
                             self::VIEW_SPLIT_FORMAT, self::VIEW_RECORD);
        $addActions = array(self::ADD_RECORD, self::BLOCK_ADD);
        $modifyActions = array(self::EDIT_RECORD, self::BLOCK_EDIT);
        $allButDeleteActions = array_merge($viewActions, $addActions,
                                           $modifyActions);
        $deleteActions = array(self::DELETE_RECORD);
        $categoryConverter[self::VIEW] = $viewActions;
        $categoryConverter[self::ADD] = $addActions;
        $categoryConverter[self::MOD] = $modifyActions;
        $categoryConverter[self::DEL] = $deleteActions;
        $categoryConverter[self::ALL_BUT_DEL] = $allButDeleteActions;
        $categoryConverter[self::ALL] =
                            array_merge($allButDeleteActions, $deleteActions);
        return $categoryConverter;
    }

    /*
     * CONSTRUCTOR:
     *
     * Populates the Access Controll List with roles, resources, and 
     * access control rules.
     *
     * ROLES: Defines one default role (guest) internally and reads
     * additional roles from the Zend Registry.
     *
     * RESOURCES: Defines resources internally for most actions in the
     * Index, Auth, and Error Controllers.  Reads additional resources from
     * the Zend Registry and deduces others by scanning rules in the database.
     * (Only rules involving activity directories and table/report actions
     * may be specified in the database, so those are the only types of 
     * resources derived from it.)
     * Resources fall into three categories, specified as follows:
     *    Controller actions:    controller::action
     *    Activity directories:  activity::index::directory
     *    Table/Report actions:  table::action::tableName
     * Table and report resources are specific actions on specific 
     * tables (note: tables, not settings).
     *
     * RULES: Defines some basic access control list rules internally and
     * reads in additional rules from the Zend Registry and the database.
     * (Only rules involving activity directories and table/report actions
     * may be specified in the database.)  ACL rules consist of (role, 
     * resource) pairings establishing what resources the role is 
     * authorized to use.
     */
    public function __construct()
    {
        // Some authorization resources and rules come from the 
        // Authentication/Authorization database.
        $this->_authInfo = new Ramp_Auth_DbTable_Auths();

	// Table access resources specify resources by categories of
	// actions rather than by individual actions.  Create a
	// "category translator" to use in translating categories to actions.
        $this->_tableCategories = self::getActionCategoryXL();


        /* ADDING ROLES */

        // Add the built-in, default role called "guest".
        $this->addRole(new Zend_Acl_Role(self::DEFAULT_ROLE));

        // Add roles defined in the Registry.
        $registryFacade = Ramp_RegistryFacade::getInstance();
        $aclRoles = $registryFacade->getAclRoles();
        if ( ! empty($aclRoles) )
        {
            $this->_addRoles($aclRoles);
        }


        /* ADDING RESOURCES */    

        // Add the basic resources: actions from the Index, Auth, and Error 
        // controllers and the pre-defined, built-in Users and 
        // Authorizations tables.
        $this->_addBasicResources();

        // Add resources defined in the Registry.
        $aclResources = $registryFacade->getAclResources();
        if ( ! empty($aclResources) )
        {
            $this->_addResources($aclResources);
        }

        // Add resources derived from authorization rules in the database.
        $this->_addResources($this->_authInfo->getResources());


        /* ASSIGNING RESOURCES TO ROLES */ 

        // Identify minimal resources available to anyone (even guests).
        $this->_establishMinimalAuthorizations();

        // Add rules defined in the Registry.
        $aclRules = $registryFacade->getAclRules();
        foreach ( $aclRules as $rule )
        {
            $components = explode(self::DELIM, $rule);
            // There must be at least 2 components: role and resource.
            if ( count($components) >= 2 )
            {
                $role = array_shift($components);
                $resource = implode(self::DELIM, $components);
                $this->allow($role, $resource);
            }
        }

        // Add authorization rules in the database.
        $this->_addRules($this->_authInfo->getAccessRules());

    }

    /**
     * Determines whether the current user is authorized to access
     * the requested  resource.
     *
     * @param   $resource  the requested resource (single resource or
     *                     array of resources)
     */
    public function authorizesCurrentUser($resource)
    {
        // Normalize, so that resource is always an array of resources 
        // (the more general case).
        $resources = is_array($resource) ? $resource : array($resource);

        foreach ( $resources as $one_resource )
        {
            // If the default role allows access to the requested resource, 
            // that's good.  Check next resource.
            if ( $this->isAllowed(self::DEFAULT_ROLE, $resource) )
            {
                continue;
            }

            // Else must be an authenticated user whose role allows access.
            $auth = Zend_Auth::getInstance();
            if ( $auth->hasIdentity() && is_object($auth->getIdentity()) )
            {
                $user = $auth->getIdentity();

                // Check the user role against the requested resource.
                if ( $this->hasRole($user->role) && $this->has($resource)
                       && $this->isAllowed($user->role, $resource) )
                {
                    // Has authorization for this resource; go on to next.
                    continue;
                }
            }

            // Authorization was needed, but user wasn't authorized.
            return false;
        }

        // Was authorized for all resources!
        return true;
    }

    /**
     * DEBUGGING: Gets all roles.
     */
    public function getRoles()
    {
        return array_keys($this->_getRoleRegistry()->getRoles());
    }

    /**
     * DEBUGGING: Gets all resources.
     */
    public function getResources()
    {
        return array_keys($this->_resources);
    }

    /**
     * DEBUGGING: Gets all rules (well, a bunch, anyway).  It's 
     * non-trivial to get rules out of the Zend Authorization black box, 
     * so "merely" reports on the rules defined in the Registry and the 
     * Database.
     */
    public function getRules()
    {
        // Too hard to return all rules, so just concentrate on rules 
        // from Registry and Database.
        $configInfo = Ramp_RegistryFacade::getInstance();
        $regRules = $configInfo->getAclRules();
        $dbAuthInfo = new Ramp_Auth_DbTable_Auths();
        $dbRules = $dbAuthInfo->getAccessRules();
        return array_merge($regRules, $dbRules);
        /*
            $simpleList = array();
            $byResourceId = $this->_rules['byResourceId'];
            foreach ( $byResourceId as $resource => $ruleInfo )
            {
                $simpleList['resource'] = "hi"; // $ruleInfo['byRuleId'];
            }
            return $simpleList;
         */
    }

    /**
     * Adds basic resources: actions from the Index, Auth, and Error
     * controllers and the pre-defined, built-in Users and Authorizations
     * tables.
     */
    protected function _addBasicResources()
    {
        //Note: Any action in a controller with multiple camel-cased words 
        //      (e.g., chooseActivityListAction in IndexController) is
        //      named as a resource like this: choose-activity-list.

        // INDEX CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('index::index'));
        $this->add(new Zend_Acl_Resource('index::choose-table'));
        $this->add(new Zend_Acl_Resource('index::choose-activity-list'));
        $this->add(new Zend_Acl_Resource('index::menu'));

        // AUTHORIZATION CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('auth::index'));
        $this->add(new Zend_Acl_Resource('auth::login'));
        $this->add(new Zend_Acl_Resource('auth::logout'));
        $this->add(new Zend_Acl_Resource('auth::unauthorized'));
        $this->add(new Zend_Acl_Resource('auth::init-password'));
        $this->add(new Zend_Acl_Resource('auth::change-password'));
        $this->add(new Zend_Acl_Resource('auth::reset-password'));
        $this->add(new Zend_Acl_Resource('auth::validate-roles'));
        $this->add(new Zend_Acl_Resource('auth::validate-acl-rules'));
        $this->add(new Zend_Acl_Resource('auth::view-acl-info'));

        // LOCK CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('lock::unavailable-lock'));
        $this->add(new Zend_Acl_Resource('lock::free-lock'));

        // ERROR CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('error::error'));

        // DOCUMENT CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('document::index'));

        // SYNTAX CHECK CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('table-syntax::index'));

        // BUILT-IN TABLES:
        // Get a list of all Table controller actions.
        $actions = self::getActionCategoryXL();
        foreach ( $actions[self::ALL] as $action )
        {
            $resourceName = "table::$action::" . self::USERS_TABLE;
            $this->add(new Zend_Acl_Resource($resourceName));
            $resourceName = "admin-table::$action::" . self::USERS_TABLE;
            $this->add(new Zend_Acl_Resource($resourceName));
            $resourceName = "table::$action::" . self::AUTHORIZATIONS_TABLE;
            $this->add(new Zend_Acl_Resource($resourceName));
        }

    }

    /**
     * Establishes minimal authorizations (resources available to anyone,
     * even guests who are not logged in).
     */
    protected function _establishMinimalAuthorizations()
    {
        $this->allow(self::DEFAULT_ROLE, 'error::error');

        $this->allow(self::DEFAULT_ROLE, 'index::menu');
        $this->allow(self::DEFAULT_ROLE, 'index::index');
        $this->allow(self::DEFAULT_ROLE, 'index::choose-table');
        $this->allow(self::DEFAULT_ROLE, 'index::choose-activity-list');

        $this->allow(self::DEFAULT_ROLE, 'auth::index');
        $this->allow(self::DEFAULT_ROLE, 'auth::login');
        $this->allow(self::DEFAULT_ROLE, 'auth::logout');
        $this->allow(self::DEFAULT_ROLE, 'auth::unauthorized');

        $this->allow(self::DEFAULT_ROLE, 'lock::unavailable-lock');

        // All users should be able to set or change their password if 
        // Ramp is handling authentication internally.
        $registryFacade = Ramp_RegistryFacade::getInstance();
        if ( $registryFacade->usingInternalAuthentication() )
        {
            $this->allow(self::DEFAULT_ROLE, 'auth::init-password');
            $this->allow(self::DEFAULT_ROLE, 'auth::change-password');
        }
    }

    /**
     * Adds roles with parent information.
     * Based on Zend Framework in Action by Allen, Lo, and Brown, 2009, p. 142.
     *
     * @param   $roles   array of (role => parents) associations,
     *                   where the "parents" in each association can be
     *                   either a single parent or  an array of parents
     */
    protected function _addRoles($roles)
    {
        foreach ( $roles as $name => $parents )
        {
            if ( ! $this->hasRole($name) )
            {
                $parents = empty($parents) ? null : $parents;
                $this->addRole(new Zend_Acl_Role($name), $parents);
            }
        }
    }

    /**
     * Adds the specified resources.
     *
     * @param   $prefix     a prefix to put in front of all resources
     * @param   $resources  a single resource or an array of resources
     */
    protected function _addResources($resources, $prefix='')
    {
        if ( ! empty($resources) )
        {
            if ( is_array($resources) )
            {
                foreach ( $resources as $resource )
                {
                    $this->_addResource($prefix, $resource);
                }
            }
            else  // $resources is actually a single resource
            {
                $this->_addResource($prefix, $resources);
            }
        }
    }

    /**
     * Adds the specified resource.
     * 
     * @param   $prefix     a prefix to put in front of all resources
     * @param   $resource   a single resource
     */
    protected function _addResource($prefix, $resource)
    {
        $fullResource = $prefix . $resource;
        if ( ! $this->has($fullResource) )
        {
            $this->add(new Zend_Acl_Resource($fullResource));
        }
    }

    /**
     * Adds the specified rules.
     *
     * @param   $rules   array of (role => rule) associations
     */
    protected function _addRules($rules)
    {
        foreach ( $rules as $key => $rule )
        {
            foreach ( $rule as $role => $resource )
            {
                try {
                    $this->allow($role, $resource);
                } catch (Exception $e)  {  /* Ignore */  }
            }
        }
    }

}
