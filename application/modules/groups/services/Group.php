<?php

class Groups_Service_Group extends Unwired_Service_Tree
{

	public function findGroup($groupId, $parents = false, $children = false)
	{
		return parent::findNode($groupId);
	}

	/**
	 * Get root node with children which are applicable for an admin user
	 * @param Users_Model_Admin $admin
	 * @return Groups_Model_Group
	 */
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

		if (!$admin->getGroupsAssigned()) {
			return array();
		}

		$groups = array();
		foreach ($admin->getGroupsAssigned() as $groupId => $roleId) {
			$group = $this->_getDefaultMapper()->find($groupId);
			$this->loadTree($group);
			$groups[] = $group;
		}

		return $groups;
	}

	public function prepareMapperListingByAdmin($mapper = null, $admin = null, $lowerOnly = true)
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
		}

		if (null === $admin) {
			$admin = Zend_Auth::getInstance()->getIdentity();
		}

		$acl = Zend_Registry::get('acl');

		$groups = $this->getGroupsByAdmin($admin);

		$resource = $mapper->getEmptyModel();

		$accessibleGroupIds = array();

		foreach ($groups as $group) {
			if (!$acl->isAllowed($group, $resource, 'view')) {
				continue;
			}

			if (!$lowerOnly || $acl->isAllowed($admin, null, 'super')) {
				$accessibleGroupIds[] = $group->getGroupId();
			}

			$iterator = new RecursiveIteratorIterator($group, RecursiveIteratorIterator::SELF_FIRST);

			foreach ($iterator as $child) {
				$accessibleGroupIds[] = $child->getGroupId();
			}
		}

		/**
		 * @todo Auto join in findBy is slow... do something
		 */
		$mapper->findBy(array('group_id' => $accessibleGroupIds), 0);

		return $mapper;
	}
}