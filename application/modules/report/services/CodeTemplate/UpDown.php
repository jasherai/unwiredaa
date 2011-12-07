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
	$total_up=$total_down=0;
        foreach ($groupTotals as $k => $v) {            
            
            $html .= '<table class="listing">';
            $html .= '<tr><th>Group / User Name</th><th style="text-align: center;">Download</th><th style="text-align: center;">Upload</td><th style="text-align: center;">Total</td></tr>';
            $htmlGroupTot = '<tr><td><strong>'.$v['total']['name'].'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($groupTotals[$k]['total']['bytes_down']).'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($groupTotals[$k]['total']['bytes_up']).'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($groupTotals[$k]['total']['bytes_down']+$groupTotals[$k]['total']['bytes_up']).'</strong></td></tr>';
            $html .= $htmlGroupTot;
	    $total_up+=$groupTotals[$k]['total']['bytes_up'];
	    $total_down+=$groupTotals[$k]['total']['bytes_down'];
            foreach ($v['ap'] as $kk => $vv) {
            	$html .= '<tr><td><strong><i>&nbsp;&nbsp;&nbsp;&nbsp;Node '.$vv['name'].' ('.$vv['mac'].')</strong></i></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($vv['bytes_down']).'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($vv['bytes_up']).'</strong></td><td style="text-align: right;"><strong>'.$this->_convertTraffic($vv['bytes_down']+$vv['bytes_up']).'</strong></td></tr>';
            	
            	foreach ($result[$k][$kk] as $key => $value) {
            		$html .= '<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $value['username'] . ' </td><td style="text-align: right;">'.$this->_convertTraffic($value['bytes_down']).'</td><td style="text-align: right;">'.$this->_convertTraffic($value['bytes_up']).'</td><td style="text-align: right;"><strong>'.$this->_convertTraffic($value['bytes_down']+$value['bytes_up']).'</strong></td></tr>';
            	}
            	
            }
            
	 
            $html .= '</table>';
        }

/*there shouldn`t be seperate tabels as the layout looks bad,. */

/*but as there already are, we continue with it,.*/
        return '<table class="listing"><tr><th width=50%>Total</th><th>Total Download</th><th>Total Upload</th><th>Total</th></tr>
<tr><td></td><td><strong>'.$this->_convertTraffic($total_up).'</strong></td><td><strong>'.$this->_convertTraffic($total_down).'</strong></td><td><strong>'.$this->_convertTraffic($total_up+$total_down).'</strong></td></tr></table>'.$html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
        $groupRel = $this->_getGroupRelations($groupIds);
        
        $select = $db->select()
                ->from(array('a' => 'acct_internet_roaming'), array('*', 'SUM(a.total_bytes_up) as bytes_up', 'SUM(a.total_bytes_down) as bytes_down'))
                ->join(array('b' => 'acct_internet_session'), 'a.session_id = b.session_id', array())
                ->join(array('c' => 'network_user'), 'b.user_id = c.user_id', array('username'), array())
                ->join(array('d' => 'node'), 'a.node_id = d.node_id', array('node_name' => 'name', 'node_mac' => 'mac'))
                ->join(array('e' => 'group'), 'd.group_id = e.group_id', array('group_id', 'group_name' => 'name'))
                ->where('e.group_id IN (?)', $groupRel)
                ->where('a.start_time >= ?', $dateFrom)
                ->where('a.start_time <= ?', $dateTo)
                ->where('NOT ISNULL(a.stop_time)')
                ->group('a.session_id');
		
        $result = $db->fetchAll($select);
   
        
        $data = array();
        foreach ($result as $key => $value) {
        	if (!isset($groupTotals[$value['group_id']])){
        		$groupTotals[$value['group_id']] = array('total' => array('bytes_up' => 0, 'bytes_down' => 0, 'name' => ''));
        	}
        	
        	$groupTotals[$value['group_id']]['total']['bytes_up'] += $value['bytes_up'];
        	$groupTotals[$value['group_id']]['total']['bytes_down'] += $value['bytes_down'];
        	$groupTotals[$value['group_id']]['total']['name'] = $value['group_name'];
        	
        	if (!isset($groupTotals[$value['group_id']]['ap'][$value['node_id']])){
        		$groupTotals[$value['group_id']]['ap'][$value['node_id']] = array('bytes_up' => 0, 'bytes_down' => 0, 'name' => '');
        	}
        	
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['bytes_up'] += $value['bytes_up'];
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['bytes_down'] += $value['bytes_down'];
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['name'] = $value['node_name'];
        	$groupTotals[$value['group_id']]['ap'][$value['node_id']]['mac'] = $value['node_mac'];
        	
        	if (isset($data[$value['group_id']][$value['node_id']][$value['username']])) {
        		$data[$value['group_id']][$value['node_id']][$value['username']]['bytes_up'] += $value['bytes_up'];
        		$data[$value['group_id']][$value['node_id']][$value['username']]['bytes_down'] += $value['bytes_down'];
        	} else {
        		$data[$value['group_id']][$value['node_id']][$value['username']] = $value;
        	}
        }
        
        return array('data' => $data, 'totals' => $groupTotals);
    }

}
