<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Mapper for Nodes_Model_Node
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Nodes_Model_Mapper_Node extends Unwired_Model_Mapper
{

	protected $_modelClass = 'Nodes_Model_Node';
	protected $_dbTableClass = 'Nodes_Model_DbTable_Node';

	protected $_locationTable = null;
	protected $_settingsTable = null;

	public function getLocationTable()
	{
		if (null ===  $this->_locationTable) {
			$this->_locationTable = new Nodes_Model_DbTable_NodeLocation();
		}

		return $this->_locationTable;
	}

	public function getSettingsTable()
	{
		if (null ===  $this->_settingsTable) {
			$this->_settingsTable = new Nodes_Model_DbTable_NodeSettings();
		}

		return $this->_settingsTable;
	}

	public function save(Unwired_Model_Generic $model)
	{
		$nodeTable = $this->getDbTable();

		$nodeTable->getAdapter()->beginTransaction();

		try {
			parent::save($model);

			$this->setDbTable($this->getLocationTable());

			parent::save($model->getLocation());

			$this->setDbTable($this->getSettingsTable());

			parent::save($model->getSettings());

			$this->setDbTable($nodeTable);

			$nodeTable->getAdapter()->commit();
		} catch (Exception $e) {
			$nodeTable->getAdapter()->rollBack();
			$this->setDbTable($nodeTable);
			throw $e;
		}

		return $model;
	}

	protected function _rowToModel(Zend_Db_Table_Row $row)
	{
		$model = parent::_rowToModel($row);

		$locationRow = $row->findDependentRowset($this->getLocationTable())->current();

		if ($locationRow) {
			$model->setLocation($locationRow->toArray());
		}

		$settingsRow = $row->findDependentRowset($this->getSettingsTable())->current();

		if ($settingsRow) {
			$model->setSettings($settingsRow->toArray());
		}

		return $model;
	}
}

