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

class Default_LogController extends Unwired_Controller_Crud
{
	protected $_defaultMapper = 'Default_Model_Mapper_Log';

	public function indexAction()
	{
		$filters = $this->_getFilters();

		$this->_getDefaultMapper()->findBy($filters, 0, 'stamp DESC');
		parent::_index();
	}

	protected function _getFilters()
	{
		$filter = array();

		$filter['entity'] = $this->getRequest()->getParam('entity', null);
		$filter['event_id'] = $this->getRequest()->getParam('event_id', null);
		$filter['entity_id'] = $this->getRequest()->getParam('entity_id', null);
		$filter['email'] = $this->getRequest()->getParam('email', null);
		$filter['remote_host'] = $this->getRequest()->getParam('remote_host', null);

		$this->view->filter = $filter;

		foreach ($filter as $key => $value) {
			if (null == $value || empty($value)) {
				unset($filter[$key]);
				continue;
			}

			if ($key == 'event_id' || $key == 'entity_id') {
				continue;
			}
			$filter[$key] = '%' . preg_replace('/[^a-z0-9\s\@\-\:\._]+/iu', '', $value) . '%';
		}

		return $filter;
	}

	public function viewAction()
	{
	    $id = (int) $this->getRequest()->getParam('id', 0);

	    if (!$id) {
	        throw new Unwired_Exception('entity_not_found', 404);
	    }

	    $logEntity = $this->_getDefaultMapper()->find($id);

	    if (!$logEntity) {
	        throw new Unwired_Exception('entity_not_found', 404);
	    }

	    $this->view->logEntity = $logEntity;

	    $class = $logEntity->getEntityName();

		if (class_exists($class)) {
		    $entity = new $class;

		    $mapperClass = str_replace('_Model_', '_Model_Mapper_', $class);

		    if (class_exists($mapperClass)) {
		    	$mapper = new $mapperClass;

		    	$currentEntity = $mapper->find($logEntity->getEntityId());

		    	$mapper = null;
		    }
		}

	}
}