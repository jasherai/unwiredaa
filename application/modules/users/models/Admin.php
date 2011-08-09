<?php

class Users_Model_Admin extends Unwired_Model_Generic implements Zend_Acl_Role_Interface
{
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
	 * @param field_type $password
	 */
	public function setPassword($password) {
		$this->_password = $password;
	}

	/**
	 * @return the $firstname
	 */
	public function getFirstname() {
		return $this->_firstname;
	}

	/**
	 * @param field_type $firstname
	 */
	public function setFirstname($firstname) {
		$this->_firstname = $firstname;
	}

	/**
	 * @return the $lastname
	 */
	public function getLastname() {
		return $this->_lastname;
	}

	/**
	 * @param field_type $lastname
	 */
	public function setLastname($lastname) {
		$this->_lastname = $lastname;
	}

	/**
	 * @return the $email
	 */
	public function getEmail() {
		return $this->_email;
	}

	/**
	 * @param field_type $email
	 */
	public function setEmail($email) {
		$this->_email = $email;
	}

	/**
	 * @return the $phone
	 */
	public function getPhone() {
		return $this->_phone;
	}

	/**
	 * @param field_type $phone
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
	 * @param field_type $address
	 */
	public function setAddress($address) {
		$this->_address = $address;
	}

	/**
	 * @return the $city
	 */
	public function getCity() {
		return $this->_city;
	}

	/**
	 * @param field_type $city
	 */
	public function setCity($city) {
		$this->_city = $city;
	}

	/**
	 * @return the $zip
	 */
	public function getZip() {
		return $this->_zip;
	}

	/**
	 * @param field_type $zip
	 */
	public function setZip($zip) {
		$this->_zip = $zip;
	}

	/**
	 * @return the $country
	 */
	public function getCountry() {
		return $this->_country;
	}

	/**
	 * @param field_type $country
	 */
	public function setCountry($country) {
		$this->_country = $country;
	}


}