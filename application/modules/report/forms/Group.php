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
 * Report group form
 * @author G. Sokolov <joro@web-teh.net>
 */
class Report_Form_Group extends Unwired_Form {
	public function init() {
		parent::init ();

		$this->addElement ( 'text', 'title', array ('label' => 'report_group_title', 'required' => true, 'class' => 'span-5', 'validators' => array ('len' => array ('validator' => 'StringLength', 'options' => array ('min' => 2 ) ) ) ) );
		$this->addElement ( 'text', 'description', array ('label' => 'report_group_description', 'required' => true, 'class' => 'span-5', 'validators' => array ('len' => array ('validator' => 'StringLength', 'options' => array ('min' => 2 ) ) ) ) );

		$this->addElement ( 'text', 'date_from', array ('label' => 'report_group_date_from', 'required' => true, 'class' => 'span-5', 'validators' => array ('len' => array ('validator' => 'Date' ) ) ) );

		$this->addElement ( 'text', 'date_to', array ('label' => 'report_group_date_to', 'required' => true, 'class' => 'span-5', 'validators' => array ('len' => array ('validator' => 'Date' ) ) ) );


		$this->addElement('select', 'report_type', array('label' => 'report_group_report_type',
				'required' => true,
				'class' => 'span-5',
				'multiOptions' => array(
						'0' => 'report_group_report_type_manual',
						'1' => 'report_group_report_type_interval',
						)
				));

		$this->addElement('select', 'report_interval', array('label' => 'report_group_report_interval',
				'required' => true,
				'class' => 'span-5',
				'multiOptions' => array(
						'1' => 'day',
						'2' => 'week',
						'3' => 'month',
						'4' => 'year',
				)
		));
		$this->addElement ( 'text', 'email', array ('label' => 'report_group_email', 'required' => false, 'class' => 'span-5', 'validators' => array ('len' => array ('validator' => 'StringLength', 'options' => array ('min' => 2 ) ) ) ) );

		$decorators = $this->getElement('email')->getDecorators();

		$firstDecorators = array_slice($decorators, 0, 2, true);
		$firstDecorators[] = 'Description';

		$decorators = array_merge($firstDecorators, $decorators);

		$this->getElement('email')->setDecorators($decorators)
		                          ->setDescription('report_group_email_description');

		$this->addElement('multiCheckbox', 'groups_assigned', array('label' => 'report_edit_form_group',
											  	 			  'required' => true,
															  'separator' => '',
															  'registerInArrayValidator' => false));



		$this->getElement ( 'groups_assigned' )->addErrorMessage ( 'report_edit_form_error_group' );
		$this->addElement ( 'hidden', 'available_roles', array ('label' => 'report_edit_form_group_role', 'required' => false, 'class' => 'span-5', 'registerInArrayValidator' => false ) );
		//$this->addElement ( 'select', 'available_roles', array ('label' => 'report_edit_form_group_role', 'required' => false, 'class' => 'span-5', 'registerInArrayValidator' => false ) );

		$mapper = null;

		$this->addElement ( 'submit', 'form_element_submit', array ('label' => 'report_group_edit_form_save', 'tabindex' => 20, 'class' => 'button', 'decorators' => array ('ViewHelper', array (array ('span' => 'HtmlTag' ), array ('tag' => 'span', 'class' => 'button green' ) ) ) ) );
		$this->addElement ( 'href', 'form_element_cancel', array ('label' => 'report_group_edit_form_cancel', 'tabindex' => 20, 'href' => (isset ( $this->getView ()->refererUrl )) ? $this->getView ()->refererUrl : null, 'data' => array ('params' => array ('module' => 'report', 'controller' => 'group', 'action' => 'index' ), 'route' => 'default', 'reset' => true ), 'decorators' => array ('ViewHelper', array (array ('span' => 'HtmlTag' ), array ('tag' => 'span', 'class' => 'button blue' ) ) ) ) );

		$this->addDisplayGroup ( array ('title', 'description', 'date_from', 'date_to', 'report_type', 'report_interval' ), 'personal' );
		//$this->addDisplayGroup ( array ('title', 'description', 'date_from', 'date_to' ), 'personal' );

		$this->addDisplayGroup(array('email',
				'groups_assigned',
				'available_roles'),
				'access');

		$this->addDisplayGroup ( array ('form_element_submit', 'form_element_cancel' ), 'formbuttons' );

		$this->setDisplayGroupDecorators ( array ('FormElements', 'HtmlTag' => array ('decorator' => 'HtmlTag', 'options' => array ('tag' => 'div', 'class' => 'span-9' ) ) ) );
		$this->getDisplayGroup ( 'formbuttons' )->setDecorators ( array ('FormElements', 'HtmlTag' => array ('decorator' => 'HtmlTag', 'options' => array ('tag' => 'div', 'class' => 'buttons span-18' ) ) ) );
	}

	public function populate(array $values) {
		if (isset ( $values ['groups_assigned'] ) && count ( $values ['groups_assigned'] )) {
			foreach ( $values ['groups_assigned'] as $key => $value ) {
				$this->getElement ( 'groups_assigned' )->addMultiOption ( $key, $value );
			}
		}
		parent::populate ( $values );
	}

	public function isValid($data) {
		$valid = parent::isValid ( $data );
		if (! $valid) {

			return false;
		}

		return true;
	}

	public function getValues($suppressArrayNotation = false) {
		$values = parent::getValues ( $suppressArrayNotation );

		if (! isset ( $values ['groups_assigned'] ) && $values ['groups_assigned'] == null) {
			$values ['groups_assigned'] = array ();
		}

		return $values;
	}
}