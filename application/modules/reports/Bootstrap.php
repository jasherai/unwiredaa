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
 * Reports module bootstrap
 * @author G. Sokolov <joro@web-teh.net>
 */
class Reports_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		//$acl->addResource(new Reports_Service_Acl());
		$acl->addResource(new Reports_Model_CodeTemplate());
		$acl->addResource(new Reports_Model_Group());
		$acl->addResource(new Reports_Model_Items());

		return $acl;
	}
}