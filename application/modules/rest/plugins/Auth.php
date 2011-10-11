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

class Rest_Plugin_Auth extends Zend_Controller_Plugin_Abstract
                       implements Zend_Auth_Adapter_Interface
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$route = Zend_Controller_Front::getInstance()
										   ->getRouter()
										   		->getCurrentRouteName();

		if ($route != 'api') {
			return;
		}


		$appKey = $this->getRequest()->getParam('app_key', null);

		if (!$appKey) {
			$this->_authError();
			return;
		}

		$service = new Rest_Service_Auth();

		$admin = $service->getAdminByKey($appKey);

		if (!$admin || !$service->checkKey($appKey, $admin)) {
		    $this->_authError();
		    return;
		}

		$appKey = $service->getKeyByAdmin($admin);

		/*if (!$service->checkRequest($appKey, $request)) {
		    $this->_authError();
		    return;
		}*/
		Zend_Auth::getInstance()->clearIdentity();
		Zend_Auth::getInstance()->getStorage()->write($admin);
	}

	protected function _authError()
	{
	    $this->getRequest()->setModuleName('rest')
							   ->setControllerName('error')
							   ->setActionName('not-authorized');
		return;
	}

	/* (non-PHPdoc)
     * @see Zend_Auth_Adapter_Interface::authenticate()
     */
    public function authenticate()
    {
        // TODO Auto-generated method stub

    }

}