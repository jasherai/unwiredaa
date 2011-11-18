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

class Captive_Service_Group extends Unwired_Service_Tree
{

	public function findGroup($groupId, $parents = false, $children = false)
	{
		return parent::findNode($groupId, $parents, $children);
	}

}