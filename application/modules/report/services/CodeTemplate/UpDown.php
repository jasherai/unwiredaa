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
class Report_Service_CodeTemplate_UpDown extends Report_Service_CodeTemplate_Abstract {
	

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		
		$groupRel = $this->_getGroupRelations ( $groupIds );
		
		$select = $db->select ()
			->from ( array ('a' => 'acct_internet_roaming' ), array ('*', 'SUM(a.total_bytes_up) as bytes_up', 'SUM(a.total_bytes_down) as bytes_down' ) )
			->join ( array ('b' => 'acct_internet_session' ), 'a.session_id = b.session_id', array () )
			->join ( array ('c' => 'network_user' ), 'b.user_id = c.user_id', array ('username' ), array () )
			->join ( array ('d' => 'node' ), 'a.node_id = d.node_id', array ('node_name' => 'name', 'node_mac' => 'mac' ) )
			->join ( array ('e' => 'group' ), 'd.group_id = e.group_id', array ('group_id', 'group_name' => 'name' ) )
			->where ( 'e.group_id IN (?)', $groupRel )
			->where("DATE(a.start_time) BETWEEN '$dateFrom' AND '$dateTo'")
			->where ( 'NOT ISNULL(a.stop_time)' )
			->order ( array('node_name ASC','username ASC') )
			->group ( 'a.id');
		
		$result = $db->fetchAll ( $select );
		
		$data = array ();
		foreach ( $result as $key => $value ) {
			if (! isset ( $groupTotals [$value ['group_id']] )) {
				$groupTotals [$value ['group_id']] = array ('total' => array ('bytes_up' => 0, 'bytes_down' => 0, 'name' => '' ) );
			}
			
			$groupTotals [$value ['group_id']] ['total'] ['bytes_up'] += $value ['bytes_up'];
			$groupTotals [$value ['group_id']] ['total'] ['bytes_down'] += $value ['bytes_down'];
			$groupTotals [$value ['group_id']] ['total'] ['name'] = $value ['group_name'];
			
			if (! isset ( $groupTotals [$value ['group_id']] ['ap'] [$value ['node_id']] )) {
				$groupTotals [$value ['group_id']] ['ap'] [$value ['node_id']] = array ('bytes_up' => 0, 'bytes_down' => 0, 'name' => '' );
			}
			
			$groupTotals [$value ['group_id']] ['ap'] [$value ['node_id']] ['bytes_up'] += $value ['bytes_up'];
			$groupTotals [$value ['group_id']] ['ap'] [$value ['node_id']] ['bytes_down'] += $value ['bytes_down'];
			$groupTotals [$value ['group_id']] ['ap'] [$value ['node_id']] ['name'] = $value ['node_name'];
			$groupTotals [$value ['group_id']] ['ap'] [$value ['node_id']] ['mac'] = $value ['node_mac'];
			
			if (isset ( $data [$value ['group_id']] [$value ['node_id']] [$value ['username']] )) {
				$data [$value ['group_id']] [$value ['node_id']] [$value ['username']] ['bytes_up'] += $value ['bytes_up'];
				$data [$value ['group_id']] [$value ['node_id']] [$value ['username']] ['bytes_down'] += $value ['bytes_down'];
			} else {
				$data [$value ['group_id']] [$value ['node_id']] [$value ['username']] = $value;
			}
		}
		
		$tables = array();
		$graphics = array();
		
		$total_up = $total_down = 0;
		foreach ( $groupTotals as $k => $v ) {
			$total_up += $groupTotals [$k] ['total'] ['bytes_up'];
			$total_down += $groupTotals [$k] ['total'] ['bytes_down'];
			
			$graphics[] = array($groupTotals [$k]['total']['name'], ($groupTotals [$k]['total'] ['bytes_down'] + $groupTotals [$k]['total'] ['bytes_up']));
			
			$table = array(
				'colDefs' => array(
					array('report_result_group_username', 'report_result_download', 'report_result_upload', 'report_result_total'),
				),
				'rows' => array(
					array(
						'data' => array(
							$v['total']['name'], 
							$this->_convertTraffic ( $groupTotals [$k] ['total'] ['bytes_down'] ), 
							$this->_convertTraffic ( $groupTotals [$k] ['total'] ['bytes_up'] ),
							$this->_convertTraffic ( $groupTotals [$k] ['total'] ['bytes_down'] + $groupTotals [$k] ['total'] ['bytes_up'] ) 
						),
						'class' => array('bold', 'bold right', 'bold right', 'bold right')
					),
				)
			);
			
			foreach ( $v ['ap'] as $kk => $vv ) {
				
				$table['rows'][] = array(
					'data' => array(
						'&nbsp;&nbsp;&nbsp;&nbsp;'.$vv ['name'].' (' . $vv ['mac'] . ') ',
						$this->_convertTraffic ( $vv ['bytes_down'] ),
						$this->_convertTraffic ( $vv ['bytes_up'] ),
						$this->_convertTraffic ( $vv ['bytes_down'] + $vv ['bytes_up'] )
					),
					'class' => array('bold italic', 'bold italic right', 'bold italic right', 'bold italic right')
				);
				
				foreach ( $data [$k] [$kk] as $key => $value ) {
					$table['rows'][] = array(
						'data' => array(
							'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $value ['username'],
							$this->_convertTraffic ( $value ['bytes_down'] ),
							$this->_convertTraffic ( $value ['bytes_up'] ),
							$this->_convertTraffic ( $value ['bytes_down'] + $value ['bytes_up'] )
						),
						'class' => array('', 'right', 'right', 'right')
					);
				}
			}
			
			$tables[] = $table;
			
		}
		
		$total_table = array(
			'colDefs' => array(
				array(
					array(
						'name' => 'report_result_total',
						'width' => '50%'
					), 'report_result_total_download', 'report_result_total_upload', 'report_result_total'
				)
			),
			'rows' => array(
				array(
					'data' => array('', $this->_convertTraffic($total_down), $this->_convertTraffic($total_up), $this->_convertTraffic(($total_down + $total_up))),
					'class' => array('bold', 'bold right', 'bold right', 'bold right')
				)
			)
		);
		
		array_unshift($tables, $total_table);
		$tables[] = $total_table;
		
		$result = array(
			'graphics' => array(
				array(
					'name' => 'report_result_group',
					'type' => 'piechart',
					'headers' => array('report_result_group', 'report_result_traffic'),
					'rows' => $graphics
				),
			),
			'tables' => $tables
		);
		
		
		return $result;
	}

}
