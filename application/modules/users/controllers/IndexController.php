<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Users/Index controller
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Users_IndexController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		$mapper = new Zend_Paginator(new Users_Model_AdminMapper());
	}

	public function loginAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

		$form = new Users_Form_Login();

		if (!$form->isValid($this->getRequest()->getPost())) {
			return;
		}

		$data = $form->getValues();

		$service = new Users_Model_AdminService();

		if (!$service->login($data['username'], $data['password'])) {
			echo $this->view->uiMessage('user_login_failed', 'error');
			return;
		}

		$this->view->uiMessage('user_login_success', 'success');

		$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
	}

}
