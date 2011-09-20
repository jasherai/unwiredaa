<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Base class for a CRUD action controller
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Unwired_Controller_Crud extends Unwired_Controller_Action
{
	protected $_currentUser = null;

	protected $_defaultMapper = null;

	protected $_autoRedirect = true;

	protected $_referer = null;

	protected $_actionsToReferer = array('add', 'edit', 'delete');

	public function __construct(Zend_Controller_Request_Abstract $request,
								Zend_Controller_Response_Abstract $response,
								array $invokeArgs = array())
	{
		parent::__construct($request, $response, $invokeArgs);

		if (Zend_Auth::getInstance()->hasIdentity()) {
			$this->_currentUser = Zend_Auth::getInstance()->getIdentity();
		}
	}

	public function getCurrentUser()
	{
		return $this->_currentUser;
	}

	public function preDispatch()
	{
		if (null === $this->_currentUser || !$this->getAcl()->hasRole($this->_currentUser)) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

		if (!$this->getAcl()->isAllowed($this->_currentUser, $this->_getDefaultMapper()->getEmptyModel(), 'view')) {
			$this->view->uiMessage('access_not_allowed_view', 'error');
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

		if ($this->getInvokeArg('bootstrap')->hasResource('session')) {
			$session = $this->getInvokeArg('bootstrap')->getResource('session');

			if (null === $session->referer) {
				$session->referer = $this->getRequest()->getServer('HTTP_REFERER');
			}

			if (!in_array($this->getRequest()->getActionName(), $this->_actionsToReferer)) {
				$session->referer = null;
			}

			$this->_referer = $session->referer;
		}
	}

	public function postDispatch()
	{
		if (!isset($this->view->currentUser)) {
			$this->view->currentUser = $this->getCurrentUser();
		}
	}

	protected function _index(Unwired_Model_Mapper $mapper = null)
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
		}

		if (!$this->getAcl()->isAllowed($this->_currentUser, $mapper->getEmptyModel(), 'view')) {
			$this->view->uiMessage('access_not_allowed_view', 'error');
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

		$paginator = new Zend_Paginator($mapper);

		$pageNumber = max(1, (int) $this->getRequest()->getParam('page', 1));

		$paginator->setItemCountPerPage(20);

		$paginator->setCurrentPageNumber($pageNumber);

		$this->view->paginator = $paginator;
	}

	protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
		}

		if (null === $entity) {
			$entity = $mapper->getEmptyModel();
		}

		if (!$this->getAcl()->isAllowed($this->_currentUser, $entity, 'edit')) {
			$this->view->uiMessage('access_not_allowed_add', 'error');
			$this->_setAutoRedirect(true);
			$this->_gotoIndex();
		}

		if (null === $form) {
			$formClass = str_replace('Model', 'Form', get_class($entity));

			$form = new $formClass;
		}

		$form->populate($entity->toArray());

		$this->view->entity = $entity;
		$this->view->form = $form;

		if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
			return false;
		}

		try {
			$entity->fromArray($form->getValues());

			$mapper->save($entity);

			$this->view->uiMessage('information_saved_successfully', 'success');

			$this->_gotoIndex();

			return true;

		} catch (Exception $e) {
			$this->view->uiMessage('information_notsaved_error', 'error');

			if ($e instanceof Unwired_Exception) {
				$message = $e->getPrevious()->getMessage();
			} else {
				$message = $e->getMessage();
			}

			$this->getInvokeArg('bootstrap')
					  ->getResource('log')
					  	->err($message);
		}

		return false;
	}

	protected function _edit(Unwired_Model_Mapper $mapper = null,
							 Zend_Form $form = null)
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
		}

		$id = (int) $this->getRequest()->getParam('id');

		if (!$id) {
			$this->view->uiMessage('entity_not_found', 'error');
			$this->_gotoIndex();
		}

		$entity = $mapper->find($id);

		if (!$entity) {
			$this->view->uiMessage('entity_not_found', 'error');
			$this->_gotoIndex();
		}

		return $this->_add($mapper, $entity, $form);
	}

	protected function _delete(Unwired_Model_Mapper $mapper = null)
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
		}

		if (!$this->getAcl()->isAllowed($this->_currentUser, $mapper->getEmptyModel(), 'delete')) {
			$this->view->uiMessage('access_not_allowed_delete', 'error');
			$this->_setAutoRedirect(true);
			$this->_gotoIndex();
		}

		$id = (int) $this->getRequest()->getParam('id');

		if (!$id) {
			$this->view->uiMessage('entity_not_found', 'error');
			$this->_gotoIndex();
		}

		$entity = $mapper->find($id);

		if (!$entity) {
			$this->view->uiMessage('entity_not_found', 'error');
			$this->_gotoIndex();
		}

		try {
			$mapper->delete($entity);
			$this->view->uiMessage('information_deleted', 'success');
		} catch (Exception $e) {
			$this->view->uiMessage('information_not_deleted', 'error');
		}
		$this->_gotoIndex();
	}

	protected function _hasAutoRedirect()
	{
		return (bool) $this->_autoRedirect;
	}

	protected function _setAutoRedirect($flag = true)
	{
		$this->_autoRedirect = (bool) $flag;

		return $this;
	}

	protected function _gotoIndex()
	{
		if (!$this->_hasAutoRedirect()) {
			return;
		}

		if (null !== $this->_referer) {
			$this->_helper->redirector->gotoUrlAndExit($this->_referer);
		}

		$this->_helper->redirector->gotoRouteAndExit(array( 'module' => $this->getRequest()->getParam('module'),
															'controller' => $this->getRequest()->getParam('controller'),
															'action' => 'index'), 'default', true);
	}

	/**
	 * Get (or guess) the default mapper used by this controller
	 * @return Unwired_Model_Mapper
	 */
	protected function _getDefaultMapper()
	{
		if (null === $this->_defaultMapper) {
			preg_match('/^(.*)_([a-z0-0]+)Controller$/i', get_class($this), $match);

			$mapperClass = $match[1] . '_Model_Mapper_' . $match[2];
			$this->_defaultMapper = new $mapperClass;
		} else if (is_string($this->_defaultMapper)) {
			$this->_defaultMapper = new $this->_defaultMapper;
		}

		return $this->_defaultMapper;
	}

}