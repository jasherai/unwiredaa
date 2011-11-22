<?php
/**
 * Unwired AA GUI
 *
 * Author & Copyright (c) 2011 Unwired Networks GmbH
 * alexander.szlezak@unwired.at
 *
 * Licensed under the terms of the Affero Gnu Public License version 3
 * (AGPLv3 - http://www.gnu.org/licenses/agpl.html) or our proprietory
 * license available at http://www.unwired.at/license.html
 */

class Report_GroupController extends Unwired_Controller_Crud {
	
	public function indexAction() {
		$groupService = new Groups_Service_Group ();
		$reportMapper = new Report_Model_Mapper_Group ();
		$reportCodeTemplateMapper = new Report_Model_Mapper_Index ();
		
		$filter = $this->_getFilters ();
		
		//$groupService->prepareMapperListingByAdmin ( $reportMapper, null, false, $filter );
		$reportMapper->findby(array('codetemplate_id' => $this->getRequest()->getParam('id')), 0, 'date_added DESC');
		
		$parent_id = $this->getRequest ()->getParam ( 'id' );
		$parent = $reportCodeTemplateMapper->find ( $parent_id );
		
		$this->view->assign ( 'parent', $parent );
		
		$this->_index ($reportMapper);
	}
	
	protected function _getFilters() {
		$filter = array ();
		
		$filter ['title'] = $this->getRequest ()->getParam ( 'title', null );
		$filter ['codetemplate_id'] = $this->getRequest ()->getParam ( 'id', null );
		
		$this->view->filter = $filter;
		
		foreach ( $filter as $key => $value ) {
			if (null == $value || empty ( $value )) {
				unset ( $filter [$key] );
				continue;
			}
			
			$filter [$key] = '%' . preg_replace ( '/[^a-z0-9ÄÖÜäöüßêñéçìÈùø\s\@\-\:\.]+/iu', '', $value ) . '%';
		}
		
		return $filter;
	}

	protected function _add(Unwired_Model_Mapper $mapper = null, Unwired_Model_Generic $entity = null, Zend_Form $form = null) {
		
		$groupService = new Groups_Service_Group();
		
		$rootGroup = $groupService->getGroupTreeByAdmin();
		
		$this->view->rootGroup = $rootGroup;
		
		parent::_add ( $mapper, $entity, $form );
	}
	
	public function addAction() {
		$parent = ( int ) $this->getRequest ()->getParam ( 'id', 1 );
		
		$entity = $this->_getDefaultMapper ()->getEmptyModel ();
		
		$entity->setCodetemplateId ( $parent );
		$entity->setDateAdded ( date('Y-m-d H:i:s') );
		$entity->setRecepients($this->getRequest()->getParam('email'));
		
		$this->_add ( null, $entity );
		
		$this->_helper->viewRenderer->setScriptAction ( 'edit' );
	}
	
	public function editAction() {
		/*
		if ($this->getRequest()->isPost()) {
			$entity->setRecepients($this->getRequest()->getParam('email'));
		}
		*/
		$this->_edit ();
	}
	
	public function reportsAction() {
		$groupService = new Report_Service_Group ();
		$reportMapper = new Report_Model_Mapper_Group ();
		$resultMapper = new Report_Model_Mapper_Result();
		
		
		$resultMapper->findby(array('group_id' => $this->getRequest()->getParam('id')), 0, 'date_added DESC');
		
		$this->_defaultMapper = $resultMapper;
		
		$parent_id = $this->getRequest ()->getParam ( 'id' );
		$parent = $reportMapper->find ( $parent_id );
		
		$this->view->assign ( 'parent', $parent );
		
		$this->_index($resultMapper);
	}
	
	public function generateAction() {
		$ctMapper = new Report_Model_Mapper_Index();
		$rMapper = new Report_Model_Mapper_Group();
		$iMapper = new Report_Model_Mapper_Result();
		$report = $rMapper->find($this->getRequest()->getParam('id'));
		
		$parent = $ctMapper->find($report->getCodetemplateId());
		
		$className = $parent->getClassName();
		$reportGenerator = new $className;
		
		
		$result = $reportGenerator->getReport($report->getGroupsAssigned(), $report->getDateFrom(), $report->getDateTo());
		
		$entity = new Report_Model_Items();
		$entity->setDateAdded(date('Y-m-d H:i:s'));
		$entity->setData($result['data']);
		$entity->setHtmldata($result['html']);
		$entity->setGroupId($this->getRequest()->getParam('id'));
		$iMapper->save($entity);
		$this->_helper->redirector->gotoUrlAndExit('/report/group/view/id/'.$this->getRequest()->getParam('id'));
	}
	
	public function viewAction() {
		
		$rMapper = new Report_Model_Mapper_Result();
		$report = $rMapper->find($this->getRequest()->getParam('id'));
		
		$gMapper = new Report_Model_Mapper_Group();
		$parent = $gMapper->find($report->getGroupId());
		
		$this->view->parent = $parent;
		$this->view->report = $report;
	}
	
	public function deleteAction()
	{
		$this->_delete();
	}

}