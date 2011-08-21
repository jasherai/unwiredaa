<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Mapper for Users_Model_Admin
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Model_Mapper_Admin extends Unwired_Model_Mapper
{

	protected $_modelClass = 'Users_Model_Admin';
	protected $_dbTableClass = 'Users_Model_DbTable_AdminUser';


	public function save(Unwired_Model_Generic $model)
	{
		try {
			$model = parent::save($model);

			$tableAdminGroup = new Users_Model_DbTable_AdminGroup();

			$tableAdminGroup->delete(array('user_id = ?' => $model->getUserId()));

			foreach ($model->getGroupIds() as $groupId) {
				$tableAdminGroup->insert(array('user_id' => $model->getUserId(),
											   'group_id' => $groupId));
			}
			$tableAdminGroup = null;
		} catch (Exception $e) {
			throw $e;
		}

		return $model;
	}

	public function rowToModel(Zend_Db_Table_Row $row)
	{
		$model = parent::rowToModel($row);

		$groupRows = $row->findDependentRowset('Users_Model_DbTable_AdminGroup');

		$groupIds = array();
		foreach ($groupRows as $groupRow) {
			$groupIds[] = $groupRow->group_id;
		}

		$model->setGroupIds($groupIds);

		return $model;
	}

}

