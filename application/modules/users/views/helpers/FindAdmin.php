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

class Users_View_Helper_FindAdmin extends Zend_View_Helper_Abstract
{
	protected $_mapper = null;

	/**
	 *
	 * @return Users_Model_Mapper_Admin
	 */
	protected function _getMapperAdmin()
	{
		if (null === $this->_mapper) {
			$this->_mapper = new Users_Model_Mapper_Admin();
		}

		return $this->_mapper;
	}

	/**
	 * Find admin user
	 *
	 * @param integer $id
	 * @return Users_Model_Admin
	 */
	public function findAdmin($id)
	{
		$id = (int) $id;

		return $this->_getMapperAdmin()->find($id);
	}
}