<?php

class Unwired_Validate_NetworkRange extends Zend_Validate_Abstract
{
	const NOT_IN_RANGE = "ipNotInRange";

	protected $_ip = null;

	protected $_netmask = null;

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_IN_RANGE  => 'Ip %value% is outside network range',
    );

	public function __construct($ip, $netmask = '255.255.255.0')
	{
		if ($ip instanceof Zend_Config) {
			$ip = $ip->toArray();
		}

		if (is_array($ip)) {
			if (!isset($ip['ip'])) {
				throw new Unwired_Exception('IP address for network range is not specified');
			}

			$ip = $ip['ip'];

			if (isset($ip['netmask'])) {
				$netmask = $ip['netmask'];
			}
		}

		$this->setIp($ip);
		$this->setNetmask($netmask);
	}

	public function getIp()
	{
		return $this->_ip;
	}

	public function setIp($ip)
	{
		$this->_ip = $ip;
		return $this;
	}

	public function getNetmask()
	{
		return $this->_netmask;
	}

	public function setNetmask($netmask)
	{
		$this->_netmask = $netmask;
		return $this;
	}


	public function isValid($value)
	{
		if (!$this->_ipInRange($value)) {
			$this->_error(self::NOT_IN_RANGE);
			return false;
		}

		return true;
	}


	/**
	 * Check if specified IP address is in network range
	 *
	 * Based on ip_in_range.php by Paul Gregg <pgregg@pgregg.com>
	 * Source website: http://www.pgregg.com/projects/php/ip_in_range/
	 *
	 * @param string $ip IP address to check
	 * @return bool
	 */
	protected function _ipInRange($ip) {

	    if (strpos($this->getNetmask(), '.') !== false) {
	      // $netmask is a 255.255.0.0 format
	      $netmask = str_replace('*', '0', $this->getNetmask());
	      $netmaskDec = ip2long($netmask);
	      return ( (ip2long($ip) & $netmaskDec) == (ip2long($this->getIp()) & $netmaskDec) );
	    }

	    // $netmask is a CIDR size block
	    // fix the range argument
	    $x = explode('.', $this->getIp());

	    while(count($x)<4) {
      		$x[] = '0';
	    }

	    list($a,$b,$c,$d) = $x;
	    $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
	    $rangeDec = ip2long($range);
	    $ipDec = ip2long($ip);

	    # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
	    #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

	    # Strategy 2 - Use math to create it
	    $wildcardDec = pow(2, (32 - $this->getNetmask())) - 1;
	    $netmaskDec = ~ $wildcardDec;

	    return (($ipDec & $netmaskDec) == ($rangeDec & $netmaskDec));
	}
}

