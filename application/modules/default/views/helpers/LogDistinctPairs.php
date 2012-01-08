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

class Zend_View_Helper_LogDistinctPairs extends Zend_View_Helper_Abstract
{

	public function logDistinctPairs($pair = 'event')
	{
		$service = new Default_Service_Log();

		switch ($pair) {
		    case 'event':
		        $ids = $service->getDistinctEvents();
		    break;

		    case 'entity':
		    default:
		        $ids = $service->getDistinctEntities();
		    break;
		}

		foreach ($ids as $key => $value) {
			$ids[$key] = $this->view->translate($value);
		}
		
		asort($ids);
		
		return $ids;
	}
}