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
 * Base functionality for a DB mapper
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Unwired_Model_Mapper implements Zend_Paginator_AdapterAggregate {

    protected $_dbTable = null;

    protected $_dbTableClass = null;

    protected $_modelClass = null;

    protected $_customSelect = null;

    protected $_defaultOrder = null;

    protected $_defaultJoinType = Zend_Db_Select::INNER_JOIN;

    protected $_repository = array();

    protected $_paginatorAdapter = null;

    protected $_eventBroker = null;

    static protected $_defaultEventBroker = null;

    protected $_eventsDisabled = false;


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

    public function getDefaultOrder()
    {
    	return $this->_defaultOrder;
    }

    public function setDefaultOrder($order)
    {
    	$this->_defaultOrder = $order;

    	return $this;
    }

    public function getDefaultJoinType()
    {
        return $this->_defaultJoinType;
    }

    public function setDefaultJoinType($type = Zend_Db_Select::INNER_JOIN)
    {
        $this->_defaultJoinType = $type;

        return $this;
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

        $oldData = array();

        if (!$row) {
        	$row = $this->getDbTable()->fetchNew();
        } else {
			$oldData = $row->toArray();
        }

        try {
        	$row->setFromArray($data);

        	/* foreach ($data as $col => $value) {
        		$row->$col = $value;
        	} */

        	$id = $row->save();

        	$model->fromArray($row->toArray());

        	if (empty($oldData)) {
        		$eventId = 'add';
        	} else {
        		$eventId = 'edit';
        	}

        	$this->sendEvent($eventId, $model, $id, $model->toArray());

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

		$modelId = array();

        foreach ($primary as $col) {
        	$modelId[$col] = $data[$col];

        	if (null === $data[$col]) {
        		if (count($primary) == 1) {
        			break;
        		}
        		$primaryFilter[] = $col . ' IS NULL';
        		$nulled++;
        	} else {
        		$primaryFilter[$col . ' = ?'] = $data[$col];
        		$modelId = $data[$col];
        	}
        }

        $result = $this->getDbTable()->delete($primaryFilter);

        if ($result > 0) {
        	$this->sendEvent('delete', $model, $modelId);
        }

        return $result;
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
    public function findBy($conditions, $limit = null, $order = null)
    {
        $table = $this->getDbTable();

    	if ($conditions instanceof Zend_Db_Select) {
    		$select = $conditions;
    	} elseif (is_array($conditions)) {

			$select = $this->getDbTable()->select(true);

			$cols = $this->getDbTable()->info(Zend_Db_Table::COLS);

			/**
			 * @todo Optimize this
			 */
			foreach ($conditions as $field => $value) {

				if (!in_array($field, $cols)) {
					$dependents = $this->getDbTable()->getDependentTables();

					foreach ($dependents as $depTable) {
						$tableInstance = (is_string($depTable)) ? new $depTable : $depTable;

						if (!in_array($field, $tableInstance->info(Zend_Db_Table::COLS))) {
							continue;
						}

						$joinTableName = $tableInstance->info(Zend_Db_Table::NAME);
						$fromTableName = $this->getDbTable()->info(Zend_Db_Table::NAME);

						$tablePkInfo = $this->getDbTable()->info(Zend_Db_Table::PRIMARY);
						$joinCols = array_intersect($tablePkInfo,
													$tableInstance->info(Zend_Db_Table::PRIMARY));

						$groupCondition = array();
						$joinCondition = array();

						/**
						 * @todo First check direct dependents by primary key then try any match
						 */
						if (empty($joinCols)) {
							$joinCols = array_intersect($this->getDbTable()->info(Zend_Db_Table::COLS),
													    $tableInstance->info(Zend_Db_Table::COLS));

							foreach ($tablePkInfo as $pk) {
						    	$groupCondition[] = "{$fromTableName}.{$pk}";
							}

							foreach ($joinCols as $col) {
								$joinCondition[] = "{$joinTableName}.{$col} = {$fromTableName}.{$col}";
							}
						} else {
							foreach ($joinCols as $col) {
								$groupCondition[] = "{$fromTableName}.{$col}";
								$joinCondition[] = "{$joinTableName}.{$col} = {$fromTableName}.{$col}";
							}
						}




						$select->setIntegrityCheck(false);
						if ($this->getDefaultJoinType() == Zend_Db_Select::INNER_JOIN) {
					        $select->joinInner($joinTableName, implode(' AND ', $joinCondition));
						} else {
						    $select->joinLeft($joinTableName, implode(' AND ', $joinCondition));
						}

						$select->group(implode(",", $groupCondition));

						$joinCols = null;
						$joinCondition = null;

						break;
					}
				}

				switch ($value) {
					case null:
						/**
						 * 0 is considered null in case
						 */
						if (is_numeric($value)) {
							$select->where($table->getAdapter()->quoteIdentifier($field) . ' = 0');
						} else {
							$select->where($table->getAdapter()->quoteIdentifier($field) . ' IS NULL');
						}
					break;

					case (is_array($value)):
						$select->where($table->getAdapter()->quoteIdentifier($field) . ' IN (?)', $value);
					break;

					case (strpos($value, '%') !== false):
						$select->where($table->getAdapter()->quoteIdentifier($field) . ' LIKE ?', $value);
					break;

					default:
						$select->where($table->getAdapter()->quoteIdentifier($field) . ' = ?', $value);
					break;
				}
			}
    	} else {
    		throw new Unwired_Exception('Unwired_Model_Mapper::findBy expects array with conditions or select instance');
    	}

    	if ($order) {
    		$select->order($order);
    	} else if ($this->getDefaultOrder()) {
    		$select->order($this->getDefaultOrder());
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
     * @param string $order
     * @return array
     */
    public function fetchAll($order = null)
    {
        $resultSet = $this->getDbTable()->fetchAll(null, $order);

        return $this->rowsetToModels($resultSet);
    }

    public function rowToModel(Zend_Db_Table_Row $row, $updateRepo = false)
    {
    	$id = $row->{current($this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY))};

    	if ((null !== $id) && !$updateRepo && $this->_hasInRepository($id)) {
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


    /**
     * Send event to event broker to be dispatched
     *
     * @param string $event
     * @param Unwired_Model_Generic $entity
     * @param integer $entityId
     * @param array $params
     */
    public function sendEvent($event, Unwired_Model_Generic $entity, $entityId, array $params = array())
    {
    	if ($this->isEventsDisabled()) {
    		return true;
    	}

		$broker = $this->getEventBroker();

		if (!$broker) {
			return false;
		}

		$data = array('entity' => $entity,
					  'entityId' => $entityId,
					  'user'	=> Zend_Auth::getInstance()->getIdentity(),
					  'params' => $params);

		$message = new Unwired_Event_Message($event, $data);

		$broker->dispatch($message);

		return true;
    }

    /**
     * Get event broker
     * @return Unwired_Event_Broker
     */
    public function getEventBroker()
    {
    	if (null === $this->_eventBroker) {
    		$this->_eventBroker = self::getDefaultEventBroker();
    	}

    	return $this->_eventBroker;
    }

    /**
     * Set event broker
     * @param Unwired_Event_Broker $broker
     * @return Unwired_Event_Broker
     */
    public function setEventBroker(Unwired_Event_Broker $broker)
    {
    	$this->_eventBroker = $broker;

    	return $this;
    }

    /**
     * Get default event broker
     * @return Unwired_Event_Broker
     */
    static public function getDefaultEventBroker()
    {
    	if (null === self::$_defaultEventBroker && Zend_Registry::isRegistered('Unwired_Event_Broker')) {
    		self::$_defaultEventBroker = Zend_Registry::get('Unwired_Event_Broker');
    	}

    	return self::$_defaultEventBroker;
    }

    /**
     * Set default event broker
     * @param Unwired_Event_Broker $broker
     * @return Unwired_Event_Broker
     */
    static public function setDefaultEventBroker(Unwired_Event_Broker $broker)
    {
    	self::$_defaultEventBroker = $broker;
    }

    public function isEventsDisabled()
    {
    	return (bool) $this->_eventsDisabled;
    }

    public function setEventsDisabled($disabled = false)
    {
    	$this->_eventsDisabled = (bool) $disabled;

  		return $this;
    }

    public function setPaginatorAdapter(Zend_Paginator_Adapter_Interface $adapter = null)
    {
    	$this->_paginatorAdapter = $adapter;

    	return $this;
    }

	/* (non-PHPdoc)
	 * @see Zend_Paginator_AdapterAggregate::getPaginatorAdapter()
	 * @return Unwired_Paginator_Adapter_Mapper
	 */
	public function getPaginatorAdapter() {
		if ($this->_paginatorAdapter == null) {
			$this->_paginatorAdapter = new Unwired_Paginator_Adapter_Mapper($this, $this->_customSelect);
		}

		return $this->_paginatorAdapter;
	}

}