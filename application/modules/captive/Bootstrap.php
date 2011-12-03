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

class Captive_Bootstrap extends Unwired_Application_Module_Bootstrap
{
	protected function _initAclResources()
	{
		$acl = parent::_initAclResources();

		$acl->addResource(new Zend_Acl_Resource('captive_template'));
		$acl->addResource(new Zend_Acl_Resource('captive_splashpage'));

		return $acl;
	}

    protected function _initWidgetLoader()
    {
        $widgetLoader = new Unwired_Loader_Widget(array('namespace' => 'Widget',
                                                        'basePath'  => dirname(__FILE__) . '/widgets'));

        return $widgetLoader;
    }

    protected function _initSplashpage()
    {
        $splashpageOptions = $this->getOption('splashpage');

        if (!$splashpageOptions || !is_array($splashpageOptions)) {
            $splashPageOptions = array();
        }

        Zend_Registry::set('splashpages', $splashpageOptions);

        return $splashpageOptions;
    }

    protected function _initShellPaths()
    {
        $shellpaths = $this->getOption('shellpaths');

        if (!$shellpaths || !is_array($shellpaths)) {
            $shellpaths = array('scp' => '/usr/bin/scp',
                                'ssh' => '/usr/bin/ssh');
        }

        Zend_Registry::set('shellpaths', $shellpaths);

        return $shellpaths;
    }
}