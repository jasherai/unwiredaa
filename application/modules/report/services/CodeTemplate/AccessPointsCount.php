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
class Report_Service_CodeTemplate_AccessPointsCount extends Report_Service_CodeTemplate_Abstract {

    protected $_node = 'node';
    protected $_group = 'group';

    protected function getTemplate($groupIds, $data) {
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        
        /*
        foreach ($groupIds as $gid) {
            if (!isset($groupTotals[$gid]) || $groupTotals[$gid]['cnt'] == 0){
                continue;
            }
        */

        $html = '';
        foreach ($groupTotals as $k => $v) {
			$html .= '<table class="listing">'; 
			$html .= '<tr><th>Device Group</th><th>Device Name</th><th>Device Mac</th><th>AP Status</th></tr>';
        
			$htmlGroupTot = '<tr><td colspan="3"><strong>Total: </strong></td><td><strong>' . $v['cnt'] .'</strong></td></tr>';
	        $html .= $htmlGroupTot;
	            
			foreach ($result[$k] as $key => $value) {
				$html .= '<tr><td>'.$value['group_name'].'</td><td> ' . $value['name'] . '</td><td>'.$value['mac'].'</td><td>'.(($value['status'] == 'enabled')?($value['online_status'] == 1 ? 'Online': 'Offline'):'Planning').'</td></tr>';
			}
	            
			$html .= $htmlGroupTot;
			$html .= '</table><br/>';
        }
        
        //}

        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$groupTotals = array();
		
        foreach ($groupIds as $k => $v) {
        	
        	
        	$groupTotals[$v] = array('cnt' => 0, 'offline_cnt' => 0, 'online_cnt' => 0);
	        $groupRel = $this->_getGroupRelations(array($v));
	        
	        $select = $db->select()
	                ->from(array('a' => $this->_node))
	                ->join(array('b' => $this->_group), 'b.group_id = a.group_id', array('group_id', 'name as group_name'))
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
        
        return array('data' => $result, 'totals' => $groupTotals);
        
       
    }

}
