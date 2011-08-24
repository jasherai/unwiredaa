<?php

class Groups_Model_Role extends Unwired_Model_Generic implements Zend_Acl_Role_Interface,
																 Zend_Acl_Resource_Interface
{
	protected $_roleId = null;

	protected $_parentId = null;

	protected $_name = null;

	protected $_permissions = array();

	/**
	 * @return the $roleId
	 */
	public function getRoleId() {
		return $this->_roleId;
	}

	/**
	 * @param field_type $roleId
	 */
	public function setRoleId($roleId) {
		$this->_roleId = $roleId;
		return $this;
	}

	public function getParentId()
	{
		return $this->_parentId;
	}

	public function setParentId($parentId)
	{
		$this->_parentId = $parentId;

		return $this;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	/**
	 * @return the $permissions
	 */
	public function getPermissions() {
		return $this->_permissions;
	}

	/**
	 * @param array $permissions
	 */
	public function setPermissions($permissions) {
		$this->_permissions = $permissions;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Resource_Interface::getResourceId()
	 */
	public function getResourceId() {
		return 'groups-role';
	}
}