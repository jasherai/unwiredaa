<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
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

		$this->setLegend('Node settings');

		$this->setDecorators(array('FormElements',
								   'fieldset' => array('decorator' => 'fieldset',
							           				   'options' => array ('class' => 'span-19 last')),
								   'Form'));

		$this->addElement('checkbox', 'dhcp', array('label' => 'nodes_index_edit_form_dhcp',
													'required' => true));

		$this->addElement('text', 'ipaddress', array('label' => 'nodes_index_edit_form_ipaddress',
													'required' => true,
													'validators' => array('ip')));

		$this->addElement('text', 'netmask', array('label' => 'nodes_index_edit_form_netmask',
													'required' => true,
													'validators' => array('ip')));

		$this->addElement('text', 'gateway', array('label' => 'nodes_index_edit_form_gateway',
													'required' => true,
													'validators' => array('ip')));

		$this->addElement('text', 'dnsservers', array('label' => 'nodes_index_edit_form_dnsservers',
													'required' => true,
													'validators' => array('dns')));


		$this->addElement('select', 'bandwidthup', array('label' => 'nodes_index_edit_form_bandwidthup',
													'required' => true,
													'class' => 'span-4'));

		$this->addElement('select', 'bandwidthdown', array('label' => 'nodes_index_edit_form_bandwidthdown',
													'required' => true,
													'class' => 'span-4'));

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
													'validators' => array('Int')));

		$this->addElement('text', 'ssid', array('label' => 'nodes_index_edit_form_ssid',
													'required' => true,
													'validators' => array('ssid')));

		$this->addElement('select', 'channel', array('label' => 'nodes_index_edit_form_channel',
													'required' => true,
													'class' => 'span-4'));

		for ($i=1; $i<=13; $i++) {
			$this->getElement('channel')->addMultiOption($i, $i);
		}

		$this->addElement('checkbox', 'roaming', array('label' => 'nodes_index_edit_form_roaming',
													'required' => true));

		$this->addElement('select', 'activefrom', array('label' => 'nodes_index_edit_form_activefrom',
													'required' => false,
													'class' => 'span-4'));
		$this->addElement('select', 'activeto', array('label' => 'nodes_index_edit_form_activeto',
													'required' => false,
													'class' => 'span-4'));

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

		$this->addDisplayGroup(array('bandwidthup', 'bandwidthdown','trafficlimit','ssid','channel','roaming','activefrom','activeto'),
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