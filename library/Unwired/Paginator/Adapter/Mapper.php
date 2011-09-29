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
 * Paginator adapter for DB mapper
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Unwired_Paginator_Adapter_Mapper extends Zend_Paginator_Adapter_DbTableSelect
{
	protected $_mapper = null;

	public function __construct(Unwired_Model_Mapper $mapper, Zend_Db_Select $select = null)
	{
		$this->_mapper = $mapper;

		if (null === $select) {
			$select = $mapper->getDbTable()->select(true);
		}

		$select->setIntegrityCheck(false);
		parent::__construct($select);
	}

	/**
	 * Get the mapper instance
	 * @return Unwired_Model_Mapper
	 */
	public function getMapper()
	{
		return $this->_mapper;
	}

	/* (non-PHPdoc)
	 * @see Zend_Paginator_Adapter_Interface::getItems()
	 * @return array
	 */
	public function getItems($offset, $itemCountPerPage) {
		$items = parent::getItems($offset, $itemCountPerPage);

		$result = array();

		foreach ($items as $item) {
			$entry = $this->getMapper()->rowToModel($item);

			$result[] = $entry;
		}

		return $result;
	}
}