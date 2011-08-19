<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * DB table gateway for node_settings table
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Nodes_Model_DbTable_NodeSettings extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'node_settings';

	protected $_referenceMap = array(
            'Node'  => array(
                'columns'           => 'node_id',
                'refTableClass'     => 'Nodes_Model_DbTable_Node',
                'refColumns'        => 'node_id'
                )
             );
}