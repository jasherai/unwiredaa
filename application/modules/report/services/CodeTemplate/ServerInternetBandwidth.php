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

class Report_Service_CodeTemplate_ServerInternetBandwidth extends Report_Service_CodeTemplate_Abstract {

	private function new_table($rows)
	{
		/*build table and add total line add beginning and end*/
		return array(/*table definition*/
			'colDefs'=>array(/*array of coldefs*/
				array(/*second coldef*/
					array( /*advanced column def as array*/
						'name'=>'Time'
						,'translatable'=>false
						,'width'=>'40%'
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Max'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Download'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Upload'
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

		$intv=300;$table="internet";
		$tstmt=$db->query("SET @intv = $intv;");
                $tstmt=$db->query("SET @temp_last_bytes_up = 0;");
                $tstmt=$db->query("SET @last_bytes_up = 0;");
                $tstmt=$db->query("SET @temp_last_bytes_down = 0;");
                $tstmt=$db->query("SET @last_bytes_down = 0;");
                $tstmt=$db->query("SET @temp_last_node_id = 0;");
                $tstmt=$db->query("SET @last_node_id = 0;");
		$tstmt=$db->query("
SELECT (UNIX_TIMESTAMP(time) DIV @intv) as epoch, SUM(delta_bytes_up) as interval_bytes_up, SUM(delta_bytes_down) as interval_bytes_down
FROM
(
	SELECT i1.session_id, i1.time
	, if((@temp_last_bytes_up:=@last_bytes_up)=@last_bytes_up,if((@last_bytes_up:=bytes_up)=bytes_up,if(type='Start',0,bytes_up-@temp_last_bytes_up),-1),-2) as delta_bytes_up 
	, if((@temp_last_bytes_down:=@last_bytes_down)=@last_bytes_down,if((@last_bytes_down:=bytes_down)=bytes_down,if(type='Start',0,bytes_down-@temp_last_bytes_down),-1),-2) as delta_bytes_down 
	, if((@temp_last_node_id:=@last_node_id)=@last_node_id,if((@last_node_id:=node_id)=node_id,if(type='Roaming',@temp_last_node_id,node_id),-1),-2) as delta_node_id
	FROM
	(
		(
			SELECT session_id, roaming_count, bytes_up, bytes_down, time, type, node_id from acct_".$table."_interim 
			WHERE type IN ('Start','Roaming') AND NOT ISNULL(node_id)
			AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL @intv SECOND) AND '$dateTo'
			ORDER BY session_id, roaming_count, time
		)
		UNION
		(
			SELECT session_id, roaming_count, MAX(bytes_up), MAX(bytes_down), MAX(time) as time, type, node_id from acct_".$table."_interim 
			WHERE type in ('Interim','Stop') AND NOT ISNULL(node_id)
			AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL @intv SECOND) AND '$dateTo'
			GROUP BY session_id, roaming_count, (UNIX_TIMESTAMP(time) DIV @intv)
			ORDER BY session_id, roaming_count, time
		)
		ORDER BY session_id, roaming_count, time
	) as i1
) as i2
WHERE delta_node_id >= 0 AND delta_bytes_up>=0 AND delta_bytes_down>=0 AND (delta_bytes_up>0 OR delta_bytes_down>0)
GROUP BY epoch
HAVING epoch >= (UNIX_TIMESTAMP('$dateFrom') DIV @intv)
ORDER BY epoch;
");/*for large traffic reports, using the sessions, and above only for open sessions, migth be much faster*/

/*join garden and internet in mysql*/
/*or in php together with chart/interval correction, if there is no traffic*/

                /*initialize*/
                $tables=array();
                $rows=array();
                $g_header=array('labels');$g_data=array();
                $_95=array();
                while ($trow=$tstmt->fetch()){
/*ad ~ 3.4% resp. 17.5% overhead (to uplink downlink)*/
                        $up=round((($trow[1]*8/850)+($trow[2]*8/990))/$intv);
                        $down=round((($trow[2]*8/850)+($trow[1]*8/990))/$intv);
                        $_95[]=$max=max($up,$down);
                        $data=array(date("Y-m-d H:i",($trow[0]*$intv))
                                ,$max /*max*/
                                ,$down /*down*/
                                ,$up /*up*/
                        );
                        $rows[]=array(/*data row*/
                                        'data'=>$data
                                        ,'translatable'=>false
                                        ,'class'=>array('bold','right','right','right')
                                ); /*end of data row*/
                        $g_data[]=$data;
                }
                $tables[]=$this->new_table($rows);
                rsort($_95);
                $p95=($_95[count($_95)/20]);

                return array(
                        'graphics'=>array(/*array of charts*/
                                'main_chart'=>array(/*chart defintion*/
                                        'name'=>'Internet Traffic in mbit/sec (95/5 is at '.$p95.' kbit/sec)'
					,'width'=>800 //default: 350
					,'height'=>600 //default: 300
					,'type'=>'SteppedAreaChart'
					,'headers'=>array('label','max','down','up')
					,'rows'=>$g_data
                                )
                        )
			,'tables'=>$tables);
	}
}
