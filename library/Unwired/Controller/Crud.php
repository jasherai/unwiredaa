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
		if (null === $this->_currentUser) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}
	}

	protected function _index(Unwired_Model_Mapper $mapper = null)
	{
		if (null === $mapper) {
			$mapper = $this->_getDefaultMapper();
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

		if (null === $form) {
			$formClass = str_replace('Model', 'Form', get_class($entity));

			$form = new $formClass;
		}

		$form->populate($entity->toArray());

		$this->view->entity = $entity;
		$this->view->form = $form;

		if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		try {
			$entity->fromArray($form->getValues());

			$mapper->save($entity);

			$this->view->uiMessage('information_saved_successfully', 'success');

			$this->_gotoIndex();
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

		$this->_add($mapper, $entity, $form);
	}

	protected function _delete(Unwired_Model_Mapper $mapper = null)
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

		try {
			$mapper->delete($entity);
			$this->view->uiMessage('information_deleted', 'success');
		} catch (Unwired_Exception $e) {
			$this->view->uiMessage('information_not_deleted', 'error');
		}
		$this->_gotoIndex();
	}

	protected function _gotoIndex()
	{
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
		}

		return $this->_defaultMapper;
	}

}