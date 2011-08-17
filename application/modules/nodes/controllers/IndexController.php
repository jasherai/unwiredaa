<?php
class Nodes_IndexController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$this->_index(new Nodes_Model_Mapper_Node());
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
		$this->_add(new Nodes_Model_Mapper_Node());
		$this->_helper->viewRenderer->setScriptAction('edit');
	}

	public function editAction()
	{
		$this->_edit(new Nodes_Model_Mapper_Node());
	}

	public function deleteAction()
	{
		$this->_delete(new Nodes_Model_Mapper_Node());
		// @todo node deletion
	}
}