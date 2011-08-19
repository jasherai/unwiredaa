<?php

class Groups_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		$acl->addResource(new Zend_Acl_Resource('groups-group'));
		$acl->addResource(new Zend_Acl_Resource('groups-policy'));
		$acl->addResource(new Zend_Acl_Resource('groups-role'));

		$front = $this->getApplication()->getResource('frontcontroller');

		$front->registerPlugin(new Groups_Plugin_Acl());

		return $acl;
	}
}