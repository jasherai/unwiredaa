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

class Rest_Model_DbTable_Key extends Zend_Db_Table_Abstract
{
    protected $_name = 'rest_key';

    protected $_dependentTables = array('Users_Model_DbTable_AdminUser');
}