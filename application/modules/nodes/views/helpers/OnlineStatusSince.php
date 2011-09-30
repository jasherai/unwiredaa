<?php

class Nodes_View_Helper_OnlineStatusSince extends Zend_View_Helper_Abstract
{
	public function onlineStatusSince(Nodes_Model_Node $node)
	{
		$status = $node->getOnlineStatus();

		$timestamp = new Zend_Date($node->getOnlineStatusChanged());

		$class = $status ? 'online' : 'offline';

		return "<span class=\"{$class}\">"
			   . $this->view->translate('nodes_viewhelper_online_status_' . $status, $timestamp)
			   . '</span>';
	}
}