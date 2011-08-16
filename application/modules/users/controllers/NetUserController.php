<?php
class Users_NetUserController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$this->_index();
	}

	protected function _add()
	{
		$groupService = new Groups_Service_Group();

		$rootGroup = $groupService->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;
		parent::_add();
	}
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
		// @todo user deletion
	}
}