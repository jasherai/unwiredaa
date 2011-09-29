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
 * DB table gateway for network_user_policy
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Users_Model_DbTable_NetworkUserPolicy extends Zend_Db_Table_Abstract
{

    protected $_name = 'network_user_policy';

   	protected $_referenceMap = array(
            'User'  => array(
                'columns'           => 'user_id',
                'refTableClass'     => 'Users_Model_DbTable_NetworkUser',
                'refColumns'        => 'user_id'
                ),
            'Policy'  => array(
                'columns'           => 'policy_id',
                'refTableClass'     => 'Groups_Model_DbTable_Policy',
                'refColumns'        => 'policy_id'
                ),
             );
}

