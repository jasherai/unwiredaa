<?php

class Groups_Service_Group {

	protected $_groupMapper = null;

	/**
	 * Get the group mapper
	 * @return Groups_Model_Mapper_Group
	 */
	public function getGroupMapper()
	{
		if (null === $this->_groupMapper) {
			$this->_groupMapper = new Groups_Model_Mapper_Group();
		}

		return $this->_groupMapper;
	}

	public function findGroup($groupId, $parents = false, $children = false)
	{
		$group = $this->getGroupMapper()->find($groupId);

		if (!$group) {
			return false;
		}

		if ($parents) {
			$group->setParent($this->getGroupParent($group, true));
		}

		if ($children) {
			$group->setChildren($this->getGroupChildren($group, true));
		}

		return $group;
	}

	public function getGroupTreeByAdmin(Users_Model_Admin $admin = null)
	{
		$groups = $this->getGroupsByAdmin($admin);

		if (!$groups) {
			return null;
		}
		/**
		 * Assume that there's only one root group (network)
		 */

		$root = $groups[0];

		while ($parent = $root->getParent()) {
			$parent->addChild($root);
			$root = $parent;
		}

		return $root;
	}

	/**
	 * Get groups that the user belongs to
	 *
	 * @param Users_Model_Admin $admin
	 * @return array()
	 */
	public function getGroupsByAdmin(Users_Model_Admin $admin = null)
	{
		if (null === $admin) {
			$admin = Zend_Auth::getInstance()->getIdentity();
		}

		if (!$admin->getGroupIds()) {
			return array();
		}

		$groups = array();
		foreach ($admin->getGroupIds() as $id) {
			$group = $this->getGroupMapper()->find($id);
			$this->loadRelatedGroups($group);
			$groups[] = $group;
		}

		return $groups;
	}

	/**
	 * Load both parent and child groups
	 *
	 * @param Groups_Model_Group $group
	 * @return Groups_Service_Group
	 */
	public function loadRelatedGroups(Groups_Model_Group $group)
	{
		$group->setParent($this->getGroupParent($group));
		$group->setChildren($this->getGroupChildren($group));

		return $this;
	}

	/**
	 * Get group parent(s)
	 *
	 * @param Groups_Model_Group $group
	 * @param bool $recursive
	 * @return Groups_Model_Group
	 */
	public function getGroupParent(Groups_Model_Group $group, $recursive = true)
	{
		if (null === $group->getParentId()) {
			return null;
		}

		$parent = $this->getGroupMapper()->find($group->getParentId());

		$parent->addChild($group);

		if ($recursive) {
			$current = $parent;

			while ($current->getParentId()) {
				$prev = $current;
				$current = $this->getGroupMapper()->find($prev->getParentId());
				$current->addChild($prev);
				$prev->setParent($current);
			}
		}

		return $parent;
	}

	/**
	 * Get group children
	 *
	 * @param Groups_Model_Group $group
	 * @param bool $recursive
	 * @return array
	 */
	public function getGroupChildren(Groups_Model_Group $group = null, $recursive = true)
	{
		if (null == $group) {
			$children = $this->getGroupMapper()->findBy(array('parent_id' => null));
		} else {
			$children = $this->getGroupMapper()->findBy(array('parent_id' => $group->getGroupId()));
		}

		if ($recursive) {
			foreach ($children as $child) {
				$child->setChildren($this->getGroupChildren($child, $recursive));
			}
		}

		return $children;
	}
}