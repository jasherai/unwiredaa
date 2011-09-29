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

class Users_Service_Acl implements Zend_Acl_Assert_Interface
{
	protected $_recursion = 0;
	/* (non-PHPdoc)
	 * @see Zend_Acl_Assert_Interface::assert()
	 */
	public function assert(Zend_Acl $acl,
						   Zend_Acl_Role_Interface $role = null,
						   Zend_Acl_Resource_Interface $resource = null,
						   $privilege = null)
	{
		if (!$role instanceof Users_Model_Admin) {
			return true;
		}

		if ((!$resource instanceof Users_Model_Admin && !$resource instanceof Users_Model_NetUser)/* || null === $resource->getParentId()*/) {
			return true;
		}

		/**
		 * default check on empty model
		 */
		if (!$resource->getUserId()) {
			return true;
		}

		if ($resource instanceof Users_Model_NetUser) {
			$checkGroups = array($resource->getGroupId());
		} else {
			$checkGroups = array_keys($resource->getGroupsAssigned());
		}

		$prefix = 'groups_group_';

		$result = false;
		foreach (array_keys($role->getGroupsAssigned()) as $groupId) {
			foreach ($checkGroups as $checkGroupId) {

				if (!$acl->has($prefix . $checkGroupId) || !$acl->has($prefix . $groupId)) {
					continue;
				}

				/**
				 * Checked category is child of current administrator's category
				 */
				if (!$acl->inherits($prefix . $checkGroupId, $prefix . $groupId)) {
					continue;
				}

				$result = $acl->isAllowed($prefix . $groupId, $resource, $privilege);

				if ($result) {
					break 2;
				}
			}
		}

		return $result;
	}
}