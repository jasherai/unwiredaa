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
        $data = $model->toArray();

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

        try {
        	if (!count($primaryFilter) || $nulled == count($primary)) {
        		$data = array_diff_key($data, array_flip($primary));

        		$pkData = $this->getDbTable()->insert($data);

	        	/**
	        	 * Populate the model with primary key values from the inserted row
	        	 */
	        	if (is_array($pkData)) {
	        		$data = array_merge($data, $pkData);
	        	} else {
	        		$primaryKey = array_pop($primary);
	        		$data[$primaryKey] = $pkData;
	        	}

	        	$model->fromArray($data);
	        } else {
	        	$this->getDbTable()->update($data);
	        }
        } catch (Exception $e) {
        	throw new Unwired_Exception('Error saving the information', 500, $e);
        }

        return $model;
    }

    /**
     * Find single entity by its ID
     *
     * @param mixed $id
     * @return Unwired_Model_Generic
     */
    public function find($id)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return null;
        }

        $row = $result->current();

        $model = new $this->_modelClass;

        $model->fromArray($row->toArray());

        return $model;
    }

    /**
     * Find entities by criteria
     *
     * @param array $conditions
     * @param int|null $limit
     */
    public function findBy(array $conditions, $limit = null)
    {
		$select = $this->getDbTable()->select(true);

		foreach ($conditions as $field => $value) {
			switch ($value) {
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

		return $this->_rowsetToModels($result);
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

        return $this->_rowsetToModels($resultSet);
    }

    protected function _rowToModel(Zend_Db_Table_Row $row)
    {
    	$model = $this->getEmptyModel();

    	$model->fromArray($row->toArray());

    	return $model;
    }

    protected function _rowsetToModels(Zend_Db_Table_Rowset $rowset)
    {
    	$result = array();
    	foreach ($rowset as $row) {
    		$result[] = $this->_rowToModel($row);
    	}

    	return $result;
    }

	/* (non-PHPdoc)
	 * @see Zend_Paginator_AdapterAggregate::getPaginatorAdapter()
	 * @return Unwired_Paginator_Adapter_Mapper
	 */
	public function getPaginatorAdapter() {
		return new Unwired_Paginator_Adapter_Mapper($this, $this->_customSelect);
	}

}