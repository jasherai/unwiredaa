<?php

class Groups_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	protected $_assertInstances = array();

	/**
	 *
	 * @var Zend_Acl
	 */
	protected $_acl = null;

	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
	{

		/**
		 * The access list
		 * @var Zend_Acl
		 */
		$acl = Zend_Registry::get('acl');

		$this->_acl = $acl;

		$mapper = new Groups_Model_Mapper_Role();

		$roles = $mapper->fetchAll();

		foreach ($roles as $role) {

			$acl->addRole($role);
			foreach ($role->getPermissions() as $rule) {
				$assertInstance = null;
				if (null !== $rule['resource']) {
					if (!$acl->has($rule['resource'])) {
						continue;
					}

					$parts = explode('-', $rule['resource']);

					$className = ucfirst($parts[0]) .'_Service_Acl';

					if (!isset($this->_assertInstances[$className])) {
						$this->_assertInstances[$className] = new Unwired_Acl_Assert_Proxy($className);
					}

					$assertInstance = $this->_assertInstances[$className];
				}

				$acl->allow($role, $rule['resource'], $rule['permissions'], $assertInstance);
			}
		}

		$auth = Zend_Auth::getInstance();

		if (!$auth->hasIdentity()) {
			return;
		}

		$admin = $auth->getIdentity();

		$service = new Groups_Service_Group();

		$groups = $service->getGroupsByAdmin($admin);

		$policyGroups = array();
		$groupsAllowed = array();
		foreach ($groups as $group) {
			$groupsAllowed[] = $this->_addGroup($group);
			$policyGroups[$group->getRoleId()] = "{$group->getRoleId()}";
		}

		$acl->addRole($admin, $policyGroups);

		$acl->allow($admin, $groupsAllowed, 'access');
	}

	protected function _addGroup(Groups_Model_Group $group, Zend_Acl_Resource $parent = null)
	{
		$resource = new Zend_Acl_Resource($group->getGroupResourceId());

		$this->_acl->addResource($resource, $parent);

		foreach ($group->getChildren() as $child) {
			$this->_addGroup($child, $resource);
		}

		return $resource;
	}
}