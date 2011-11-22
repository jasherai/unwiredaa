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
class Report_Service_CodeTemplate_PartConnectedCDevices extends Report_Service_CodeTemplate_Abstract {

    protected $_garden_sess = 'acct_garden_session';
    protected $_network_user = 'network_user';
    protected $_group = 'group';
    protected $_internet_sess = 'acct_internet_session';

    protected function getTemplate($groupIds, $data) {
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '';
        
        
        $html .= '<table border=1>';
        $html .= '<tr><th>Group Name</th><th>Clients Connected Without Internet</th></tr>';
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
		
        $_gresult = $db->fetchAll($select);
        
        $gresult = array();
        foreach ($_gresult as $key => $value) {
        	if ($value['username'] == '') {
        		$value['cnt_by_group'] = 0;	
        	}
        	
        	$gresult[$value['group_id']] = $value;
        }
        
        

        $select = $db->select()
	        ->from(array('c' => $this->_group), array('group_name' => 'name', 'group_id'))
	        ->joinLeft(array('b' => $this->_network_user), 'b.group_id = c.group_id', array('username'))
	        ->joinLeft(array('a' => $this->_internet_sess), 'a.user_id = b.user_id AND a.start_time >= '.$dateFrom.' AND a.start_time <= '.$dateTo, array('COUNT(*) as cnt_by_group'))
	        ->where('c.group_id IN (?)', $groupIds)
	        ->group('c.group_id');
        
        $_iresult = $db->fetchAll($select);
    	$iresult = array();
        foreach ($_iresult as $key => $value) {
        	
        	if ($value['username'] == '') {
        		$value['cnt_by_group'] = 0;
        	}
        	
        	$iresult[$value['group_id']] = $value;
        }
        
        
        $groupTotals = 0;
        $result = array();
        foreach ($iresult as $key => $value) {
        	
        	$groupTotals += ($gresult[$key]['cnt_by_group']-$iresult[$key]['cnt_by_group']); //@todo: they shouldn't be negative, make sure
        	
        	$value['cnt_by_group'] = $gresult[$key]['cnt_by_group']-$iresult[$key]['cnt_by_group'];
        	print_r($value);
        	$result[$value['group_id']] = $value;
        }
        
        
        return array ('data' => $result, 'totals' => $groupTotals );
        
       
    }

}