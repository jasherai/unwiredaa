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

    protected $_cache = null;

    protected function _getCache()
    {
        if (null === $this->_cache) {
            $cacheMgr = $this->getInvokeArg('bootstrap')->getResource('Cachemanager');

            $this->_cache = $cacheMgr->getCache('default');
        }

        return $this->_cache;
    }

    public function indexAction()
    {
    	/**
    	 * @todo Make ajax calls to load nodes only in current viewport
    	 */

        $nodes = $this->getCache()->load('device_map_data');

        if (!$nodes) {
            $mapper = new Nodes_Model_Mapper_Node();

            $nodes = $mapper->fetchAll();

            $this->getCache()->save($nodes, 'device_map_data', array('node'), 3600);
        }

        $this->view->nodes = $nodes;
    }

    public function statsAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $location = $this->getRequest()->getParam('location', null);

        $stats = array();

        if (!$location) {
             echo $this->view->json(array());
             return;
        }

        $stats = $this->getCache()->load('device_map_stats_' . $location);

        if (empty($stats)) {
            $serviceChilli = new Default_Service_Chilli();

            $stats = $serviceChilli->getDeviceStatistics($location);

            $this->getCache()->save($stats, 'device_map_stats_' . $location, array('node', $location), 20);
        }

        echo $this->view->json($stats);
    }


}

