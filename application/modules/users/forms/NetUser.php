<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Network user form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Form_NetUser extends Unwired_Form
{
	public function init()
	{
		parent::init();

		$this->addElement('text', 'username', array('label' => 'users_netuser_edit_form_username',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'firstname', array('label' => 'users_netuser_edit_form_firstname',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'lastname', array('label' => 'users_netuser_edit_form_lastname',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'email', array('label' => 'users_netuser_edit_form_email',
													'required' => true,
													'validators' => array('len' => array('validator' => 'EmailAddress'))));

		$this->addElement('text', 'phone', array('label' => 'users_netuser_edit_form_phone',
													'required' => true,
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^\+[0-9]+[0-9\s]+[0-9]+$/')))));
		$this->addElement('text', 'address', array('label' => 'users_netuser_edit_form_address',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 5)))));
		$this->addElement('text', 'city', array('label' => 'users_netuser_edit_form_city',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 3)))));
		$this->addElement('text', 'zip', array('label' => 'users_netuser_edit_form_zip',
													'required' => true,
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^[a-z0-9]+[a-z0-9\s]+$/i')))));
		$this->addElement('CountrySelect', 'country', array('label' => 'users_netuser_edit_form_country',
															'required' => true,
															'class' => 'span-5'));

		$this->addElement('hidden', 'group_id', array('label' => 'users_netuser_edit_form_group',
											  	 	  'required' => true,
													  'validators' => array('Int')));
		$this->getElement('group_id')->addErrorMessage('users_netuser_edit_form_error_group');

		$this->addElement('password', 'password', array('label' => 'users_netuser_edit_form_password',
														'required' => true,
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 6)))));

		$this->addElement('password', 'cfmpassword', array('label' => 'users_netuser_edit_form_cfmpassword',
														'required' => true,
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 6)))));

		$this->addElement('text', 'mac', array('label' => 'users_netuser_edit_form_mac',
												'required' => true,
												'validators' => array('mac')));

		$this->addElement('multiselect', 'policy_ids', array('label' => 'users_netuser_edit_form_policy',
															 'required' => true));

		/**
		 * Add policy groups as options
		 */
		$policyMapper = new Groups_Model_Mapper_Policy();

		$policies = $policyMapper->fetchAll();

		foreach ($policies as $policy) {
			$this->getElement('policy_ids')->addMultiOption($policy->getPolicyId(), $policy->getName());
		}

		$this->addElement('submit', 'form_element_submit', array('label' => 'users_netuser_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('href', 'form_element_cancel', array('label' => 'users_netuser_edit_form_cancel',
	 														 	 'tabindex' => 20,
																 'data' => array(
																				'params' => array('module' => 'users',
																					  			  'controller' => 'net-user',
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

		$this->addDisplayGroup(array('username',
									 'password',
									 'cfmpassword',
									 'group_id',
									 'policy_ids',
									 'mac'),
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

			 $this->getElement('cfmpassword')->addError('users_netuser_edit_form_error_password_match');
			 $this->markAsError();
			 return false;
		}
		return true;
	}
}