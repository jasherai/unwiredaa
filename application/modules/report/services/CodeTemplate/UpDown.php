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

    protected $_inet_table = 'acct_internet_session';
    protected $_network_user = 'network_user';
    protected $_group = 'group';
    protected $_node = 'node';
    protected $_roaming_table = 'acct_internet_roaming';
    
    
    protected function getTemplate($groupIds, $data) {
        //$groupRel = $this->_getGroupRelations($groupIds);
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '';
        foreach ($groupTotals as $k => $v) {            
            
            $html .= '<table class="listing">';
            $html .= '<tr><th>Group / User Name</th><th style="text-align: center;">Total Bytes Up</th><th style="text-align: center;">Total Bytes Down</td></tr>';
            $htmlGroupTot = '<tr><td><strong>'.$v['total']['name'].'</strong></td><td style="text-align: right;"><strong>'.$groupTotals[$k]['total']['up'].'b</strong></td><td style="text-align: right;"><strong>'.$groupTotals[$k]['total']['down'].'b</strong></td></tr>';
            $html .= $htmlGroupTot;
            foreach ($v['ap'] as $kk => $vv) {
            	$html .= '<tr><td><strong><i>&nbsp;&nbsp;&nbsp;&nbsp;Node '.$vv['name'].' ('.$vv['mac'].')</strong></i></td><td style="text-align: right;"><strong>'.$vv['up'].'b</strong></td><td style="text-align: right;"><strong>'.$vv['down'].'b</strong></td></tr>';
            	
            	foreach ($result[$k][$kk] as $key => $value) {
            		$html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $value['username'] . ' <!--('.$value['user_id'].')--></td><td style="text-align: right;">'.$value['total_bytes_up'].'b</td><td style="text-align: right;">'.$value['total_bytes_down'].'b</td></tr>';
            	}
            	
            }
            
            
            
            /*
            foreach ($result[$k] as $key => $value) {
                if ($topgid == $value['group_id']) {
                    $html .= '<tr><td> ' . $value['username'] . ' <!--('.$value['user_id'].')--></td><td style="text-align: right;">'.$value['total_bytes_up'].'b</td><td style="text-align: right;">'.$value['total_bytes_down'].'b</td></tr>';
                 }
            }
            */
//             /$html .= $htmlGroupTot;
            $html .= '</table>';
        }
        

        //$html = '';
        
        
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
        $groupRel = $this->_getGroupRelations($groupIds);
        
        $select = $db->select()
                ->from(array('a' => $this->_inet_table), array('*', 'SUM(total_bytes_up) as total_bytes_up', 'SUM(total_bytes_down) as total_bytes_down'))
                ->join(array('b' => $this->_network_user), 'a.user_id = b.user_id', array('group_id', 'username'))
                ->join(array('c' => $this->_group), 'b.group_id = c.group_id', array('group_name' => 'name'))
                ->join(array('d' => $this->_roaming_table), 'a.session_id = d.session_id', array('node_id'))
                ->join(array('e' => $this->_node), 'd.node_id = e.node_id', array('node_name' => 'name', 'node_mac' => 'mac'))
                ->where('b.group_id IN (?)', $groupRel)
                ->where('a.start_time >= ?', $dateFrom)
                ->where('a.start_time <= ?', $dateTo)
                ->group('user_id');
		
        $result = $db->fetchAll($select);
		
        
        $data = array();
        foreach ($result as $key => $value) {
        	if (!isset($groupTotals[$value['group_id']])){
        		$groupTotals[$value['group_id']] = array('total' => array('up' => 0, 'down' => 0, 'name' => ''));
        	}
        	
        	$groupTotals[$value['group_id']]['total']['up'] += $value['total_bytes_up'];
        	$groupTotals[$value['group_id']]['total']['down'] += $value['total_bytes_down'];
        	$groupTotals[$value['group_id']]['total']['name'] = $value['group_name'];
        	
        	if (!isset($groupTotals[$value['group_id']]['ap'][$value['node_id']])){
        		$groupTotals[$value['group_id']]['ap'][$value['node_id']] = array('up' => 0, 'down' => 0, 'name' => '');
        	}
        	
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['up'] += $value['total_bytes_up'];
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['down'] += $value['total_bytes_down'];
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['name'] = $value['node_name'];
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['mac'] = $value['node_mac'];
        	
        	$data[$value['group_id']][$value['node_id']][] = $value;
        }
        
        return array('data' => $data, 'totals' => $groupTotals);
    }

}