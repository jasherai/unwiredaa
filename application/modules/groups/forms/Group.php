<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * System group form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Groups_Form_Group extends Zend_Form
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

		$this->addElement('text', 'name', array('label' => 'groups_group_edit_form_name',
												'required' => true,
												'validators' => array('len' => array('validator' => 'StringLength',
																				     'options' => array('min' => 2)))));

		$this->addElement('text', 'parent_id', array('label' => 'groups_group_edit_form_parent',
													 'required' => true,
													 'validators' => array('Int')));

		$this->addElement('select', 'role_id', array('label' => 'groups_group_edit_form_role',
													'required' => true));

		/**
		 * Add roles as options to select
		 */
		$roleMapper = new Groups_Model_Mapper_Role();

		$roles = $roleMapper->fetchAll();

		foreach ($roles as $role) {
			$this->getElement('role_id')->addMultiOption($role->getRoleId(), $role->getName());
		}

		$this->addElement('submit', 'form_element_submit', array('label' => 'groups_group_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('button', 'form_element_cancel', array('label' => 'groups_group_edit_form_cancel',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
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
													 	     						  'class' => 'formbuttons'))));
	}
}