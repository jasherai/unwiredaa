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
 * DB table gateway for report_codetemplate table
 * @author G. Sokolov <joro@web-teh.net>
 */

class Reports_Model_DbTable_Codetemplate extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'report_codetemplate';

	protected $_dependentTables = array('Reports_Model_DbTable_Group');
}