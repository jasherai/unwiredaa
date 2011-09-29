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
class Users_Form_Admin extends Unwired_Form
{
	public function init()
	{
		parent::init();

		$this->addElement('text', 'firstname', array('label' => 'users_admin_edit_form_firstname',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'lastname', array('label' => 'users_admin_edit_form_lastname',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'email', array('label' => 'users_admin_edit_form_email',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'EmailAddress'))));

		$this->addElement('text', 'phone', array('label' => 'users_admin_edit_form_phone',
													'required' => false,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^\+[0-9]+[0-9\s]+[0-9]+$/')))));
		$this->addElement('text', 'address', array('label' => 'users_admin_edit_form_address',
													'required' => false,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 5)))));
		$this->addElement('text', 'city', array('label' => 'users_admin_edit_form_city',
													'required' => false,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 3)))));
		$this->addElement('text', 'zip', array('label' => 'users_admin_edit_form_zip',
													'required' => false,
													'class' => 'span-5',
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^[a-z0-9]+[a-z0-9\s]+$/i')))));

		$this->addElement('CountrySelect', 'country', array('label' => 'users_admin_edit_form_country',
															'required' => true,
															'class' => 'span-5'));

		$this->addElement('multiCheckbox', 'groups_assigned', array('label' => 'users_admin_edit_form_group',
											  	 			  'required' => true,
															  'separator' => '',
															  'registerInArrayValidator' => false));
		$this->getElement('groups_assigned')->addErrorMessage('users_admin_edit_form_error_group');

		$this->addElement('select', 'available_roles', array('label' => 'users_admin_edit_form_group_role',
											  	 			  'required' => false,
															  'class' => 'span-5',
															  'registerInArrayValidator' => false));

		/**
		 * @todo Move the acl stuff to a service and check for different access levels per category
		 */
		$service = new Groups_Service_Role();

		$roles = $service->fetchTree();

		$acl = Zend_Registry::get('acl');
		//$acl = new Zend_Acl();
		$admin = Zend_Auth::getInstance()->getIdentity();

		$adminRoles = array_unique($admin->getGroupsAssigned());

		$iterator = new RecursiveIteratorIterator($roles, RecursiveIteratorIterator::SELF_FIRST);

		foreach ($iterator as $role) {
			foreach ($adminRoles as $parentRoleId) {
				if ($acl->isAllowed($admin, null, 'super') || $acl->inheritsRole($role, $parentRoleId)
					|| ($role->getRoleId() == $parentRoleId && $acl->isAllowed($admin, $admin->getResourceId(), 'special'))) {
					$this->getElement('available_roles')->addMultiOption($role->getRoleId(), $role->getName());
					break;
				}
			}
		}

		$mapper = null;

		$this->addElement('password', 'password', array('label' => 'users_admin_edit_form_password',
														'required' => true,
														'class' => 'span-5',
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 8)))));

		$this->addElement('password', 'cfmpassword', array('label' => 'users_admin_edit_form_cfmpassword',
														'required' => true,
														'class' => 'span-5',
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 8)))));

		$this->addElement('submit', 'form_element_submit', array('label' => 'users_admin_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('href', 'form_element_cancel', array('label' => 'users_admin_edit_form_cancel',
	 														 	 'tabindex' => 20,
																 'href' => (isset($this->getView()->refererUrl)) ?
																					$this->getView()->refererUrl : null,
																 'data' => array(
																				'params' => array('module' => 'users',
																					  			  'controller' => 'admin',
																					  			  'action' => 'index'),
																				'route' => 'default',
																				'reset' => true
																			),
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button blue')),
																						)));
		$this->addDisplayGroup(array('firstname',
									 'lastname',
									 'email',
									 'phone',
									 'address',
									 'city',
									 'zip',
									 'country'),
				 			   'personal');

		$this->addDisplayGroup(array('password',
									 'cfmpassword',
									 'groups_assigned',
									 'available_roles'),
				 			   'access');

		$this->addDisplayGroup(array('form_element_submit', 'form_element_cancel'),
							   'formbuttons');

	    $this->setDisplayGroupDecorators(array('FormElements',
		   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
	    														  'options' => array ('tag' => 'div',
													 	     						  'class' => 'span-9'))));
	    $this->getDisplayGroup('formbuttons')
	    				 ->setDecorators(array('FormElements',
		   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
	    														  'options' => array ('tag' => 'div',
													 	     						  'class' => 'buttons span-18'))));
	}

	public function populate(array $values)
	{
		if (count($values['groups_assigned'])) {
			foreach ($values['groups_assigned'] as $key => $value) {
				$this->getElement('groups_assigned')->addMultiOption($key, $value);
			}
		}
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