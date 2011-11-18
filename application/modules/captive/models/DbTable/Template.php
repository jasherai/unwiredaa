<?php

class Captive_Model_DbTable_Template extends Zend_Db_Table_Abstract
{
    protected $_name = 'template';


    protected $_dependentTables = array('Captive_Model_DbTable_Content',
                                        'Captive_Model_DbTable_SplashPage',
                                        'Captive_Model_DbTable_GroupTemplate',
                                        'Captive_Model_DbTable_SplashPageSettings');

    public function init()
    {
        $moduleBoostraps = Zend_Controller_Front::getInstance()
                                ->getParam('bootstrap')
                                    ->getResource('modules');

        $dbAdapter = $moduleBoostraps->captive->getResource('db');

        $this->_setAdapter($dbAdapter);
    }
}