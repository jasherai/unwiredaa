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

class Reports_Service_CodeTemplate_UniqueUsers extends Reports_Service_CodeTemplate_Abstract {

	private function new_table($rows,$parent_group_id,$db,$dateFrom,$dateTo)
	{
		/*calculate group total?*/
//echo microtime().", $parent_group_id<br>";
/*mysql can not handle the time constraints (can`t merge multiple indexes) -> use other DBMS or other query, but faster on large results (but mieght get it wrong with unclosed sessions)*/
//		if (!$parent_group_id)
		$stmt=$db->query("SELECT count(DISTINCT user_mac), ".(!$parent_group_id?"'Total'":"gp.name")." as name
FROM acct_internet_session s
INNER JOIN acct_interner_roaming r ON s.session_id=r.session_id
INNER JOIN node n ON r.node_id = n.node_id
INNER JOIN `group` g on g.group_id = n.group_id
INNER JOIN `group` gp on gp.group_id = g.parent_id
WHERE ".($parent_group_id?"gp.group_id = '$parent_group_id' AND ":"")."
(	r.start_time BETWEEN '$dateFrom' AND '$dateTo'
	OR r.stop_time BETWEEN '$dateFrom' AND '$dateTo'
	OR ( r.start_time < '$dateFrom' AND ( r.stop_time > '$dateFrom' OR ISNULL(r.stop_time)))
)");
//		else
/*simpler time constraint, faster in mysql, and maybe even groupable by time, and especially faster with limited groups and especially time!*/
/*		$stmt=$db->query("SELECT count(DISTINCT user_mac), ".(!$parent_group_id?"'Total'":"gp.name")." as name
FROM acct_garden_session s
INNER JOIN acct_garden_interim i ON s.session_id=i.session_id
INNER JOIN node n ON i.node_id = n.node_id
INNER JOIN `group` g on g.group_id = n.group_id
INNER JOIN `group` gp on gp.group_id = g.parent_id
WHERE ".($parent_group_id?"gp.group_id = '$parent_group_id' AND ":"")."	i.time BETWEEN '$dateFrom' AND '$dateTo'");*/

		$row=$stmt->fetch();

		/*build table and add total line add beginning and end*/
		return array(/*table definition*/
			'colDefs'=>array(/*array of coldefs*/
				array(/*coldef*/
					array( /*advanced column def as array*/
						'name'=>$row[1]
						,'translatable'=>false
						,'width'=>'80%'
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>$row[0]
						,'translatable'=>false
						,'class'=>'right'
					)
				) /* end of first coldef*/
				,(!$parent_group_id?array():array(/*second coldef*/
					array( /*advanced column def as array*/
						'name'=>'Nodes'
						,'translatable'=>false
						,'width'=>'80%'
						,'class'=>'right'
					)
					,array( /*advanced column def as array*/
						'name'=>'Count'
						,'translatable'=>false
						,'class'=>'right'
					)
				)) /* end of second coldef*/
			) /*end of coldefs*/
			,'rows'=>$rows
		);
	}

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$db->setFetchMode(Zend_Db::FETCH_NUM);
/*todo:
respect groupIds
include garden counts (either seperate queries and php, or mysql union)
calculate total
maybe do really correct hierarchy
*/
//echo ($time=microtime())."<hr>";
		$tables=array();
		$rows=array();
		/*query node-groups*/
/* slow, as mysql can not use index for start_Time and stop_time simutaneously*/
		$tstmt=$db->query("SELECT g.name, count(DISTINCT user_mac), g.parent_id
FROM acct_internet_session s
INNER JOIN acct_internet_roaming r ON s.session_id=r.session_id
INNER JOIN node n ON r.node_id = n.node_id
INNER JOIN `group` g on g.group_id = n.group_id
WHERE r.start_time BETWEEN '$dateFrom' AND '$dateTo'
OR r.stop_time BETWEEN '$dateFrom' AND '$dateTo'
OR r.start_time < '$dateFrom' AND ( r.stop_time > '$dateFrom' OR ISNULL(r.stop_time))
GROUP BY g.group_id
ORDER BY g.parent_id, g.name;");

/*simpler to use indexes, but enourmous data, might be faster for small timeranges, or node_id sets*/
/*		$tstmt=$db->query("SELECT g.name, count(DISTINCT user_mac), g.parent_id
FROM acct_garden_session s
INNER JOIN acct_garden_interim i ON s.session_id=i.session_id
INNER JOIN node n ON i.node_id = n.node_id
INNER JOIN `group` g on g.group_id = n.group_id
WHERE i.time BETWEEN '$dateFrom' AND '$dateTo'
GROUP BY g.group_id
ORDER BY g.parent_id, g.name;");*/
		/*initialize*/
		$last_group_id=false;
		while ($trow=$tstmt->fetch()){
/*start new table if group changed*/
			if ($trow[2]!=$last_group_id) {
				if ($last_group_id) { /*no need to start new table if we have no old one*/
					$tables[]=$this->new_table($rows,$last_group_id,$db,$dateFrom,$dateTo);
					$rows=array();
				}
				$last_group_id=$trow[2];
			}
			$rows[]=array(/*data row*/
					'data'=>array($trow[0],$trow[1])
					,'translatable'=>false
					,'class'=>array('','right')
				); /*end of data row*/
		}
		$tables[]=$this->new_table($rows,$last_group_id,$db,$dateFrom,$dateTo);
		$tables[]=$this->new_table(array(),false,$db,$dateFrom,$dateTo);
//echo "<hr>".microtime()."<br>";
//die();
		return array('tables'=>$tables);
	}
}
