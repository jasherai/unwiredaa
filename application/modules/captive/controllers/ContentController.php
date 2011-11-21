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
    protected $_actionsToReferer = array('template');

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

	public function splashpageAction()
	{
	    if (!$this->getAcl()->isAllowed($this->_currentUser, 'captive_splashpage', 'add')) {
			$this->view->uiMessage('access_not_allowed_view', 'error');
			$this->_helper->redirector->gotoRouteAndExit(array(), 'default', true);
		}

        $id = $this->getRequest()->getParam('id');

        if (!$id) {
            $this->_gotoIndex();
        }

        /**
         * Get the splashpage
         */
        $mapperSplash = new Captive_Model_Mapper_SplashPage();

        $splashPage = $mapperSplash->find($id);

        if (!$splashPage) {
            $this->_gotoIndex();
        }

        $mapperSplash = null;

        $settings = $splashPage->getSettings();

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

        $serviceSplashPage = new Captive_Service_SplashPage();

        /**
         * Try to save contents
         */
        if ($this->getRequest()->isPost())
        {
            $contents = $this->getRequest()->getPost('content');
            if (!empty($contents) && is_array($contents)) {
                try {
                    $serviceSplashPage->saveSplashContents($splashPage, $contents);
                    $this->view->uiMessage('captive_content_splashpage_content_saved', 'success');
                    $this->_gotoIndex();
                } catch (Exception $e) {
                    $this->view->uiMessage('captive_content_splashpage_content_error', 'error');
                }
            } else {
                $this->view->uiMessage('captive_content_splashpage_no_content_provided', 'error');
            }
        }

        /**
         * Get template content blocks
         */
        $contents = $serviceSplashPage->getSplashPageContents($splashPage);

        $this->view->splashPage = $splashPage;
        $this->view->contents = $contents;
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

        $serviceSplashPage = new Captive_Service_SplashPage();

        /**
         * Try to save contents
         */
        if ($this->getRequest()->isPost())
        {
            $contents = $this->getRequest()->getPost('content');
            if (!empty($contents) && is_array($contents)) {
                try {
                    $serviceSplashPage->saveTemplateContents($template, $contents);
                    $this->view->uiMessage('captive_content_template_content_saved', 'success');
                    $this->_gotoIndex();
                } catch (Exception $e) {
                    $this->view->uiMessage('captive_content_template_content_error', 'error');
                }
            } else {
                $this->view->uiMessage('captive_content_template_no_content_provided', 'error');
            }
        }

        /**
         * Get template content blocks
         */
        $contents = $serviceSplashPage->getTemplateContent($template);

        $this->view->template = $template;
        $this->view->contents = $contents;
    }
}