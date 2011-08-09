<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Simple helper to display messages to user
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Unwired_View_Helper_UiMessage extends Zend_View_Helper_Abstract
{
	protected $_messages = array();

	public function __construct()
	{
		$session = new Zend_Session_Namespace('uiMessage');

		if (isset($session->messages) && count($session->messages)) {
			$this->_messages = $session->messages;
		}

		$session = null;
	}

	public function uiMessage($message, $type = 'info')
	{
		if (!isset($this->_messages[$type])) {
			$this->_messages[$type] = array();
		}

		$this->_messages[$type][] = $message;

		return $this;
	}

	public function getMessages() {
		return $this->_messages;
	}

	public function clearMessages()
	{
		$this->_messages = array();
		return $this;
	}

	public function __toString()
	{
		$result = '';
		foreach ($this->_messages as $type => $messages) {
			$result = "<div class=\"{$type}\">\n";
			foreach ($messages as $message) {
				$result .= '<p>' . $message . "</p>\n";
			}
			$result .= "</div>\n";
		}

		$this->clearMessages();

		return $result;
	}


	public function __destruct()
	{
		$session = new Zend_Session_Namespace('uiMessage');

		if (!empty($this->_messages)) {
			$session->messages = $this->_messages;
		} else {
			$session->messages = null;
		}

		$session = null;
	}
}