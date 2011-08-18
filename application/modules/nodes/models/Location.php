<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Node location model
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Nodes_Model_Location extends Unwired_Model_Generic
{
	protected $_nodeId = null;

	protected $_address = null;

	protected $_city = null;

	protected $_zip = null;

	protected $_country = null;

	protected $_latitude = null;

	protected $_longitude = null;

	/**
	 * @return the $nodeId
	 */
	public function getNodeId() {
		return $this->_nodeId;
	}

	/**
	 * @param integer $nodeId
	 */
	public function setNodeId($nodeId) {
		$this->_nodeId = $nodeId;

		return $this;
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

		return $this;
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

		return $this;
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

		return $this;
	}

	/**
	 * @return the $country
	 */
	public function getCountry() {
		return $this->_country;
	}

	/**
	 * @param string $country
	 */
	public function setCountry($country) {
		$this->_country = $country;

		return $this;
	}

	/**
	 * @return the $latitude
	 */
	public function getLatitude() {
		return $this->_latitude;
	}

	/**
	 * @param float $latitude
	 */
	public function setLatitude($latitude) {
		$this->_latitude = $latitude;

		return $this;
	}

	/**
	 * @return the $lng
	 */
	public function getLongitude() {
		return $this->_longitude;
	}

	/**
	 * @param float $longitude
	 */
	public function setLongitude($longitude) {
		$this->_longitude = $longitude;

		return $this;
	}

}