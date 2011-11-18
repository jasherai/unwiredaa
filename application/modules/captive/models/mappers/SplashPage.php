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
                $modelTemplate = new Captive_Model_Template();
                $modelTemplate->fromArray($rowTemplate->toArray());

                $model->setTemplate($modelTemplate);
            }

            $groupRows = $row->findDependentRowset('Captive_Model_DbTable_GroupSplashPage');

            $groupIds = array();

            foreach ($groupRows as $groupRow) {
                $groupIds[] = $groupRow->group_id;
            }

            $model->setGroupIds($groupIds);
	    }

	    return $model;
	}
}