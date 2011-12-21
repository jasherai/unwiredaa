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
class Report_Service_CodeTemplate_TopTLD extends Report_Service_CodeTemplate_Abstract
{

	public function getData($groupIds, $dateFrom, $dateTo) {
	        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
			/*$groupRel = $this->_getGroupRelations($groupIds);*/

			$select = $db->select()
					->from(array('dns_log'),array('count(*) as cnt','tld','sld'))
			                ->where('tld NOT IN (?)', array('arpa','lan','local','mobi','home','_TCP','office'))
			                ->where('sld != ""')
			                ->where('DATE(time) >= ?', $dateFrom)
			                ->where('DATE(time) <= ?', $dateTo)
			                ->group(array('tld', 'sld'))
					->order('cnt DESC');

			$items = $db->fetchAll($select);
			$result = array();
			$total=0;
			foreach ($items as $key => $value) {
				$total+=$value['cnt'];
				if (!is_array($result[$value['tld']]['sld'])){
					$result[$value['tld']]['sld']=array();
					$result[$value['tld']]['count']=$value['cnt'];
				}
				else {
					$result[$value['tld']]['count']+=$value['cnt'];
				}
				if ($value['cnt']>100) {
					$result[$value['tld']]['sld'][$value['sld']]=$value['cnt'];
				} else {
					if (!$result[$value['tld']]['sld']['[other]']) $result[$value['tld']]['sld']['[other]']=$value['cnt'];
					else $result[$value['tld']]['sld']['[other]']+=$value['cnt'];
				}
			}

			uasort($result, function($a, $b) {
				if ($a['count'] == $b['count']) {
					return 0;
				}
				return ($a['count'] > $b['count']) ? -1 : 1;
			});
	        return array('data' => $result,'total' => $total);
	}
}

