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
        
        
        $html .= '<table border=1>';
        $html .= '<tr><th>Group Name</th><th style="text-align: center;">Internet Connected Clients</th><th style="text-align: center;">MAC Authenticated</th></tr>';
        $html .= '<tr><th>Total</th><th style="text-align:right;">'.$data['totals']['total'].'</th><th style="text-align:right;">'.$data['totals']['mac'].'</th>';
        foreach ($result as $key => $value) {
        	$html .= '<tr><td>'.$value['group_name'].'</td><td style="text-align:right;">'.$value['cnt_by_group'].'</td><td style="text-align:right;">'.$value['mac'].'</td>';	
        }
        $html .= '<tr><th>Total</th><th style="text-align:right;">'.$data['totals']['total'].'</th><th style="text-align:right;">'.$data['totals']['mac'].'</th>';
        
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
        $select = $db->select()
	        ->from(array('c' => $this->_group), array('group_name' => 'name', 'group_id'))
	        ->joinLeft(array('b' => $this->_network_user), 'b.group_id = c.group_id', array('username'))
	        ->joinLeft(array('a' => $this->_internet_sess), 'a.user_id = b.user_id AND a.start_time >= '.$dateFrom.' AND a.start_time <= '.$dateTo, array('COUNT(*) as cnt_by_group'))
	        ->where('c.group_id IN (?)', $groupIds)
	        ->group('c.group_id');
        
        $_iresult = $db->fetchAll($select);
    	$iresult = array();
    	$groupTotal = 0;
        foreach ($_iresult as $key => $value) {
        	
        	if ($value['username'] == '') {
        		$value['cnt_by_group'] = 0;
        	}
        	$groupTotals += $value['cnt_by_group'];
        	$iresult[$value['group_id']] = $value;
        }
        
        $select = $db->select()
	        ->from(array('c' => $this->_group), array('group_name' => 'name', 'group_id'))
	        ->joinLeft(array('b' => $this->_network_user), 'b.group_id = c.group_id', array('username'))
	        ->joinLeft(array('a' => $this->_internet_sess), 'a.user_id = b.user_id AND a.start_time >= '.$dateFrom.' AND a.start_time <= '.$dateTo.' AND groupname = "MACAuthenticated"', array('COUNT(*) as cnt_by_group'))
	        ->where('c.group_id IN (?)', $groupIds)
	        ->group('c.group_id');
        
        $_mresult = $db->fetchAll($select);
   		$maccount = 0;
        foreach ($_mresult as $key => $value) {
        
        	if ($value['username'] == '') {
        		$value['cnt_by_group'] = 0;
        	}
        
        	$iresult[$value['group_id']]['mac'] = $value['cnt_by_group'];
        	$maccount += $value['cnt_by_group'];
        }
        
        
        return array ('data' => $iresult, 'totals' => array('total' => $groupTotals, 'mac' => $maccount) );
        
       
    }

}