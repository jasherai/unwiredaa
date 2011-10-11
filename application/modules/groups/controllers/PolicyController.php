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

class Groups_PolicyController extends Unwired_Controller_Crud
{

	public function indexAction()
	{
		$this->_index();

	}

	/*public function viewAction()
	{
		$id = (int) $this->getRequest()->getParam('id', 0);

		if (!$id) {
			$this->view->uiMessage('groups_group_not_found', 'error');
			return;
		}

		$service = new Groups_Service_Group();

		$group = $service->findGroup($id, true, false);

		$this->view->group = $group;
	}*/

	/*protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
	{
		$service = new Groups_Service_Group();

		$rootGroup = $service->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;

		parent::_add($mapper, $entity, $form);
	}*/

	public function addAction()
	{
		$this->_add();
		$this->_helper->viewRenderer->setScriptAction('edit');
	}

	public function editAction()
	{
		$this->_edit();
	}

	public function deleteAction()
	{
		$this->_delete();
	}
}