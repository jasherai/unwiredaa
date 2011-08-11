<?php

class Groups_IndexController extends Unwired_Controller_Crud
{
	public function init()
	{
		$this->_defaultMapper = new Groups_Model_Mapper_Group();
	}

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
	}
}