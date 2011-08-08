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
class Users_Bootstrap extends Zend_Application_Module_Bootstrap
{
	public function _initViewHelperPath()
	{
		if (!$this->hasPluginResource('view')) {
			$this->registerPluginResource('view');
		}

		$this->bootstrapView();

		$view = $this->getResource('view');

		$view->addHelperPath(APPLICATION_PATH . '/modules/users/view/helpers', 'Users_View_Helper');

		return null;
	}
}