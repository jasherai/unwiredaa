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

class Rest_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	/**
	 * Initialize REST routes
	 */
    protected function _initRestRoutes()
    {
    	if (!$this->hasResource('frontController')) {
    		$this->bootstrap('frontController');
    	}

    	$front = $this->getResource('frontController');

    	$router = $front->getRouter();

    	$route = new Zend_Controller_Router_Route('api');

		$router->addRoute("api", $route->chain(new Zend_Rest_Route($front,
																   array(),
																   array())));
    }

    protected function _initRestAuth()
    {
		$this->getApplication()->bootstrap('frontcontroller');

		$front = $this->getApplication()->getResource('frontcontroller');

		$restAuth = new Rest_Plugin_Auth();

		$front->registerPlugin($restAuth);

		return $restAuth;
    }

	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		$acl->addResource(new Zend_Acl_Resource('rest_key'));

		return $acl;
	}
}