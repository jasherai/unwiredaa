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

class Unwired_Event_Broker
{
	protected $_queues = array();

	protected $_handlers = array();

	public function addHandler(Unwired_Event_Handler_Interface $handler, $queue = null)
	{

	}
}