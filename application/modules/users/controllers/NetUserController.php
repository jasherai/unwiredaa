<?php
class Users_NetUserController extends Unwired_Controller_Crud
{
	public function init()
	{
		parent::init();

		$this->_actionsToReferer[] = 'enable';
		$this->_actionsToReferer[] = 'disable';
	}

	public function indexAction()
	{
		$groupService = new Groups_Service_Group();

		$userMapper = new Users_Model_Mapper_NetUser();

		$filter = $this->_getFilters();

		$groupService->prepareMapperListingByAdmin($userMapper, null, false, $filter);

		$this->_index($userMapper);
	}

	protected function _getFilters()
	{
		$filter = array();

		$filter['username'] = $this->getRequest()->getParam('username', null);
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
		$form = new Users_Form_NetUser();

		$form->getElement('password')->setRequired(false);
		$form->getElement('cfmpassword')->setRequired(false);
		$this->_edit(null, $form);
	}

	public function deleteAction()
	{
		$this->_delete();
		// @todo user deletion
	}

	public function enableAction()
	{
		$this->_toggleEnabled(true);
	}

	public function disableAction()
	{
		$this->_toggleEnabled(false);
	}

	protected function _toggleEnabled($on = true)
	{
		$id = (int) $this->getRequest()->getParam('id', 0);

		if (!$id) {
			$this->view->uiMessage('users_netuser_toggle_user_not_found', 'error');
			$this->_gotoIndex();
		}

		$mapper = $this->_getDefaultMapper();

		$user = $mapper->find($id);

		if (!$user) {
			$this->view->uiMessage('users_netuser_toggle_user_not_found', 'error');
			$this->_gotoIndex();
		}

		$policyIds = $user->getPolicyIds();

		if ($on) {
			$policyIds = array_diff($policyIds, array(3));
		} else if (!in_array(3, $policyIds)) {
			$policyIds[] = 3;
		}

		$user->setPolicyIds($policyIds);
		try {
			$mapper->save($user);
			if ($on) {
				$message = $this->view->translate('users_netuser_enable_success', $user->getUsername());
			} else {
				$message = $this->view->translate('users_netuser_disable_success', $user->getUsername());
			}
			$this->view->uiMessage($message, 'success');

		} catch (Exception $e) {
			if ($on) {
				$message = $this->view->translate('users_netuser_enable_failed', $user->getUsername());
			} else {
				$message = $this->view->translate('users_netuser_disable_failed', $user->getUsername());
			}
			$this->view->uiMessage($message, 'error');

		}

		$this->_gotoIndex();
	}
}