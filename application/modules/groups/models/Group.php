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

class Groups_Model_Group extends Unwired_Model_Tree implements Zend_Acl_Role_Interface,
															   Zend_Acl_Resource_Interface
{
	protected $_groupId = null;

	protected $_parentId = null;

	protected $_name = null;

	protected $_parent = null;

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
		return $this;
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
		return $this;
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Role_Interface::getRoleId()
	 */
	public function getRoleId()
	{
		return $this->getGroupResourceId();
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Resource_Interface::getResourceId()
	 */
	public function getResourceId() {
		return 'groups_group';
	}

	public function getGroupResourceId()
	{
		return $this->getResourceId() . '_' . $this->getGroupId();
	}

	/* (non-PHPdoc)
	 * @see Unwired_Model_Tree::getTreeBranchId()
	 */
	public function getTreeBranchId() {
		return $this->getGroupId();

	}

	/* (non-PHPdoc)
	 * @see Unwired_Model_Tree::getTreeBranchName()
	 */
	public function getTreeBranchName() {
		return $this->getName();
	}
}