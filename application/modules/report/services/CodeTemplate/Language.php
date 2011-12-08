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
    
    protected function getTemplate($groupIds, $data) {
        //$groupRel = $this->_getGroupRelations($groupIds);
        
        $result = $data['data'];
        $groupTotals = $data['totals'];
        
        $html = '
        <script type="text/javascript">
	      google.load("visualization", "1", {packages:["corechart"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        var data = new google.visualization.DataTable();
	        data.addColumn("string", "Language");
	        data.addColumn("number", "Users Count");
	    ';
        	$html .= 'data.addRows('.count($result).');';
        	$j = 0;
	        foreach ($result as $key => $value) {
	        	$html .= 'data.setValue('.$j.', 0, "'.$key.'");';
	        	$html .= 'data.setValue('.$j.', 1, '.count($value).');';
	        	$j++;
	        }
	        
		$html .= '
	        var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
	        chart.draw(data, {width: 450, height: 300, title: "Users by language"});
	      }
	    </script>
        
        ';
        
        
        $html .= '<div id="chart_div"></div>';
        $html .= '<table class="listing">';
        $html .= '<tr><th>Network username</th><th style="text-align: center;">MAC</th><th style="text-align: center;">Language</th></tr>';
        
        foreach ($result as $k => $v) {            
            
            $html .= '<tr><td ><strong>'.$k.'</strong></td><td colspan="2"><strong>'.count($v).'</strong></td></tr>';
 
            foreach ($v as $key => $value) {
            	$html .= '<tr><td>'.$value['username'].'</td><td>'.$this->_getMac($value['user_mac']).'</td><td>'.$k.'</td></tr>'; 
            }
            
        }
		$html .= '</table>';
	
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
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
        
        return array('data' => $data, 'totals' => array());
    }

}