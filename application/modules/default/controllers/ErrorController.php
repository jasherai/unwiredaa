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
 * Error controller to handle errors/unhandled exceptions
 *
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Default_ErrorController extends Unwired_Controller_Action
{
    public $contexts = array('error'  => array('xml'));

    public function init()
    {
        parent::init();

        $router = $this->getFrontController()
                            ->getRouter();

        $route = $router->getCurrentRoute();

        $routeName = $router->getCurrentRouteName();

        if ($route instanceof Zend_Rest_Route
            || ($route instanceof Zend_Controller_Router_Route_Chain && $routeName == 'api')) {

            $this->_helper->contextSwitch()->initContext('xml');

            $this->getRequest()->setParam('format', 'xml');
        }
    }

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'default_error_error_generic';
            return;
        }

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
            case ($errors->exception->getCode() == 404):
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'default_error_error_not_found';
                break;
            case ($errors->exception->getCode() == 403):
                $this->getResponse()->setHttpResponseCode(403);
                $this->view->message = 'default_error_error_access_denied';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);

                // Log exception, if logger available
                $log = $this->getLog();
                if ($log) {
                    $logInfo = 'Exception: ' .$this->exception->getMessage() . "\n\t"
                    		 . 'Trace: ' . $this->exception->getTraceAsString() . "\n\t"
                             . 'Request: ' . var_export($errors->request->getParams(), true);
                    $log->crit($logInfo);
                }

                $this->view->message = 'default_error_error_application';
                break;
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

