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
class Users_Form_Admin extends Unwired_Form
{
	public function init()
	{
		parent::init();

		$this->addElement('text', 'firstname', array('label' => 'users_admin_edit_form_firstname',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'lastname', array('label' => 'users_admin_edit_form_lastname',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'email', array('label' => 'users_admin_edit_form_email',
													'required' => true,
													'validators' => array('len' => array('validator' => 'EmailAddress'))));

		$this->addElement('text', 'phone', array('label' => 'users_admin_edit_form_phone',
													'required' => true,
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^\+[0-9]+[0-9\s]+[0-9]+$/')))));
		$this->addElement('text', 'address', array('label' => 'users_admin_edit_form_address',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 5)))));
		$this->addElement('text', 'city', array('label' => 'users_admin_edit_form_city',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 3)))));
		$this->addElement('text', 'zip', array('label' => 'users_admin_edit_form_zip',
													'required' => true,
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^[a-z0-9]+[a-z0-9\s]+$/i')))));

		$this->addElement('CountrySelect', 'country', array('label' => 'users_admin_edit_form_country',
															'required' => true,
															'class' => 'span-5'));

		$this->addElement('multiCheckbox', 'group_ids', array('label' => 'users_admin_edit_form_group',
											  	 			  'required' => true));

		$this->addElement('password', 'password', array('label' => 'users_admin_edit_form_password',
														'required' => true,
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 6)))));

		$this->addElement('password', 'cfmpassword', array('label' => 'users_admin_edit_form_cfmpassword',
														'required' => true,
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 6)))));

		$this->addElement('submit', 'form_element_submit', array('label' => 'users_admin_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('button', 'form_element_cancel', array('label' => 'users_admin_edit_form_cancel',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
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
									 'group_ids'),
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
		if (count($values['group_ids'])) {
			foreach ($values['group_ids'] as $id) {
				$this->getElement('group_ids')->addMultiOption($id, $id);
			}
		}
		parent::populate($values);
	}
}