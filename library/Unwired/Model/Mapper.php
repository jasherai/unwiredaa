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

class Unwired_Model_Mapper {

    protected $_dbTable = null;

    protected $_modelClass = null;

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
            $this->setDbTable('Application_Model_DbTable_Guestbook');
        }
        return $this->_dbTable;
    }

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

    public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new $this->_modelClass;

            $entry->fromArray($row->toArray());

            $entries[] = $entry;
        }

        return $entries;
    }

}