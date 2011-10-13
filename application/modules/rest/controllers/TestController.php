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

class Rest_TestController extends Unwired_Controller_Action
{
    protected $_currentUser = null;

    public function preDispatch()
    {
        parent::preDispatch();

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->_currentUser = Zend_Auth::getInstance()->getIdentity();
        }

        if (null === $this->_currentUser || !$this->getAcl()->hasRole($this->_currentUser)) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

		if (!$this->getAcl()->isAllowed($this->_currentUser, 'rest_key', 'special')) {
			$this->view->uiMessage('access_not_allowed_view', 'error');
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}
    }

    public function indexAction()
    {

    }

    public function signAction()
    {

        $params = $this->getRequest()->getParams();

        $appKey = $this->getRequest()->getParam('app_key', '');
        $appSecret = $this->getRequest()->getParam('app_secret', '');

        $resturl = $this->getRequest()->getParam('resturl', '');

        unset($params['module']);
        unset($params['controller']);
        unset($params['action']);
        if (empty($params['id'])) {
            unset($params['id']);
        }
        unset($params['app_secret']);
        unset($params['resturl']);

        $date = new Zend_Date();
        $params['timestamp'] = $date->getTimestamp() - $date->getGmtOffset();

        $data = $resturl . '?' . http_build_query($params);

        $checkSignature = Zend_Crypt_Hmac::compute($appSecret, 'md5', $data);

        $params['signature'] = $checkSignature;

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

      //  Zend_Json::encode($valueToEncode)
        echo $this->view->json(array(http_build_query($params)));
        die('[' + http_build_query($params) + ']');
    }
}