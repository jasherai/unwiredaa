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

/**
 * Mapper for Reports_Model_Group
 * @author G. Sokolov <joro@web-teh.net>
 */
class Report_Model_Mapper_Group extends Unwired_Model_Mapper
{

	protected $_modelClass = 'Report_Model_Group';
	protected $_dbTableClass = 'Report_Model_DbTable_Group';

	protected $_defaultOrder = 'date_added DESC';

	public function save(Unwired_Model_Generic $model)
	{
		try {
			$model = parent::save($model);

			$nodes = new Report_Model_DbTable_Node();

			$nodes->delete(array('report_group_id = ?' => $model->getReportGroupId()));

			foreach ($model->getGroupsAssigned() as $groupId => $nodeId) {
				$nodes->insert(array(
						'report_group_id' => $model->getReportGroupId(),
						'group_id' => $nodeId
				));
			}

			$nodes = null;

			$nodes = new Report_Model_DbTable_Recepients();

			$nodes->delete(array('report_group_id = ?' => $model->getReportGroupId()));

			foreach ($model->getRecepients() as $groupId => $email) {
				$nodes->insert(array(
						'report_group_id' => $model->getReportGroupId(),
						'email' => $email
				));
			}
			$nodes = null;

		} catch (Exception $e) {
			throw $e;
		}

		return $model;
	}

	public function rowToModel(Zend_Db_Table_Row $row)
	{
		$model = parent::rowToModel($row);

		$groupRows = $row->findDependentRowset('Report_Model_DbTable_Node');

		$groupsAssigned = array();
		foreach ($groupRows as $groupRow) {

			$groupsAssigned[$groupRow->group_id] = new Nodes_Model_Node();
			$tmp = $groupRow->findParentRow('Groups_Model_DbTable_Group');

			$groupsAssigned[$groupRow->group_id]->setName($tmp->name);
			$groupsAssigned[$groupRow->group_id]->setGroupId($tmp->group_id);
			//$groupsAssigned[$groupRow->node_id]->setMac($tmp[0]->mac);
		}


		$model->setGroupsAssigned($groupsAssigned);


		$recepientRows = $row->findDependentRowset('Report_Model_DbTable_Recepients');

		$recepients = array();
		foreach ($recepientRows as $recepientRow) {
			$recepients[$recepientRow->recepient_id] = $recepientRow->email;
		}

		$model->setRecepients($recepients);

		return $model;
	}



}

