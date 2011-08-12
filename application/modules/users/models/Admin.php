<?php

class Users_Model_Admin extends Unwired_Model_Generic implements Zend_Acl_Role_Interface
{
	protected $_userId = null;

	protected $_groupIds = array();

	protected $_password = null;

	protected $_firstname = null;

	protected $_lastname = null;

	protected $_email = null;

	protected $_phone = null;

	protected $_address = null;

	protected $_city = null;

	protected $_zip = null;

	protected $_country = null;

	/**
	 * @return the $userId
	 */
	public function getUserId() {
		return $this->_userId;
	}

	/**
	 * @param integer $userId
	 */
	public function setUserId($userId) {
		$this->_userId = (int) $userId;
		return $this;
	}

	/**
	 * @return the $groupIds
	 */
	public function getGroupIds() {
		return $this->_groupIds;
	}

	/**
	 * @param array $groupIds
	 */
	public function setGroupIds(array $groupIds) {
		$this->_groupIds = $groupIds;
		return $this;
	}

	/**
	 * Check if user is direct member of a group
	 * @param integer $groupId
	 * @return boolean
	 */
	public function hasGroupId($groupId)
	{
		return in_array($groupId, $this->_groupIds);
	}

	/**
	 * ACL role unique identifier
	 *
	 * @see Zend_Acl_Role_Interface::getRoleId()
	 */
	public function getRoleId()
	{
		return $this->getEmail();
	}

	/**
	 * @return the $password
	 */
	public function getPassword() {
		return $this->_password;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		if (strlen($password) != 40) {
			$this->_password = sha1($password);
		}
		return $this;
	}

	/**
	 * @return the $firstname
	 */
	public function getFirstname() {
		return $this->_firstname;
	}

	/**
	 * @param string $firstname
	 */
	public function setFirstname($firstname) {
		$this->_firstname = $firstname;
		return $this;
	}

	/**
	 * @return the $lastname
	 */
	public function getLastname() {
		return $this->_lastname;
	}

	/**
	 * @param string $lastname
	 */
	public function setLastname($lastname) {
		$this->_lastname = $lastname;
		return $this;
	}

	/**
	 * @return the $email
	 */
	public function getEmail() {
		return $this->_email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->_email = $email;
		return $this;
	}

	/**
	 * @return the $phone
	 */
	public function getPhone() {
		return $this->_phone;

	}

	/**
	 * @param string $phone
	 */
	public function setPhone($phone) {
		$this->_phone = $phone;
	}

	/**
	 * @return the $address
	 */
	public function getAddress() {
		return $this->_address;
	}

	/**
	 * @param string $address
	 */
	public function setAddress($address) {
		$this->_address = $address;
		return $this;
	}

	/**
	 * @return the $city
	 */
	public function getCity() {
		return $this->_city;
	}

	/**
	 * @param string $city
	 */
	public function setCity($city) {
		$this->_city = $city;
		return $this;
	}

	/**
	 * @return the $zip
	 */
	public function getZip() {
		return $this->_zip;
	}

	/**
	 * @param string $zip
	 */
	public function setZip($zip) {
		$this->_zip = $zip;
		return $this;
	}

	/**
	 * @return the $country
	 */
	public function getCountry() {
		return $this->_country;
	}

	/**
	 * @param string $country Two character country code
	 */
	public function setCountry($country) {
		$this->_country = $country;
		return $this;
	}

	public function __wakeup()
	{
		if (!class_exists('Users_Model_Admin')) {
			require_once(APPLICATION_PATH . '/modules/users/models/Admin.php');
		}
	}

}