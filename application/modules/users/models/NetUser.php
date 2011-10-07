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

/**
 * Network user model
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Model_NetUser extends Unwired_Model_Generic implements Zend_Acl_Role_Interface,
																   Zend_Acl_Resource_Interface
{
	protected $_userId = null;

	protected $_groupId = null;

	protected $_username = null;

	protected $_password = null;

	protected $_firstname = null;

	protected $_lastname = null;

	protected $_email = null;

	protected $_phone = null;

	protected $_address = null;

	protected $_city = null;

	protected $_zip = null;

	protected $_country = 'AT';

	protected $_mac = null;

	protected $_radiusSync = 0;

	protected $_policyIds = array(1);

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
	 * @return the $groupId
	 */
	public function getGroupId() {
		return $this->_groupId;
	}

	/**
	 * @param integer $groupId
	 */
	public function setGroupId($groupId) {
		$this->_groupId = (int) $groupId;
		return $this;
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
	 * @return the $username
	 */
	public function getUsername() {
		return $this->_username;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->_username = $username;
		return $this;
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
		if (!empty($password)) {
			if (strlen($password) != 32) {
				$password = md5($password);
			}

			$this->_password = $password;
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

	/**
	 * @return the $mac
	 */
	public function getMac() {
		return $this->_mac;
	}

	/**
	 * @param string $mac
	 */
	public function setMac($mac) {
		/*
		if (!preg_match('/^[A-Z0-9]+$/i', $mac)) {
			$mac = preg_replace('/[^A-Z0-9]+/i', '', $mac);
		}

		$mac = strtoupper(implode('-', str_split($mac, 2)));
		$this->_mac = $mac;
		*/

		$this->_mac = strtoupper(preg_replace('/[^A-F0-9]/i', '', $mac));

		return $this;
	}

	public function isRadiusSync()
	{
		return (int) (bool) $this->_radiusSync;
	}

	public function setRadiusSync($synced = 0)
	{
		$this->_radiusSync = (int) (bool) $synced;

		return $this;
	}

	/**
	 * @return array $policyIds
	 */
	public function getPolicyIds() {
		return $this->_policyIds;
	}

	/**
	 * @param array $policyIds
	 */
	public function setPolicyIds($policyIds = array(1)) {
		if (!is_array($policyIds)) {
			$policyIds = array(1);
		}

		$this->_policyIds = $policyIds;

		return $this;
	}

	public function getResourceId()
	{
		return 'users_netuser';
	}
}