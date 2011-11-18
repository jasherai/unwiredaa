<?php

class Captive_Model_Mapper_Template extends Unwired_Model_Mapper
{
	protected $_modelClass = 'Captive_Model_Template';

	protected $_dbTableClass = 'Captive_Model_DbTable_Template';

    public function rowToModel(Zend_Db_Table_Row $row, $updateRepo = false)
    {
        $model = parent::rowToModel($row, $updateRepo);

        if (!$model) {
            return null;
        }

        $groupRows = $row->findDependentRowset('Captive_Model_DbTable_GroupTemplate');

        $groups = array();

        foreach ($groupRows as $group) {
            $groups[] = $group->group_id;
        }

        $groupRows = null;

        $model->setGroupsAssigned($groups);

        $settingRows = $row->findDependentRowset('Captive_Model_DbTable_SplashPageSettings');

        $settings = array();

        foreach ($settingRows as $setting) {
            if (preg_match('/^[ao]{1}:\d+:\{/', $setting->value)) {
                $value = unserialize($setting->value);
            } else {
                $value = $setting->value;
            }
            $settings[$setting->name] = $value;
        }

        $settingRows = null;

        $model->setSettings($settings);

        return $model;
    }

    /**
     * Persist single entity to database
     *
     * @param Unwired_Model_Generic $model
     * @throws Unwired_Exception In case something goes wrong
     * @return Unwired_Model_Generic
     */
    public function save(Unwired_Model_Generic $model)
    {
        try {
            parent::save($model);

            $tableGroupTemplate = new Captive_Model_DbTable_GroupTemplate();

            $tableGroupTemplate->delete(array('template_id = ?' => $model->getTemplateId()));

            foreach ($model->getGroupsAssigned() as $groupId) {
                $tableGroupTemplate->insert(array('group_id' => $groupId,
                                                  'template_id' => $model->getTemplateId()));
            }

            $tableSettings = null;

            $tableSettings = new Captive_Model_DbTable_SplashPageSettings();

            $tableSettings->delete(array('template_id = ?' => $model->getTemplateId()));

            foreach ($model->getSettings() as $name => $value) {
                if (is_array($value)) {
                    $value = serialize($value);
                }

                $tableSettings->insert(array('splash_id' => null,
                                             'template_id' => $model->getTemplateId(),
                                             'name' => $name,
                                             'value' => $value));
            }

            $tableSettings = null;
        } catch (Exception $e) {
            throw $e;
        }

        return $model;
    }
}
