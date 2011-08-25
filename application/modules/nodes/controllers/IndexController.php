<?php
class Nodes_IndexController extends Unwired_Controller_Crud
{
	public function init()
	{
		parent::init();
		$this->_defaultMapper = new Nodes_Model_Mapper_Node();
	}

	public function indexAction()
	{
		$groupService = new Groups_Service_Group();

		$groupService->prepareMapperListingByAdmin($this->_getDefaultMapper());
		$this->_index();
	}

	protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
	{
		$groupService = new Groups_Service_Group();

		$rootGroup = $groupService->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;

		$this->_setAutoRedirect(false);
		$result = parent::_add($mapper, $entity, $form);
		if ($result) {
			$nodeService = new Nodes_Service_Node();
			if ($nodeService->writeUci($this->view->entity)) {
				$this->_setAutoRedirect(true)
					 ->_gotoIndex();
			}

			$this->view->uiMessage('nodes_index_edit_cannot_write_uci', 'warning');
		}

		return $result;
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
		// @todo node deletion
	}
}