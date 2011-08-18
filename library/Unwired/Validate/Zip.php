<?php
class Unwired_Validate_Zip extends Zend_Validate_Regex
{
	const NOT_ZIP = "zipNotValid";

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_ZIP  => 'Invalid ZIP code',
    );

	public function __construct()
	{
		parent::__construct('/^[0-9A-Z\s]{3,10}$/i');
	}

	public function isValid($value)
	{
		$valid = parent::isValid($value);

		if (!$valid) {
			$this->_errors = array();
			$this->_messages = array();
			$this->_error(self::NOT_ZIP);
		}

		return $valid;
	}
}