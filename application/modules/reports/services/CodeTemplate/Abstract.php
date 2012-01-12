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

abstract class Reports_Service_CodeTemplate_Abstract {

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

	protected function _convertTraffic($amount) {
		if ($amount < 1000000) {
			return number_format($amount/1000, 2).'KB';
		} if ($amount < 1000000000) {
			return number_format($amount/1000000, 2).'MB';
		} else {
			return number_format($amount/1000000000, 2).'GB';
		}
	}

	protected function _getMac($dec) {

		$tmp = str_pad(base_convert($dec, 10, 16), 12, 0, STR_PAD_LEFT);
		$result = '';
		for ($i=0;$i<6;$i++) {
			$result[] = substr($tmp, $i*2, 2);
		}
		return strtoupper(implode('-', $result));

	}

	//alias of getData, override if you need different data for CSV
	public function getCsv($groupIds, $dateFrom, $dateTo) {
		return $this->getData($groupIds, $dateFrom, $dateTo);
	}

	//alias of getData, override if you need different data for CSV
	public function getPdf($groupIds, $dateFrom, $dateTo) {
		return $this->getData($groupIds, $dateFrom, $dateTo);
	}

	abstract public function getData($groupIds, $dateFrom, $dateTo);

}