<?php

class Groups_Model_Group extends Unwired_Model_Generic
{
	protected $_groupId = null;

	protected $_parentId = null;

	protected $_roleId = null;

	protected $_name = null;

	protected $_parent = null;

	protected $_children = array();

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
	}

	/**
	 * @return the $parentId
	 */
	public function getParentId() {
		return $this->_parentId;
	}

	/**
	 * @param integer|null $parentId
	 */
	public function setParentId($parentId) {
		$this->_parentId = $parentId;
	}

	/**
	 * @return the $roleId
	 */
	public function getRoleId() {
		return $this->_roleId;
	}

	/**
	 * @param integer $roleId
	 */
	public function setRoleId($roleId) {
		$this->_roleId = $roleId;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->_name = $name;
	}

	public function getChildren()
	{
		return $this->_children;
	}

	public function setChildren(array $children)
	{
		$this->_children = $children;
		return $this;
	}

	public function addChild(Groups_Model_Group $child) {
		if (!in_array($child, $this->_children)) {
			$this->_children[] = $child;
		}

		return $this;
	}

	public function getParent()
	{
		return $this->_parent;
	}

	public function setParent(Groups_Model_Group $parent = null)
	{
		$this->_parent = $parent;
		return $this;
	}
}