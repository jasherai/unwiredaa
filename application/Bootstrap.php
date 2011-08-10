<?php
/**
* Unwired AA GUI
* Author & Copyright (c) 2011 Unwired Networks GmbH
* alexander.szlezak@unwired.at
* Licensed unter the terms of http://www.unwired.at/license.html
*/

/**
 * Application bootstrap class
 *
 * Resources initialization methods are currently in this class
 * but if specific resource initialization / code grows, it should
 * be moved to a separate resource class
 *
 * @author B. Krastev <bkrastev@web-teh.net>
 */
class Unwired_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initUmask()
    {
        umask(002);
    }

    /**
     * Register default module with its namespace
     *
     */
    protected function _initAutoloader()
    {
        return new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Default',
            'basePath'  => APPLICATION_PATH . '/modules/default',
        ));
    }

    /**
     * Get rid of the annoying message "It is not safe to rely on systems timezone..."
     *
     */
    protected function _initTimeZone()
    {
        date_default_timezone_set('Europe/Sofia');
    }


    /**
     * Init session namespace for current project
     *
     * @return Zend_Session_Namespace
     */
    protected function _initSession()
    {
        $options = $this->getOption('resources');

        if (!$options || !isset($options['session']['namespace'])) {
            $namespace = null;
        } else {
            $namespace = $options['session']['namespace'];
        }

        $session = new Zend_Session_Namespace($namespace);

        if (Zend_Auth::getInstance()->hasIdentity() && $session->rememberMe) {
            Zend_Session::rememberMe(31536000);
        }

        return $session;
    }
}

