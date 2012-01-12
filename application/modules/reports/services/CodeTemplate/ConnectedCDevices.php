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
class Reports_Service_CodeTemplate_ConnectedCDevices extends Reports_Service_CodeTemplate_Abstract {

    protected $_garden_sess = 'acct_garden_session';
    protected $_network_user = 'network_user';
    protected $_group = 'group';
    protected $_internet_sess = 'acct_internet_session';

    protected function getTemplate($groupIds, $data) {
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '';
        foreach ($result as $k => $v) {
        
	        $html .= '<table class="listing">';
	        $html .= '<tr><th>Group Name</th><th>Network name (Username/MAC Address)</th></tr>';
	       
	        $html .= '<tr><th><strong>Guests</strong></th><th><strong>'.$groupTotals['total'][$k].'</th></strong></tr>';
	        foreach ($v['normal'] as $key => $value) {
	        	$html .= '<tr><td>'.$value['group_name'].'</td><td>'.$value['username'].'</td></tr>';
	        }
	        
	        $html .= '<tr><th><strong>MAC Authenticated</strong></td><th><strong>'.$groupTotals['mac'][$k].'</th></strong></tr>';
	        foreach ($v['mac'] as $key => $value) {
	        	$html .= '<tr><td>'.$value['group_name'].'</td><td>'.$value['username'].'</td></tr>';
	        }
        }
        //print_r($data);

        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	
        foreach ($groupIds as $k => $v) {
        
        	$groupRel = $this->_getGroupRelations(array($v));
        	 
	        $select = $db->select()
	        	->from(array('a' => 'acct_internet_session'), array('*'))
	        	->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
	        	->join(array('c' => 'node'), 'b.node_id = c.node_id')
	        	->join(array('d' => 'group'), 'c.group_id = d.group_id', array('group_name' => 'name', 'group_id'))
	        	->joinLeft(array('e' => 'network_user'), 'a.user_id = e.user_id', array('username'))
	        	->where('a.start_time >= ?', $dateFrom)
	        	->where('a.start_time <= ?', $dateTo)
		        ->where('a.groupname = "Guest"')
		        ->where('d.group_id IN (?)', $groupRel);

	        $_iresult = $db->fetchAll($select);
	        
	    	$iresult = array();
	    	$groupTotals[$v] = 0;
	        foreach ($_iresult as $key => $value) {
	        	if (!isset($iresult[$v]['normal'][$value['username']])) {
	        		$groupTotals[$v] ++;
	        		$iresult[$v]['normal'][$value['username']] = $value;
	        	}
	        }
	        

	        $select = $db->select()
		        ->from(array('a' => 'acct_internet_session'), array('*'))
		        ->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
		        ->join(array('c' => 'node'), 'b.node_id = c.node_id')
		        ->join(array('d' => 'group'), 'c.group_id = d.group_id', array('group_name' => 'name', 'group_id'))
		        ->joinLeft(array('e' => 'network_user'), 'a.user_id = e.user_id', array('username'))
		        ->where('d.group_id IN (?)', $groupRel)
		        ->where('a.start_time >= ?', $dateFrom)
		        ->where('a.start_time <= ?', $dateTo)
		        ->where('(a.groupname = "MACAuthenticated" OR a.groupname = "Authenticated")');
			
	        $_mresult = $db->fetchAll($select);
	   		$maccount[$v] = 0;
	        foreach ($_mresult as $key => $value) {
	        
	        	if (!isset($iresult[$v]['mac'][$value['username']])) {
	        		$iresult[$v]['mac'][$value['username']] = $value;
	        		$maccount[$v] ++;
	        	}
	        	
	        	
	        }
	        
        }
        
        
        return array ('data' => $iresult, 'totals' => array('total' => $groupTotals, 'mac' => $maccount) );
        
       
    }

}