<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Login form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Form_Login extends Zend_Form
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

		$this->addElement('text', 'username', array('label' => 'form_element_label_username',
													'required' => true,
													'validators' => array('len' => array('validator' => 'StringLength',
																					     'options' => array('min' => 2)))));

		$this->addElement('password', 'password', array('label' => 'form_element_label_password',
														'required' => true,
														'validators' => array('len' => array('validator' => 'StringLength',
																					     	 'options' => array('min' => 6)))));

		$this->addElement('submit', 'form_element_submit', array('label' => 'form_button_label_login',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button')),
																						array(array('div' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'div',
																		   				 			  'class' => 'formbuttons')))));
	}
}