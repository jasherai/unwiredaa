<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * DB table gateway for admin_user_group
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Model_DbTable_AdminGroup extends Zend_Db_Table_Abstract
{

    protected $_name = 'admin_user_group';


	protected $_referenceMap = array(
            'Admin'  => array(
                'columns'           => 'user_id',
                'refTableClass'     => 'Users_Model_DbTable_AdminUser',
                'refColumns'        => 'user_id'
                ),
             'Group'  => array(
                'columns'           => 'group_id',
                'refTableClass'     => 'Groups_Model_DbTable_Group',
                'refColumns'        => 'group_id'
                )
             );

}

