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
        
        	$groupRel = $this->_getGroupRelations($groupIds);
        	
	        $select = $db->select()
		        ->from(array('c' => $this->_group), array('group_name' => 'name', 'group_id'))
		        ->join(array('b' => $this->_network_user), 'b.group_id = c.group_id', array('username'))
		        ->joinLeft(array('a' => $this->_internet_sess), 'a.user_id = b.user_id AND a.start_time >= '.$dateFrom.' AND a.start_time <= '.$dateTo.' AND groupname = "Guest"', array('*'))
		        ->where('c.group_id IN (?)', $groupRel);
	        
	        $_iresult = $db->fetchAll($select);
	    	$iresult = array();
	    	$groupTotals[$v] = 0;
	        foreach ($_iresult as $key => $value) {
	        	
	        	if ($value['username'] == '') {
	        		$value['cnt_by_group'] = 0;
	        	}
	        	$groupTotals[$v] ++;
	        	$iresult[$v]['normal'][] = $value;
	        }
	        

	        $select = $db->select()
		        ->from(array('c' => $this->_group), array('group_name' => 'name', 'group_id'))
		        ->join(array('b' => $this->_network_user), 'b.group_id = c.group_id', array('username'))
		        ->joinLeft(array('a' => $this->_internet_sess), 'a.user_id = b.user_id AND a.start_time >= '.$dateFrom.' AND a.start_time <= '.$dateTo.' AND groupname = "MACAuthenticated"', array('*'))
		        ->where('c.group_id IN (?)', $groupRel)
		        ->group('c.group_id');
	        
	        $_mresult = $db->fetchAll($select);
	   		$maccount[$v] = 0;
	        foreach ($_mresult as $key => $value) {
	        
	        	if ($value['username'] == '') {
	        		$value['cnt_by_group'] = 0;
	        	}
	        
	        	$iresult[$v]['mac'][] = $value;
	        	
	        	$maccount[$v] ++;
	        }
	        
        }
        
        
        return array ('data' => $iresult, 'totals' => array('total' => $groupTotals, 'mac' => $maccount) );
        
       
    }

}