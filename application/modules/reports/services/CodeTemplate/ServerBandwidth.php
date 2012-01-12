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

class Reports_Service_CodeTemplate_ServerBandwidth extends Reports_Service_CodeTemplate_Abstract {

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
						'name'=>'Upload'
						,'translatable'=>false
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Download'
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
		$db->setFetchMode(Zend_Db::FETCH_ASSOC);

		$intv=300;
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
	(
		SELECT i1.session_id, i1.time
		, if((@temp_last_bytes_up:=@last_bytes_up)=@last_bytes_up,if((@last_bytes_up:=bytes_up)=bytes_up,if(type='Start',0,bytes_up-@temp_last_bytes_up),-1),-2) as delta_bytes_up 
		, if((@temp_last_bytes_down:=@last_bytes_down)=@last_bytes_down,if((@last_bytes_down:=bytes_down)=bytes_down,if(type='Start',0,bytes_down-@temp_last_bytes_down),-1),-2) as delta_bytes_down 
		, if((@temp_last_node_id:=@last_node_id)=@last_node_id,if((@last_node_id:=node_id)=node_id,if(type='Roaming',@temp_last_node_id,node_id),-1),-2) as delta_node_id
		FROM
		(
			(
				SELECT session_id, roaming_count, bytes_up, bytes_down, time, type, node_id from acct_internet_interim 
				WHERE type IN ('Start','Roaming') AND NOT ISNULL(node_id)
				AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL @intv SECOND) AND '$dateTo'
				ORDER BY session_id, roaming_count, time
			)
			UNION
			(
				SELECT session_id, roaming_count, MAX(bytes_up), MAX(bytes_down), MAX(time) as time, type, node_id from acct_internet_interim 
				WHERE type in ('Interim','Stop') AND NOT ISNULL(node_id)
				AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL @intv SECOND) AND '$dateTo'
				GROUP BY session_id, roaming_count, (UNIX_TIMESTAMP(time) DIV @intv)
				ORDER BY session_id, roaming_count, time
			)
			ORDER BY session_id, roaming_count, time
		) as i1
	)
	UNION
	(
		SELECT i1.session_id, i1.time
		, if((@temp_last_bytes_up:=@last_bytes_up)=@last_bytes_up,if((@last_bytes_up:=bytes_up)=bytes_up,if(type='Start',0,bytes_up-@temp_last_bytes_up),-1),-2) as delta_bytes_up 
		, if((@temp_last_bytes_down:=@last_bytes_down)=@last_bytes_down,if((@last_bytes_down:=bytes_down)=bytes_down,if(type='Start',0,bytes_down-@temp_last_bytes_down),-1),-2) as delta_bytes_down 
		, if((@temp_last_node_id:=@last_node_id)=@last_node_id,if((@last_node_id:=node_id)=node_id,if(type='Roaming',@temp_last_node_id,node_id),-1),-2) as delta_node_id
		FROM
		(
			(
				SELECT session_id, roaming_count, bytes_up, bytes_down, time, type, node_id from acct_garden_interim 
				WHERE type IN ('Start','Roaming') AND NOT ISNULL(node_id)
				AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL @intv SECOND) AND '$dateTo'
				ORDER BY session_id, roaming_count, time
			)
			UNION
			(
				SELECT session_id, roaming_count, MAX(bytes_up), MAX(bytes_down), MAX(time) as time, type, node_id from acct_garden_interim 
				WHERE type in ('Interim','Stop') AND NOT ISNULL(node_id)
				AND time BETWEEN DATE_SUB('$dateFrom',INTERVAL @intv SECOND) AND '$dateTo'
				GROUP BY session_id, roaming_count, (UNIX_TIMESTAMP(time) DIV @intv)
				ORDER BY session_id, roaming_count, time
			)
			ORDER BY session_id, roaming_count, time
		) as i1
	)
) as i2
WHERE delta_node_id >= 0 AND delta_bytes_up>=0 AND delta_bytes_down>=0 AND (delta_bytes_up>0 OR delta_bytes_down>0)
GROUP BY epoch
HAVING epoch >= (UNIX_TIMESTAMP('$dateFrom') DIV @intv)
ORDER BY epoch;
");

		/*initialize*/
		$tables=array();
		$rows=array();
		$g_header=array('labels');$g_data=array();
		$_95=array();
		while ($trow=$tstmt->fetch()){
/*ad ~ 3.4% resp. 17.5% overhead (to uplink downlink)*/
			$down=round((($trow['interval_bytes_up']*8/850)+($trow['interval_bytes_down']*8/990))/$intv);
			$up=round((($trow['interval_bytes_down']*8/850)+($trow['interval_bytes_up']*8/990))/$intv);
			$_95[]=$max=max($up,$down);
			$data=array(date("Y-m-d H:i",($trow['epoch']*$intv))
				,$max /*max*/
				,$up /*up*/
				,$down /*down*/
			);
			$rows[]=array(/*data row*/
					'data'=>$data
					,'translatable'=>false
					,'class'=>array('bold','right','right','right')
				); /*end of data row*/
			$g_data[]=$data;
		}
