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
class Report_Service_CodeTemplate_DNS extends Report_Service_CodeTemplate_Abstract {

	protected function getTemplate($groupIds, $data) {

	        $result = $data['data'];
	        $total = $data['total'];

        	$html = '
	        <script type="text/javascript">
		      google.load("visualization", "1", {packages:["corechart"]});
		      google.setOnLoadCallback(drawChart);
		      function drawChart() {
		        var data = new google.visualization.DataTable();
		        data.addColumn("string", "TLD");
		        data.addColumn("number", "Requests");
		    ';
	        	$html .= 'data.addRows('.count($result).');';
	        	$j = 0;
		        foreach ($result as $key => $value) {
		        	$html .= 'data.setValue('.$j.', 0, "'.$key.'");';
		        	$html .= 'data.setValue('.$j.', 1, '.$value['count'].');';
		        	$j++;
		        }

			$html .= '
		        var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
		        chart.draw(data, {width: 450, height: 300, title: "Top Level Domains"});
		      }
		    </script>
	        ';

	        $html .= '<div id="chart_div"></div>';

		$head_html = '<tr><th>Domain</th><th>Requests</th><th>Share</th></tr>';

		$total_html = $head_html.'<tr><td><strong>Total</strong></td><td><strong>'.$total.'</strong></td><td>-</td></tr>';

	        $html .= '<table class="listing">'.$total_html;

		$other_tld=0;
	        foreach ($result as $k => $v) {
			$total+=$v['cnt'];
			if ($v['count']>100)
			{
				$html .= $head_html.'<tr><td><strong>*.'.$k.'</strong></td>
<td><strong>'.$v['count'].'</strong></td>
<td><strong>'.round($v['count']*100/$total,2).'%</strong></td></tr>';
				if (!((count($v['sld'])==1) && ($v['sld']['[other]']))) /*do not list one [other].**/
					 foreach ($v['sld'] as $sk => $sv) {
						$html .= '<tr><td>'.$sk.'.'.$k.'</td>
<td>'.$sv.'</td>
<td>'.round($sv*100/$total,2).'%</td></tr>';
					}
		        } else	{
				$other_tld+=$v['cnt'];
			}
		}
		if ($other_tld>0) {
			$html .= $head_html.'<tr><td><strong>*.[other]</strong></td>
<td><strong>'.$other_tld.'</strong></td>
<td><strong>'.round($other_tld*100/$total,2).'%</strong></td></tr>';
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
			$total=0;
			foreach ($items as $key => $value) {
				$total+=$value['cnt'];
				if (!is_array($result[$value['tld']]['sld'])){
					$result[$value['tld']]['sld']=array();
					$result[$value['tld']]['count']=$value['cnt'];
				}
				else {
					$result[$value['tld']]['count']+=$value['cnt'];
				}
				if ($value['cnt']>100) {
					$result[$value['tld']]['sld'][$value['sld']]=$value['cnt'];
				} else {
					if (!$result[$value['tld']]['sld']['[other]']) $result[$value['tld']]['sld']['[other]']=$value['cnt'];
					else $result[$value['tld']]['sld']['[other]']+=$value['cnt'];
				}
			}

			uasort($result, function($a, $b) {
				if ($a['count'] == $b['count']) {
					return 0;
				}
				return ($a['count'] > $b['count']) ? -1 : 1;
			});
	        return array('data' => $result,'total' => $total);
	}
}

