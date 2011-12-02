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
 * Report Group
 * @author G. Sokolov <joro@web-teh.net>
 */
class Report_Model_Group extends Unwired_Model_Generic  implements Zend_Acl_Role_Interface,
																 Zend_Acl_Resource_Interface
{
	protected $_reportGroupId = null;
	
	protected $_codetemplateId = null;

	protected $_title = null;
	
	protected $_dateAdded = null;
	
	protected $_nodeId = null;
	
	protected $_dateFrom = null;
	
	protected $_dateTo = null;
	
	protected $_reportType = null;
	
	protected $_reportInterval = 'day';
	
	protected $_description = null;
	
	protected $_groupsAssigned = array();
	
	protected $_recepients = array();

	/**
	 * @return the $_recepients
	 */
	public function getRecepients() {
		return $this->_recepients;
	}

	/**
	 * @param multitype: $_recepients
	 */
	public function setRecepients($_recepients) {
		if (is_array($_recepients)) {
			$this->_recepients = $_recepients;
		} elseif (is_string($_recepients)) {
			$this->_recepients = explode(', ', $_recepients);
		}
	}
	
	/**
	 * @return the $groupsAssigned
	 */
	public function getGroupsAssignedFormatted() {
		$groups = $this->getGroupsAssigned();
		$result = array();
		foreach ($groups as $key => $value) {
			$result[] = $value->getName();
		}
		return implode("\n", $result);
	}

	/**
	 * @return the $groupsAssigned
	 */
	public function getGroupsAssigned() {
		return $this->_groupsAssigned;
	}
	
	/**
	 * key = group id, value = role id
	 * @param array $groupsAssigned
	 */
	public function setGroupsAssigned(array $groupsAssigned = array()) {
		$this->_groupsAssigned = $groupsAssigned;
		return $this;
	}
	
	public function getGroupAssignedRoleId($groupId)
	{
		/**
		 * @todo Possible ACL problem with false as result
		 */
		return isset($this->_groupsAssigned[$groupId]) ? $this->_groupsAssigned[$groupId] : false;
	}
	
	/**
	 * @return the $_reportType
	 */
	public function getReportType() {
		return $this->_reportType;
	}

	/**
	 * @return the $_reportInterval
	 */
	public function getReportInterval() {
		return $this->_reportInterval;
	}

	/**
	 * @param field_type $_reportType
	 */
	public function setReportType($_reportType) {
		$this->_reportType = $_reportType;
	}

	/**
	 * @param field_type $_reportInterval
	 */
	public function setReportInterval($_reportInterval) {
		$this->_reportInterval = $_reportInterval;
	}

	/**
	 * @return the $groupId
	 */
	public function getReportGroupId() {
		return $this->_reportGroupId;
	}

	/**
	 * @param integer $reportGroupId
	 */
	public function setReportGroupId($reportGroupId) {
		$this->_reportGroupId = $reportGroupId;

		return $this;
	}
	
	/**
	 * @return the $templateId
	 */
	public function getCodetemplateId() {
		return $this->_codetemplateId;
	}

	/**
	 * @param integer $codetemplateId
	 */
	public function setCodetemplateId($codetemplateId) {
		$this->_codetemplateId = $codetemplateId;

		return $this;
	}
		
	/**
	 * @return the $title
	 */
	public function getTitle() {
		return $this->_title;
	}
	
	/**
	 * @param the $title
	 */
	public function setTitle($title) {
		$this->_title = $title;
		
		return $this; 
	}

	/**
	 * @return sql date $dateAdded
	 */
	public function getDateAdded() {
		return $this->_dateAdded;
	}
	
	/**
	 * @param sql date $dateAdded
	 */
	public function setDateAdded($dateAdded) {
		$this->_dateAdded = $dateAdded;

		return $this;
	}
	
	/**
	 * @return the $nodeId
	 */
	public function getNodeId() {
		return $this->_nodeId;
	}

	/**
	 * @param integer $groupId
	 */
	public function setNodeId($nodeId) {
		$this->_nodeId = $nodeId;

		return $this;
	}
	
/**
	 * @return sql date $dateFrom
	 */
	public function getDateFrom() {
		if ($this->_dateFrom != '') {
			return date('Y-m-d', strtotime($this->_dateFrom));
		} else {
			return date('Y-m-d');
		}
	}
	
	/**
	 * @param sql date $dateFrom
	 */
	public function setDateFrom($dateFrom) {
		$this->_dateFrom = $dateFrom;

		return $this;
	}
	
/**
	 * @return sql date $dateTo
	 */
	public function getDateTo() {
		if ($this->_dateTo != '') {
			return date('Y-m-d', strtotime($this->_dateTo));
		} else {
			return date('Y-m-d');
		}
	}
	
	/**
	 * @param sql date $dateTo
	 */
	public function setDateTo($dateTo) {
		$this->_dateTo = $dateTo;

		return $this;
	}
	
	/**
	 * @return the $description
	 */
	public function getDescription() {
		return $this->_description;
	}
	
	/**
	 * @param string $description
	 */
	public function setDescription($description) {
		$this->_description = $description;

		return $this;
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
		return 'reports_group';
	}

}