//do not display table
//		$tables[]=$this->new_table($rows);
		rsort($_95);
		$p95=($_95[count($_95)/20]);

/*1h*/		$down_avg=($g_data[0][3]*6)+($g_data[1][3]*4)+($g_data[2][3]*2);
		$down_avgl=array($g_data[0][3],$g_data[1][3],$g_data[0][3],$g_data[2][3],$g_data[0][3],$g_data[1][3],$g_data[0][3],$g_data[1][3],$g_data[0][3],$g_data[2][3],$g_data[0][3],$g_data[1][3]);
		$up_avg=($g_data[0][2]*6)+($g_data[1][2]*4)+($g_data[2][2]*2);
		$up_avgl=array($g_data[0][2],$g_data[1][2],$g_data[0][2],$g_data[2][2],$g_data[0][2],$g_data[1][2],$g_data[0][2],$g_data[1][2],$g_data[0][2],$g_data[2][2],$g_data[0][2],$g_data[1][2]);
/**/		/*add 95/5 to chart*/
		for ($i=0;$i<count($g_data);$i++){
			$g_data[$i][1]=$p95;
			/*calculate running average*/
/*1h*/			$down_avg+=$down_avgl[]=$g_data[$i][3];
			$up_avg+=$up_avgl[]=$g_data[$i][2];
			$down_avg-=array_shift($down_avgl);
			$up_avg-=array_shift($up_avgl);
			$g_data[max(0,$i-6)][4]=round($up_avg/12);
			$g_data[max(0,$i-6)][5]=round($down_avg/12);
/**/		}

		/*calculate clipping area for chart and number of axis lines (or find smarter way to have nice scale) -> this will only scale up to 25mbit max!*/
		//$p99=$_95[count($_95)/1000]; -> does not look too well (especially in correctly clipping browsers)
		$p99=$_95[0];
		$dv=10;
		$max_lines=17;
		while (true){ /*step up from 10kbit in 10,20,50,100 logic*/
			if (($p99/$dv) < $max_lines) break;
			$dv*=2;
			if (($p99/$dv) < $max_lines) break;
			$dv*=5;
		}
		$mb=ceil($p99/$dv); //use 99/1 percentile to define scale

		return array(
			'graphics'=>array(/*array of charts*/
                                'main_chart'=>array(/*chart defintion*/
					'name'=>'Traffic in kbit/sec'
					,'width'=>950 //default: 350
					,'height'=>600 //default: 300
/*adda 95/5 line via an opaque area*/
					,'type'=>'SteppedAreaChart' /*SteppedAreaChart*/
					,'nativeOptions'=>'areaOpacity:0
						,series:{
							1:{areaOpacity:1}
							,2:{areaOpacity:1}
						}
						,lineWidth:1
						,colors:["red","#0000BB","#00AA00"
/*1h*/							,"5544FF","#22FF22"
/**/							]
						,vAxis:{
							maxValue:'.($mb*$dv).'
							,viewWindow:{
								min:0
								,max:'.($mb*$dv).'
							}
						,gridlines:{count: '.($mb+1).'}
					}'

/*add a 95/5 line via a combo chart (but as bars are next to each other this does not look very well)
					,'type'=>'ComboChart'
					,'nativeOptions'=>'seriesType:"bars",series:{0:{type:"line"}}'*/

					,'headers'=>array('label','95/5','up','down'
/*1h*/							,'up 1h','down 1h'
/**/					)
					,'rows'=>$g_data
                                )
                        )
			,'tables'=>$tables);
	}
}
