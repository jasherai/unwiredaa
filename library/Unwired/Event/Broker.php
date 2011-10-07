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

	/**
	 * Add event message handler
	 *
	 * @param Unwired_Event_Handler_Interface $handler
	 * @param unknown_type $queue
	 * @return Unwired_Event_Broker
	 */
	public function addHandler(Unwired_Event_Handler_Interface $handler, $queue = null)
	{
		$this->_handlers[] = $handler;

		return $this;
	}

	/**
	 * Dispatch message to handlers
	 *
	 * @param Unwired_Event_Message $message
	 * @return Unwired_Event_Broker
	 */
	public function dispatch(Unwired_Event_Message $message)
	{
		foreach ($this->_handlers as $handler) {
			$handler->handle($message);
		}

		return $this;
	}
}