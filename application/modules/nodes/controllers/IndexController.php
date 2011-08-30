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

		$filter['name'] = $this->getRequest()->getParam('name', null);
		$filter['mac'] = $this->getRequest()->getParam('mac', null);
		$filter['ipaddress'] = $this->getRequest()->getParam('ipaddress', null);

		$this->view->filter = $filter;

		foreach ($filter as $key => $value) {
			if (null == $value || empty($value)) {
				unset($filter[$key]);
				continue;
			}

			$filter[$key] = '%' . preg_replace('/[^a-z0-9\s\-\:\.]+/iu', '', $value) . '%';
		}

		$groupService->prepareMapperListingByAdmin($this->_getDefaultMapper(),
													null,
													false,
													$filter);
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

		if (null !== $entity) {
			$entity->setToUpdate(true);
		}

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