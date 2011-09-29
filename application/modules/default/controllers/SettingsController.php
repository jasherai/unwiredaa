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

class Default_SettingsController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$this->_index();
	}

	public function editAction()
	{
		$this->_edit();
	}
}