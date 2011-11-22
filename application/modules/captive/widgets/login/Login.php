<?php

class Widget_Login extends Unwired_Widget_Abstract
{

    public function render($content)
    {
        $chilliData = $this->getView()->chilliData;
        $chilliData = $this->getView()->user;

        $splashPage = $this->getView()->splashPage;

        try {
            return $this->getView()->render(($splashPage->isMobile() ? 'login-mobile.phtml' : 'login.phtml'));
        } catch (Exception $e) {
            die($e->getMessage());
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