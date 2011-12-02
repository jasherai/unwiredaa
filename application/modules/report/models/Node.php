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

/**
 * Report Node
 * @author G. Sokolov <joro@web-teh.net>
 */
class Report_Model_Node extends Unwired_Model_Generic 
{
	protected $_groupId = null;
	
	protected $_nodeId = null;
	/**
	 * @return the $_nodeId
	 */
	public function getNodeId() {
		return $this->_nodeId;
	}
	
	/**
	 * @param NULL $_nodeId
	 */
	public function setNodeId($_nodeId) {
		$this->_nodeId = $_nodeId;
	}

	/**
	 * @return the $_groupId
	 */
	public function getGroupId() {
		return $this->_groupId;
	}

	/**
	 * @param NULL $_groupId
	 */
	public function setGroupId($_groupId) {
		$this->_groupId = $_groupId;
	}

}