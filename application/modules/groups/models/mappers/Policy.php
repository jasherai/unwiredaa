<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Mapper for Groups_Model_Policy
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Groups_Model_Mapper_Policy extends Unwired_Model_Mapper {

    protected $_dbTableClass = 'Groups_Model_DbTable_Policy';

    protected $_modelClass = 'Groups_Model_Policy';

    public function save(Unwired_Model_Generic $model)
    {
    	try {
			$model = parent::save($model);

			$this->_saveRadiusData($model);

    	} catch (Unwired_Exception $e) {
    		throw $e;
    	}

    	return $model;
    }

    protected function _saveRadiusData(Unwired_Model_Generic $model)
    {
    	$db = $this->getDbTable()->getAdapter();

    	$db->delete('radgroupcheck', "groupname = '{$model->getName()}'");

		foreach ($model->getRulesReply() as $rule) {
			if (empty($rule['attribute'])) {
				continue;
			}
			$rule['groupname'] = $model->getName();
			$db->insert('radgroupreply', $rule);
		}

    	$db->delete('radgroupreply', "groupname = '{$model->getName()}'");

		foreach ($model->getRulesCheck() as $rule) {
			if (empty($rule['attribute'])) {
				continue;
			}
			$rule['groupname'] = $model->getName();
			$db->insert('radgroupcheck', $rule);
		}
    }

    public function rowToModel(Zend_Db_Table_Row $row)
    {
    	$model = parent::rowToModel($row);

		$db = $this->getDbTable()->getAdapter();

		$check = $db->fetchAll("SELECT * FROM `radgroupcheck` WHERE `groupname`='{$model->getName()}'");

		$model->setRulesCheck($check);

		$reply = $db->fetchAll("SELECT * FROM `radgroupreply` WHERE `groupname`='{$model->getName()}'");

		$model->setRulesReply($reply);

		return $model;

    }

}