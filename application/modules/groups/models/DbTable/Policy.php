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
 * DB table gateway for policy_group table
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Groups_Model_DbTable_Policy extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'policy_group';

	protected $_dependentTables = array('Users_Model_DbTable_NetworkUserPolicy');

}
