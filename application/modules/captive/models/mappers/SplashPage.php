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

            $groupsAssigned = array();

            foreach ($groupRows as $groupRow) {
                $model->setGroupId($groupRow->group_id);
                $model->setSelected($groupRow->selected);
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

	public function save(Unwired_Model_Generic $model)
	{
	    try {
	        $model = parent::save($model);

	        $tableGroupSplashPage = new Captive_Model_DbTable_GroupSplashPage();

	        $tableGroupSplashPage->delete(array('splash_id = ?' => $model->getSplashId()));

	        $tableGroupSplashPage->update(array('selected' => 0),
	                                      array('group_id = ?' => $model->getGroupId()));

	        $tableGroupSplashPage->insert(array('splash_id' => $model->getSplashId(),
	                                            'group_id' => $model->getGroupId(),
	                                            'selected' => $model->getSelected()));
	    } catch (Exception $e) {
	        die($e->getMessage());
	        throw $e;
	    }

	    return $model;
	}
}