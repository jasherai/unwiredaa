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

class Rest_Model_Mapper_Key extends Unwired_Model_Mapper
{
	protected $_dbTableClass = 'Rest_Model_DbTable_Key';

	protected $_modelClass = 'Rest_Model_Key';


	public function prepareIndex(array $filters = array())
	{
	    $select = $this->getDbTable()->select();

	    $select->setIntegrityCheck(false)
	           ->from('admin_user')
	           ->joinLeft('rest_key',
	           			  'admin_user.user_id = rest_key.user_id',
	                       array('*', new Zend_Db_Expr('IFNULL(rest_key.user_id, admin_user.user_id) as `user_id`')))
	           ->order(array('email ASC', 'key DESC'));

	    foreach ($filters as $field => $value) {
	        if (strpos($value, '%') !== false) {
	            $select->where($this->getDbTable()->getAdapter()->quoteIdentifier($field) . ' LIKE ?', $value);
	        } else {
	            $select->where($this->getDbTable()->getAdapter()->quoteIdentifier($field) . ' = ?', $value);
	        }
	    }
        //$this->findBy($select, 0, array('email ASC', 'key DESC'));

        $this->_customSelect = $select;

        return $this;
	}

	public function rowToModel(Zend_Db_Table_Row $row, $updateRepo = false)
	{
	    $model = parent::rowToModel($row, $updateRepo);

	    if ($model && isset($row->email)) {
	        $admin = new Users_Model_Admin();
	        $admin->fromArray($row->toArray());
	        $model->setAdmin($admin);
	    }

	    return $model;
	}
}