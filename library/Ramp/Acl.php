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
 * @version    $Id: Acl.php 1 2012-11-13 Justin Leatherwood $
 *
 * This class was added based on a tutorial found at
 * http://www.ens.ro/2012/03/20/zend-authentication-and-authorization-tutorial-with-zend_auth-and-zend_acl/
 * and on Zend Framework in Action by Allen, Lo, and Brown, 2009, pp. 140-146.
 *
 */
class Ramp_Acl extends Zend_Acl
{
    const DEFAULT_ROLE = 'guest';

    const DELIM = '::';              // Delimiter separating resource sections
    const ACTIVITY_PREFIX = 'activity::index'; // Start of Activity resources

    const PUBLIC_ACTS_RESOURCE = 'activity::index::PublicActivities';

    // Keys for ACL Roles and Activity List Directories in Zend Registry.
    const ACL_ROLES = 'rampAclRoles';
    const ACTIVITY_LIST_DIRS = 'rampAclActivityListDirs';

    // ACL categories for Table actions.
    const VIEW = 'View';
    const ADD = 'AddRecords';
    const MOD = 'ModifyRecords';
    const DEL = 'DeleteRecords';
    const ALL = 'All';

    protected $_authInfo;
    protected $_tableCategories;
    protected $_tableAccessRules;

    /**
     * STATIC (CLASS) FUNCTION:
     *
     * Create an associative array of table action categories and the
     * list of actions associated with each category.
     */
    public static function createCategoryConverter()
    {
	// Since the table access rules in the database are by
	// category rather than by individual actions, need to
        // create a "category converter" that associates categories
        // with lists of actions.  The 'index' action is part of the
        // VIEW category because it always forwards to either a search
        // // or view action.
        $viewActions = array('index', 'search', 'list-view', 'table-view',
                             'record-view');
        $addActions = array('add');
        $modifyActions = array('record-edit');
        $deleteActions = array('delete');
        $categoryConverter[self::VIEW] = $viewActions;
        $categoryConverter[self::ADD] = $addActions;
        $categoryConverter[self::MOD] = $modifyActions;
        $categoryConverter[self::DEL] = $deleteActions;
        $categoryConverter[self::ALL] =
                            array_merge($viewActions, $addActions,
                                        $modifyActions, $deleteActions);
        return $categoryConverter;
    }

    /*
     * CONSTRUCTOR:
     *
     * Defines one default role (guest) and resources for the actions in 
     * the Index, Auth, and Error Controllers.  Reads Activity 
     * Controller resources (directories that may contain activity 
     * lists) from the Zend Registry, and reads Table Controller 
     * resources (action / table combinations) and Activity and Table 
     * Access Rules from the database.
     */
    public function __construct()
    {

        /* ADDING ROLES */

        // Add the default role called "guest".
        $this->addRole(new Zend_Acl_Role(self::DEFAULT_ROLE));

        // Add other roles defined in the Registry.
        if ( Zend_Registry::isRegistered(self::ACL_ROLES) )
        {
            $aclRoles = Zend_Registry::get(self::ACL_ROLES);
            $this->_addRoles($aclRoles);
        }


        /* ADDING RESOURCES */    

        // Some authorization resources and rules come from the 
        // Authentication/Authorization database.
        $this->_authInfo = new Application_Model_DbTable_Auths();

        // Table access resources in the database specify resources by 
        // categories of actions rather than by individual actions.  
        // Create a "category converter" to use in converting categories
        // to  actions.
        $this->_tableCategories = self::createCategoryConverter();

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
        $this->add(new Zend_Acl_Resource('auth::add-user'));
        $this->add(new Zend_Acl_Resource('auth::validate-acl-rules'));

        // ERROR CONTROLLER: all actions
        $this->add(new Zend_Acl_Resource('error::error'));

        // ACTIVITY CONTROLLER: Resources are the directories where 
        // activity list specification files can be found.

        // The PublicActivities directory should be provided for 
        // activities that should be visible even to "guest" users.
        $publicActivities = self::PUBLIC_ACTS_RESOURCE;
        $this->add(new Zend_Acl_Resource($publicActivities));

        // Add other activity list directory resources defined in the Registry.
        if ( Zend_Registry::isRegistered(self::ACTIVITY_LIST_DIRS) )
        {
            $actListResources = Zend_Registry::get(self::ACTIVITY_LIST_DIRS);
            $actPrefix = self::ACTIVITY_PREFIX . self::DELIM;
            $this->_addResources($actPrefix, $actListResources);
        }

        // Add other activity list directory resources defined in the database.
        $this->_addResources('', $this->_authInfo->getActivityResources());

        // TABLE AND REPORT CONTROLLERS: Resources are specific actions on 
        // specific tables (note: tables, not settings).

        // Add table resources defined in the database.
        $this->_addResources('', $this->_authInfo->getTableResources());


        /* ASSIGNING RESOURCES TO ROLES */ 

        // Identify minimal resources available to anyone (even guests)
        $this->allow(self::DEFAULT_ROLE, 'error::error');

        $this->allow(self::DEFAULT_ROLE, 'index::menu');
        $this->allow(self::DEFAULT_ROLE, 'index::index');
        $this->allow(self::DEFAULT_ROLE, 'index::choose-table');
        $this->allow(self::DEFAULT_ROLE, 'index::choose-activity-list');

        $this->allow(self::DEFAULT_ROLE, 'auth::index');
        $this->allow(self::DEFAULT_ROLE, 'auth::login');
        $this->allow(self::DEFAULT_ROLE, 'auth::logout');
        $this->allow(self::DEFAULT_ROLE, 'auth::unauthorized');

        $this->allow(self::DEFAULT_ROLE, self::PUBLIC_ACTS_RESOURCE);

        // Allow access to activities and tables based on authorization
        // rules in the database.
        $this->_addRules($this->_authInfo->getActivityAccessRules());
        $this->_addRules($this->_authInfo->getTableAccessRules());

        // FUTURE: $this->allow(self::DEFAULT_ROLE, 'document::index::about');

        // The database administrator should be able to add or edit users and ACLs,
        // and check whether the ACL rules are valid, whether through normal
        // RAMP channels or through special actions in the Auth controller.
        // (Note, though, that users and rules can be added, edited, and deleted 
        // through normal RAMP channels, i.e., using the Table controller.)
        $this->allow('ramp_dba', 'auth::add-user');
        $this->allow('ramp_dba', 'auth::validate-acl-rules');

        // If the following two tables are not defined in the database, they 
        // should be defined here.
        // $this->add(new Zend_Acl_Resource('table::index::ramp_auth_users'));
        // $this->add(new Zend_Acl_Resource('table::index::ramp_auth_auths'));

    }

    /**
     * Add roles with parent information.
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
     * Add the specified resources.
     *
     * @param   $prefix     a prefix to put in front of all resources
     * @param   $resources  a single resource or an array of resources
     */
    protected function _addResources($prefix, $resources)
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
     * Add the specified resource.
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
     * Add the specified rules.
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
