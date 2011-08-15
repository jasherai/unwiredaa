<?php
class Nodes_IndexController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$this->_index(new Nodes_Model_Mapper_Node());
	}

	public function addAction()
	{
		$this->_add(new Nodes_Model_Mapper_Node());
		$this->_helper->viewRenderer->setScriptAction('edit');
	}

	public function editAction()
	{
		$this->_edit(new Nodes_Model_Mapper_Node());
	}

	public function deleteAction()
	{
		$this->_delete(new Nodes_Model_Mapper_Node());
		// @todo node deletion
	}
}