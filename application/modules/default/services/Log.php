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
		$mapper = new Default_Model_Mapper_Log();

		$select = $mapper->getDbTable()->select(true);

		$select->reset('columns')
			   ->columns(array('entity', 'entity_name'))
			   ->distinct(true)
			   ->where('entity IS NOT NULL');

		$result = $mapper->getDbTable()->fetchAll($select);

		$entityIds = array();

		$acl = null;
		if (Zend_Registry::isRegistered('acl')) {
			$acl = Zend_Registry::get('acl');
		}

		foreach ($result as $row) {
			if ($acl) {
				$entityName = strtolower(preg_replace('/^(.*?)_.*_(.*)$/i', '$1_$2', $row->entity_name));
				if (!$acl->has($entityName)) {
					$entityName = $row->entity_name;
				} else {
					$entityName = 'resource_' . $entityName;
				}
			} else {
				$entityName = $row->entity_name;
			}

			$entityIds[$row->entity] = $entityName;
		}

		return $entityIds;

	}
}