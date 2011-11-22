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

abstract class Report_Service_CodeTemplate_Abstract {
	abstract protected function getTemplate($groupIds, $data);
	
	abstract protected function getData($groupIds, $dateFrom, $dateTo);
	
	//default report generator, to be replaced if table is to be made, @todo cleanup when reports!!!
	protected $_group = 'group';
	
	protected function _getGroupRelations($groupIds) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		
		$relations = array ();
		
		foreach ( $groupIds as $key => $value ) {
			$relations [$value] = $value; // bind the sources to themselfs
		}
		
		$toGet = $groupIds;
		
		while ( ! empty ( $toGet ) ) {
			$gid = array_pop ( $toGet );
			
			$select = $db->select ()->from ( array ('a' => $this->_group ) )->where ( 'a.parent_id = ?', $gid );
			
			$result = $db->fetchAll ( $select );
			
			foreach ( $result as $key => $value ) {
				$relations [$value ['group_id']] = $relations [$gid];
				array_push ( $toGet, $value ['group_id'] );
			}
		}
		
		return array_keys($relations);
	}
	
	public function getReport($groupIds, $dateFrom, $dateTo) {
		$data = $this->getData ( $groupIds, $dateFrom, $dateTo );
		
		$html = $this->getTemplate ( $groupIds, $data );
		
		return array ('data' => $data, 'html' => $html );
	}
}