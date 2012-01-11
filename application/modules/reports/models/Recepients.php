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
 * Report Recepients
 * @author G. Sokolov <joro@web-teh.net>
 */
class Reports_Model_Recepients extends Unwired_Model_Generic
{

	protected $_recepientId = null;

	protected $_groupId = null;

	protected $_email = null;

	/**
	 * @return int $_recepientId
	 */
	public function getRecepientId() {
		return $this->_recepientId;
	}

	/**
	 * @return the $_groupId
	 */
	public function getGroupId() {
		return $this->_groupId;
	}

	/**
	 * @return the $_email
	 */
	public function getEmail() {
		return $this->_email;
	}

	/**
	 * @param int $_recepientId
	 */
	public function setRecepientId($_recepientId) {
		$this->_recepientId = $_recepientId;
	}

	/**
	 * @param int $_groupId
	 */
	public function setGroupId($_groupId) {
		$this->_groupId = $_groupId;
	}

	/**
	 * @param string $_email
	 */
	public function setEmail($_email) {
		$this->_email = $_email;
	}


}