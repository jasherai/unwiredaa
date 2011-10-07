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

class Unwired_Event_Message_Data
{

	protected $_data = array();

	public function __construct(array $data = array())
	{
		$this->fromArray($data);
	}

	public function __call($name, $args)
	{
		$matches = array();

		if (!preg_match('/^(get|set)(.*)$/i', $name, $matches))
		{
			throw new Unwired_Exception(__CLASS__ . '::' . $name . ' does not exist!');
		}

		if ($matches[1] == 'get') {
			return $this->$matches[2];
		}

		return $this->__set($matches[2], $args[0]);
	}

	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}

	public function __get($key)
	{
		if (!isset($this->$key)) {
			return null;
		}

		return $this->_data[$key];
	}

	public function __isset($key)
	{
		return array_key_exists($key, $this->_data);
	}

	public function toArray()
	{
		return $this->_data;
	}

	public function fromArray(array $data = array())
	{
		$this->_data = $data;

		return $this;
	}
}