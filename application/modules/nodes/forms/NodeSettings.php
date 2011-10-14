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
 * Network node settings form
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Nodes_Form_NodeSettings extends Unwired_Form
{
	public function init()
	{
		parent::init();

		$this->setLegend('nodes_index_edit_form_legend_settings');

		$this->setDecorators(array('FormElements',
								   'fieldset' => array('decorator' => 'fieldset',
							           				   'options' => array ('class' => 'span-19 last')),
								   'Form'));

		$this->addElement('checkbox', 'dhcp', array('label' => 'nodes_index_edit_form_dhcp',
													'required' => true));

		$this->addElement('text', 'ipaddress', array('label' => 'nodes_index_edit_form_ipaddress',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('ip')));

		$this->addElement('text', 'netmask', array('label' => 'nodes_index_edit_form_netmask',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('ip')));

		$this->addElement('text', 'gateway', array('label' => 'nodes_index_edit_form_gateway',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('ip')));

		$this->addElement('text', 'dnsservers', array('label' => 'nodes_index_edit_form_dnsservers',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('dns')));


		$this->addElement('select', 'bandwidthup', array('label' => 'nodes_index_edit_form_bandwidthup',
													'required' => true,
													'class' => 'span-5'));

		$this->addElement('select', 'bandwidthdown', array('label' => 'nodes_index_edit_form_bandwidthdown',
													'required' => true,
													'class' => 'span-5'));

		/**
		 * @todo Network speeds
		 */
		for ($i = 128; $i <= 1024; $i = $i*2) {
			$this->getElement('bandwidthup')->addMultiOption($i, $i . ' kbit/s');
			$this->getElement('bandwidthdown')->addMultiOption($i, $i . ' kbit/s');
		}

		$this->getElement('bandwidthup')->addMultiOption(1536, '1.5 Mbit/s');
		$this->getElement('bandwidthdown')->addMultiOption(1536, '1.5 Mbit/s');

		for ($i = 2; $i <= 8; $i++) {
			$this->getElement('bandwidthup')->addMultiOption($i * 1024, $i . ' Mbit/s');
			$this->getElement('bandwidthdown')->addMultiOption($i * 1024, $i . ' Mbit/s');
		}

		for ($i = 10; $i <= 20; $i+=5) {
			$this->getElement('bandwidthup')->addMultiOption($i * 1024, $i . ' Mbit/s');
			$this->getElement('bandwidthdown')->addMultiOption($i * 1024, $i . ' Mbit/s');
		}

		$this->addElement('text', 'trafficlimit', array('label' => 'nodes_index_edit_form_trafficlimit',
													'required' => false,
													'class' => 'span-5',
													'validators' => array('Int')));

		$this->addElement('text', 'ssid', array('label' => 'nodes_index_edit_form_ssid',
													'required' => true,
													'class' => 'span-5',
													'validators' => array('ssid')));

		$this->addElement('select', 'channel', array('label' => 'nodes_index_edit_form_channel',
													'required' => true,
													'class' => 'span-5'));

		for ($i=1; $i<=13; $i++) {
			if ($i == 6) {
				continue;
			}
			$this->getElement('channel')->addMultiOption($i, $i);
		}

		$this->addElement('checkbox', 'wifi_enabled', array('label' => 'nodes_index_edit_form_wifienabled',
															'required' => false));

		$this->addElement('checkbox', 'roaming', array('label' => 'nodes_index_edit_form_roaming',
													'required' => true,
													'decorators' => array(
															'element' => array('decorator' => 'ViewHelper'),
												        	'label' => array('decorator' => 'Label',
												            				 'options' => array('optionalSuffix' => ':',
												                								'requiredSuffix' => ' * :',
																								'placement' => 'prepend')
																			),
															'errors' => 'errors',
															'htmltag' => array('decorator' => 'HtmlTag',
												            				   'options' => array ('tag' => 'div',
																								   'class' => 'formelement span-7'))
															)
														));

		$this->addElement('select', 'activefrom', array('label' => 'nodes_index_edit_form_activefrom',
													'required' => false,
													'class' => 'span-5'));
		$this->addElement('select', 'activeto', array('label' => 'nodes_index_edit_form_activeto',
													'required' => false,
													'class' => 'span-5'));

		$this->getElement('activefrom')->addMultiOption('', '');
		$this->getElement('activeto')->addMultiOption('', '');
		for ($i=1; $i<=24; $i++) {
			$this->getElement('activefrom')->addMultiOption($i, $i . ':00');
			$this->getElement('activeto')->addMultiOption($i, $i . ':00');
		}

		$acl = Zend_Registry::get('acl');
		$admin = Zend_Auth::getInstance()->getIdentity();

		if (!$acl->isAllowed($admin, new Nodes_Model_Node(), 'special')) {
			$this->getElement('ssid')->setAttrib('disabled', true)
									 ->setRequired(false);
			$this->getElement('channel')->setAttrib('disabled', true)
										->setRequired(false);
			$this->getElement('wifi_enabled')->setAttrib('disabled', true)
											 ->setRequired(false);
		}

	    $this->setDisplayGroupDecorators(array('FormElements',
		   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
	    														  'options' => array ('tag' => 'div',
													 	     						  'class' => 'span-9'))));

		$this->addDisplayGroup(array('dhcp', 'ipaddress','netmask','gateway','dnsservers'),
							   'node_dhcp', array('decorators' => array('FormElements',
									   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
								    														  'options' => array ('tag' => 'div',
																				 	     						  'class' => 'span-9')))));

		$this->addDisplayGroup(array('bandwidthup', 'bandwidthdown','trafficlimit','ssid','channel', 'wifi_enabled', 'roaming','activefrom','activeto'),
							   'node_wifi', array('decorators' => array('FormElements',
									   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
								    														  'options' => array ('tag' => 'div',
																				 	     						  'class' => 'span-9 last')))));


	}

	public function isValid($data)
	{
		if ($data['settings']['dhcp']) {
			$this->getElement('ipaddress')->setRequired(false);
			$this->getElement('netmask')->setRequired(false);
			$this->getElement('gateway')->setRequired(false);
			$this->getElement('dnsservers')->setRequired(false);
		}

		if (!$this->getElement('ssid')->isRequired()) {
			$data['settings']['ssid'] = $this->getElement('ssid')->getValue();
			$data['settings']['channel'] = $this->getElement('channel')->getValue();
		}

		if (!parent::isValid($data)) {
			return false;
		}

		$gateway = $this->getElement('gateway')->getValue();
		$netmask = $this->getElement('netmask')->getValue();

		$ipaddressElement = $this->getElement('ipaddress');

		$ipaddressElement->addValidator(new Unwired_Validate_NetworkRange($gateway, $netmask));

		return $ipaddressElement->isValid($ipaddressElement->getValue());
	}
}