<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * View helper to show login form or profile and logout links
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_View_Helper_LogInOut extends Zend_View_Helper_Abstract {

	/**
	 *
	 */
	public function logInOut() {
		$auth = Zend_Auth::getInstance();

		if (!$auth->hasIdentity()) {
			$loginForm = new Users_Form_Login();

			$loginForm->setAction($this->view->url(array('module' => 'users',
														 'controller' => 'index',
														 'action' => 'login'),
												   'default',
												   true));
			return $loginForm;
		}
		return null;
	}

	/**
	 * Sets the view field
	 * @param $view Zend_View_Interface
	 */
	public function setView(Zend_View_Interface $view) {
		$this->view = $view;
	}
}
