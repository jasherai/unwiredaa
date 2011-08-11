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
class Users_Form_Admin extends Zend_Form
{
	public function init()
	{
		parent::init();

		$this->setDecorators(array('FormElements', 'Form'));

		$this->setElementDecorators(array(
										'element' => array('decorator' => 'ViewHelper'),
							        	'label' => array('decorator' => 'Label',
							            				 'options' => array('optionalSuffix' => ':',
							                								'requiredSuffix' => ' * :',
																			'placement' => 'prepend')
														),
										'errors' => 'errors',
										'htmltag' => array('decorator' => 'HtmlTag',
							            				   'options' => array ('tag' => 'div',
																			   'class' => 'formelement'))
										));

		$this->addElement('text', 'firstname', array('label' => 'form_element_label_firstname',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'lastname', array('label' => 'form_element_label_lastname',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));
		$this->addElement('text', 'email', array('label' => 'form_element_label_email',
													'required' => true,
													'validators' => array('len' => array('validator' => 'EmailAddress'))));

		$this->addElement('text', 'phone', array('label' => 'form_element_label_phone',
													'required' => true,
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^\+[0-9]+[0-9\s]+[0-9]+$/')))));
		$this->addElement('text', 'address', array('label' => 'form_element_label_address',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 5)))));
		$this->addElement('text', 'city', array('label' => 'form_element_label_city',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 3)))));
		$this->addElement('text', 'zip', array('label' => 'form_element_label_zip',
													'required' => true,
													'validators' => array('len' => array('validator' => 'Regex',
																					     'options' => array('pattern' => '/^[a-z0-9]+[a-z0-9\s]+$/i')))));

		/**
		 * @todo Add country, groups and check validation/messages
		 */

		$this->addElement('password', 'password', array('label' => 'form_element_label_password',
														'required' => true,
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 6)))));

		$this->addElement('submit', 'form_element_submit', array('label' => 'form_button_label_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button')),
																						)));
		$this->addElement('button', 'form_element_cancel', array('label' => 'form_button_label_cancel',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button')),
																						)));
		$this->addDisplayGroup(array('form_element_submit', 'form_element_cancel'),
							   'formbuttons');
	    $this->setDisplayGroupDecorators(array('FormElements',
		   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
	    														  'options' => array ('tag' => 'div',
													 	     						  'class' => 'formbuttons'))));
	}
}