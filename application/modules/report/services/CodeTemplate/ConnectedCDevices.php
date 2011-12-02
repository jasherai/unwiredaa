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
class Report_Service_CodeTemplate_ConnectedCDevices extends Report_Service_CodeTemplate_Abstract {

    protected $_garden_sess = 'acct_garden_session';
    protected $_network_user = 'network_user';
    protected $_group = 'group';

    protected function getTemplate($groupIds, $data) {
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '';
        
        
        $html .= '<table class="listing">';
        $html .= '<tr><th>Group Name</th><th>Clients Connected</th></tr>';
        $html .= '<tr><th>Total</th><th style="text-align:right;">'.$data['totals'].'</th>';
        foreach ($result as $key => $value) {
        	$html .= '<tr><td>'.$value['group_name'].'</td><td style="text-align:right;">'.$value['cnt_by_group'].'</td>';	
        }
        $html .= '<tr><th>Total</th><th style="text-align:right;">'.$data['totals'].'</th>';
        
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = $db->select()
                ->from(array('c' => $this->_group), array('group_name' => 'name', 'group_id'))
                ->joinLeft(array('b' => $this->_network_user), 'b.group_id = c.group_id', array('username'))
                ->joinLeft(array('a' => $this->_garden_sess), 'a.user_ip = b.user_id AND a.start_time >= '.$dateFrom.' AND a.start_time <= '.$dateTo, array('COUNT(*) as cnt_by_group'))
                ->where('c.group_id IN (?)', $groupIds)
                ->group('c.group_id');
		
        $result = $db->fetchAll($select);

        
        
        $groupTotals = 0;
        
        foreach ($groupIds as $gid) {
            foreach ($result as $key => $value) {
                if ($gid == $value['group_id']) {
                	if ($value['username'] != '') {
                    	$groupTotals += $value['cnt_by_group'];
                    
                    	//$groupTotals['name'] = $value['group_name'];
                	} else {
                		$result[$key]['cnt_by_group'] = 0;
                	}
                 }
            }
        }
        
        return array('data' => $result, 'totals' => $groupTotals);
        
       
    }

}