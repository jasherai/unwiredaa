<?php

class Unwired_Validate_Ssid extends Zend_Validate_Regex
{
	const NOT_SSID = "ssidNotValid";

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_SSID  => 'Invalid SSID',
    );

	public function __construct()
	{
		parent::__construct('/^[0-9A-Z\s\-\_]{3,}$/i');
	}

	public function isValid($value)
	{
		$valid = parent::isValid($value);

		if (!$valid) {
			$this->_errors = array();
			$this->_messages = array();
			$this->_error(self::NOT_SSID);
		}

		return $valid;
	}
}