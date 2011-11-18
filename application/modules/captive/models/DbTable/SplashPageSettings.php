<?php

class Captive_Model_DbTable_SplashPageSettings extends Zend_Db_Table_Abstract
{
    protected $_name = 'splash_page_settings';

    protected $_referenceMap = array(
                'Splash' => array(
        			'columns'           => 'splash_id',
                    'refTableClass'     => 'Captive_Model_DbTable_SplashPage',
                    'refColumns'        => 'splash_id'
                    ),
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