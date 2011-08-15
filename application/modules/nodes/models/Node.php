<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Node model (aggregates location and settings)
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Nodes_Model_Node extends Unwired_Model_Generic
{
	protected $_nodeId = null;

	protected $_groupId = null;

	protected $_name = null;

	protected $_mac = null;

	protected $_status = null;

	protected $_location = null;

	protected $_settings = null;

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
	 * @return the $groupId
	 */
	public function getGroupId() {
		return $this->_groupId;
	}

	/**
	 * @param integer $groupId
	 */
	public function setGroupId($groupId) {
		$this->_groupId = $groupId;

		return $this;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @param integer $name
	 */
	public function setName($name) {
		$this->_name = $name;

		return $this;
	}

	/**
	 * @return the $mac
	 */
	public function getMac() {
		return $this->_mac;
	}

	/**
	 * @param integer $mac
	 */
	public function setMac($mac) {
		$this->_mac = $mac;

		return $this;
	}

	/**
	 * @return the $status
	 */
	public function getStatus() {
		return $this->_status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->_status = $status;

		return $this;
	}
	/**
	 * @return the $_location
	 */
	public function getLocation() {
		return $this->_location;
	}

	/**
	 * @param Nodes_Model_Location $_location
	 */
	public function setLocation(Nodes_Model_Location $location) {
		$this->_location = $location;

		return $this;
	}

	/**
	 * @return the $_settings
	 */
	public function getSettings() {
		return $this->_settings;
	}

	/**
	 * @param Nodes_Model_Settings $_settings
	 */
	public function setSettings(Nodes_Model_Location $settings) {
		$this->_settings = $settings;

		return $this;
	}

}