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
 * Index controller
 *
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Default_IndexController extends Unwired_Controller_Action
{

    public function indexAction()
    {
    	/**
    	 * @todo Make ajax calls to load nodes only in current viewport
    	 */
        $mapper = new Nodes_Model_Mapper_Node();

        $nodes = $mapper->fetchAll();

        $this->view->nodes = $nodes;
    }


}

