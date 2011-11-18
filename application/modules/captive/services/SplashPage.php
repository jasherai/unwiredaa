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
}