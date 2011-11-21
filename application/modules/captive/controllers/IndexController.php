<?php

class Captive_IndexController extends Unwired_Controller_Crud
{
    protected $_defaultMapper = 'Captive_Model_Mapper_SplashPage';

    public function indexAction()
    {
        $this->_index();
    }

    public function addAction()
    {
        $this->_add(null, null, new Captive_Form_SplashPage());
        $this->_helper->viewRenderer->setScriptAction('edit');
    }

    public function editAction()
    {
        $this->_edit(null, new Captive_Form_SplashPage());
    }
}