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
    public function _initRestRoutes()
    {
    	if (!$this->hasResource('frontController')) {
    		$this->bootstrap('frontController');
    	}

    	$front = $this->getResource('frontController');

    	$router = $front->getRouter();

    	$route = new Zend_Controller_Router_Route('api');

		$router->addRoute("api", $route->chain(new Zend_Rest_Route($front,
																   array('controller' => 'api'),
																   array())));
    }
}