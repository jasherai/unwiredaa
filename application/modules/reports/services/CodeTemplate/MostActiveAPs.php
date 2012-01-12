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
class Reports_Service_CodeTemplate_MostActiveAPs extends Reports_Service_CodeTemplate_Abstract {

	protected function getTemplate($groupIds, $data) {
		//$groupRel = $this->_getGroupRelations($groupIds);

		$result = $data ['data'];

		$html = '<tr><th>Group</th><th>Node/AP Name</th>
<th style="text-align: center;">Download</th>
<th style="text-align: center;">Upload</td>
<th style="text-align: center;">Total</td></tr>';

		foreach ( $result as $k => $vv ) {
			$html .= '<tr><td>'.$vv['group_name'].'</td><td><strong>'.$vv['node_name'].'</strong> ('.$vv['node_mac'].') </td>
<td style="text-align: right;">' . $this->_convertTraffic ( $vv ['bytes_down'] ) . '</td>
<td style="text-align: right;">' . $this->_convertTraffic ( $vv ['bytes_up'] ) . '</td>
<td style="text-align: right;">' . $this->_convertTraffic ( $vv ['bytes_total'] ) . '</td></tr>';

		}

		return '<table class="listing">'.$html.'</table>';
	}

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();

		$groupRel = $this->_getGroupRelations ( $groupIds );

		$select = $db->select ()
			->from( array ('a' => 'acct_internet_roaming' ), array (/*'*', */'SUM(a.total_bytes_up) as bytes_up', 'SUM(a.total_bytes_down) as bytes_down', 'SUM(a.total_bytes_down+a.total_bytes_up) as bytes_total' ) )
			->join( array ('b' => 'acct_internet_session' ), 'a.session_id = b.session_id', array () )
			->join( array ('c' => 'network_user' ), 'b.user_id = c.user_id', array ())
			->join( array ('d' => 'node' ), 'a.node_id = d.node_id', array ('node_id', 'node_name' => 'name', 'node_mac' => 'mac' ) )
			->join( array ('e' => 'group' ), 'd.group_id = e.group_id', array ('group_id', 'group_name' => 'name' ) )
			->where( 'e.group_id IN (?)', $groupRel )
			->where("DATE(a.start_time) BETWEEN '$dateFrom' AND '$dateTo'")
			->where( 'NOT ISNULL(a.stop_time)' )
			->order( array('bytes_total DESC') )
			->group( 'a.node_id')
			->limit(50);

		$records = $db->fetchAll ( $select );

		//Zend_Debug::dump($records); die();

		$tables = array();
        $graphics = array();


        $totals = array('data' => array(
                            'name' => array('data' => 'report_result_total',
                                  'translatable' => true
                            ),
            '',
            'down' => 0,
            'up' => 0,
            'total' => 0
          ),
          'class' => array(
             'name' => "bold", '', 'down' => "bold right", 'up' => "bold right", 'total' => "bold right"
          ));

        $results = array();
        $graphics = array();

        foreach ($records as $record) {
            $results[$record['node_id']] = array('data' => array(
                                            'device' => $record['node_name'],
                                            'group' => $record['group_name'],
                                            'down' => $this->_convertTraffic($record['bytes_down']),
                                            'up' => $this->_convertTraffic($record['bytes_up']),
                                            'total' => $this->_convertTraffic($record['bytes_total'])
                                          ),
                                          'class' => array(
                                            'device' => '', 'group'=>'', 'down' => "right", 'up' => "right", 'total' => "right"
                                          ));

            $graphics[/*$record['node_id']*/] = array($record['node_name'], $record['bytes_total']);

            $totals['data']['down'] += $record['bytes_down'];
            $totals['data']['up'] += $record['bytes_up'];
            $totals['data']['total'] += $record['bytes_total'];
        }

        $totals['data']['down'] = $this->_convertTraffic($totals['data']['down']);
        $totals['data']['up'] = $this->_convertTraffic($totals['data']['up']);
        $totals['data']['total'] = $this->_convertTraffic($totals['data']['total']);

        array_unshift($results, $totals);
        array_push($results, $totals);

        return array('graphics' => array(
                        array('name' => 'report_most_active_device',
                              'type' => 'piechart',
                              'headers' => array('report_device_name', 'report_result_total'),
                              'rows' => $graphics)
                     ),
                     'tables' => array(
                        array(
                            'colDefs' => array(array('report_device_name', 'report_device_group', 'report_result_download', 'report_result_upload', 'report_result_total')),
                            'rows' => $results
                        )
                      ));



        $user = array();
        foreach ($groupTotals as $k => $v) {
        	foreach ($result[$k] as $key => $value) {
        		$user[$value['username']] = $value['down_total'];
        	}
        }

        foreach ($user as $key => $value):
        	$graphics[] = array($key, $value);
        endforeach;

        foreach ($groupTotals as $k => $v) {
        	$table = array(
        		'colDefs' => array(
        			array(
        				'report_device_name', 'report_result_download', 'report_result_upload', 'report_result_total'
        			)
        		)
        	);

        	$total_row = array(
        		'data' => array(array('data' => 'report_result_total', 'translatable' => true), $this->_convertTraffic($v['down_total']), $this->_convertTraffic($v['up_total']), $this->_convertTraffic($v['down_total']+$v['up_total'])),
        		'class' => array('bold', 'bold right', 'bold right', 'bold right')
        	);

        	$table['rows'][] = $total_row;

        	foreach ($result[$k] as $key => $value) {
        		$table['rows'][] = array(
        				'data' => array($value['username'], $this->_convertTraffic($value['down_total']), $this->_convertTraffic($value['up_total']), $this->_convertTraffic($value['down_total']+$value['up_total'])),
        				'class' => array('', 'right', 'right', 'right')
        		);
        	}

        	$table['rows'][] = $total_row;

        	$tables[] = $table;

        }


        $report = array(
        	'graphics' => array(
        			array(
        					'name' => 'report_status_ap_count',
        					'type' => 'piechart',
        					'headers' => array('report_result_user', 'report_result_traffic'),
        					'rows' => $graphics
        			),
        	),
        	'tables' => $tables
        );

        return $report;
	}
}
