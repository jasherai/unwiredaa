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
class Report_Service_CodeTemplate_TopDNS extends Report_Service_CodeTemplate_Abstract {

	protected function getTemplate($groupIds, $data) {

	        $result = $data['data'];
	        $total = $data['total'];
	        $tlds = $data['tlds'];

        	$html = '
	        <script type="text/javascript">
		      google.load("visualization", "1", {packages:["corechart"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data1 = new google.visualization.DataTable();
		        var data2 = new google.visualization.DataTable();
		        data1.addColumn("string", "Domain");
		        data1.addColumn("number", "Requests");
		        data2.addColumn("string", "TLD");
		        data2.addColumn("number", "Requests");
		    ';
	        	$html .= 'data1.addRows('.count($result).');';
	        	$j = 0;
		        foreach ($result as $key => $value) {
		        	$html .= 'data1.setValue('.$j.', 0, "'.$key.'");';
		        	$html .= 'data1.setValue('.$j.', 1, '.$value.');';
		        	$j++;
		        }

	        	$html .= 'data2.addRows('.count($tlds).');';
	        	$j = 0;
		        foreach ($tlds as $key => $value) {
		        	$html .= 'data2.setValue('.$j.', 0, "'.$key.'");';
		        	$html .= 'data2.setValue('.$j.', 1, '.$value.');';
		        	$j++;
		        }

			$html .= '
		        var chart1 = new google.visualization.PieChart(document.getElementById("chart_div1"));
		        var chart2 = new google.visualization.PieChart(document.getElementById("chart_div2"));
		        chart1.draw(data1, {width: 450, height: 300, title: "Top Domains"});
		        chart2.draw(data2, {width: 450, height: 300, title: "Top TLDs"});
		      }
		    </script>
	        ';

	        $html .= '<table border=0><tr>
<td><div id="chart_div1" style="margin-left:-60px;"></div></td>
<td><div id="chart_div2" style="margin-left:-60px;"></div></td></tr></table>';

		$head_html = '<tr><th>No.</th><th>Domain</th><th>Requests</th><th>Share</th></tr>';

		$total_html = '<tr><td><strong>-</strong></td>
<td><strong>Total</strong></td>
<td><strong>'.$total.'</strong></td>
<td>-</td></tr>';
/*
$html .=  '!<pre>';
	        	$j = 0;
		        foreach ($result as $key => $value) {
		        	$html .= 'data1.setValue('.$j.', 0, "'.$key.'");';
		        	$html .= 'data1.setValue('.$j.', 1, '.$value.');
';
		        	$j++;
		        }
	        	$j = 0;
		        foreach ($tlds as $key => $value) {
		        	$html .= 'data2.setValue('.$j.', 0, "'.$key.'");';
		        	$html .= 'data2.setValue('.$j.', 1, '.$value.');
';
		        	$j++;
		        }
$html .=  '</pre>!';*/

	        $html .= '<table class="listing">'.$head_html.$total_html;

		$other_tld=0;$i=1;
	        foreach ($result as $domain => $count) {
/*use same limit for pie too -> move this to getData*/
			if ($i <= 100)
			{
				$html .= '<tr><td>'.($i++).'</td>
<td>'.$domain.'</td>
<td>'.$count.'</td>
<td>'.round($count*100/$total,2).'%</td></tr>';
		        } else	{
				$other_tld+=$count;
			}
		}
		if ($other_tld > 0) {
			$html .= '<tr><td>-</td>
<td>[other]</td>
<td>'.$other_tld.'</td>
<td>'.round($other_tld*100/$total,2).'%</td></tr>';
		}
	        return $html.$total_html.'</table><br/>';
	}

	protected function getData($groupIds, $dateFrom, $dateTo) {
	        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
			/*$groupRel = $this->_getGroupRelations($groupIds);*/

			$select = $db->select()
					->from(array('dns_log'),array('count(*) as cnt','tld','sld'))
			                ->where('tld NOT IN (?)', array('arpa','lan','local','mobi','home','_TCP','office'))
			                ->where('sld != ""')
			                ->where('DATE(time) >= ?', $dateFrom)
			                ->where('DATE(time) <= ?', $dateTo)
			                ->group(array('tld', 'sld'))
					->order('cnt DESC');

			$items = $db->fetchAll($select);
			$result = array();
			$tlds=array();
			$total=0;
			foreach ($items as $key => $value) {
				$total+=$value['cnt'];
				$result[$value['sld'].'.'.$value['tld']]=$value['cnt'];
				$tlds[$value['tld']]=$tlds[$value['tld']]+$value['cnt'];
			}
/*
			uasort($result, function($a, $b) {
				if ($a['count'] == $b['count']) {
					return 0;
				}
				return ($a['count'] > $b['count']) ? -1 : 1;
			});*/
	        return array('data' => $result,'total' => $total,'tlds' => $tlds);
	}
}
