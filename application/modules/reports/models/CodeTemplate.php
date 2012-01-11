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
 * Report Codetemplate
 * @author G. Sokolov <joro@web-teh.net>
 */
class Reports_Model_CodeTemplate extends Unwired_Model_Generic implements Zend_Acl_Role_Interface,
																 Zend_Acl_Resource_Interface
{
	protected $_codetemplateId = null;

	protected $_className = null;

	protected $_title = null;

	/**
	 * @return the $codeTemplateId
	 */
	public function getCodeTemplateId() {
		return $this->_codetemplateId;
	}

	/**
	 * @param integer $codeTemplateId
	 */
	public function setCodeTemplateId($codeTemplateId) {
		$this->_codetemplateId = $codeTemplateId;

		return $this;
	}

	/**
	 * @return the $className
	 */
	public function getClassName() {
		return $this->_className;
	}

	/**
	 * @param string $className
	 */
	public function setClassName($className) {
		$this->_className = $className;

		return $this;
	}

	/**
	 * @return the $title
	 */
	public function getTitle() {
		return $this->_title;
	}

	/**
	 * @param string $nodeId
	 */
	public function setTitle($title) {
		$this->_title = $title;

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
		return 'reports_index';
	}
}