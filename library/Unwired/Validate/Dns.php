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

class Unwired_Validate_Dns extends Zend_Validate_Abstract
{
	const NOT_DNS = "dnsNotValid";

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_DNS  => 'Invalid DNS server(s)',
    );

	public function isValid($value)
	{
		if (!preg_match('/^[a-z0-9\.\-\s]*$/i', $value)) {
			$this->_error(self::NOT_DNS);
			return false;
		}

		$servers = explode(' ', $value);

		$validateIp = new Zend_Validate_Ip();
		$validateHost = new Zend_Validate_Hostname();

		foreach ($servers as $server) {
			$server = trim($server);
			if (!$validateIp->isValid($server) && !$validateHost->isValid($server)) {
				$this->_error(self::NOT_DNS);
				return false;
			}
		}

		return true;
	}
}