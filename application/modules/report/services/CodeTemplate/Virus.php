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

class Report_Service_CodeTemplate_Virus extends Report_Service_CodeTemplate_Abstract {

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();
		$db->setFetchMode(Zend_Db::FETCH_NUM);

		$tables=array();

		/*query totals*/
		$stmt=$db->query("SELECT 'virus', count(*) AS cnt FROM proxy_log WHERE stamp BETWEEN '$dateFrom' AND '$dateTo' AND category like '%virus%' ORDER BY cnt DESC;");

		while ($row=$stmt->fetch())
		{
			$rows=array();
			$total=array(
				array(/*total data row*/
					'data'=>array(
						array('data'=>'Total','translatable'=>false)
						,array('data'=>$row[1],'translatable'=>false)
					)
					,'class'=>array('left bold total','right bold total') /*!!?? total class is likely to be unexisting*/
				) /*end of total data row*/
			);
			/*query top 50 virus threats*/
			$tstmt=$db->query("SELECT virusname, count(*) AS cnt FROM proxy_log WHERE category like '%virus%' and stamp BETWEEN '$dateFrom' AND '$dateTo' GROUP BY virusname ORDER BY cnt DESC limit 50;");
			while ($trow=$tstmt->fetch()){
				$rows[]=array(/*data row*/
						'data'=>array(
							array('data'=>$trow[0],'translatable'=>false)
							,array('data'=>$trow[1],'translatable'=>false)
						)
						,'class'=>array('left','right')
					); /*end of data row*/
			}
			/*build table and add total line add beginning and end*/
			$tables[]=array(/*table definition*/
				'colDefs'=>array(/*array of coldefs*/
					array(/*coldef*/
						array( /*advanced column def as array*/
							'name'=>'Virusname'
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
