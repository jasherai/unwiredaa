<?php
class Users_AdminController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$groupService = new Groups_Service_Group();

		$adminMapper = new Users_Model_Mapper_Admin();

		$groupService->prepareMapperListingByAdmin($adminMapper);

		$this->_index($adminMapper);
	}

	protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
	{
		$groupService = new Groups_Service_Group();

		$rootGroup = $groupService->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;

		parent::_add($mapper, $entity, $form);
	}

	public function addAction()
	{
		$this->_add();
		$this->_helper->viewRenderer->setScriptAction('edit');
	}

	public function editAction()
	{
		$form = new Users_Form_Admin();

		$form->getElement('password')->setRequired(false);
		$form->getElement('cfmpassword')->setRequired(false);
		$this->_edit(null, $form);
	}

	public function deleteAction()
	{
		$this->_delete();
		// @todo user deletion
	}
}