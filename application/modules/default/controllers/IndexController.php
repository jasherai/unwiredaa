<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Index controller
 *
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

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

