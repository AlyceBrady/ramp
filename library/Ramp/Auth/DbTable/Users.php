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
 * @copyright  Copyright (c) 2012 Chris Cain
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 *
 * Written by Chris Cain, with additions by Justin Leatherwood,
 * November 2012.
 * Further additions by Alyce Brady, November 2013.
 *
 * This class represents the RAMP Users table (ramp_auth_users),
 * which contains unique usernames.  For each username, the table
 * should always indicate whether the user account is active or inactive
 * and should specify the user's role for authorization purposes.  The 
 * table may also contain the user's RAMP password (if the
 * application is set up to do internal authentication) and identifying
 * and contact information (name, email address, etc.).  If the RAMP 
 * application includes such identifying and contact information in 
 * another table (such as the Smart Person table), then this Users table
 * should contain "pointers" to that table in the form of the unique ids
 * from that table associated with each user (domainID).
 */
class Ramp_Auth_DbTable_Users extends Zend_Db_Table_Abstract
{
    const TABLE_NAME    = 'ramp_auth_users';
    const USERNAME      = 'username';
    const PASSWORD      = 'password';
    const ACTIVE        = 'active';
    const IS_ACTIVE     = 'TRUE';
    const ROLE_COL      = 'role';
    const DB_DEFAULT    = 'DEFAULT';

    protected $_name=self::TABLE_NAME;

    protected $_roles = null;
    protected $_default_pw = null;

    /**
     * Gets all roles used in the Users table.
     */
    public function getRoles()
    {
        // If the roles have already been retrieved, just return them.
        if ( ! empty($this->_roles) )
        {
            return $this->_roles;
        }

        // Get the roles from the Users table.
        $rows = $this->fetchAll();
        foreach ( $rows as $row )
            $this->_roles[] = $row[self::ROLE_COL];

        // Eliminate duplicates
        $this->_roles = array_unique($this->_roles);

        return $this->_roles;
    }

    /**
     * Gets the default password from the table's meta-data.
     */
    public function getDefaultPassword()
    {
        // Get the default password if it hasn't already been retrieved.
        if ( empty($this->_default_pw) )
        {
            $defaultValues = $this->info(Zend_Db_Table_Abstract::METADATA);
            $this->_default_pw =
                    $defaultValues[self::PASSWORD][self::DB_DEFAULT];
        }
        return $this->_default_pw;
    }

    /**
     * Gets the authentication, authorization, and optional identifying
     * information for a particular user.
     *
     * @return stdClass   basic user information, not including the password
     */
    public function getUserInfo($username)
    {
        // Create WHERE condition for username = $username.
        $where = array(self::USERNAME . ' = ?' => $username);

        // Get the user information.
        $results = $this->fetchAll($where);
        if ( count($results) == 0 )
        {
            throw new Exception("Error: $username is not in " .
                                $this->_name . " table!");
        }
        elseif ( count($results) > 1 )
        {
            throw new Exception("Error: there are multiple entries for " .
                                "$username in " .  $this->_name . " table!");
        }

        $userInfo = new stdClass();
        foreach ( $results[0] as $column => $value )
        {
            if ( $column != self::PASSWORD )
            {
                $userInfo->{$column} = $value;
            }
        }
        return $userInfo;
    }

    /**
     * Resets the password for the specified user in the Users table to 
     * the default value.
     *
     * @return true if the user's password was changed; false otherwise 
     *            (user is not in database or password was already the default)
     */
    public function resetPassword($username)
    {
        // Create WHERE condition for username = $username.
        $where = array(self::USERNAME . ' = ?' => $username);

        // Get the default password.
        $password = $this->getDefaultPassword();

        $data = array(self::USERNAME => $username,
                      self::PASSWORD => $password);

        $count = $this->update($data, $where);
        if ( $count > 1 )
        {
            throw new Exception("Error: Updated $count rows!");
        }
        return $count == 1;
    }

    /**
     * Sets the password for the specified user in the Users table.
     */
    public function setPassword($username, $password)
    {
        // Create WHERE condition for username = $username.
        $where = array(self::USERNAME . ' = ?' => $username);

        // Encrypt the password before adding it to the database.
        $password = crypt($password, $this->_getSalt());

        $data = array(self::USERNAME => $username,
                      self::PASSWORD => $password);

        $count = $this->update($data, $where);
        if ( $count != 1 )
        {
            throw new Exception("Error: Updated $count rows!");
        }
    }

    /**
     * Creates a random salt for initial passwords.  The salt prefix 
     * indicates the encryption algorithm to be used by the PHP crypt 
     * function, which should be the strongest encryption algorithm
     * available (at least of those available as of PHP 5.3.7).
     *
     * @return  a salt that identifies the encryption algorithm to use
     */
    protected function _getSalt()
    {
        $chars =
           './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $salt22 = '';
        for ( $i=0; $i<22; $i++ )
            $salt22 .= $chars[rand(0,63)];
        $rounds='rounds=5003$';
        $strength = "08$";

        if (CRYPT_SHA512 == 1)
        {
            $salt = '$6$' . $rounds . $salt22;
        }

        else if (CRYPT_SHA256 == 1)
        {
            $salt = '$5$' . $rounds . $salt22;
        }

        else if (CRYPT_BLOWFISH == 1)
        {
            $salt = '$2y$' . $strength . $salt22;
        }

        else if (CRYPT_MD5 == 1)
        {
            $salt =  '$1$' . substr($salt22, 0, 8);
        }

        else if (CRYPT_EXT_DES == 1)
        {
            $salt = '_' . substr($salt22, 0, 8);
        }

        else /* if (CRYPT_STD_DES == 1) */
        {
            $salt = substr($salt22, 0, 2);
        }

        return $salt;
    }

}

