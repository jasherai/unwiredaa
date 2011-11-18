<?php

class Captive_Model_DbTable_GroupSplashPage extends Zend_Db_Table_Abstract
{
    protected $_name = 'group_splash_page';

    protected $_referenceMap = array(
                'Group' => array(
        			'columns'           => 'group_id',
                    'refTableClass'     => 'Group_Model_DbTable_Group',
                    'refColumns'        => 'group_id'
                    ),
                'Splash' => array(
        			'columns'           => 'splash_id',
                    'refTableClass'     => 'Captive_Model_DbTable_SplashPage',
                    'refColumns'        => 'splash_id'
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