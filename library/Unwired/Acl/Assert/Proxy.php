<?php

class Unwired_Acl_Assert_Proxy implements Zend_Acl_Assert_Interface
{

	protected $_assertInstance = null;

	public function __construct($assert)
	{
		if ($assert instanceof Zend_Acl_Assert_Interface || is_string($assert)) {
			$this->_assertInstance = $assert;
		}
	}

	public function getAssertInstance()
	{
		if (is_string($this->_assertInstance)) {
			if (class_exists($this->_assertInstance)) {
				$this->_assertInstance = new $this->_assertInstance;
			} else {
				$this->_assertInstance = null;
			}
		}

		return $this->_assertInstance;
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Assert_Interface::assert()
	 */
	public function assert(Zend_Acl $acl, Zend_Acl_Role_Interface $role = null, Zend_Acl_Resource_Interface $resource = null, $privilege = null) {
		$assert = $this->getAssertInstance();

		if (null === $assert) {
			return true;
		}

		return $assert->assert($acl, $role, $resource, $privilege);
	}
}