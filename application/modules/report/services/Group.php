<?php
class Report_Service_Group extends Unwired_Service_Tree
{
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
}