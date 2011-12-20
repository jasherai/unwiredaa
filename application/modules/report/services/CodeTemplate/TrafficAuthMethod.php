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
class Report_Service_CodeTemplate_TrafficAuthMethod extends Report_Service_CodeTemplate_Abstract {

	protected function getTemplate($groupIds, $data) {
		//$groupRel = $this->_getGroupRelations($groupIds);


		$result = $data ['data'];
		$groupTotals = $data ['totals'];

		ksort($groupTotals);

		$html = '';
		$total_up = $total_down = 0;

		$html .= '<table class="listing">';
		$html .= '<tr><th>Group / User Name</th><th style="text-align: center;">Download</th><th style="text-align: center;">Upload</td><th style="text-align: center;">Total</td></tr>';

		foreach ($groupTotals as $key => $value) {
			$html .= '<tr><td><strong>'.$key.'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($value['bytes_down']).'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($value['bytes_up']).'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($value['total']).'</strong></td></tr>';
			foreach ($result[$key] as $k => $v) {
				$html .= '<tr><td>'.$v['username'].'</td><td style="text-align: right;">'.$this->_convertTraffic($v['bytes_down']).'</td><td style="text-align: right;">'.$this->_convertTraffic($v['bytes_up']).'</td><td style="text-align: right;">'.$this->_convertTraffic($v['total']).'</td></tr>';
			}


			$total_up += $value['bytes_up'];
			$total_down += $value['bytes_down'];
		}

		$html .= '<tr><th>Total</th><th style="text-align: right;">' . $this->_convertTraffic ( $total_up ) . '</th><th style="text-align: right;">' . $this->_convertTraffic ( $total_down ) . '</th><th style="text-align: right;">' . $this->_convertTraffic ( $total_up + $total_down ) . '</th></tr>';

		$html .= '</table>';


		/*there shouldn`t be seperate tabels as the layout looks bad,. */
		return $html;
		/*but as there already are, we continue with it,.*/
		/*return '<table class="listing"><tr><th width=50%>Total</th><th>Total Download</th><th>Total Upload</th><th>Total</th></tr>
<tr><td>Total</td><td><strong>' . $this->_convertTraffic ( $total_up ) . '</strong></td><td><strong>' . $this->_convertTraffic ( $total_down ) . '</strong></td><td><strong>' . $this->_convertTraffic ( $total_up + $total_down ) . '</strong></td></tr></table>' . $html;
		*/
	}

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();

		$groupRel = $this->_getGroupRelations ( $groupIds );

		$select = $db->select ()
			->from ( array ('a' => 'acct_internet_roaming' ), array ('*', 'SUM(a.total_bytes_up) as bytes_up', 'SUM(a.total_bytes_down) as bytes_down' ) )
			->join ( array ('b' => 'acct_internet_session' ), 'a.session_id = b.session_id', array ('groupname') )
			->join ( array ('c' => 'network_user' ), 'b.user_id = c.user_id', array ('username' ), array () )
			->join ( array ('d' => 'node' ), 'a.node_id = d.node_id', array ('node_name' => 'name', 'node_mac' => 'mac' ) )
			->join ( array ('e' => 'group' ), 'd.group_id = e.group_id', array ('group_id', 'group_name' => 'name' ) )
			->where ( 'e.group_id IN (?)', $groupRel )
			->where ( 'DATE(a.start_time) >= ?', $dateFrom )
			->where ( 'DATE(a.start_time) <= ?', $dateTo )
			->where ( 'NOT ISNULL(a.stop_time)' )
			->group ( 'c.username' );

		$result = $db->fetchAll ( $select );

		$data = array ();
		foreach ($result as $key => $value) {
			$value['total'] = $value['bytes_up'] + $value['bytes_down'];
			if (!isset($data[$value['groupname']])) {
				$data[$value['groupname']] = array();
			}

			$data[$value['groupname']][] = $value;

			if (!isset($groupTotals[$value['groupname']])) {
				$groupTotals[$value['groupname']] = array('total' => 0, 'bytes_up' => 0, 'bytes_down' => 0);
			}

			$groupTotals[$value['groupname']]['total'] += $value['total'];
			$groupTotals[$value['groupname']]['bytes_up'] += $value['bytes_up'];
			$groupTotals[$value['groupname']]['bytes_down'] += $value['bytes_down'];
		}

		return array ('data' => $data, 'totals' => $groupTotals );
	}

}
