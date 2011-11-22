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
    
    protected function getTemplate($groupIds, $data) {
        //$groupRel = $this->_getGroupRelations($groupIds);
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '';
        
        foreach ($groupIds as $topgid) {
            
            if (!isset($groupTotals[$topgid]) || ($groupTotals[$topgid]['up'] == 0 && $groupTotals[$topgid]['down'] == 0)){
                continue;
            }
            
            $html .= '<table border=1>';
            $html .= '<tr><th>Group / User Name</th><th style="text-align: center;">Total Bytes Up</th><th style="text-align: center;">Total Bytes Down</td></tr>';
            $htmlGroupTot = '<tr><td><strong>'.$groupTotals[$topgid]['name'].'</strong></td><td style="text-align: right;"><strong>'.$groupTotals[$topgid]['up'].'</strong></td><td style="text-align: right;"><strong>'.$groupTotals[$topgid]['down'].'</strong></td></tr>';
            $html .= $htmlGroupTot;
            
            foreach ($result as $key => $value) {
                if ($topgid == $value['group_id']) {
                    $html .= '<tr><td> ' . $value['username'] . ' <!--('.$value['user_id'].')--></td><td style="text-align: right;">'.$value['total_bytes_up'].'</td><td style="text-align: right;">'.$value['total_bytes_down'].'</td></tr>';
                 }
            }
            
            $html .= $htmlGroupTot;
            $html .= '</table>';
        }
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $groupRel = $this->_getGroupRelations($groupIds);
        
        $select = $db->select()
                ->from(array('a' => $this->_inet_table), array('*', 'SUM(total_bytes_up) as total_bytes_up', 'SUM(total_bytes_down) as total_bytes_down'))
                ->join(array('b' => $this->_network_user), 'a.user_id = b.user_id', array('group_id', 'username'))
                ->join(array('c' => $this->_group), 'b.group_id = c.group_id', array('group_name' => 'name'))
                ->where('b.group_id IN (?)', $groupRel)
                ->where('a.start_time >= ?', $dateFrom)
                ->where('a.start_time <= ?', $dateTo)
                ->group('user_id');

        $result = $db->fetchAll($select);

        $groupTotals = array();
        
        foreach ($groupRel as $gid => $topgid) {
            if (!isset($groupTotals[$topgid])){
                $groupTotals[$topgid] = array('up' => 0, 'down' => 0, 'name' => '');
            }
            foreach ($result as $key => $value) {
                if ($gid == $value['group_id']) {
                    $groupTotals[$topgid]['up'] += $value['total_bytes_up'];
                    $groupTotals[$topgid]['down'] += $value['total_bytes_down'];
                    $groupTotals[$topgid]['name'] = $value['group_name'];
                 }
            }
        }
        
        return array('data' => $result, 'totals' => $groupTotals);
    }

}