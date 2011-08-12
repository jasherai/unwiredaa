<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * DB table gateway for admin_user
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Model_DbTable_AdminUser extends Zend_Db_Table_Abstract
{

    protected $_name = 'admin_user';

	protected $_dependentTables = array('Users_Model_DbTable_AdminGroup');
}

