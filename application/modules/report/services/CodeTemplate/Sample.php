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

class Report_Service_CodeTemplate_Sample extends Report_Service_CodeTemplate_Abstract {

	public function getData($groupIds, $dateFrom, $dateTo) {
		$db = Zend_Db_Table_Abstract::getDefaultAdapter ();

		$row=array(array('data'=>array('a','b'),'class'=>array('bold','left')));
		return array(/*the result array*/
			'graphics'=>array(/*array of charts*/
				'test_id1'=>array(/*chart defintion*/
					'name'=>'testPie'
					,'width'=>200 //default: 350
					,'height'=>200 //default: 300
					,'type'=>'PieChart' //default: PieChart (wants rows with only 2 columns, first is label)
					,'headers'=>array( //column labels (even if there values is not used anywhere, they are required!)
						'c1'
						,'c2'
					)
					,'rows'=>array(/*with as many columsn as defined in headers*/
						array('a1',3) //second column must be numerical
						,array('b1',6)
					)
				)
				,'test_id2'=>array(/*chart defintion*/
					'name'=>'testLine'
					,'width'=>500 //default: 350
					,'height'=>200 //default: 300
					,'type'=>'LineChart' //default: PieChart (wants rows with only 2 columns, first is label)
					,'headers'=>array( //column labels (even if there values is not used anywhere, they are required!)
						'labels'
						,'line 1'
						,'line 2'
					)
					,'rows'=>array(/*with as many columsn as defined in headers*/
						array('l1',3,9) //second column must be numerical
						,array('l2',6,6)
						,array('l3',3,5)
						,array('l4',7,3)
					)
				)
			)
			,'tables'=>array(/*array of tables*/
				array( /*table 1*/
					'colDefs'=>array(/*array of coldefs*/
						array(/*first coldef*/
							'column a' /*simple column as single name*/
							,array( /*advanced column def as array*/
								'name'=>'column b'
								,'class'=>'bold'
							)
							,'column c'
						) /* end of first coldef*/
						,array(/*second coldef (second line of table header)*/
							array( /*advanced column def*/
								'name'=>'combined cell'
								,'colspan'=>3
							)
						) /*end of second coldef*/
					) /*end of coldefs*/
					,'rows'=>array(/*array of rows*/
						array(/*first data row*/
							'data'=>array('a1','b1','c1')/*just data for our 2 columns*/
							,'class'=>array('right','','right') /*we can add style (classes)*/
						) /*end of first data row*/
						,array(/*second data row*/
							'data'=>array('a2','b2','c2')/*data for our second row*/
							,'class'=>array('right','right','bold')
						) /*end of second data row*/
						,array(
							'data'=>array('a3 (class left is broken)','b3 (and not even an intact table cell)','c3')
							,'class'=>array('left','left','right')
						)
						,array(/*second data row*/
							'data'=>array('a4','b4','c4')
							,'class'=>array('left','left','bold')
						)
					) /*end of rows*/
				) /*end of table 1*/
			)/*end of array of tables*/
			/*there could be an array of graphics too, likely with identical structure as tables*/
		); /*end of result array*/
	}
}
