<?php
/**
 * Application bootstrap class
 *
 * Resources initialization methods are currently in this class
 * but if specific resource initialization / code grows, it should
 * be moved to a separate resource class
 *
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
    public function _initTimeZone()
    {
        date_default_timezone_set('Europe/Sofia');
    }

    /**
     * Initialize Zend_Layout
     *
     * @return Zend_Layout
     */
    public function _initLayout()
    {
        /**
         * Check if view is initialized
         */
        if (!$this->hasResource('View')) {
            $this->registerPluginResource('View');
            $this->bootstrapView();
        }

        $view = $this->getResource('View');

        $options = $this->getOption('resources');

        /**
         * Merge layout config options with view instance
         * so action views and layout views are the same object and share assigned variables
         */
        $layoutOptions = array('view' => $view);
        if ($options && is_array($options) && isset($options['layout'])
            && is_array($options['layout'])) {

            $layoutOptions = array_merge($layoutOptions, $options['layout']);

            if (isset($layoutOptions['doctype'])) {
                $view->doctype()->setDoctype($layoutOptions['doctype']);
            }
        }

        /**
         * Set content type and charset
         */
        $view->headMeta()->prependHttpEquiv('Content-Type',
                                            'text/html; charset=' . $view->getEncoding());

        $layout = Zend_Layout::startMvc($layoutOptions);

        return $layout;
    }


    /**
     * Init session namespace for current project
     *
     * @return Zend_Session_Namespace
     */
    public function _initSession()
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

