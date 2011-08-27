<?php

class Default_SettingsController extends Unwired_Controller_Crud
{
	public function indexAction()
	{
		$this->_index();
	}

	public function editAction()
	{
		$this->_edit();
	}
}