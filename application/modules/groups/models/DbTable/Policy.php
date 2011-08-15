<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * DB table gateway for policy_group table
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Groups_Model_DbTable_Policy extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'policy_group';

	/*protected $_dependentTables = array('Users_Model_DbTable_AdminGroup');

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
             );*/
}
