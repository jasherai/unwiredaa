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

class Zend_View_Helper_ShowEntityLog extends Zend_View_Helper_Abstract
{
    public function showEntityLog(Unwired_Model_Generic $entity, $id = null)
    {
        $mapper = new Default_Model_Mapper_Log();

        $acl = Zend_Registry::get('acl');

        $currentUser = Zend_Auth::getInstance()->getIdentity();

		if (!$acl->isAllowed($currentUser, $mapper->getEmptyModel(), 'view')) {
			return '';
		}

		$logEntries = $mapper->findBy(array('entity' => crc32(get_class($entity)),
		                                    'entity_id' => $id),
		                              10,
		                			  'stamp DESC');


		$this->view->logEntries = $logEntries;

		return $this->view->render('log/entity-log.phtml');
    }

}