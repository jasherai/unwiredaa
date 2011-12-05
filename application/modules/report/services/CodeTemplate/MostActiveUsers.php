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

    protected $_node = 'node';
    protected $_group = 'group';
    protected $_internet_sess = 'acct_internet_session';
    protected $_network_user = 'network_user';

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
			$html .= '<tr><th>Device Name</th><th>Download</th><th>Upload</th></tr>';
			
        	
			$htmlGroupTot = '<tr><td><strong>Total: </strong></td><td style="text-align: right;"><strong>' . $groupTotals[$k]['down_total'] .'b</strong></td><td style="text-align: right;"><strong>' . $groupTotals[$k]['up_total'] .'b</strong></td></tr>';
	        $html .= $htmlGroupTot;
	            
			foreach ($result[$k] as $key => $value) {
				$html .= '<tr><td>'.$value['username'].'</td><td style="text-align: right;">'.$value['down_total'].'b</td><td style="text-align: right;">'.$value['up_total'].'b</td></tr>';
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
        	
        	
        	$groupTotals[$v] = array('cnt' => 0, 'down_total' => 0, 'up_total' => 0);
	        $groupRel = $this->_getGroupRelations(array($v));
	        
	        $select = $db->select()
	                ->from(array('a' => $this->_internet_sess), 'SUM(a.total_bytes_up) as up_total, SUM(a.total_bytes_down) as down_total')
	                ->join(array('c' => $this->_network_user), 'a.user_id = c.user_id')
	                ->join(array('b' => $this->_group), 'b.group_id = c.group_id', array('group_id', 'name as group_name'))
	                ->where('b.group_id IN (?)', $groupRel)
	                ->group('a.user_id')
	                ->order('down_total DESC')
	                ->limit(50);
	        
	        $result[$v] = $db->fetchAll($select);
			
            foreach ($result[$v] as $key => $value) {

				$groupTotals[$v]['down_total'] += $value['down_total'];
				$groupTotals[$v]['up_total'] += $value['up_total'];
            }
        }
        
        return array('data' => $result, 'totals' => $groupTotals);
        
       
    }

}