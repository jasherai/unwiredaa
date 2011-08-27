<?php

class Default_Model_Settings extends Unwired_Model_Generic implements Zend_Acl_Resource_Interface
{
	protected $_settingId = null;

	protected $_key = null;

	protected $_value = null;

	/**
	 * @return the $settingId
	 */
	public function getSettingId() {
		return $this->_settingId;
	}

	/**
	 * @param field_type $settingId
	 */
	public function setSettingId($settingId) {
		$this->_settingId = $settingId;
		return $this;
	}

	/**
	 * @return the $key
	 */
	public function getKey() {
		return $this->_key;
	}

	/**
	 * @param field_type $key
	 */
	public function setKey($key) {
		$this->_key = $key;
		return $this;
	}

	/**
	 * @return the $value
	 */
	public function getValue() {
		return $this->_value;
	}

	/**
	 * @param field_type $value
	 */
	public function setValue($value) {
		$this->_value = $value;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Resource_Interface::getResourceId()
	 */
	public function getResourceId() {
		return 'default_setting';
	}




}