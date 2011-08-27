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
			$adapter = $tableUserPolicy->getAdapter();
			$tableUserPolicy = null;

			$adapter->delete('radcheck', "`username`='{$model->getUsername()}' AND `attribute`='MD5-Password'");

			if ($model->getPassword() !== null) {
				/**
				 * @todo This shouldn't be possibe! (null password)
				 */
				$adapter->insert('radcheck', array('username' => $model->getUsername(),
												   'attribute' => 'MD5-Password',
												   'op' => ':=',
												   'value' => $model->getPassword()));
			}
		} catch (Exception $e) {
			throw $e;
		}

		return $model;
	}

	public function rowToModel(Zend_Db_Table_Row $row)
	{
		$model = parent::rowToModel($row);

		$adapter = $this->getDbTable()->getAdapter();

		$select = $adapter->select();

		$select->from('radcheck', 'value')
			   ->where('username = ?', $model->getUsername())
			   ->where('attribute = ?', 'MD5-Password');

		$password = $adapter->fetchOne($select);

		$model->setPassword($password);

		$result = $row->findDependentRowset('Users_Model_DbTable_NetworkUserPolicy');

		$policyIds = array();

		foreach ($result as $policyRow) {
			$policyIds[] = $policyRow->policy_id;
		}

		$model->setPolicyIds($policyIds);

		return $model;
	}
}

