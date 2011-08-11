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
	protected $_session = null;

	protected $_messages = array();

	protected $_translate = null;

	public function __construct()
	{
		$session = new Zend_Session_Namespace('uiMessage');

		if (isset($session->messages) && count($session->messages)) {
			$this->_messages = $session->messages;
		}

		$session = null;
	}

	public function uiMessage($message = null, $type = 'info')
	{
		if ($message) {
			if (!isset($this->_messages[$type])) {
				$this->_messages[$type] = array();
			}

			if ($this->getTranslate()) {
				$message = $this->getTranslate()->translate($message);
			}

			$this->_messages[$type][] = $message;
		}

		$this->getSession()->messages = $this->_messages;

		return $this;
	}

	public function getSession()
	{
		if (null === $this->_session) {
			$session = new Zend_Session_Namespace('uiMessage');
		}

		return $session;
	}

	public function getTranslate()
	{
		if (null === $this->_translate) {
			if (Zend_Registry::isRegistered('Zend_Translate')) {
				$this->_translate = Zend_Registry::get('Zend_Translate');
			}
		}
		return $this->_translate;
	}

	public function getMessages() {
		return $this->_messages;
	}

	public function clearMessages()
	{
		$this->_messages = array();

		$this->getSession()->messages = null;

		return $this;
	}

	public function __toString()
	{
		$result = '';

		foreach ($this->_messages as $type => $messages) {
			$result .= "<div class=\"message {$type}\">\n";
			foreach ($messages as $message) {
				$result .= '<p>' . $message . "</p>\n";
			}
			$result .= "</div>\n";
		}

		$this->clearMessages();

		return $result;
	}
}