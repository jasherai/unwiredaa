<?php

class Captive_Model_DbTable_SplashPage extends Zend_Db_Table_Abstract
{
    protected $_name = 'splash_page';

    protected $_dependentTables = array('Captive_Model_DbTable_GroupSplashPage',
    									'Captive_Model_DbTable_SplashPageSettings');

    protected $_referenceMap = array(
                'Template' => array(
        			'columns'           => 'template_id',
                    'refTableClass'     => 'Captive_Model_DbTable_Template',
                    'refColumns'        => 'template_id'
                    ),
            );

    public function init()
    {
        $moduleBoostraps = Zend_Controller_Front::getInstance()
                                ->getParam('bootstrap')
                                    ->getResource('modules');

        $dbAdapter = $moduleBoostraps->captive->getResource('db');

        $this->_setAdapter($dbAdapter);
    }
}