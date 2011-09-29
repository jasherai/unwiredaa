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

class Users_ProfileController extends Unwired_Controller_Crud
{

	protected $_defaultMapper = 'Users_Model_Mapper_Admin';

	public function preDispatch()
	{
		if (null === $this->_currentUser || !$this->getAcl()->hasRole($this->_currentUser)) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}
	}

	public function indexAction()
	{
		$this->_add(null, $this->_currentUser, new Users_Form_Profile());
	}

}