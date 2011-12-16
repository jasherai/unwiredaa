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
class Report_Service_CodeTemplate_OperatingSystem extends Report_Service_CodeTemplate_Abstract {

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
                ->where('a.user_agent != ""')
                ->where('DATE(a.time) >= ?', $dateFrom)
                ->where('DATE(a.time) <= ?', $dateTo)                
                ->group('a.username');
		
        $result = $db->fetchAll($select);
   
        $data = array();
        foreach ($result as $key => $value) {
        	if (stripos($value['user_agent'], 'Android') !== false) {
        		$data['Android'][] = $value;
        	} elseif (stripos($value['user_agent'], 'iPhone') !== false || stripos($value['user_agent'], 'iPad') !== false) {
        		$data['iOS'][] = $value;
        	} elseif (stripos($value['user_agent'], 'BlackBerry') !== false) {
        		$data['Blackberry'][] = $value;
        	} elseif (stripos($value['user_agent'], 'Windows NT') !== false) {
        		$data['Windows'][] = $value;
        	} elseif (stripos($value['user_agent'], 'Windows Phone') !== false) {
        		$data['WindowsPhone'][] = $value;
        	} elseif (stripos($value['user_agent'], 'Linux') !== false && stripos($value['user_agent'], 'arm') !== false) {
        		$data['LinuxMobile'][] = $value;
        	} elseif (stripos($value['user_agent'], 'Linux') !== false) {
        		$data['Linux'][] = $value;
        	} elseif (stripos($value['user_agent'], 'Symbian') !== false) {
        		$data['Symbian'][] = $value;
        	} else {
        		$data['Other'][] = $value;
        	}
        }
        
        foreach ($data as $key => $value):
       		$graphics[] = array($key, count($value));
        endforeach;
        
        $table = array(
        		'colDefs' => array(
        				array(
        						'report_result_os', array('name' => 'report_result_users', 'colspan' => 2)
        				),
        		),
        );
        
        ksort($data);
        
        foreach ($data as $key => $value) {
        	$table['rows'][] = array(
        			'data' => array($key, array('data' => count($value), 'colspan' => 2)),
        			'class' => array('bold', 'bold right')
        	);
        	foreach ($value as $k => $v) {
        		$table['rows'][] = array(
        			'data' => array($v['username'], $this->_getMac($v['user_mac']), $key)
        		);
        	}
        }
        
        $tables[] = $table;
        
        $result = array(
        		'graphics' => array(
        				array(
        						'name' => 'report_result_top_oses',
        						'type' => 'piechart',
        						'headers' => array('report_result_os', 'report_result_users'),
        						'rows' => $graphics
        				),
        		),
        		'tables' => $tables
        );
        
        return $result;
        
        //return array('data' => $data, 'totals' => array());
    }

}