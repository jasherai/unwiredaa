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

class Report_Service_CodeTemplate_BillingB extends Report_Service_CodeTemplate_Abstract {

	private function new_table($rows,$name,$totalUp,$totalDown,$totalTotal)
	{
		/*build table and add total line add beginning and end*/
		return array(/*table definition*/
			'colDefs'=>array(/*array of coldefs*/
				array(
					array( /*advanced column def as array*/
						'name'=>$name
						,'translatable'=>false
						,'class'=>'bold'
					)
					,array( /*advanced column def as array*/
						'name'=>'<div align=right>'.round($totalUp/1024).' KB<div>'
						,'translatable'=>false
						,'class'=>'right' /*does not work !!??*/
					)
					,array( /*advanced column def as array*/
						'name'=>'<div align=right>'.round($totalDown/1024).' KB<div>'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'<div align=right>'.round(($totalUp+$totalDown)/1024).' KB<div>'
						,'translatable'=>false
						,'class'=>'right'
					)
				)
				,array(/*second coldef*/
					array( /*advanced column def as array*/
						'name'=>'Time'
						,'translatable'=>false
						,'width'=>'20%'
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Upload'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Download'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Total'
						,'translatable'=>false
						,'class'=>'right'
					)
				) /* end of second coldef*/
			) /*end of coldefs*/
			,'rows'=>$rows
		);
	}

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$db->setFetchMode(Zend_Db::FETCH_NUM);

		$tstmt=$db->query("SET @tlen = 7;");
                $tstmt=$db->query("SET @temp_last_bytes_up = 0;");
                $tstmt=$db->query("SET @last_bytes_up = 0;");
                $tstmt=$db->query("SET @temp_last_bytes_down = 0;");
                $tstmt=$db->query("SET @last_bytes_down = 0;");
                $tstmt=$db->query("SET @temp_last_node_id = 0;");
                $tstmt=$db->query("SET @last_node_id = 0;");
		$tstmt=$db->query("SELECT LEFT(time,@tlen) as epoch, g.group_id, SUM(delta_bytes_up) as interval_bytes_up, SUM(delta_bytes_down) as interval_bytes_down, g.name
FROM (SELECT i1.session_id, i1.time
, if((@temp_last_bytes_up:=@last_bytes_up)=@last_bytes_up,if((@last_bytes_up:=bytes_up)=bytes_up,if(type='Start',0,bytes_up-@temp_last_bytes_up),-1),-2) as delta_bytes_up 
, if((@temp_last_bytes_down:=@last_bytes_down)=@last_bytes_down,if((@last_bytes_down:=bytes_down)=bytes_down,if(type='Start',0,bytes_down-@temp_last_bytes_down),-1),-2) as delta_bytes_down 
, if((@temp_last_node_id:=@last_node_id)=@last_node_id,if((@last_node_id:=node_id)=node_id,if(type='Roaming',@temp_last_node_id,node_id),-1),-2) as delta_node_id
FROM (
(SELECT session_id, roaming_count, bytes_up, bytes_down, time, type, node_id from acct_internet_interim 
WHERE type IN ('Start','Roaming') AND NOT ISNULL(node_id)
AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL 1 HOUR) AND '$dateTo'
ORDER BY session_id, roaming_count, time)
UNION
(SELECT session_id, roaming_count, MAX(bytes_up), MAX(bytes_down), MAX(time) as time, type, node_id from acct_internet_interim 
WHERE type in ('Interim','Stop') AND NOT ISNULL(node_id)
AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL 1 HOUR) AND '$dateTo'
GROUP BY session_id, roaming_count, LEFT(time,@tlen)
ORDER BY session_id, roaming_count, time)
ORDER BY session_id, roaming_count, time
) as i1
) as i2 INNER JOIN node n on i2.delta_node_id = n.node_id INNER JOIN `group` g ON g.group_id = n.group_id
WHERE delta_node_id >= 0 AND (delta_bytes_up>0 OR delta_bytes_down>0)
AND n.billable=1 and n.status='enabled'
GROUP BY group_id, epoch
HAVING epoch >= '$dateFrom'
ORDER BY g.name, epoch;
");
		$tables=array();
		$rows=array();
		$g_header=array('labels');$g_data=array();
		/*initialize*/
		$last_group_id=false;$last_group_name="";
		$totalUp=$totalDown=0;
		while ($trow=$tstmt->fetch()){
/*start new table if group changed*/
			if ($trow[1]!=$last_group_id) {
				if ($last_group_id) { /*no need to start new table if we have no old one*/
					$tables[]=$this->new_table($rows,$last_group_name,$totalUp,$totalDown);
					$rows=array();
					$totalUp=$totalDown=0;
				}
				$last_group_id=$trow[1];
				$last_group_name=$trow[4];
				$g_header[]=$trow[4];
			}
			$rows[]=array(/*data row*/
					'data'=>array($trow[0]
					,round($trow[2]).' KB'/*.round($trow[3]*8/1024/600,2).' kbps'*/
					,round($trow[3]).' KB'/*.round($trow[4]*8/1024/600,2).' kbps'*/
					,round(($trow[2]+$trow[2])/1024).' KB')/*.round(($trow[3]+$trow[4])*8/1024/600,2).' kbps')*/
					,'translatable'=>false
					,'class'=>array('bold','right','right','right')
				); /*end of data row*/
			$totalUp+=$trow[2];
			$totalDown+=$trow[3];
			$g_data[$trow[0]]["_".(count($g_header)-1)]=round(($trow[2]+$trow[2])/(1024*1024));
		}
		$tables[]=$this->new_table($rows,$last_group_name,$totalUp,$totalDown);

		/*convert graph data array*/
		ksort($g_data);
		foreach ($g_data as $label => $data){
			$line=split(",",str_replace(",",";",$label).str_repeat(",0",count($g_header)-1));
			foreach ($data as $num => $val) {
				$line[(substr($num,1)*1)]=$val;
			}
			$gn_data[]=$line;
		}

		return array(
			'graphics'=>array(/*array of charts*/
                                'main_chart'=>array(/*chart defintion*/
					'name'=>'Traffic in MB per month'
					,'width'=>800 //default: 350
					,'height'=>600 //default: 300
					,'nativeOptions'=>'isStacked:true'
					,'type'=>'SteppedAreaChart'
					,'headers'=>$g_header
					,'rows'=>$gn_data
                                )
                        )
			,'tables'=>$tables);
	}
}
