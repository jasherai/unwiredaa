<?php
/**
* Unwired AA GUI
*
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
*
* Licensed under the terms of the Affero Gnu Public License version 3
* (AGPLv3 - http://www.gnu.org/licenses/agpl.html) or our proprietory
* license available at http://www.unwired.at/license.html
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
		unset($values['groups_assigned']);
		parent::populate($values);
	}

	public function getValues($suppressArrayNotation = false)
	{
		$values = parent::getValues($suppressArrayNotation);

		unset($values['groups_assigned']);

		return $values;
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