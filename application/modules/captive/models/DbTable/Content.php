<?php

class Captive_Model_DbTable_Content extends Zend_Db_Table_Abstract
{
    protected $_name = 'content';

    protected $_dependentTables = array('Captive_Model_DbTable_Content');

    protected $_referenceMap = array(
                'Language' => array(
        			'columns'           => 'language_id',
                    'refTableClass'     => 'Captive_Model_DbTable_Language',
                    'refColumns'        => 'language_id'
                    ),
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
                'TemplateContent' => array(
        			'columns'           => 'template_content',
                    'refTableClass'     => 'Captive_Model_DbTable_Content',
                    'refColumns'        => 'content_id'
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