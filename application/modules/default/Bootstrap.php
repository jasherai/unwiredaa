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

class Default_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		$acl->addResource(new Zend_Acl_Resource('default_setting'));

		return $acl;
	}

	public function _initSettings()
	{
		$this->getApplication()->bootstrap('db');

		$mapperSettings = new Default_Model_Mapper_Settings();

		$settings = $mapperSettings->fetchAll();

		$sorted = array();

		foreach ($settings  as $setting) {
			$sorted[$setting->getKey()] = $setting;
		}

		$front = $this->getApplication()->getResource('frontcontroller');

		$front->setParam('settings', $sorted);

		return $sorted;
	}
}