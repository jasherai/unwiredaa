<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Mapper for Groups_Model_Group
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Groups_Model_Mapper_Group extends Unwired_Model_Mapper {

    protected $_dbTableClass = 'Groups_Model_DbTable_Group';

    protected $_modelClass = 'Groups_Model_Group';

    public function getGroupsByAdmin(Users_Model_Admin $admin)
    {
    	//$relTable = new Users_Model_DbTable_AdminGroup();
    }
}