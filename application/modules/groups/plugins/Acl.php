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
/*Zend_Debug::dump(serialize(
	array(
		array('resource' => 'groups-group',
			  'permissions' => array('view','add','edit','delete')),
		array('resource' => 'groups-role',
			  'permissions' => array('view','add','edit','delete')),
		array('resource' => 'groups-policy',
			  'permissions' => array('view','add','edit','delete')),
		array('resource' => 'nodes-node',
			  'permissions' => array('view','add','edit','delete')),
		array('resource' => 'users-admin',
			  'permissions' => array('view','add','edit','delete')),
		array('resource' => 'users-admin',
			  'permissions' => array('view','add','edit','delete'))
	)
)); die();*/
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
			$groupsAllowed[] = $this->_addGroup($group,
												null,
												$admin->getGroupAssignedRoleId($group->getGroupId()));
		}

		$acl->addRole($admin, $admin->getGroupsAssigned());

		$acl->allow($admin, $groupsAllowed, 'access');

		Zend_View_Helper_Navigation::setDefaultAcl($acl);
		Zend_View_Helper_Navigation::setDefaultRole($admin);
	}

	protected function _addGroup(Groups_Model_Group $group, Zend_Acl_Resource $parent = null, $parentRole = null)
	{
		$resource = new Zend_Acl_Resource($group->getGroupResourceId());

		$role = new Zend_Acl_Role($group->getGroupResourceId());


		if (null === $parentRole) {
			$parentRole = $parent->getResourceId();
		}

		if (!$this->_acl->hasRole($role)) {
			$this->_acl->addRole($role, $parentRole);

			$this->_acl->addResource($resource, $parent);
		}

		foreach ($group->getChildren() as $child) {
			$this->_addGroup($child, $resource);
		}

		return $resource;
	}
}