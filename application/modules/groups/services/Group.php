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

	public function getGroupsByAdmin(Users_Model_Admin $admin = null)
	{
		if (null === $admin) {
			$admin = Zend_Auth::getInstance()->getIdentity();
		}

		if (null === $admin->getGroupId()) {
			$group = $this->getGroupMapper()->getEmptyModel();
		} else {
			$group = $this->getGroupMapper()->find($admin->getGroupId());
		}

		$this->loadRelatedGroups($group);

		return $group;
	}

	public function loadRelatedGroups(Groups_Model_Group $group)
	{
		$group->setParent($this->getGroupParent($group));
		$group->setChildren($this->getGroupChildren($group));

		return $this;
	}

	public function getGroupParent(Groups_Model_Group $group, $recursive = true)
	{
		if (null === $group->getParentId()) {
			return null;
		}

		$parent = $this->getGroupMapper()->find($group->getParentId());

		if ($recursive) {
			$current = $parent;

			while ($current->getParentId()) {
				$prev = $current;
				$current = $this->getGroupMapper()->find($prev->getParentId());
				$prev->setParent($current);
			}
		}

		return $parent;
	}

	public function getGroupChildren(Groups_Model_Group $group = null, $recursive = true)
	{
		if (null == $group) {
			$children = $this->getGroupMapper()->findBy(array('parent_id' => null));
		} else {
			$children = $this->getGroupMapper()->findBy(array('parent_id' => $group->getGroupId()));
		}

		if ($recursive) {
			foreach ($children as $child) {
				$this->setChildren($this->getGroupChildren($child));
			}
		}

		return $children;
	}
}