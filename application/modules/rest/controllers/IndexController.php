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

class Rest_IndexController extends Unwired_Controller_Crud
{
    protected $_defaultMapper = 'Rest_Model_Mapper_Key';

    protected $_actionsToReferer = array('generate', 'enable', 'disable', 'delete');

    public function indexAction()
    {
        $mapper = new Rest_Model_Mapper_Key();

        $filters = $this->_getFilters();

        $mapper->prepareIndex($filters);

        parent::_index($mapper);
    }

    public function generateAction()
    {
        $id = (int) $this->getRequest()->getParam('uid', null);

        if (!$id) {
            $this->view->uiMessage('rest_index_generate_user_not_found', 'error');
			$this->_gotoIndex();
			return;
        }

        $mapperAdmin = new Users_Model_Mapper_Admin();

        $admin = $mapperAdmin->find($id);

        if (!$admin) {
            $this->view->uiMessage('rest_index_generate_user_not_found', 'error');
			$this->_gotoIndex();
			return;
        }

        $mapperAdmin = null;

        $mapper = new Rest_Model_Mapper_Key();
        $key = $mapper->findOneBy(array('user_id' => $id));

        if ($key) {
            $this->view->uiMessage('rest_index_generate_user_haskey', 'error');
			$this->_gotoIndex();
			return;
        }

        $service = new Rest_Service_Auth();

        $key = $service->generateKey($admin);

        $secret = $service->generateSecret($key, $admin);

        $newKey = new Rest_Model_Key();
        $newKey->setKey($key)
               ->setSecret($secret)
               ->setUserId($id);

        try {
            $mapper->save($newKey);
            $this->view->uiMessage('information_saved_successfully', 'success');
        } catch (Exception $e) {
            $this->view->uiMessage('information_notsaved_error', 'error');
        }

        $this->_gotoIndex();
    }

    public function enableAction()
    {
        $this->_toggle(true);
    }

    public function disableAction()
    {
        $this->_toggle(false);
    }

    public function deleteAction()
    {
        parent::_delete();
    }

    protected function _toggle($enabled = true)
    {
        $id = (int) $this->getRequest()->getParam('id', null);

        if (!$id) {
            $this->view->uiMessage('rest_index_toggle_key_not_found', 'error');
			$this->_gotoIndex();
			return;
        }

        $mapper = new Rest_Model_Mapper_Key();
        $key = $mapper->find($id);

        if (!$key) {
            $this->view->uiMessage('rest_index_toggle_key_not_found', 'error');
			$this->_gotoIndex();
			return;
        }

        try {
            $key->setActive((int) (bool) $enabled);
            $mapper->save($key);

            $this->view->uiMessage('information_saved_successfully', 'success');
        } catch (Exception $e) {
            $this->view->uiMessage('information_notsaved_error', 'error');
        }

        $this->_gotoIndex();
    }

    protected function _getFilters()
    {
		$filter = array();

		$filter['email'] = $this->getRequest()->getParam('email', null);
		$filter['key'] = strtoupper($this->getRequest()->getParam('key', null));

		$this->view->filter = $filter;

		foreach ($filter as $key => $value) {
			if (null == $value || empty($value)) {
				unset($filter[$key]);
				continue;
			}

			$filter[$key] = '%' . preg_replace('/[^a-z0-9\s\@\-\:\.]+/iu', '', $value) . '%';
		}

		return $filter;
    }
}