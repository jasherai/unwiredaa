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
 * Node model (aggregates location and settings)
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Nodes_Model_Node extends Unwired_Model_Generic implements Zend_Acl_Resource_Interface
{
	protected $_nodeId = null;

	protected $_groupId = null;

	protected $_name = null;

	protected $_mac = null;

	protected $_status = 'planning';

	protected $_toUpdate = 0;

	protected $_location = null;

	protected $_settings = null;

	protected $_group = null;

	protected $_onlineStatus = 0;

	protected $_onlineStatusChanged = 0;

	protected $_onlineUsersCount = 0;

	protected $_deleted = 0;

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
		$this->_nodeId = (int) $nodeId;

		$this->getLocation()->setNodeId($this->_nodeId);
		$this->getSettings()->setNodeId($this->_nodeId);

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

	public function getGroup()
	{
		return $this->_group;
	}

	public function setGroup(Groups_Model_Group $group = null)
	{
		$this->_group = $group;
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
		$this->_mac = strtoupper(preg_replace('/[^A-F0-9]/i', '', $mac));

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
	 * @return the $_onlineStatus
	 */
	public function getOnlineStatus() {
		return $this->_onlineStatus;
	}

	/**
	 * @param bool $status
	 */
	public function setOnlineStatus($status = 0) {
		$this->_onlineStatus = (int) (bool) $status;

		return $this;
	}

	/**
	 * @return the $_onlineStatusChanged
	 */
	public function getOnlineStatusChanged() {
		return $this->_onlineStatusChanged;
	}

	/**
	 * @param timestamp $changed
	 */
	public function setOnlineStatusChanged($changed) {
		$this->_onlineStatusChanged = $changed;

		return $this;
	}

	public function getOnlineUsersCount()
	{
		return $this->_onlineUsersCount;
	}

	public function setOnlineUsersCount($count = 0)
	{
		$this->_onlineUsersCount = (int) $count;

		return $this;
	}

	/**
	 * @return the $_toUpdate
	 */
	public function getToUpdate() {
		return $this->_toUpdate;
	}

	/**
	 * @param field_type $toUpdate
	 */
	public function setToUpdate($toUpdate) {
		$this->_toUpdate = (int) (bool) $toUpdate;

		return $this;
	}

	public function isDeleted()
	{
		return (int) $this->_deleted;
	}

	public function setDeleted($deleted = false)
	{
		$this->_deleted = (int) (bool) $deleted;

		return $this;
	}

	/**
	 * @return Nodes_Model_Location $location
	 */
	public function getLocation() {
		if (null === $this->_location){
			$this->_location = new Nodes_Model_Location();
		}
		return $this->_location;
	}

	/**
	 * @param Nodes_Model_Location|array $location
	 * @return Nodes_Model_Node
	 */
	public function setLocation($location) {
		if ($location instanceof Nodes_Model_Location) {
			$this->_location = $location;
		} elseif (is_array($location)) {
			$this->getLocation()->fromArray($location);
		} else {
			throw new Unwired_Exception('Trying to set invalid value for node location');
		}

		$this->getLocation()->setNodeId($this->getNodeId());
		return $this;
	}

	/**
	 * @return Nodes_Model_Settings $settings
	 */
	public function getSettings() {
		if (null === $this->_settings){
			$this->_settings = new Nodes_Model_Settings();
		}

		return $this->_settings;
	}

	/**
	 * @param Nodes_Model_Settings|array $settings
	 * @return Nodes_Model_Node
	 */
	public function setSettings($settings) {
		if ($settings instanceof Nodes_Model_Settings) {
			$this->_settings = $settings;
		} elseif (is_array($settings)) {
			$this->getSettings()->fromArray($settings);
		} else {
			throw new Unwired_Exception('Trying to set invalid value for node settings');
		}

		$this->getSettings()->setNodeId($this->getNodeId());

		return $this;
	}

	public function toArray()
	{
		$data = parent::toArray();

		$data['settings'] = $data['settings']->toArray();
		$data['location'] = $data['location']->toArray();

		return $data;
	}

	/* Resource Id
	 * @see Zend_Acl_Resource_Interface::getResourceId()
	 */
	public function getResourceId() {
		return 'nodes_node';
	}

}