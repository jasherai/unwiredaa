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
class Reports_Service_CodeTemplate_AccessPointsCount extends Reports_Service_CodeTemplate_Abstract {

   
    public function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$groupTotals = array();
		
        foreach ($groupIds as $k => $v) {
        	
        	
        	$groupTotals[$v] = array('cnt' => 0, 'offline_cnt' => 0, 'online_cnt' => 0);
	        $groupRel = $this->_getGroupRelations(array($v));
	        
	        $select = $db->select()
	                ->from(array('a' => 'node')) 
	                ->join(array('b' => 'group'), 'b.group_id = a.group_id', array('group_id', 'name as group_name'))
	                ->where('b.group_id IN (?)', $groupRel)
			->order(array('b.name', 'a.name'));
			
	             
	        $result[$v] = $db->fetchAll($select);
			
            foreach ($result[$v] as $key => $value) {
                
				$groupTotals[$v]['cnt'] += 1;
				if ($value['online_status'] == 1){
					$groupTotals[$v]['online_cnt'] += 1;
				}else{
					$groupTotals[$v]['offline_cnt'] += 1;
				}
                    
				//$groupTotals[$v]['name'] = $value['name'];
            }
        }
        
        $counts = array('online' => 0, 'offline' => 0, 'planning' => 0);
        foreach ($groupTotals as $k => $v) {
        	foreach ($result[$k] as $key => $value) {
        		if ($value['status'] == 'enabled') {
        			if ($value['online_status'] == 1) {
        				$counts['online']++;
        			} else {
        				$counts['offline']++;
        			}
        		} else {
        			$counts['planning']++;
        		}
        	}
        }
        
        
        $tables = array();
        $graphics = array();
        
        foreach ($counts as $key => $value): 
        	$graphics[] = array('report_result_'.$key, $value);
       	endforeach;
       	
       	foreach ($groupTotals as $k => $v) {
       		$table = array(
       			'colDefs' => array(
       				array(
       					'report_device_group', 'report_device_name', 'report_device_mac', 'report_result_ap_status'
       				) 
       			)
       		);
       		
       		foreach ($result[$k] as $key => $value) {
       			$table['rows'][] = array(
       					'data' => array($value['group_name'], $value['name'], $value['mac'], 
       							array('data' => (($value['status'] == 'enabled')?($value['online_status'] == 1 ? 'report_result_online': 'report_result_offline'):'report_result_planning'), 'translatable' => true)
       				)
       			);
       		}
       		
       		$tables[] = $table;
       	}
        
        $data = array(
			'graphics' => array(
				array(
					'name' => 'report_status_ap_count',
					'type' => 'PieChart',
					'headers' => array('report_result_status', 'report_result_ap_count'),
					'rows' => $graphics
				),
			),
			'tables' => $tables
		);
		
		
		return $data;
       
    }

}
