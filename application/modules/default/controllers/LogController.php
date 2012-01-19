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

class Default_LogController extends Unwired_Controller_Crud
{
	protected $_defaultMapper = 'Default_Model_Mapper_Log';

	public function indexAction()
	{
		$filters = $this->_getFilters();

		$this->_getDefaultMapper()->findBy($filters, 0, 'stamp DESC');
		parent::_index();
	}

	protected function _getFilters()
	{
		$filter = array();

		$filter['entity'] = $this->getRequest()->getParam('entity', null);
		$filter['event_id'] = $this->getRequest()->getParam('event_id', null);
		$filter['entity_id'] = $this->getRequest()->getParam('entity_id', null);
		$filter['email'] = $this->getRequest()->getParam('email', null);
		$filter['remote_host'] = $this->getRequest()->getParam('remote_host', null);

		$this->view->filter = $filter;

		foreach ($filter as $key => $value) {
			if (null == $value || empty($value)) {
				unset($filter[$key]);
				continue;
			}

			if ($key == 'event_id' || $key == 'entity_id') {
				continue;
			}
			$filter[$key] = '%' . preg_replace('/[^a-z0-9\s\@\-\:\._]+/iu', '', $value) . '%';
		}

		return $filter;
	}

	public function viewAction()
	{
	    $id = (int) $this->getRequest()->getParam('id', 0);

	    if ($this->getRequest()->isXmlHttpRequest()) {
	        $this->_helper->layout->disableLayout();
	    }

	    if (!$id) {
	        throw new Unwired_Exception('entity_not_found', 404);
	    }

	    $logEntity = $this->_getDefaultMapper()->find($id);

	    if (!$logEntity) {
	        throw new Unwired_Exception('entity_not_found', 404);
	    }

	    $this->view->logEntity = $logEntity;

	    $this->view->entity = null;

	    $this->view->currentEntity = null;

	    $this->view->form = null;

	    $class = $logEntity->getEntityName();

		if (!class_exists($class)) {
		    return;
		}

	    $entity = new $class;

	    if ($entity instanceof Zend_Acl_Resource_Interface && !$this->getAcl()->isAllowed($this->_currentUser, $entity, 'view')) {
	        return;
	    }

	    $entityData = @unserialize($logEntity->getEventData());

	    $this->view->entity = $entity;

	    if ($entityData) {
	        $entity->fromArray($entityData);
	    }

	    $mapperClass = str_replace('_Model_', '_Model_Mapper_', $class);

	    if (class_exists($mapperClass)) {
	    	$mapper = new $mapperClass;

	    	$currentEntity = $mapper->find($logEntity->getEntityId());

	    	$this->view->currentEntity = $currentEntity;
	    	$mapper = null;
	    }

	    $formClass = str_replace('_Model_', '_Form_', $class);

	    if (class_exists($formClass)) {
	    	$form = new $formClass;

	    	if (!$entityData && $currentEntity) {
	    	    $entity = $currentEntity;
	    	}

            if ($form instanceof Unwired_Form) {
                $form->setEntity($entity);
            }

            $form->populate($entity->toArray());
            $forms = $form->getSubForms();

            $forms[] = $form;

            foreach ($forms as $theForm) {
                foreach ($theForm->getElements() as $name => $element) {
                    $element->setAttrib('disabled', 'disabled');

                    if ($element instanceof Zend_Form_Element_Button || $element instanceof Unwired_Form_Element_Href ||
                        $element instanceof Zend_Form_Element_Submit || $element instanceof Zend_Form_Element_Password) {
                        $theForm->removeElement($name);
                    }
                }
            }

            $this->view->form = $form;

            $this->loadFormTranslations($form);

	    }
	}

	/**
     * Load possible translations specific to form
     *
     */
    public function loadFormTranslations($form)
    {
        $name = str_replace('Form_', '', get_class($form));

        $filter = new Zend_Filter_Word_CamelCaseToDash();

        if (strpos($name, '_')) {
            $splitted = explode('_', $name);
            $module = array_shift($splitted);
            $module[0] = strtolower($module[0]);
            $controller = implode('/', $splitted);
        } else {
            $module = 'default';
            $controller = $name;
        }

        $controller = strtolower($filter->filter($controller));

        /**
         * The translator instance
         *
         * @var Zend_Translate
         */
        $translate = Zend_Registry::get('Zend_Translate');

        switch ($translate->getAdapter()->toString()) {
        	case 'Array':
        	    $fileExtension = 'php';
        	break;
            case 'Gettext':
                $fileExtension = 'mo';
            break;
            case 'XmlTm':
                $fileExtension = 'tm';
            break;

        	default:
        		$fileExtension = strtolower($translate->getAdapter()->toString());
        	break;
        }
        $translationFile = APPLICATION_PATH . "/modules/{$module}/languages/{$translate->getLocale()}/{$controller}"
        				  . '.' . $fileExtension;

        if (file_exists($translationFile)) {
            $translate->getAdapter()->addTranslation($translationFile, $translate->getLocale());
            /**
             * Translation loaded then return
             */
            return;
        }

        /**
         * Possible controller name is index
         */
        $translationFile = APPLICATION_PATH . "/modules/{$module}/languages/{$translate->getLocale()}/index"
        				  . '.' . $fileExtension;
        if (file_exists($translationFile)) {
            $translate->getAdapter()->addTranslation($translationFile, $translate->getLocale());
        }
    }
}