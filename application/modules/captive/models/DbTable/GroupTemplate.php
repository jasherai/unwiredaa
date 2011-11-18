<?php

class Captive_Model_DbTable_GroupTemplate extends Zend_Db_Table_Abstract
{
    protected $_name = 'group_template';


    protected $_referenceMap = array(
                'Group' => array(
        			'columns'           => 'group_id',
                    'refTableClass'     => 'Group_Model_DbTable_Group',
                    'refColumns'        => 'group_id'
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