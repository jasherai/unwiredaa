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

class Default_Service_Log
{
	public function getDistinctEntities()
	{
		$pairs = $this->_getDistinctPairs('entity', 'entity_name');

		if (empty($pairs)) {
		    return $pairs;
		}

		$acl = null;
		if (Zend_Registry::isRegistered('acl')) {
			$acl = Zend_Registry::get('acl');
		}

		foreach ($pairs as $id => $name) {

		    $dbEntityName = $name;

			if ($acl) {
				$entityName = strtolower(preg_replace('/^(.*?)_.*_(.*)$/i', '$1_$2', $dbEntityName));
				if (!$acl->has($entityName)) {
					$entityName = $dbEntityName;
				} else {
					$entityName = 'resource_' . $entityName;
				}
			} else {
				$entityName = $dbEntityName;
			}

			$pairs[$id] = $entityName;
		}

		return $pairs;

	}

	public function getDistinctEvents()
	{
        return $this->_getDistinctPairs('event_id', 'event_name');
	}

	protected function _getDistinctPairs($idcol, $namecol)
	{
		$mapper = new Default_Model_Mapper_Log();

		$select = $mapper->getDbTable()->select(true);

		$select->reset('columns')
			   ->columns(array($idcol))
			   ->distinct(true)
			   ->where($idcol . ' IS NOT NULL');

		$result = $mapper->getDbTable()->fetchAll($select);

		$pairs = array();

		foreach ($result as $row) {
    		$select->reset('columns')
			   ->columns(array($namecol))
			   ->distinct(false)
			   ->where($idcol . ' = ?', $row->$idcol);

		    $dbName = $mapper->getDbTable()->getAdapter()->fetchOne($select);
		    if (empty($dbName)) {
		        continue;
		    }

			$pairs[$row->$idcol] = $dbName;
		}

		return $pairs;
	}
}