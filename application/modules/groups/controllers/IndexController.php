<?php

class Groups_IndexController extends Unwired_Controller_Crud
{
	public function init()
	{

	}

	public function indexAction()
	{
		$service = new Groups_Service_Group();

		$rootGroup = $service->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;

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
	}
}