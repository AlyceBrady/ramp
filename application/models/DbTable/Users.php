<?php

/*
 * Written by Chris Cain, possibly with additions by Justin Leatherwood,
 * November 2012.
 */
class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name='ramp_auth_users';

    /**
     * Add the specified user to the users table in the database.
     */
    public function addUser($username, $password, $first_name, $last_name,
                            $email, $role, $domainID = null)
    {
        $data = array('username' => $username,
                      'password' => $password,
                      'first_name' => $first_name,
                      'last_name' => $last_name,
                      'email' => $email,
                      'role' => $role,
                      'domainID'=> $domainID
                     );
        $this->insert($data);
    }

}

