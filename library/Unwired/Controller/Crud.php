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

class Unwired_Controller_Crud extends Zend_Controller_Action
{
	protected $_currentUser = null;

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

	protected function _indexAction(Unwired_Model_Mapper $mapper)
	{
		$paginator = new Zend_Paginator($mapper);

		$pageNumber = max(1, (int) $this->getRequest()->getParam('page', 1));

		$paginator->setItemCountPerPage(20);

		$paginator->setCurrentPageNumber($pageNumber);

		$this->view->paginator = $paginator;
	}

	public function addAction()
	{

	}

	public function editAction()
	{

	}

	public function deleteAction()
	{

	}
}