<?php
class Users_ProfileController extends Unwired_Controller_Crud
{

	protected $_defaultMapper = 'Users_Model_Mapper_Admin';

	public function preDispatch()
	{
		if (null === $this->_currentUser || !$this->getAcl()->hasRole($this->_currentUser)) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}
	}

	public function indexAction()
	{
		$this->_add(null, $this->_currentUser, new Users_Form_Profile());
	}

}