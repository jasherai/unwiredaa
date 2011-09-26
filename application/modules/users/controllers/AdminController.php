<?php
class Users_AdminController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$groupService = new Groups_Service_Group();

		$adminMapper = new Users_Model_Mapper_Admin();

		$filter = $this->_getFilters();
		$user = $this->getCurrentUser();

		$lowerOnly = true;
		if ($this->getAcl()->isAllowed($user, $adminMapper->getEmptyModel(), 'special')) {
			$lowerOnly = false;
		}

		$groupService->prepareMapperListingByAdmin($adminMapper, null, $lowerOnly, $filter);

		$this->_index($adminMapper);
	}

	protected function _getFilters()
	{
		$filter = array();

		$filter['email'] = $this->getRequest()->getParam('email', null);
		$filter['firstname'] = strtoupper($this->getRequest()->getParam('firstname', null));
		$filter['lastname'] = $this->getRequest()->getParam('lastname', null);
		$filter['city'] = $this->getRequest()->getParam('city', null);
		$filter['country'] = $this->getRequest()->getParam('country', null);

		$this->view->filter = $filter;

		foreach ($filter as $key => $value) {
			if (null == $value || empty($value)) {
				unset($filter[$key]);
				continue;
			}

			$filter[$key] = '%' . preg_replace('/[^a-z0-9\s\-\.]+/iu', '', $value) . '%';
		}

		return $filter;
	}

	protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
	{
		if (null !== $entity && $entity->getUserId() == $this->getCurrentUser()->getUserId()) {
			$this->_helper->redirector->gotoRouteAndExit(array('module' => 'users',
															   'controller' => 'profile',
															   'action' => 'index'),
														'default',
														true);
		}

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