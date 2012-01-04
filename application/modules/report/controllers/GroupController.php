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

	public function init()
	{
		$contextSwitch = $this->_helper->getHelper('contextSwitch');
		$contextSwitch->addContext('csv', array(
                        				'suffix'    => 'csv',
                        				'headers'   => array('Content-Type' => 'text/csv; charset=utf-8',
                        						'Content-disposition' => 'attachment; filename='
                        						. date("Y-m-d_H-i-s") . '.csv'),
		                           ))
		              ->addContext('pdf', array(
                            				'suffix'    => 'csv',
                            				'headers'   => array(/*'Content-Type' => 'application/pdf',
                            						'Content-disposition' => 'attachment; filename='
                            						. date("Y-m-d_H-i-s") . '.pdf'*/),
                            		))
            		  ->addActionContext('view', 'csv')
            		  ->addActionContext('view', 'pdf')
            		  ->addActionContext('instant', 'csv')
            		  ->addActionContext('instant', 'pdf')
            		  ->initContext();

		parent::init();
	}

	public function indexAction() {
		$groupService = new Groups_Service_Group();
		$reportMapper = new Report_Model_Mapper_Group();
		$reportCodeTemplateMapper = new Report_Model_Mapper_CodeTemplate();

		$filter = $this->_getFilters ();

		$filter['codetemplate_id'] = $this->getRequest()->getParam('id');

		$groupService->prepareMapperListingByAdmin($reportMapper, null, false, $filter);
		//$reportMapper->findby(array('codetemplate_id' => $this->getRequest()->getParam('id')), 0, 'date_added DESC');

		$parentId = $this->getRequest()->getParam('id');

		$parent = $reportCodeTemplateMapper->find($parentId);

		$this->view->parent = $parent;

		$this->_index($reportMapper);
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

		$ctMapper = new Report_Model_Mapper_CodeTemplate();


		$resultMapper->findby(array('report_group_id' => $this->getRequest()->getParam('id')), 0, 'date_added DESC');

		$this->_defaultMapper = $resultMapper;

		$parent_id = $this->getRequest ()->getParam ( 'id' );

		$parent = $reportMapper->find ( $parent_id );

		$parent_parent = $ctMapper->find($parent->getCodetemplateId());

		$this->view->assign ( 'parent_parent', $parent_parent );
		$this->view->assign ( 'parent', $parent );

		$this->_index($resultMapper);
	}

	public function generateAction() {
		$ctMapper = new Report_Model_Mapper_CodeTemplate();
		$rMapper = new Report_Model_Mapper_Group();
		$iMapper = new Report_Model_Mapper_Result();
		$report = $rMapper->find($this->getRequest()->getParam('id'));

		$parent = $ctMapper->find($report->getCodetemplateId());

		$className = $parent->getClassName();
		$reportGenerator = new $className;


		$result = $reportGenerator->getData(array_keys($report->getGroupsAssigned()), $report->getDateFrom(), $report->getDateTo());

		$entity = new Report_Model_Items();
		$entity->setDateAdded(date('Y-m-d H:i:s'));
		$entity->setData($result);
		$entity->setReportGroupId($this->getRequest()->getParam('id'));
		$iMapper->save($entity);
		$this->_helper->redirector->gotoUrlAndExit('/report/group/view/id/'.$entity->getItemId());
	}

	public function instantAction() {

	    $codeTemplateId = (int) $this->getRequest()->getParam ( 'id',  1);

	    $mapperCodeTemplate = new Report_Model_Mapper_CodeTemplate();

	    $codeTemplate = $mapperCodeTemplate->find($codeTemplateId);

	    if (!$codeTemplate) {
	        $this->view->uiMessage('report_group_instant_codetemplate_notselected', 'error');
			$this->_gotoIndex();
	    }

	    $groupService = new Groups_Service_Group();

		$rootGroup = $groupService->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;

		$this->view->instant = true;

	    $form = new Report_Form_Instant();

	    $this->view->form = $form;
	    $this->_helper->viewRenderer->setScriptAction('edit');

	    $report = new Report_Model_Group();

		$report->setCodetemplateId($codeTemplate->getCodetemplateId());
		$report->setDateAdded(date('Y-m-d H:i:s'));

		$this->view->entity = $report;

	    if (!$this->getRequest()->isPost() && !$this->getRequest()->getParam('groups_assigned')) {
	        $dateTo = new Zend_Date();

	        $dateTo->setDay(1)
	               ->addMonth(1)
	               ->subDay(1);

	        $form->getElement('date_from')->setValue(date('Y-m-01'));

	        $form->getElement('date_to')->setValue($dateTo->toString('yyyy-MM-dd'));

	        return;
	    }

	    $groupsAssigned = $this->getRequest()->getParam('groups_assigned');

	    if (is_string($groupsAssigned)) {
	        $groupsAssigned = array($groupsAssigned => $groupsAssigned);
	        $this->getRequest()->setParam('groups_assigned', $groupsAssigned);
	    }

	    if (!$form->isValid($this->getRequest()->getParams())) {
	            try {
					$report->fromArray($form->getValues());
				} catch (Exception $e) {
					// nothing
				}
	        return;
	    }

	    $report->fromArray($form->getValues());

	    $groupsAssigned = $report->getGroupsAssigned();

	    if (!$this->getRequest()->isPost()) {
	        $groupsAssigned = array_combine($groupsAssigned, $groupsAssigned);
	    }

	    foreach ($groupsAssigned as $groupId => $value) {
	        $group = $groupService->findGroup($groupId);
	        $groupsAssigned[$groupId] = $group;
	    }

	    $report->setGroupsAssigned($groupsAssigned);

		$className = $codeTemplate->getClassName();
		$reportGenerator = new $className;

		$result = $reportGenerator->getData(array_keys($report->getGroupsAssigned()), $report->getDateFrom(), $report->getDateTo());

		$this->view->parent_parent = $codeTemplate;
		$this->view->parent = $report;

		$items = new Report_Model_Items();
		$items->setDateAdded(date('Y-m-d H:i:s'));
		$items->setData($result);
		$items->setReportGroupId($codeTemplate->getCodetemplateId());

		$this->view->report = $items;

		$this->view->data = $items->getData(true);
		$this->_helper->viewRenderer->setScriptAction('view');

		$this->_exportReportData($report, $items);
	}

	public function viewAction() {

		$rMapper = new Report_Model_Mapper_Result();
		$report = $rMapper->find($this->getRequest()->getParam('id'));
		$ctMapper = new Report_Model_Mapper_CodeTemplate();


		$gMapper = new Report_Model_Mapper_Group();
		$parent = $gMapper->find($report->getReportGroupId());
		$parent_parent = $ctMapper->find($parent->getCodetemplateId());

		$this->view->parent_parent = $parent_parent;
		$this->view->parent = $parent;
		$this->view->report = $report;

		$this->view->data = $report->getData(true);
//Zend_Debug::dump($this->view->data); die();
		$this->_exportReportData($parent, $report);
	}

	protected function _exportReportData(Report_Model_Group $reportGroup, Report_Model_Items $reportData)
	{
		if ($this->_helper->contextSwitch->getCurrentContext() == 'csv'
		    || $this->_helper->contextSwitch->getCurrentContext() == 'pdf') {
			$this->getResponse()->setHeader('Content-disposition',
					"attachment; filename=" . str_replace(' ', '_', $reportGroup->getTitle()) . '_' . str_replace(array(' ', '-'), '_', $reportData->getDateAdded())
			        . '_' . rand(1,10000) . '.' . $this->_helper->contextSwitch->getCurrentContext(),
					true);
		}

		if ($this->_helper->contextSwitch->getCurrentContext() == 'pdf') {
		    $this->_helper->viewRenderer->setNoRender();
		    $this->_helper->layout->disableLayout();

		    $html = $this->view->render('group/view.pdf.phtml');

		    if (!class_exists('DOMPDF')) {
                require_once('dompdf/dompdf_config.inc.php');
                $autoloader = Zend_Loader_Autoloader::getInstance();
                $autoloader->pushAutoloader('DOMPDF_autoload', '');
		    }

            $dompdf = new DOMPDF();
            $dompdf->set_paper("a4","portrait");
            $dompdf->load_html($html);
            $dompdf->set_base_path(PUBLIC_PATH);
            $dompdf->render();
            //$dompdf->stream("sample_report.pdf");
            echo $dompdf->output();
		}
	}

	public function deleteAction()
	{
		$this->_delete();
	}

	public function deleteresultAction()
	{
		$rMapper = new Report_Model_Mapper_Result();

		if (!$this->getAcl()->isAllowed($this->_currentUser, $rMapper->getEmptyModel(), 'delete')) {
			$this->view->uiMessage('access_not_allowed_delete', 'error');
			$this->_setAutoRedirect(true);
			$this->_gotoIndex();
		}

		$id = (int) $this->getRequest()->getParam('id');
		$entity = $rMapper->find($id);
		$rMapper->delete($entity);


		$this->_helper->redirector->gotoRouteAndExit(array( 'module' => $this->getRequest()->getParam('module'),
															'controller' => $this->getRequest()->getParam('controller'),
															'action' => 'reports',
															'id' => $entity->getReportGroupId()), 'default', true);

	}

}