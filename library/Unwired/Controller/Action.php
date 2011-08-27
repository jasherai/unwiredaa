<?php

class Unwired_Controller_Action extends Zend_Controller_Action
{
	protected $_acl = null;

	public function init()
	{
		$this->loadControllerTranslations();
		$this->view->systemSettings = $this->getInvokeArg('settings');
	}

	/**
	 * Get the system access list
	 * @return Zend_Acl
	 */
	public function getAcl()
	{
		if (null === $this->_acl) {
			if (Zend_Registry::isRegistered('acl')) {
				$this->_acl = Zend_Registry::get('acl');
			}
		}

		return $this->_acl;
	}

	public function setAcl(Zend_Acl $acl)
	{
		$this->_acl = $acl;

		return $this;
	}

	/**
     * Load translations specific to current controller
     *
     */
    public function loadControllerTranslations()
    {
        $name = str_replace('Controller',
                                       '',
                                       get_class($this));

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
        }
    }
}