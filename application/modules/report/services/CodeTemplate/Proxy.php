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

class Report_Service_CodeTemplate_Proxy extends Report_Service_CodeTemplate_Abstract {

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$db->setFetchMode(Zend_Db::FETCH_NUM);

		$tables=array();

		/*query totals and name of categories*/
		$stmt=$db->query("SELECT category, count(*) AS cnt FROM proxy_log WHERE stamp BETWEEN '$dateFrom' AND '$dateTo' GROUP BY category ORDER BY cnt DESC;");

		while ($row=$stmt->fetch())
		{
			$rows=array();
			$total=array(
				array(/*total data row*/
					'data'=>array('Total',$row[1])
					,'translatable'=>false /*is this even correct place to specify translatable*/
					,'class'=>array('left bold total','right bold total') /*!!?? total class is likely to be unexisting*/
				) /*end of second data row*/
			);
			/*query top 10 domains*/
			$tstmt=$db->query("SELECT substring_index(domain,'.','-2') AS tld, count(*) AS cnt FROM proxy_log WHERE category='$row[0]' and stamp BETWEEN '$dateFrom' AND '$dateTo' GROUP BY tld ORDER BY cnt DESC limit 10;");
			while ($trow=$tstmt->fetch()){
				$rows[]=array(/*total data row*/
						'data'=>array($trow[0],$trow[1])
						,'translatable'=>false
						,'class'=>array('left','right')
					); /*end of second data row*/
			}
			/*build table and add total line add beginning and end*/
			$tables[]=array(/*table definition*/
				'colDefs'=>array(/*array of coldefs*/
					array(/*first coldef instead of table header*/
						array( /*advanced column def as array*/
							'name'=>'Category: '.$row[0]
							,'translatable'=>false
							,'colspan'=>2
							,'class'=>'bold'
						)
					) /* end of first coldef*/
					,array(/*second coldef*/
						array( /*advanced column def as array*/
							'name'=>'Domain'
							,'translatable'=>false
							,'width'=>'80%'
							,'class'=>'right'
						)
						,array( /*advanced column def as array*/
							'name'=>'Count'
							,'translatable'=>false
							,'class'=>'right'
						)
					) /* end of first coldef*/
				) /*end of coldefs*/
				,'rows'=>array_merge(/*$total,*/$rows,$total)
			);
		}

		return array('tables'=>$tables);
	}
}
