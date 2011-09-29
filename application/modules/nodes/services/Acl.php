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

class Nodes_Service_Acl implements Zend_Acl_Assert_Interface
{
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

		if (!$resource instanceof Nodes_Model_Node || null === $resource->getGroupId()) {
			return true;
		}

		$groupRole = 'groups_group_' . $resource->getGroupId();
		if (!$acl->hasRole($groupRole)) {
			return false;
		}

		return $acl->isAllowed($groupRole, $resource, $privilege);
	}


}