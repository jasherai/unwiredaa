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
class Report_Service_CodeTemplate_Domains extends Report_Service_CodeTemplate_Abstract {

    //protected $_node = 'node';
    //protected $_group = 'group';
    //protected $_internet_sess = 'acct_internet_session';
    //protected $_network_user = 'network_user';

    protected function getTemplate($groupIds, $data) {
        
        $result = $data['data'];
        $groupTotals = $data['totals'];

        $html = '
        <script type="text/javascript">
	      google.load("visualization", "1", {packages:["corechart"]});
	      google.setOnLoadCallback(drawChart);
	      function drawChart() {
	        var data = new google.visualization.DataTable();
	        data.addColumn("string", "Vendor");
	        data.addColumn("number", "Users Count");
	    ';
        	$html .= 'data.addRows('.count($result).');';
        	$j = 0;
	        foreach ($result as $key => $value) {
	        	$html .= 'data.setValue('.$j.', 0, "'.$value['url'].'");';
	        	$html .= 'data.setValue('.$j.', 1, '.$value['count'].');';
	        	$j++;
	        }
	        
		$html .= '
	        var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
	        chart.draw(data, {width: 450, height: 300, title: "Users by Vendor"});
	      }
	    </script>
        
        ';
        
        
        $html .= '<div id="chart_div"></div>';
        
        $html .= '<table class="listing">';
        $html .= '<tr><th>Domain</th><th>User Count</th></tr>';
        foreach ($result as $k => $v) {
			$html .= '<tr><td>'.$v['url'].'</td><td>'.$v['count'].'</td></tr>';
        }
        $html .= '</table><br/>';
        //}
		
        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$groupTotals = array();
		
        
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
		                ->group('a.internet_session_id');
	        
		$items = $db->fetchAll($select);
		$result = array();
		foreach ($items as $key => $value) {
			$res = preg_match('!http://([^/]+)/!', $value['user_url'], $matches);
			if (!isset($result[$matches[1]])) {
				$result[$matches[1]]['url'] = $matches[1];
				$result[$matches[1]]['count'] = 1;
			} else {
				$result[$matches[1]]['count']++;
			}
		}
		
		usort($result, function($a, $b) {
			if ($a['count'] == $b['count']) {
				return 0;
			}
			return ($a['count'] > $b['count']) ? -1 : 1;
		});
        
        return array('data' => $result, 'totals' => $groupTotals);
        
       
    }

}
