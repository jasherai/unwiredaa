<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Admin info form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Form_Profile extends Users_Form_Admin
{
	public function init()
	{
		parent::init();

		$this->removeElement('groups_assigned');
		$this->removeElement('available_roles');
		$this->removeElement('access');

		$this->getElement('password')->setRequired(false);
		$this->getElement('cfmpassword')->setRequired(false);
	}

	public function populate($values)
	{
		$values['groups_assigned'] = array();
		parent::populate($values);
	}

	public function isValid($data)
	{
		$valid = parent::isValid($data);

		if (!$valid) {
			return false;
		}

		$password = $this->getElement('password')->getValue();
		$cfmpassword = $this->getElement('cfmpassword')->getValue();

		if ((!empty($password) || !empty($cfmpassword)) &&
			 $password != $cfmpassword) {

			 $this->getElement('cfmpassword')->addError('users_admin_edit_form_error_password_match');
			 $this->markAsError();
			 return false;
		}
		return true;
	}
}