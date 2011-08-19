<?php

class Unwired_Application_Module_Bootstrap extends Zend_Application_Module_Bootstrap
{
	/**
	 * Load controller specific navigation
	 *
	 */
	protected function _initModuleNav()
	{

		$this->getApplication()->bootstrap('navigation');

		$nav = $this->getApplication()->getResource('navigation');

		$navConfigPath = APPLICATION_PATH . '/modules/'
					   . strtolower($this->getModuleName()) . '/configs/navigation.ini';

		if (file_exists($navConfigPath)) {
			$conf = new Zend_Config_Ini($navConfigPath,
										APPLICATION_ENV);
			$nav->addPages($conf);
		}
	}

	protected function _initGlobalTranslations()
	{
		$this->getApplication()->bootstrap('translate');

		$translate = $this->getApplication()->getResource('translate');

		$path = APPLICATION_PATH . '/modules/'
			  . strtolower($this->getModuleName() . '/languages/' . $translate->getLocale() . '/global.ini');

		if (file_exists($path)) {
			$translate->getAdapter()->addTranslation($path, $translate->getLocale());
		}
	}

	protected function _initAclResources()
	{
		if (Zend_Registry::isRegistered('acl')) {
			return Zend_Registry::get('acl');
		}

		$acl = new Zend_Acl();

		/**
		 * Deny everything to everyone by default
		 */
		$acl->deny(null, null, null);

		Zend_Registry::set('acl', $acl);

		return $acl;
	}


}