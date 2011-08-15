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
	protected $_node_id = null;

	protected $_address = null;

	protected $_city = null;

	protected $_zip = null;

	protected $_country = null;

	protected $_lat = null;

	protected $_lng = null;

	/**
	 * @return the $node_id
	 */
	public function getNode_id() {
		return $this->_node_id;
	}

	/**
	 * @param field_type $node_id
	 */
	public function setNode_id($node_id) {
		$this->_node_id = $node_id;

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
	 * @param field_type $country
	 */
	public function setCountry($country) {
		$this->_country = $country;

		return $this;
	}

	/**
	 * @return the $lat
	 */
	public function getLat() {
		return $this->_lat;
	}

	/**
	 * @param field_type $lat
	 */
	public function setLat($lat) {
		$this->_lat = $lat;

		return $this;
	}

	/**
	 * @return the $lng
	 */
	public function getLng() {
		return $this->_lng;
	}

	/**
	 * @param field_type $lng
	 */
	public function setLng($lng) {
		$this->_lng = $lng;

		return $this;
	}

}