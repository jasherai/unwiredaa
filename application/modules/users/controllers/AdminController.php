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
	}

	public function editAction()
	{
		$this->_edit();
	}

	public function deleteAction()
	{
		// @todo user deletion
	}
}