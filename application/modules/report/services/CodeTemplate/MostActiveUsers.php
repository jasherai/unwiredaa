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
class Report_Service_CodeTemplate_MostActiveUsers extends Report_Service_CodeTemplate_Abstract {


    public function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$groupTotals = array();
		
        foreach ($groupIds as $k => $v) {
        	
        	
        	$groupTotals[$v] = array('cnt' => 0, 'down_total' => 0, 'up_total' => 0, 'traffic_total' => 0);
	        $groupRel = $this->_getGroupRelations(array($v));

/*use the roaming sessions, as the contain only the traffic of the nodes in the seleted groups, and are not harmed/multiplied by the number of roamings per user*/
                $select = $db->select()
                                ->from(array('u' => 'network_user'), 'u.*')
                        ->join(array('s' => 'acct_internet_session'), 'u.user_id = s.user_id')
                        ->join(array('r' => 'acct_internet_roaming'), 's.session_id = r.session_id', 'SUM(r.total_bytes_up) as up_total, SUM(r.total_bytes_down) as down_total, SUM(r.total_bytes_up)+SUM(r.total_bytes_down) as traffic_total')
                        ->join(array('n' => 'node'), 'r.node_id = n.node_id')
                        ->join(array('g' => 'group'), 'n.group_id = g.group_id', array('group_id', 'name as group_name'))
                        ->where('g.group_id IN (?)', $groupRel)
                        ->where("DATE(r.start_time) BETWEEN '$dateFrom' AND '$dateTo' ")
			->where ( 'NOT ISNULL(r.stop_time)' )
                        ->group('u.user_id')
                        ->order('traffic_total DESC') /*order by total (up+down) is better -> move the + from php to sql*/
                        ->limit(50);
	        
	        $result[$v] = $db->fetchAll($select);
			
            foreach ($result[$v] as $key => $value) {

				$groupTotals[$v]['down_total'] += $value['down_total'];
				$groupTotals[$v]['up_total'] += $value['up_total'];
				$groupTotals[$v]['traffic_total'] += ($value['down_total'] + $value['up_total']);
            }
        }
        
        
        $tables = array();
        $graphics = array();
        

        
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
