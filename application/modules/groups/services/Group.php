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

	public function prepareMapperListingByAdmin($mapper = null, $admin = null, $lowerOnly = true, $params = array())
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

		$params['group_id'] = $accessibleGroupIds;
		/**
		 * @todo Auto join in findBy is slow... do something
		 */
		$mapper->findBy($params, 0);

		$paginatorAdapter = $mapper->getPaginatorAdapter();

		if (method_exists($paginatorAdapter, 'setGroups')) {
			$paginatorAdapter->setGroups($groups);
		}

		return $mapper;
	}

	public function prepareMapperListing(Groups_Model_Group $group, $mapper = null, $children = false, $params = array())
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
		}

		$acl = Zend_Registry::get('acl');


		if ($children) {
			$group->setChildren($this->getNodeChildren($group));
		}

		$resource = $mapper->getEmptyModel();

		$accessibleGroupIds = array();

		if (!$acl->isAllowed($group, $resource, 'view')) {
			throw new Unwired_Exception('Access to group not allowed');
		}

		$accessibleGroupIds[] = $group->getGroupId();

		$iterator = new RecursiveIteratorIterator($group, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $child) {
			$accessibleGroupIds[] = $child->getGroupId();
		}

		$params['group_id'] = $accessibleGroupIds;
		/**
		 * @todo Auto join in findBy is slow... do something
		 */
		$mapper->findBy($params, 0);

		$paginatorAdapter = $mapper->getPaginatorAdapter();

		if (method_exists($paginatorAdapter, 'setGroups')) {
			$paginatorAdapter->setGroups(array($group));
		}

		return $mapper;
	}
}