<?php

class Unwired_Model_Generic
{

	/**
	 * Populate properties from array
	 *
	 * @param array $data
	 * @return Unwired_Model_Generic
	 */
	public function fromArray(array $data)
	{
		$filter = new Zend_Filter_Word_UnderscoreToCamelCase();
		foreach ($data as $key => $value) {
			$method = 'set' . $filter->filter($key);

			if (!method_exists($this, $method)) {
				continue;
			}

			$this->$method($value);
		}

		return $this;
	}

	/**
	 * Get model properties as array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$props = get_object_vars($this);

		$filter = new Zend_Filter_Word_CamelCaseToUnderscore();

		$result = array();

		foreach ($props as $property) {
			if ($property[0] != '_') {
				continue;
			}

			$key = $filter->filter(str_replace('_', '', $property));
			$method = 'get' . $key;

			if (!method_exists($this, $method)) {
				continue;
			}

			$result[$key] = $this->$method();
		}

		return $result;
	}
}