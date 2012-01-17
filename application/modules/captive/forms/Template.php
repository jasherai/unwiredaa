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
 * Splash page form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Captive_Form_Template extends Unwired_Form
{
	public function init()
	{
		parent::init();

        $mapperLanguages = new Captive_Model_Mapper_Language();

		$this->addElement('text', 'name', array('label' => 'captive_template_edit_form_name',
												'required' => true,
												'validators' => array('len' => array('validator' => 'StringLength',
																				     'options' => array('min' => 2)),
																	  'db' => array('validator' => 'Db_NoRecordExists',
																				    'options' => array(
		                                                                                        'adapter' => $mapperLanguages->getDbTable()->getAdapter(),
																								'table' => 'template',
																						        'field' => 'name'
																					)))));

		/*$this->addElement('file', 'filename', array('label' => 'captive_template_edit_form_filename',
												'required' => false));*/

		$this->addElement('multiselect', 'language_ids', array('label' => 'captive_template_edit_form_settings_languages',
															  'belongsTo' => 'settings',
															  'class' => 'span-8',
															  'required' => true));

		/**
		 * Languages
		 */
		$mapperLanguages = new Captive_Model_Mapper_Language();

		$languages = $mapperLanguages->fetchAll();

		$elementLanguage = $this->getElement('language_ids');

		foreach ($languages as $language) {
		    $elementLanguage->addMultiOption($language->getLanguageId(), $language->getName());
		}

		$languages = null;
		$mapperLanguages = null;

		/**
		 * Auth methods
		 */
		$this->addElement('multiselect', 'auth', array('label' => 'captive_template_edit_form_settings_auth',
		                                               'belongsTo' => 'settings',
		                                               'class' => 'span-8',
													   'required' => true));


		$this->getElement('auth')->addMultiOptions(array('guest' => 'captive_template_edit_form_settings_auth_guest',
		                                                 'autologin' => 'captive_template_edit_form_settings_auth_autologin',
		                                                 'login' => 'captive_template_edit_form_settings_auth_login'));

		$this->addElement('multiCheckbox', 'groups_assigned', array('label' => 'captive_template_edit_form_group',
											  	 			  'required' => true,
															  'separator' => '',
															  'registerInArrayValidator' => false));

		$this->getElement('groups_assigned')->addErrorMessage('captive_template_edit_form_error_group');


		$this->addElement('textarea', 'analytics', array('label' => 'captive_template_edit_form_settings_analytics',
		                                               'belongsTo' => 'settings',
		                                               'class' => 'span-8',
													   'required' => false));
		/*
		 * Buttons
		 */
		$this->addElement('submit', 'form_element_submit', array('label' => 'captive_template_edit_form_save',
	 														 	 'tabindex' => 20,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
        $this->addElement('submit', 'form_element_submit_edit', array('label' => 'captive_template_edit_form_save_and_edit_content',
	 														 	 'tabindex' => 21,
																 'class'	=> 'button',
															 	 'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button green')),
																						)));
		$this->addElement('href', 'form_element_cancel', array('label' => 'captive_template_edit_form_cancel',
	 														   'tabindex' => 22,
															   'href' => (isset($this->getView()->refererUrl)) ?
																					$this->getView()->refererUrl : null,
															   'data' => array(
																				'params' => array('module' => 'captive',
																					  			  'controller' => 'template',
																					  			  'action' => 'index'),
																				'route' => 'default',
																				'reset' => true
																			),
															   'decorators' => array('ViewHelper',
																				 		array(array('span' => 'HtmlTag'),
						            				   									 	   array ('tag' => 'span',
																		   				 		 	  'class' => 'button blue')),
																						)));

	    $this->addDisplayGroup(array('name', 'filename', 'language_ids', 'auth', 'analytics'), 'templatedata');

		$this->addDisplayGroup(array('groups_assigned'), 'groupinfo');

		$this->addDisplayGroup(array('form_element_submit', 'form_element_submit_edit', 'form_element_cancel'),
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
	    $entity = $this->getEntity();

	    $exclude = '';

	    if ($entity && $entity->getTemplateId()) {
	        $exclude = 'template_id != \'' . $entity->getTemplateId() . '\'';

    	    $this->getElement('name')->getValidator('Db_NoRecordExists')
                              ->setExclude($exclude);
	    }

	    return parent::isValid($data);
	}
}