<?php

class Captive_Model_Mapper_SplashPage extends Unwired_Model_Mapper
{
	protected $_modelClass = 'Captive_Model_SplashPage';
	protected $_dbTableClass = 'Captive_Model_DbTable_SplashPage';

	/**
	 * (non-PHPdoc)
	 * @see Unwired_Model_Mapper::rowToModel()
	 */
	public function rowToModel(Zend_Db_Table_Row $row, $updateRepo = false)
	{
	    $model = parent::rowToModel($row, $updateRepo);

	    if ($model) {
            $rowTemplate = $row->findParentRow('Captive_Model_DbTable_Template');

            if ($rowTemplate) {
                $mapperTemplate = new Captive_Model_Mapper_Template();

                $modelTemplate = $mapperTemplate->rowToModel($rowTemplate);

                $model->setTemplate($modelTemplate);
            }

            $groupRows = $row->findDependentRowset('Captive_Model_DbTable_GroupSplashPage');

            $groupIds = array();

            foreach ($groupRows as $groupRow) {
                $groupIds[] = $groupRow->group_id;
            }

            $settingsRows = $row->findDependentRowset('Captive_Model_DbTable_SplashPageSettings');

            $settings = array();

            foreach ($settingsRows as $setting) {
                if (preg_match('/^[ao]{1}:\d+:\{/', $setting->value)) {
                    $value = unserialize($setting->value);
                } else {
                    $value = $setting->value;
                }
                $settings[$setting->name] = $value;
            }

            $model->setSettings($settings);
	    }

	    return $model;
	}
}