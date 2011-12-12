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
 * Node settings model
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Nodes_Model_Settings extends Unwired_Model_Generic
{
	protected $_nodeId = null;

	protected $_ssid = 'FreeWlanSkiamade';

	protected $_activefrom = null;

	protected $_activeto = null;

	protected $_dhcp = null;

	protected $_ipaddress = null;

	protected $_netmask = null;

	protected $_gateway = null;

	protected $_dnsservers = null;

	protected $_bandwidthup = null;

	protected $_bandwidthdown = null;

	protected $_trafficlimit = null;

	protected $_channel = 11;

	protected $_wifiEnabled = 1;

	protected $_roaming = 1;

	/**
	 * @return the $nodeId
	 */
	public function getNodeId() {
		return $this->_nodeId;
	}

	/**
	 * @param integer $nodeId
	 */
	public function setNodeId($nodeId) {
		$this->_nodeId = $nodeId;

		return $this;
	}

	/**
	 * @return the $activefrom
	 */
	public function getActivefrom() {
		return $this->_activefrom;
	}

	/**
	 * @param field_type $activefrom
	 */
	public function setActivefrom($activefrom) {
		if ((int) $activefrom > 24 || (int) $activefrom < 1) {
			$activefrom = null;
		}
		$this->_activefrom = $activefrom;

		return $this;
	}

	/**
	 * @return the $activeto
	 */
	public function getActiveto() {
		return $this->_activeto;
	}

	/**
	 * @param field_type $activeto
	 */
	public function setActiveto($activeto) {
		if ((int) $activeto > 24 || (int) $activeto < 1) {
			$activeto = null;
		}
		$this->_activeto = $activeto;

		return $this;
	}

	/**
	 * @return the $dhcp
	 */
	public function getDhcp() {
		if (null === $this->_dhcp && empty($this->_ipaddress)) {
			$this->_dhcp = true;
		}

		return (int) (bool) $this->_dhcp;
	}

	/**
	 * @param bool $dhcp
	 */
	public function setDhcp($dhcp = false) {
		$this->_dhcp = (int) (bool) $dhcp;

		if (!$this->_dhcp) {
			$this->setIpaddress(null);
			$this->setNetmask(null);
			$this->setGateway(null);
			$this->setDnsservers(null);
		}

		return $this;
	}

	/**
	 * @return the $ipaddress
	 */
	public function getIpaddress() {
		return $this->_ipaddress;
	}

	/**
	 * @param field_type $ipaddress
	 */
	public function setIpaddress($ipaddress) {
		$this->_ipaddress = $ipaddress;

		return $this;
	}

	/**
	 * @return the $netmask
	 */
	public function getNetmask() {
		return $this->_netmask;
	}

	/**
	 * @param field_type $netmask
	 */
	public function setNetmask($netmask) {
		$this->_netmask = $netmask;

		return $this;
	}

	/**
	 * @return the $gateway
	 */
	public function getGateway() {
		return $this->_gateway;
	}

	/**
	 * @param field_type $gateway
	 */
	public function setGateway($gateway) {
		$this->_gateway = $gateway;

		return $this;
	}

	/**
	 * @return the $dnsservers
	 */
	public function getDnsservers() {
		return $this->_dnsservers;
	}

	/**
	 * @param field_type $dnsservers
	 */
	public function setDnsservers($dnsservers) {
		$this->_dnsservers = $dnsservers;

		return $this;
	}

	/**
	 * @return the $bandwidthup
	 */
	public function getBandwidthup() {
		return $this->_bandwidthup;
	}

	/**
	 * @param field_type $bandwidthup
	 */
	public function setBandwidthup($bandwidthup) {
		$this->_bandwidthup = $bandwidthup;

		return $this;
	}

	/**
	 * @return the $bandwidthdown
	 */
	public function getBandwidthdown() {
		return $this->_bandwidthdown;
	}

	/**
	 * @param field_type $bandwidthdown
	 */
	public function setBandwidthdown($bandwidthdown) {
		$this->_bandwidthdown = $bandwidthdown;

		return $this;
	}

	/**
	 * @return the $trafficlimit
	 */
	public function getTrafficlimit() {
		return $this->_trafficlimit;
	}

	/**
	 * @param field_type $trafficlimit
	 */
	public function setTrafficlimit($trafficlimit) {
		$this->_trafficlimit = $trafficlimit;

		return $this;
	}

	/**
	 * @return the $_ssid
	 */
	public function getSsid() {
		return $this->_ssid;
	}

	/**
	 * @param string $ssid
	 */
	public function setSsid($ssid) {
		$this->_ssid = $ssid;

		return $this;
	}

	/**
	 * @return the $channel
	 */
	public function getChannel() {
		return $this->_channel;
	}

	/**
	 * @param field_type $channel
	 */
	public function setChannel($channel) {
		$this->_channel = $channel;

		return $this;
	}

	/**
	 * @return the $wifiEnabled
	 */
	public function getWifiEnabled() {
		return $this->_wifiEnabled;
	}

	/**
	 * @param bool $enabled
	 */
	public function setWifiEnabled($enabled = true) {
		$this->_wifiEnabled = (int) (bool) $enabled;

		return $this;
	}

	/**
	 * @return the $roaming
	 */
	public function getRoaming() {
		return $this->_roaming;
	}

	/**
	 * @param field_type $roaming
	 */
	public function setRoaming($roaming) {
		$this->_roaming = $roaming;

		return $this;
	}


}
