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

class Report_Service_CodeTemplate_Bandwidth extends Report_Service_CodeTemplate_Abstract {

	private function new_table($rows)
	{
		/*build table and add total line add beginning and end*/
		return array(/*table definition*/
			'colDefs'=>array(/*array of coldefs*/
				array(/*second coldef*/
					array( /*advanced column def as array*/
						'name'=>'Group'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Node'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
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

		$tables=array();
		$rows=array();
		/*query node-groups*/
		$tstmt=$db->query("SET @tlen = 15;");
                $tstmt=$db->query("SET @temp_last_bytes_up = 0;");
                $tstmt=$db->query("SET @last_bytes_up = 0;");
                $tstmt=$db->query("SET @temp_last_bytes_down = 0;");
                $tstmt=$db->query("SET @last_bytes_down = 0;");
                $tstmt=$db->query("SET @temp_last_node_id = 0;");
                $tstmt=$db->query("SET @last_node_id = 0;");
		$tstmt=$db->query("SELECT LEFT(time,@tlen) as epoch, n.node_id, n.group_id, SUM(delta_bytes_up) as interval_bytes_up, SUM(delta_bytes_down) as interval_bytes_down 
, n.name, g.name
FROM (SELECT i1.session_id, i1.time
, if((@temp_last_bytes_up:=@last_bytes_up)=@last_bytes_up,if((@last_bytes_up:=bytes_up)=bytes_up,if(type='Start',0,bytes_up-@temp_last_bytes_up),-1),-2) as delta_bytes_up 
, if((@temp_last_bytes_down:=@last_bytes_down)=@last_bytes_down,if((@last_bytes_down:=bytes_down)=bytes_down,if(type='Start',0,bytes_down-@temp_last_bytes_down),-1),-2) as delta_bytes_down 
, if((@temp_last_node_id:=@last_node_id)=@last_node_id,if((@last_node_id:=node_id)=node_id,if(type='Roaming',@temp_last_node_id,node_id),-1),-2) as delta_node_id
FROM (
(SELECT session_id, roaming_count, bytes_up, bytes_down, time, type, node_id from acct_internet_interim 
WHERE type IN ('Start','Roaming') 
AND time BETWEEN '$dateFrom' AND '$dateTo'
ORDER BY session_id, roaming_count, time)
UNION
(SELECT session_id, roaming_count, MAX(bytes_up), MAX(bytes_down), MAX(time) as time, type, node_id from acct_internet_interim 
WHERE type in ('Interim','Stop')
AND time > DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY session_id, roaming_count, LEFT(time,@tlen)
ORDER BY session_id, roaming_count, time)
ORDER BY session_id, roaming_count, time
) as i1
) as i2 INNER JOIN node n on i2.delta_node_id = n.node_id INNER JOIN `group` g ON g.group_id = n.group_id
WHERE delta_node_id >= 0 AND (delta_bytes_up>0 OR delta_bytes_down>0)
GROUP BY group_id, node_id, epoch
ORDER BY group_id, node_id, epoch;
");
		/*initialize*/
		$last_group_id=false;
		while ($trow=$tstmt->fetch()){
/*start new table if group changed*/
			if ($trow[2]!=$last_group_id) {
				if ($last_group_id) { /*no need to start new table if we have no old one*/
					$tables[]=$this->new_table($rows);
					$rows=array();
				}
				$last_group_id=$trow[2];
			}
			$rows[]=array(/*data row*/
					'data'=>array($trow[6],$trow[5],$trow[0].'0-'.substr($trow[0],14,1).'9'
					,/*$trow[3].'Bytes '.*/round($trow[3]*8/1024/600).'kbps'
					,/*$trow[4].'Bytes '.*/round($trow[4]*8/1024/600).'kbps'
					,/*($trow[3]+$trow[4]).'Bytes '.*/round(($trow[3]+$trow[4])*8/1024/600).'kbps')
					,'translatable'=>false
					,'class'=>array('bold','bold','bold','right','right','right')
				); /*end of data row*/
		}
		$tables[]=$this->new_table($rows);

		return array('tables'=>$tables);
	}
}
