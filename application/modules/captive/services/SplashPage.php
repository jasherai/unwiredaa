<?php

class Captive_Service_SplashPage
{
    /**
     * Find the active splash page for a device group
     *
     * @param Captive_Model_Group $group
     * @return Captive_Model_SplashPage
     */
    public function findActiveSplashPage(Captive_Model_Group $group)
    {
        $mapperSplash = new Captive_Model_Mapper_SplashPage();

        $splashPage = null;

        $parent = $group;
        /**
         * Walk the tree up to the root node.
         * It is assumed that at least the root node will have active splash page set.
         */
        do {
            $splashPage = $mapperSplash->findOneBy(array('active' => 1,
                                                         'selected' => 1,
                                                         'group_id' => $group->getGroupId()));

            if ($splashPage) {
                return $splashPage;
            }

            $parent = $parent->getParent();
        } while (null !== $parent);

        return null;
    }

    public function getSplashPageContents(Captive_Model_SplashPage $splashPage,
                                          Captive_Model_Language $language)
    {
        $mapperContent = new Captive_Model_Mapper_Content();

        if ($splashPage->isMobile()) {
            $order = 'order_mobile ASC';
        } else {
            $order = 'order_web ASC';
        }

        $contents = $mapperContent->findBy(array('splash_id' => $splashPage->getSplashId(),
                                                 'language_id' => $language->getLanguageId(),
                                                 'type'    => 'content'),
                                           null,
                                           $order);

        $templateContents = $mapperContent->findBy(array('template_id' => $splashPage->getTemplateId(),
                                                         'language_id' => $language->getLanguageId(),
                                                         'type'    => 'content'),
                                                   null,
                                                   $order);

        $contents = array_merge($contents, $templateContents);

        return $contents;

    }

    public function getTemplateContent(Captive_Model_Template $template)
    {
        $mapperContent = new Captive_Model_Mapper_Content();

        $settings = $template->getSettings();

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

        return $contentSorted;
    }

    public function saveTemplateContents(Captive_Model_Template $template, array $contents)
    {
        /**
         * @todo this is quick and dirty workaround for 22nd Nov
         */

        $mapperContent = new Captive_Model_Mapper_Content();

        $success = 0;

        foreach ($contents as $type => $languageData) {
            if ($type != 'terms' && $type != 'imprint') {
                continue;
            }

            foreach ($languageData as $languageId => $content) {
                try {
                    $model = $mapperContent->getEmptyModel();
                    $model->fromArray($content);
                    $model->setLanguageId($languageId)
                          ->setType($type)
                          ->setTemplateId($template->getTemplateId());

                    $mapperContent->save($model);
                    $success++;
                } catch (Exception $e) {
                    throw $e;
                }
            }
        }

        /**
         * @todo add html content blocks
         */
        return $success;
    }
}