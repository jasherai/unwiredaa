<?php
class Users_AdminController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$this->_index();
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
		// @todo user deletion
	}
}