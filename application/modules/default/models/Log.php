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

class Default_Model_Log extends Unwired_Model_Generic implements Zend_Acl_Resource_Interface
{
	protected $_logId;

	protected $_userId;

	protected $_entity;

	protected $_entityName;

	protected $_entityId;

	protected $_eventId;

	protected $_eventName;

	protected $_eventData;

	protected $_eventLevel = 100;

	protected $_stamp;

	/**
	 * @return the $logId
	 */
	public function getLogId() {
		return $this->_logId;
	}

	/**
	 * @param field_type $logId
	 */
	public function setLogId($logId) {
		$this->_logId = $logId;

		return $this;
	}

	/**
	 * @return the $userId
	 */
	public function getUserId() {
		return $this->_userId;
	}

	/**
	 * @param field_type $userId
	 */
	public function setUserId($userId) {
		$this->_userId = $userId;

		return $this;
	}

	/**
	 * @return the $entity
	 */
	public function getEntity() {
		return $this->_entity;
	}

	/**
	 * @param field_type $entity
	 */
	public function setEntity($entity) {
		$this->_entity = $entity;

		return $this;
	}

	/**
	 * @return the $entityName
	 */
	public function getEntityName()
	{
		return $this->_entityName;
	}

	/**
	 * @param string $name
	 */
	public function setEntityName($name)
	{
		$this->_entityName = $name;

		return $this;
	}

	/**
	 * @return the $entityId
	 */
	public function getEntityId() {
		return $this->_entityId;
	}

	/**
	 * @param field_type $entityId
	 */
	public function setEntityId($entityId) {
		$this->_entityId = $entityId;

		return $this;
	}

	/**
	 * @return the $eventId
	 */
	public function getEventId() {
		return $this->_eventId;
	}

	/**
	 * @param field_type $eventId
	 */
	public function setEventId($eventId) {
		$this->_eventId = $eventId;

		return $this;
	}

	/**
	 * @return the $eventName
	 */
	public function getEventName() {
		return $this->_eventName;
	}

	/**
	 * @param field_type $eventName
	 */
	public function setEventName($eventName) {
		$this->_eventName = $eventName;

		return $this;
	}

	/**
	 * @return the $eventData
	 */
	public function getEventData() {
		return $this->_eventData;
	}

	/**
	 * @param field_type $eventData
	 */
	public function setEventData($eventData) {
		$this->_eventData = $eventData;

		return $this;
	}

	/**
	 * @return the $eventLevel
	 */
	public function getEventLevel() {
		return $this->_eventLevel;
	}

	/**
	 * @param field_type $eventLevel
	 */
	public function setEventLevel($level = 100) {
		$this->_eventLevel = abs((int) $level);

		return $this;
	}

	/**
	 * @return the $stamp
	 */
	public function getStamp() {
		return $this->_stamp;
	}

	/**
	 * @param field_type $stamp
	 */
	public function setStamp($stamp) {
		$this->_stamp = $stamp;

		return $this;
	}

	public function getResourceId()
	{
		return 'default_log';
	}
}