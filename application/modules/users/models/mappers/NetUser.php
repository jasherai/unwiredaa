<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Mapper for Users_Model_NetUser
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Model_Mapper_NetUser extends Unwired_Model_Mapper
{

	protected $_modelClass = 'Users_Model_NetUser';
	protected $_dbTableClass = 'Users_Model_DbTable_NetworkUser';

	public function save(Unwired_Model_Generic $model)
	{
		try {
			$model = parent::save($model);

			$tableUserPolicy = new Users_Model_DbTable_NetworkUserPolicy();

			$tableUserPolicy->delete(array('user_id = ?' => $model->getUserId()));

			foreach ($model->getPolicyIds() as $policy_id) {
				$tableUserPolicy->insert(array('user_id' => $model->getUserId(),
											   'policy_id' => $policy_id));
			}
			$tableUserPolicy = null;
		} catch (Exception $e) {
			throw $e;
		}

		return $model;
	}

	public function delete(Unwired_Model_Generic $model)
	{
		$result = parent::delete($model);

		if ($result) {
			//$
		}
	}
}

