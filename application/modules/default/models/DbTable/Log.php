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
 * DB table gateway for log table
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Default_Model_DbTable_Log extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'log';

	/**
	 * @todo This is wrong but works!
	 * Fix later
	 *
	 * @var unknown_type
	 */
	protected $_dependentTables = array('Users_Model_DbTable_AdminUser');
	/*
	protected $_referenceMap = array(
            'Admin'  => array(
                'columns'           => 'user_id',
                'refTableClass'     => 'Users_Model_DbTable_AdminUser',
                'refColumns'        => 'user_id'
                )
             );
    */
}
