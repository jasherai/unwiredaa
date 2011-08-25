<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Mapper for Groups_Model_Role
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Groups_Model_Mapper_Role extends Unwired_Model_Mapper {

    protected $_dbTableClass = 'Groups_Model_DbTable_Role';

    protected $_modelClass = 'Groups_Model_Role';

    public function rowToModel(Zend_Db_Table_Row $row)
    {
    	$id = $row->{current($this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY))};

    	if ($this->_hasInRepository($id)) {
    		return $this->_getFromRepository($id);
    	}

    	$model = $this->getEmptyModel();

    	$data = $row->toArray();

    	$data['permissions'] = empty($data['permissions']) ? array() : unserialize($data['permissions']);

    	if (!is_array($data['permissions'])) {
    		Zend_Debug::dump($data['permissions']); die();
    	}
    	$model->fromArray($data);

    	$this->_addToRepository($model, $id);

    	return $model;
    }

	protected function _modelToRowdata(Unwired_Model_Generic $model)
    {
    	$data = $model->toArray();

    	$data['permissions'] = serialize($data['permissions']);

    	return $data;
    }

}