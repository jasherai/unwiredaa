<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Policy group form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Groups_Form_Policy extends Unwired_Form
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
																			   'class' => 'formelement span-18'))
										));

		$this->addElement('text', 'name', array('label' => 'groups_policy_edit_form_name',
												'required' => true,
												'validators' => array('len' => array('validator' => 'StringLength',
																				     'options' => array('min' => 2)))));

		$this->addElement('text', 'priority', array('label' => 'groups_policy_edit_form_priority',
												'required' => true,
												'validators' => array('Int',
																	  'Between' => array('validator' => 'Between',
																				     	'options' => array('min' => 0,
																										   'max' => 10000)))));

		$this->getElement('priority')->addErrorMessage('groups_policy_edit_form_message_priority');

		$this->addElement('hidden', 'rules_reply', array('label' => 'groups_policy_edit_form_rules_reply',
														 'required' => false,
														 'isArray' => true));
		$this->addElement('hidden', 'rules_check', array('label' => 'groups_policy_edit_form_rules_check',
														 'required' => false,
														 'isArray' => true));

		$this->addElement('submit', 'form_element_submit', array('label' => 'groups_policy_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('href', 'form_element_cancel', array('label' => 'groups_policy_edit_form_cancel',
	 														 	 'tabindex' => 20,
																 'data' => array(
																				'params' => array('module' => 'groups',
																					  			  'controller' => 'policy',
																					  			  'action' => 'index'),
																				'route' => 'default',
																				'reset' => true
																			),
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button blue')),
																						)));

		$this->addDisplayGroup(array('form_element_submit', 'form_element_cancel'),
							   'formbuttons');
	    $this->setDisplayGroupDecorators(array('FormElements',
		   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
	    														  'options' => array ('tag' => 'div',
													 	     						  'class' => 'buttons span-18'))));
	}
}