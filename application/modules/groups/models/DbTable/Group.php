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
 * DB table gateway for group table
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Groups_Model_DbTable_Group extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'group';

	protected $_dependentTables = array('Users_Model_DbTable_AdminGroup', 'Report_Model_DbTable_Node');

	protected $_referenceMap = array(
            'Role'  => array(
                'columns'           => 'role_id',
                'refTableClass'     => 'Groups_Model_DbTable_Role',
                'refColumns'        => 'user_id'
                ),
             'Parent'  => array(
                'columns'           => 'parent_id',
                'refTableClass'     => 'Groups_Model_DbTable_Group',
                'refColumns'        => 'group_id'
                )
             );
}
