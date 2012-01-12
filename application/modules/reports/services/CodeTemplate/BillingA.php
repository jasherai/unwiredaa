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
class Reports_Service_CodeTemplate_BillingA extends Reports_Service_CodeTemplate_Abstract {

	private function prepareGroupTable($db)
	{
		/*build group structure*/
		/*create temporary table*/
		/*fill with node_id report_part_id relations (depending on report type (summarizeable), and planned depth)*/
	}
   
    public function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

	$tblname=prepareGroupTable($db);

	$groupTotals = array();
		
        foreach ($groupIds as $k => $v) {
        	
        	
        	$groupTotals[$v] = array('cnt' => 0, 'offline_cnt' => 0, 'online_cnt' => 0);
	        $groupRel = $this->_getGroupRelations(array($v));
	        

	        $result[$v] = $db->fetchAll("SELECT count(*) as cnt, `b`.`group_id`, `b`.`name` AS `group_name` 
FROM `node` AS `a` INNER JOIN `group` AS `b` ON b.group_id = a.group_id 
WHERE (b.group_id IN (".implode(",",$groupRel).")) AND (billable = 1) AND (a.status = 'enabled') 
GROUP BY `b`.`group_id` ORDER BY group_name ASC");
			
            foreach ($result[$v] as $key => $value) {
                
				$groupTotals[$v]['cnt'] += 1;
				if ($value['online_status'] == 1){
					$groupTotals[$v]['online_cnt'] += 1;
				}else{
					$groupTotals[$v]['offline_cnt'] += 1;
				}
                    
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
       					'report_device_group', 'billable_aps'
       				) 
       			)
       		);
       		
       		foreach ($result[$k] as $key => $value) {
       			$table['rows'][] = array(
       					'data' => array($value['group_name'], 
       							array('data' => $value['cnt'], 'translatable' => false)
       				)
       			);
       		}
       		
       		$tables[] = $table;
       	}
        
        $data = array(/*
			'graphics' => array(
				array(
					'name' => 'report_status_ap_count',
					'type' => 'piechart',
					'headers' => array('report_result_status', 'report_result_ap_count'),
					'rows' => $graphics
				),
			),*/
			'tables' => $tables
		);
		
		
		return $data;
       
    }

}
