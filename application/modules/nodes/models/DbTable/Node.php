<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * DB table gateway for node table
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Nodes_Model_DbTable_Node extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'node';

	protected $_dependentTables = array('Nodes_Model_DbTable_NodeLocation',
										'Nodes_Model_DbTable_NodeSettings');
}
