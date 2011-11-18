<?php

class Captive_Model_DbTable_Language extends Zend_Db_Table_Abstract
{
    protected $_name = 'language';

    protected $_dependentTables = array('Captive_Model_DbTable_Content');

    public function init()
    {
        $moduleBoostraps = Zend_Controller_Front::getInstance()
                                ->getParam('bootstrap')
                                    ->getResource('modules');

        $dbAdapter = $moduleBoostraps->captive->getResource('db');

        $this->_setAdapter($dbAdapter);
    }
}