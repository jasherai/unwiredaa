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

abstract class Unwired_Rest_Controller extends Unwired_Controller_Crud
{

	protected $_autoRedirect = false;

	protected $_actionsToReferer = array();

    public $contexts = array('index'  => array('xml'),
                             'delete' => array('xml'),
                             'get'	 => array('xml'),
                             'put'    => array('xml'),
                             'post'   => array('xml'));

    public function init()
    {
        parent::init();

        $router = $this->getFrontController()
                            ->getRouter();

        $route = $router->getCurrentRoute();

        $routeName = $router->getCurrentRouteName();

        //$this->_helper->contextSwitch()->initContext();

        if ($route instanceof Zend_Rest_Route
            || ($route instanceof Zend_Controller_Router_Route_Chain && $routeName == 'api')) {

            $this->_helper->contextSwitch()->initContext('xml');

            $this->getRequest()->setParam('format', 'xml');

            if (!in_array($this->getRequest()->getActionName(), array_keys($this->contexts))) {
                throw new Unwired_Exception('Page not found!', 404);
            }
        }
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction()
    {
        $this->_index();
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction()
    {
        $this->_edit();
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction()
    {
        $this->_edit();
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction()
    {
        $this->_edit();
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction()
    {
        $this->_delete();
    }
}