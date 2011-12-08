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
class Report_Service_CodeTemplate_InternetConnectedCDevices extends Report_Service_CodeTemplate_Abstract {

    protected $_garden_sess = 'acct_garden_session';
    protected $_network_user = 'network_user';
    protected $_group = 'group';
    protected $_internet_sess = 'acct_internet_session';

    protected function getTemplate($groupIds, $data) {
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '';
       
        
        foreach ($result as $k => $v) { /*show header with groupname ($k==group_id)*/
        
	        $html .= '<table class="listing">';
	        $html .= '<tr><th>Group Name</th><th style="text-align: center;">Garden Users</th><th style="text-align: center;">Internet Connected Clients</th><th style="text-align: center;">MAC Authenticated</th></tr>';
	        $html .= '<tr><td><strong>Total</td><td style="text-align:right;"><strong>'.$groupTotals['garden'][$k].'</strong></td><td style="text-align:right;"><strong>'.$groupTotals['total'][$k].'</strong></td><td style="text-align:right;"><strong>'.(((int)$groupTotals['mac'][$k] > 0)?$groupTotals['mac'][$k]:0).'</strong></td></tr>';
	        foreach ($v as $key => $value) {
	        	$html .= '<tr><td>'.$value['garden']['group_name'].'</td><td style="text-align:right;">'.$value['garden']['cnt_by_group'].'</td><td style="text-align:right;">'.$value['normal']['cnt_by_group'].'</td><td style="text-align:right;">'.(($value['mac'] > 0)?$value['mac']:0).'</td></tr>';	
	        }
	        $html .= '<tr><td><strong>Total</td><td style="text-align:right;"><strong>'.$groupTotals['garden'][$k].'</strong></td><td style="text-align:right;"><strong>'.$groupTotals['total'][$k].'</strong></td><td style="text-align:right;"><strong>'.(((int)$groupTotals['mac'][$k] > 0)?$groupTotals['mac'][$k]:0).'</strong></td></tr>';
        }
        
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
        $iresult = array();
        
        foreach ($groupIds as $k => $v) {
        
        	$groupRel = $this->_getGroupRelations(array($v));
        	
        	$select = $db->select()
	        	->from(array('a' => 'acct_garden_session'), array('COUNT(*) as cnt_by_group'))
	        	->join(array('b' => 'acct_garden_roaming'), 'a.session_id = b.session_id')
	        	->join(array('c' => 'node'), 'b.node_id = c.node_id')
	        	->join(array('d' => 'group'), 'c.group_id = d.group_id', array('group_name' => 'name', 'group_id'))
	        	->where('d.group_id IN (?)', $groupRel)
	        	->where('DATE(a.start_time) BETWEEN ? AND ? ', $dateFrom, $dateTo)
	        	->where('NOT ISNULL(a.stop_time)')
	        	->group('d.group_id');
  			
        	$_gresult = $db->fetchAll($select);
        	
        	$gtotals[$v] = 0;
        	foreach ($_gresult as $key => $value) {
        	
        		$gtotals[$v] += $value['cnt_by_group']; 
        		$iresult[$v][$value['group_id']]['garden'] = $value;
        	}
        	
	        $select = $db->select()
		        ->from(array('a' => 'acct_internet_session'), array('COUNT(*) as cnt_by_group'))
		        ->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
		        ->join(array('c' => 'node'), 'b.node_id = c.node_id')
		        ->join(array('d' => 'group'), 'c.group_id = d.group_id', array('group_name' => 'name', 'group_id'))
		        ->where('d.group_id IN (?)', $groupRel)
	        	->where('DATE(a.start_time) BETWEEN ? AND ? ', $dateFrom, $dateTo)
		        ->where('NOT ISNULL(a.stop_time)')
		        ->group('d.group_id');
	        
	        $_iresult = $db->fetchAll($select);
	    	
	    	$groupTotals[$v] = 0;
	        foreach ($_iresult as $key => $value) {
	        	
	        	$groupTotals[$v] += $value['cnt_by_group'];
	        	$iresult[$v][$value['group_id']]['normal'] = $value;
	        }
	        

	        $select = $db->select()
		        ->from(array('a' => 'acct_internet_session'), array('COUNT(*) as cnt_by_group'))
		        ->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
		        ->join(array('c' => 'node'), 'b.node_id = c.node_id')
		        ->join(array('d' => 'group'), 'c.group_id = d.group_id', array('group_name' => 'name', 'group_id'))
		        ->where('d.group_id IN (?)', $groupRel)
	        	->where('DATE(a.start_time) BETWEEN ? AND ? ', $dateFrom, $dateTo)
		        ->where('NOT ISNULL(a.stop_time)')
		        ->where('(a.groupname = "MACAuthenticated" OR a.groupname = "Authenticated")')
		        ->group('d.group_id');
	        
	        $_mresult = $db->fetchAll($select);
	   		$maccount[$v] = 0;
	        foreach ($_mresult as $key => $value) {
	        
	        	$iresult[$v][$value['group_id']]['mac'] = $value['cnt_by_group'];
	        	
	        	$maccount[$v] += $value['cnt_by_group'];
	        }
        }
        
        
        return array ('data' => $iresult, 'totals' => array('garden' => $gtotals, 'total' => $groupTotals, 'mac' => $maccount) );
        
       
    }

}
