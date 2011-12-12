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
class Report_Service_CodeTemplate_MostActiveAPs extends Report_Service_CodeTemplate_Abstract {

	protected function getTemplate($groupIds, $data) {
		//$groupRel = $this->_getGroupRelations($groupIds);

		$result = $data ['data'];

		$html = '';
		$seperator = '<tr height=15><th style="border-top:solid navy 2px; background:none;" colspan=4></th></tr>';
		$html .= $seperator.'<tr><th>Group / User Name</th>
<th style="text-align: center;">Download</th>
<th style="text-align: center;">Upload</td>
<th style="text-align: center;">Total</td></tr>';

		foreach ( $result as $k => $vv ) {
			$html .= '<tr><td>'.$vv['group_name'].'/'.$vv['node_name'].' ('.$vv['node_mac'].') </td>
<td style="text-align: right;">' . $this->_convertTraffic ( $vv ['bytes_down'] ) . '</td>
<td style="text-align: right;">' . $this->_convertTraffic ( $vv ['bytes_up'] ) . '</td>
<td style="text-align: right;">' . $this->_convertTraffic ( $vv ['bytes_total'] ) . '</td></tr>';

		}

		return '<table class="listing">'.$html.'</table>';
	}

	protected function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();

		$groupRel = $this->_getGroupRelations ( $groupIds );

		$select = $db->select ()
			->from( array ('a' => 'acct_internet_roaming' ), array ('*', 'SUM(a.total_bytes_up) as bytes_up', 'SUM(a.total_bytes_down) as bytes_down', 'SUM(a.total_bytes_down+a.total_bytes_up) as bytes_total' ) )
			->join( array ('b' => 'acct_internet_session' ), 'a.session_id = b.session_id', array () )
			->join( array ('c' => 'network_user' ), 'b.user_id = c.user_id', array ())
			->join( array ('d' => 'node' ), 'a.node_id = d.node_id', array ('node_name' => 'name', 'node_mac' => 'mac' ) )
			->join( array ('e' => 'group' ), 'd.group_id = e.group_id', array ('group_id', 'group_name' => 'name' ) )
			->where( 'e.group_id IN (?)', $groupRel )
			->where("DATE(a.start_time) BETWEEN '$dateFrom' AND '$dateTo'")
			->where( 'NOT ISNULL(a.stop_time)' )
			->order( array('bytes_total DESC') )
			->group( 'a.node_id')
			->limit(50);

		$result = $db->fetchAll ( $select );

		return array ('data' => $result);
	}
}
