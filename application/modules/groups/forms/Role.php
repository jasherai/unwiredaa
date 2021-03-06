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
 * Role form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Groups_Form_Role extends Unwired_Form
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
																			   'class' => 'formelement span-19 last'))
										));

		$this->addElement('text', 'name', array('label' => 'groups_role_edit_form_name',
												'required' => true,
												'validators' => array('len' => array('validator' => 'StringLength',
																				     'options' => array('min' => 2)))));

		$this->addElement('hidden', 'parent_id', array('label' => 'groups_role_edit_form_parent',
												'required' => true,
												'validators' => array('Int')));




		$acl = Zend_Registry::get('acl');


		$resources = array();
		foreach ($acl->getResources() as $resource) {
			if (preg_match('/\d+/', $resource)) {
				continue;
			}

			$resources[] = $resource;

			$multiOptions = array('view' => 'resource_' . $resource . '_view',
								  'add'	 => 'resource_' . $resource . '_add',
								  'edit' => 'resource_' . $resource . '_edit',
								  'delete' => 'resource_' . $resource . '_delete',
								  'special' => 'resource_' . $resource . '_special');

			foreach ($multiOptions as $idx => $option) {
				if (!$acl->isAllowed(Zend_Auth::getInstance()->getIdentity(), $resource, $idx)) {
					unset($multiOptions[$idx]);
				}
			}

			$this->addElement('multiCheckbox', $resource, array('label' => 'resource_' . $resource,
															    'required' => false,
															    'separator' => '',
																'labelClass' => 'multicheckbox',
															    'multiOptions' => $multiOptions));
			$this->getElement($resource)->setBelongsTo('permissions');
		}


		//Zend_Debug::dump($resources); die();

		$this->addElement('submit', 'form_element_submit', array('label' => 'groups_role_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('href', 'form_element_cancel', array('label' => 'groups_role_edit_form_cancel',
	 														 	 'tabindex' => 20,
																 'href' => (isset($this->getView()->refererUrl)) ?
																					$this->getView()->refererUrl : null,
																 'data' => array(
																				'params' => array('module' => 'groups',
																					  			  'controller' => 'role',
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
													 	     						  'class' => 'buttons'))));
	}
}