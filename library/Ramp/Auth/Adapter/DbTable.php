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
 * @package    Ramp_Auth
 * @copyright  Copyright (c) 2013 Alyce Brady
 * @license    http://www.cs.kzoo.edu/ramp/LICENSE.txt   Simplified BSD License
 * @version    $Id: Acl.php 1 2013-10-22 Alyce Brady $
 *
 *
 */
class Ramp_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
    const ACTIVE            = Ramp_Auth_DbTable_Users::ACTIVE;
    const IS_ACTIVE         = Ramp_Auth_DbTable_Users::IS_ACTIVE;

    protected $_notValidIdentity = false;   // Assume this is a valid identity
    protected $_pw_not_set_yet = false;     // Assume there is a password

    /**
     * setCredential() - Set the credential value to be used to an 
     * encrypted version of the given $credential parameter, using
     * the actual credential in the database as the salt.  If there
     * is no actual credential in the database (or if it is there but
     * it is empty or a default password), then this function notes
     * that, but has no way to communicate it back to the calling
     * function.  Instead, the calling function should call
     * needsPassword() after calling setCredential() to determine
     * whether the password is empty.
     *
     * @param  string $credential
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setCredential($credential)
    {
        // Determine the default password if there is one.
        $userTable = new Ramp_Auth_DbTable_Users();
        $defaultPassword = $userTable->getDefaultPassword();

        // Store the GIVEN credential while determining the salt.
        $this->_credential = $credential;

        // Get the ACTUAL credential from the database in order to 
        // determine the salt.  (There should obviously be exactly one 
        // credential for the given identity; if not, those errors are 
        // handled in the _authenticate() function.)
        $this->_authenticateSetup();
        $dbGetCredSelect = $this->_authCreateCredSelect();
        $dbGetCredSelect->where(self::ACTIVE . ' = ?', self::IS_ACTIVE);
        $resultIdentities = $this->_authenticateQuerySelect($dbGetCredSelect);
        if ( count($resultIdentities) == 0 )
        {
            $this->_notValidIdentity = true;
        }
        else if ( count($resultIdentities) == 1 )
        {
            // Replace the GIVEN credential with an encrypted version, 
            // using the ACTUAL credential as the salt.
            $resultIdentity = array_shift($resultIdentities);
            $salt = $resultIdentity[$this->_credentialColumn];
            if ( ! isset($salt) || $salt == NULL ||
                 $salt == '' || $salt == $defaultPassword )
            {
                // There is no actual credential; must be set!
                $this->_pw_not_set_yet = true;
            }
            else
            {
                // Encrypt the given credential.
                $encryptedCredential = crypt($this->_credential, $salt);
                return parent::setCredential($encryptedCredential);
            }
        }
        return $this;
    }

    /**
     * Returns true if the identity provided by the user is not a valid
     * identity (does not exist in database or is not active); returns
     * false if it IS a valid identity.
     *
     * @return boolean
     */
    public function notAValidIdentity()
    {
        return $this->_notValidIdentity;
    }

    /**
     * Returns whether the result represents a password that has never 
     * been set to an encrypted value.
     *
     * @return boolean
     */
    public function needsPassword()
    {
        return $this->_pw_not_set_yet;
    }

    /**
     *  _authCreateCredSelect() - This method accepts a Zend_Db_Select 
     *  object to retrieve the actual credential for this identity.
     *
     */
    protected function _authCreateCredSelect()
    {
        // build the SELECT statement to get credential for given identity
        $dbSelect = clone $this->getDbSelect();
        $dbSelect->from($this->_tableName, $this->_credentialColumn)
                 ->where($this->_quoteIdentifier($this->_identityColumn )
                    . ' = ?', $this->_identity);

        return $dbSelect;
    }

    /**
     *  _quoteIdentifier() - get quoted form of given column
     */
    protected function _quoteIdentifier($column)
    {
        return $this->_zendDb->quoteIdentifier($column, true);
    }


}
