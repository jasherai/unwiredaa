<?php
/**
* Unwired AA GUI
*
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
*
* Licensed under the terms of the Affero Gnu Public License version 3
* (AGPLv3 - http://www.gnu.org/licenses/agpl.html) or our proprietory
* license available at http://www.unwired.at/license.html
*/

class Rest_Model_Key extends Unwired_Model_Generic implements Zend_Acl_Resource_Interface
{
	/**
	 * @var integer
	 */
	protected $_keyId = null;

	/**
	 * @var integer
	 */
	protected $_userId = null;

	/**
	 * @var string
	 */
	protected $_key = null;

	/**
	 * @var string
	 */
	protected $_secret = null;

	/**
	 * @var boolean
	 */
	protected $_active = 1;


	protected $_admin = null;

	/**
	 * @return the $keyId
	 */
	public function getKeyId()
	{
		return $this->_keyId;
	}

	/**
	 * @param integer $keyId
	 */
	public function setKeyId($keyId)
	{
		$this->_keyId = $keyId;

		return $this;
	}

	/**
	 * @return the $userId
	 */
	public function getUserId()
	{
		return $this->_userId;
	}

	/**
	 * @param integer $userId
	 */
	public function setUserId($userId)
	{
		$this->_userId = $userId;

		return $this;
	}

	/**
	 * @return the $key
	 */
	public function getKey()
	{
		return $this->_key;
	}

	/**
	 * @param string $key
	 */
	public function setKey($key)
	{
		$this->_key = $key;

		return $this;
	}

	/**
	 * @return the $secret
	 */
	public function getSecret()
	{
		return $this->_secret;
	}

	/**
	 * @param string $secret
	 */
	public function setSecret($secret)
	{
		$this->_secret = $secret;

		return $this;
	}

	/**
	 * @return the $active
	 */
	public function isActive()
	{
		return $this->_active;
	}

	/**
	 * @param boolean $active
	 */
	public function setActive($active = true)
	{
		$this->_active = (int) (bool) $active;

		return $this;
	}

	public function getAdmin()
	{
        return $this->_admin;
	}

	public function setAdmin(Users_Model_Admin $admin = null)
	{
	    $this->_admin = $admin;
	    return $this;
	}
	public function getResourceId()
	{
	    return 'rest_key';
	}
}