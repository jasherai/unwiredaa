<?php

class Groups_Model_Policy extends Unwired_Model_Generic
{
	protected $_policyId = null;

	protected $_name = null;

	protected $_roleId = null;

	protected $_rules = array();

	/**
	 * @return the $policyId
	 */
	public function getPolicyId() {
		return $this->_policyId;
	}

	/**
	 * @param field_type $policyId
	 */
	public function setPolicyId($policyId) {
		$this->_policyId = $policyId;
		return $this;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->_name = $name;
		return $this;
	}

	/**
	 * @return the $roleId
	 */
	public function getRoleId() {
		return $this->_roleId;
	}

	/**
	 * @param field_type $roleId
	 */
	public function setRoleId($roleId) {
		$this->_roleId = $roleId;
		return $this;
	}

	/**
	 * @return the $rules
	 */
	public function getRules() {
		return $this->_rules;
	}

	/**
	 * @param field_type $rules
	 */
	public function setRules($rules) {
		$this->_rules = $rules;
		return $this;
	}

	public function addRule($rule, $value)
	{
		$this->_rules[$rule] = $value;
		return $this;
	}

}