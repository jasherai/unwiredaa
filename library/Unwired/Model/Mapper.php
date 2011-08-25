<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Base functionality for a DB mapper
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Unwired_Model_Mapper implements Zend_Paginator_AdapterAggregate {

    protected $_dbTable = null;

    protected $_dbTableClass = null;

    protected $_modelClass = null;

    protected $_customSelect = null;

    protected $_repository = array();

    public function __construct()
    {
    	if (null === $this->_modelClass) {
    		throw new Unwired_Exception('Model class not defined in mapper');
    	}
    }

    /**
     * Set the default db table gateway instance for mapper
     *
     * @param string|Zend_Db_Table_Abstract $dbTable
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }

        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }

        $this->_dbTable = $dbTable;
        return $this;
    }

    /**
     * Get the default db table instance
     *
     * @return Zend_Db_Table
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
        	if (empty($this->_dbTableClass)) {
            	throw new Unwired_Exception('DB table instance is NULL and dbTableClass is not specified');
        	}

        	$this->setDbTable($this->_dbTableClass);
        }

        return $this->_dbTable;
    }

    /**
     * Get new empty entity
     * @return Unwired_Model_Generic
     */
    public function getEmptyModel()
    {
    	return new $this->_modelClass;
    }

    /**
     * Persist single entity to database
     *
     * @param Unwired_Model_Generic $model
     * @throws Unwired_Exception In case something goes wrong
     * @return Unwired_Model_Generic
     */
    public function save(Unwired_Model_Generic $model)
    {
        $data = $this->_modelToRowdata($model);

        /**
         * Filter out stuff that's not in the table
         */
        $cols = $this->getDbTable()->info(Zend_Db_Table_Abstract::COLS);
        $data = array_intersect_key($data, array_flip($cols));

        /**
         * Find the primary key cols
         */
        $primary = $this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY);

        $primaryFilter = array();

		$nulled = 0;

        foreach ($primary as $col) {
        	if (null === $data[$col]) {
        		if (count($primary) == 1) {
        			break;
        		}
        		$primaryFilter[] = $col . ' IS NULL';
        		$nulled++;
        	} else {
        		$primaryFilter[$col . ' = ?'] = $data[$col];
        	}
        }

        $row = null;

        if (count($primaryFilter) && $nulled != count($primary)) {
        	$row = $this->getDbTable()->fetchRow($primaryFilter);
        }

        if (!$row) {
        	$row = $this->getDbTable()->fetchNew();
        }

        try {
        	foreach ($data as $col => $value) {
        		$row->$col = $value;
        	}

        	$row->save();

        	$model->fromArray($row->toArray());

        } catch (Exception $e) {
        	throw new Unwired_Exception('Error saving the information', 500, $e);
        }

        return $model;
    }

    /**
     * Delete model entry from database
     * @param Unwired_Model_Generic $model
     * @return integer
     */
    public function delete(Unwired_Model_Generic $model)
    {
    	$data = $this->_modelToRowdata($model);

        /**
         * Find the primary key cols
         */
        $primary = $this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY);

        $primaryFilter = array();

		$nulled = 0;

        foreach ($primary as $col) {
        	if (null === $data[$col]) {
        		if (count($primary) == 1) {
        			break;
        		}
        		$primaryFilter[] = $col . ' IS NULL';
        		$nulled++;
        	} else {
        		$primaryFilter[$col . ' = ?'] = $data[$col];
        	}
        }

        return $this->getDbTable()->delete($primaryFilter);
    }

    /**
     * Find single entity by its ID
     *
     * @param mixed $id
     * @return Unwired_Model_Generic
     */
    public function find($id)
    {
    	if ($this->_hasInRepository($id)) {
    		return $this->_getFromRepository($id);
    	}

        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return null;
        }

        return $this->rowToModel($result->current());
    }

    /**
     * Find entities by criteria
     *
     * @param Zend_Db_Select|array $conditions
     * @param int|null $limit
     */
    public function findBy($conditions, $limit = null)
    {
    	if ($conditions instanceof Zend_Db_Select) {
    		$select = $conditions;
    	} elseif (is_array($conditions)) {

			$select = $this->getDbTable()->select(true);

			$cols = $this->getDbTable()->info(Zend_Db_Table::COLS);

			foreach ($conditions as $field => $value) {

				if (!in_array($field, $cols)) {
					$dependents = $this->getDbTable()->getDependentTables();
					foreach ($dependents as $table) {
						$tableInstance = (is_string($table)) ? new $table : $table;

						if (!in_array($field, $tableInstance->info(Zend_Db_Table::COLS))) {
							continue;
						}

						$joinTableName = $tableInstance->info(Zend_Db_Table::NAME);
						$fromTableName = $this->getDbTable()->info(Zend_Db_Table::NAME);

						$joinCols = array_intersect($this->getDbTable()->info(Zend_Db_Table::PRIMARY),
													$tableInstance->info(Zend_Db_Table::PRIMARY));
						$joinCondition = array();

						foreach ($joinCols as $col) {
							$joinCondition[] = "{$joinTableName}.{$col} = {$fromTableName}.{$col}";
						}

						$select->setIntegrityCheck(false)
							   ->joinInner($joinTableName, implode(' AND ', $joinCondition));

						$joinCols = null;
						$joinCondition = null;
					}
				}

				switch ($value) {
					case (is_array($value)):
						$select->where($field . ' IN (?)', $value);
					break;

					case null:
						$select->where($field . ' IS NULL');
					break;

					case preg_match('/\%/i', $value):
						$select->where($field . ' LIKE ?', $value);
					break;

					default:
						$select->where($field . ' = ?', $value);
					break;
				}
			}
    	} else {
    		throw new Unwired_Exception('Unwired_Model_Mapper::findBy expects array with conditions or select instance');
    	}

		if ($limit === 0) {
			$this->_customSelect = $select;
			return null;
		}

		if ($limit) {
			$select->limit($limit);
		}


		$result = $this->getDbTable()->fetchAll($select);

		if (!$result) {
			return null;
		}

		return $this->rowsetToModels($result);
    }

    /**
     * Find one entry by some criteria
     *
     * @param array $conditions
     */
    public function findOneBy(array $conditions)
    {
    	$result = $this->findBy($conditions, 1);

    	if (!$result) {
    		return null;
    	}

    	return array_pop($result);
    }

    /**
     * Get all entries
     * @return array
     */
    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();

        return $this->rowsetToModels($resultSet);
    }

    public function rowToModel(Zend_Db_Table_Row $row, $updateRepo = false)
    {
    	$id = $row->{current($this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY))};

    	if (!$updateRepo && $this->_hasInRepository($id)) {
    		return $this->_getFromRepository($id);
    	}

    	$model = $this->getEmptyModel();

    	$model->fromArray($row->toArray());

    	$this->_addToRepository($model, $id);

    	return $model;
    }

    public function rowsetToModels(Zend_Db_Table_Rowset $rowset)
    {
    	$result = array();
    	foreach ($rowset as $row) {
    		$result[] = $this->rowToModel($row);
    	}

    	return $result;
    }

    protected function _modelToRowdata(Unwired_Model_Generic $model)
    {
    	return $model->toArray();
    }

    protected function _addToRepository(Unwired_Model_Generic $model, $id)
    {
    	$this->_repository[$id] = $model;
    	return $this;
    }

    protected function _hasInRepository($id)
    {
		return isset($this->_repository[$id]);
    }

    protected function _getFromRepository($id)
    {
		if (!$this->_hasInRepository($id)) {
			return null;
		}

		return $this->_repository[$id];
    }

    protected function _deleteFromRepository($id)
    {
		unset($this->_repository[$id]);

    	return $this;
    }

	/* (non-PHPdoc)
	 * @see Zend_Paginator_AdapterAggregate::getPaginatorAdapter()
	 * @return Unwired_Paginator_Adapter_Mapper
	 */
	public function getPaginatorAdapter() {
		return new Unwired_Paginator_Adapter_Mapper($this, $this->_customSelect);
	}

}