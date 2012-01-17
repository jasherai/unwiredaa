<?php

class Widget_Login extends Unwired_Widget_Abstract
{

    public function render($content)
    {
        $chilliData = $this->getView()->chilliData;

        $splashPage = $this->getView()->splashPage;

        $settings = $splashPage->getTemplate()->getSettings();
        $authSettings = isset($settings['auth']) ? $settings['auth'] : array('guest','login', 'autologin');

        if (!$chilliData->getTerms() && !Zend_Auth::getInstance()->hasIdentity()
            && in_array('autologin', $authSettings)) {

            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');

            $redirector->gotoRouteAndExit(array('module' => 'default',
                                                'controller' => 'user',
                                                'action' => 'guest'),
                                         'default',
                                         true);
            return '';
        }

        try {
            return $this->getView()->render(($splashPage->isMobile() ? 'login-mobile.phtml' : 'login.phtml'));
        } catch (Exception $e) {
            return '';
        }
    }

    public function renderAdmin($content, $params = array())
    {
        /**
         * @todo Login widget administration
         */
        return '';
    }
}