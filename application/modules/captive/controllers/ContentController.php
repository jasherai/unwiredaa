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

class Captive_ContentController extends Unwired_Controller_Crud
{
    protected $_actionsToReferrer = array('template');

	public function preDispatch()
	{
		if (null === $this->_currentUser || !$this->getAcl()->hasRole($this->_currentUser)) {
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

		if ($this->getInvokeArg('bootstrap')->hasResource('session')) {
			$session = $this->getInvokeArg('bootstrap')->getResource('session');

			if (null === $session->referer) {
				$session->referer = $this->getRequest()->getServer('HTTP_REFERER');
			}

			if (!in_array($this->getRequest()->getActionName(), $this->_actionsToReferer)) {
				$session->referer = null;
			}

			$this->_referer = $session->referer;

			$this->view->refererUrl = $this->_referer;
		}
	}

    public function templateAction()
    {
    	if (!$this->getAcl()->isAllowed($this->_currentUser, 'captive_template', 'add')) {
			$this->view->uiMessage('access_not_allowed_view', 'error');
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_gotoIndex();
        }

        /**
         * Get the template
         */
        $mapperTemplate = new Captive_Model_Mapper_Template();

        $template = $mapperTemplate->find($id);

        if (!$template) {
            $this->_gotoIndex();
        }

        $mapperTemplate = null;

        $settings = $template->getSettings();

        /**
         * Get template languages
         */
        $mapperLanguages = new Captive_Model_Mapper_Language();

        $languages = $mapperLanguages->findBy(array('language_id' => $settings['language_ids']));

        $languagesSorted = array();

        foreach ($languages as $language) {
            $languagesSorted[$language->getLanguageId()] = $language;
        }

        $languages = null;
        $this->view->languages = $languagesSorted;

        $mapperLanguages = null;

        /**
         * Get template content blocks
         */
        $mapperContent = new Captive_Model_Mapper_Content();

        $contents = $mapperContent->findBy(array('template_id' => $template->getTemplateId()));

        $contentSorted = array('content' => array(), 'imprint' => array(), 'terms' => array());

        if (empty($contents)) {
            foreach (array_keys($contentSorted) as $type) {
                foreach ($settings['language_ids'] as $languageId) {
                    $content = new Captive_Model_Content();

                    $content->setType($type);
                    $content->setLanguageId($languageId);
                    $content->setColumn(0);
                    $content->setOrderWeb(1);
                    $content->setOrderMobile(1);
                }
            }
        }

        foreach ($contents as $content) {
            if (!isset($contentSorted[$content->getType()][$content->getLanguageId()])) {
                $contentSorted[$content->getType()][$content->getLanguageId()] = array();
            }
            $contentSorted[$content->getType()][$content->getLanguageId()] = $content;
        }

        $this->view->template = $template;
        $this->view->contents = $contentSorted;
    }
}