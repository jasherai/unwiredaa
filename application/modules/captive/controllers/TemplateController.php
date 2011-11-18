<?php

class Captive_TemplateController extends Unwired_Controller_Crud
{
    protected $_defaultMapper = 'Captive_Model_Mapper_Template';

    public function indexAction()
    {
        $this->_index();
    }

    protected function _add(Unwired_Model_Mapper $mapper = null,
							Unwired_Model_Generic $entity = null,
							Zend_Form $form = null)
    {
        $groupService = new Groups_Service_Group();

		$rootGroup = $groupService->getGroupTreeByAdmin();

		$this->view->rootGroup = $rootGroup;

		if ($this->getRequest()->isPost() && $this->getRequest()->getParam('form_element_submit_edit')) {
		    /**
		     * Redirect to template content editing on update
		     */
		    if ($entity->getTemplateId()) {
                $this->_referer = $this->view->serverUrl($this->_helper->url->url(array('module' => 'captive',
                                                            'controller' => 'content',
                                                            'action' => 'template',
                                                            'id' => $entity->getTemplateId()),
                									  'default',
                                                      true));
		    } else {
		    /**
		     * Redirect to template content editing on add
		     */
		        $this->_autoRedirect = false;

		        $result = parent::_add($mapper, $entity, $form);

		        if (!$result) {
		            return;
		        }

		        $this->_autoRedirect = true;

		        $this->_referer = $this->view->serverUrl($this->_helper->url->url(array('module' => 'captive',
                                                            'controller' => 'content',
                                                            'action' => 'template',
                                                            'id' => $entity->getTemplateId()),
                									  'default',
                                                      true));
                $this->_gotoIndex();
		    }
		}

		parent::_add($mapper, $entity, $form);
    }

    public function addAction()
    {
        $this->_add();
    }

    public function editAction()
    {
        $this->_edit();
    }

    public function deleteAction()
    {
        $this->_delete();
    }
}