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
class Report_Service_CodeTemplate_Language extends Report_Service_CodeTemplate_Abstract {
   
    public function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
        $groupRel = $this->_getGroupRelations($groupIds);
        
        $select = $db->select()
                ->from(array('a' => 'auth_log'))
                ->join(array('b' => 'acct_internet_session'), 'a.internet_session_id = b.session_id')
                ->join(array('c' => 'acct_internet_roaming'), 'b.session_id = c.session_id')
                ->join(array('d' => 'node'), 'c.node_id = d.node_id', array('node_name' => 'name', 'node_mac' => 'mac'))
                ->join(array('e' => 'group'), 'd.group_id = e.group_id', array('group_id', 'group_name' => 'name'))
                ->join(array('f' => 'network_user'), 'b.user_id = f.user_id', array('username'))
                ->where('d.group_id IN (?)', $groupRel)
        		->where('a.accept_language != ""')
                ->where('DATE(a.time) >= ?', $dateFrom)
                ->where('DATE(a.time) <= ?', $dateTo)                  
                ->group('a.username');
		
        $result = $db->fetchAll($select);
   
        $data = array();
        foreach ($result as $key => $value) {
        	$data[strtoupper(substr($value['accept_language'], 0, 5))][] = $value;
        }
        
        ksort($data);
        
        $graphics = array();
        $tables = array();
        
        foreach ($data as $key => $value):
        	$graphics[] = array($key, count($value));
        endforeach;
        
        $table = array(
        		'colDefs' => array(
        				array(
        						'report_result_language', 'report_result_users'
        				)
        		),
        );
        
        foreach ($data as $key => $value) {
        	$table['rows'][] = array(
        			'data' => array($key, count($value)),
        			'class' => array('', 'right')
        	);
        }
        
        $tables[] = $table;
        
        $result = array(
        		'graphics' => array(
        				array(
        						'name' => 'report_result_top_languages',
        						'type' => 'piechart',
        						'headers' => array('report_result_language', 'report_result_users'),
        						'rows' => $graphics
        				),
        		),
        		'tables' => $tables
        );
        
        return $result;
        
    }

}