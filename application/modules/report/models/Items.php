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
 * Report Items
 * @author G. Sokolov <joro@web-teh.net>
 */
class Report_Model_Items extends Unwired_Model_Generic  implements Zend_Acl_Role_Interface,
																 Zend_Acl_Resource_Interface
{

	protected $_itemId = null;

	protected $_reportGroupId = null;

	protected $_title = null;

	protected $_dateAdded = null;

	protected $_data = array();
	protected $_htmldata = array();
	/**
	 * @return the $_htmldata
	 */
	public function getHtmldata() {
		return $this->_htmldata;
	}

	/**
	 * @param multitype: $_htmldata
	 */
	public function setHtmldata($_htmldata) {
		$this->_htmldata = $_htmldata;
	}

	/**
	 * @return the $_itemId
	 */
	public function getItemId() {
		return $this->_itemId;
	}

	/**
	 * @return the $_groupId
	 */
	public function getReportGroupId() {
		return $this->_reportGroupId;
	}

	/**
	 * @return the $_title
	 */
	public function getTitle() {
		return $this->_title;
	}

	/**
	 * @return the $_dateAdded
	 */
	public function getDateAdded() {
		return $this->_dateAdded;
	}

	/**
	 * @return the $_data
	 */
	public function getData($deserialize = false, $assoc = false) {
		if ($deserialize) {
			return json_decode($this->_data, $assoc);
		} else {
			return $this->_data;
		}
	}

	/**
	 * @param int $_itemId
	 */
	public function setItemId($_itemId) {
		$this->_itemId = $_itemId;
	}

	/**
	 * @param int $_groupId
	 */
	public function setReportGroupId($_groupId) {
		$this->_reportGroupId = $_groupId;
	}

	/**
	 * @param string $_title
	 */
	public function setTitle($_title) {
		$this->_title = $_title;
	}

	/**
	 * @param sql date $_dateAdded
	 */
	public function setDateAdded($_dateAdded) {
		$this->_dateAdded = $_dateAdded;
	}

	/**
	 * @param array $_data
	 */
	public function setData($_data) {
        /**
         * @todo Serialized data in DB is in fact object of stdClass?!?!!?!?
         *       Fix this mess and keep constent data types
         */
	    if (is_array($_data)) {
			$this->_data = json_encode($_data);
		} else {
		    $dataStruct = @json_decode($_data);
			$this->_data = (is_object($dataStruct) || is_array($dataStruct)) ? $_data : json_encode(array());
		}
	}

	/**
	 * ACL role unique identifier
	 *
	 * @see Zend_Acl_Role_Interface::getRoleId()
	 */
	public function getRoleId()
	{
		return $this->getTitle();
	}

	/* (non-PHPdoc)
	 * @see Zend_Acl_Resource_Interface::getResourceId()
	*/
	public function getResourceId() {
		return 'reports_items';
	}


}