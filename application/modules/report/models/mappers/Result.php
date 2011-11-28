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
 * Mapper for Reports_Model_Items
 * @author G. Sokolov <joro@web-teh.net>
 */
class Report_Model_Mapper_Result extends Unwired_Model_Mapper
{

	protected $_modelClass = 'Report_Model_Items';
	protected $_dbTableClass = 'Report_Model_DbTable_Items';

	protected $_defaultOrder = 'date_added DESC';
	
	protected $_group_id = 0;
	
	/**
	 * @return the $_group_id
	 */
	public function getGroupId() {
		return $this->_group_id;
	}

	/**
	 * @param number $_group_id
	 */
	public function setGroupId($_group_id) {
		$this->_group_id = $_group_id;
	}

	public function findby($conditions, $limit, $order) {
		if ($this->_group_id != 0) {
			$conditions['group_id'] = $this->_group_id;
		}
		
		return parent::findBy($conditions, $limit, $order);
	}
	
	public function fetchAll() {
		if ($this->_group_id != 0) {
			$conditions['group_id'] = $this->_group_id;
		}
		
		return parent::findBy($conditions);
	}
	
}

