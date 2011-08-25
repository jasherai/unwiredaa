<?php

class Groups_RoleController extends Unwired_Controller_Crud
{
	protected $_defaultMapper = 'Groups_Model_Mapper_Role';

	public function indexAction()
	{
		$service = new Groups_Service_Role();

		$rootRole = $service->getRoleTreeByAdmin();

		$this->view->rootRole = $rootRole;

	}

	public function viewAction()
	{
		$id = (int) $this->getRequest()->getParam('id', 0);

		if (!$id) {
			$this->view->uiMessage('groups_role_view_group_not_found', 'error');
			return;
		}

		$service = new Groups_Service_Role();

		$role = $service->findNode($id, true, false);

		$this->view->role = $role;
	}

	protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
	{
		$service = new Groups_Service_Role();

		$rootRole = $service->getRoleTreeByAdmin();

		$this->view->rootRole = $rootRole;

		parent::_add($mapper, $entity, $form);
	}

	public function addAction()
	{
		$this->_add();
		$this->_helper->viewRenderer->setScriptAction('edit');
	}

	public function editAction()
	{
		/*if ($this->getRequest()->isPost()) {
			Zend_Debug::dump($this->getRequest()->getPost()); die();
		}*/
		$this->_edit();
	}

	public function deleteAction()
	{
		$this->_delete();
	}
}