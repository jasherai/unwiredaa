<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Users module bootstrap
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	public function _initIdentity()
	{
		$this->getApplication()->bootstrap('db');

		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			return null;
		}

		$identity = $auth->getIdentity();

		$mapper = new Users_Model_Mapper_Admin();

		$user = $mapper->find($identity->getUserId());

		if (!$user) {
			$auth->clearIdentity();

			return;
		}

		$auth->getStorage()->write($user);

		return $user;
	}

	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		$acl->addResource(new Zend_Acl_Resource('users-admin'));
		$acl->addResource(new Zend_Acl_Resource('users-netuser'));

		return $acl;
	}
}