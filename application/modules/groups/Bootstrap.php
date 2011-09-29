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

class Groups_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		$acl->addResource(new Zend_Acl_Resource('groups_group'));
		$acl->addResource(new Zend_Acl_Resource('groups_policy'));
		$acl->addResource(new Zend_Acl_Resource('groups_role'));

		$front = $this->getApplication()->getResource('frontcontroller');

		$front->registerPlugin(new Groups_Plugin_Acl());

		return $acl;
	}
}