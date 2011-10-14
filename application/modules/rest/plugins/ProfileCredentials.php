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

class Rest_Plugin_ProfileCredentials extends Zend_Controller_Plugin_Abstract
{
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        if (!$request->getModuleName() == 'users' && !$request->getControllerName() == 'profile') {
            return;
        }

        if (!Zend_Auth::getInstance()->hasIdentity()) {
            continue;
        }

        $currentUser = Zend_Auth::getInstance()->getIdentity();

        $service = new Rest_Service_Auth();

        $key = $service->getKeyByAdmin($currentUser);

        if (!$key) {
            return;
        }

        $renderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');

        if (!$renderer->view) {
            return;
        }

        $profileKeyView = $renderer->view->partial('profile/key.phtml', 'rest', array('restKey' => $key));

        $this->getResponse()->appendBody($profileKeyView);
    }
}