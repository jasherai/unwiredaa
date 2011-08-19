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
													'required' => true));

		$this->addElement('select', 'bandwidthdown', array('label' => 'nodes_index_edit_form_bandwidthdown',
													'required' => true));

		foreach (range(256, 4096, 256) as $speed) {
			$this->getElement('bandwidthup')->addMultiOption($speed, $speed . ' kbit/s');
			$this->getElement('bandwidthdown')->addMultiOption($speed, $speed . ' kbit/s');
		}

		$this->addElement('text', 'trafficlimit', array('label' => 'nodes_index_edit_form_trafficlimit',
													'required' => true,
													'validators' => array('Int')));

		$this->addElement('text', 'ssid', array('label' => 'nodes_index_edit_form_ssid',
													'required' => true,
													'validators' => array('ssid')));

		$this->addElement('text', 'channel', array('label' => 'nodes_index_edit_form_channel',
													'required' => true,
													'validators' => array('ch' => array('validator' => 'Between',
																						 'options' => array('min' => 1,
																											'max' => 13)))));

		$this->addElement('checkbox', 'roaming', array('label' => 'nodes_index_edit_form_roaming',
													'required' => true));

	    $this->setDisplayGroupDecorators(array('FormElements',
		   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
	    														  'options' => array ('tag' => 'div',
													 	     						  'class' => 'span-9'))));

		$this->addDisplayGroup(array('dhcp', 'ipaddress','netmask','gateway','dnsservers'),
							   'node_dhcp', array('decorators' => array('FormElements',
									   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
								    														  'options' => array ('tag' => 'div',
																				 	     						  'class' => 'span-9')))));

		$this->addDisplayGroup(array('bandwidthup', 'bandwidthdown','trafficlimit','ssid','channel','roaming'),
							   'node_wifi', array('decorators' => array('FormElements',
									   							     	   'HtmlTag' => array('decorator' => 'HtmlTag',
								    														  'options' => array ('tag' => 'div',
																				 	     						  'class' => 'span-9 last')))));

	}

	public function isValid($data)
	{
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