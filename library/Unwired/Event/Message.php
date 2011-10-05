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

class Unwired_Event_Message
{
	protected $_messageId;

	protected $_data = null;

	public function __construct($messageId, $data = null)
	{
		$this->_messageId = $messageId;

		$this->_data = $data;
	}

	public function getMessageId()
	{
		return $this->_messageId;
	}

	public function getData()
	{
		return $this->_data;
	}
}