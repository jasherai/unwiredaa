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

class Unwired_Controller_Action extends Zend_Controller_Action
{
	protected $_acl = null;

	protected $_eventBroker = null;

	static protected $_defaultEventBroker;

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

    /**
     * Send event to event broker to be dispatched
     *
     * @param string $event
     * @param Unwired_Model_Generic $entity
     * @param integer $entityId
     * @param array $params
     */
    public function sendEvent($event, Unwired_Model_Generic $entity, $entityId, array $params = array())
    {
		$broker = $this->getEventBroker();

		if (!$broker) {
			return false;
		}

		$data = array('entity' => $entity,
					  'entityId' => $entityId,
					  'user'	=> Zend_Auth::getInstance()->getIdentity(),
					  'params' => $params);

		$message = new Unwired_Event_Message($event, $data);

		$broker->dispatch($message);

		return true;
    }

    /**
     * Get event broker
     * @return Unwired_Event_Broker
     */
    public function getEventBroker()
    {
    	if (null === $this->_eventBroker) {
    		$this->_eventBroker = self::getDefaultEventBroker();
    	}

    	return $this->_eventBroker;
    }

    /**
     * Set event broker
     * @param Unwired_Event_Broker $broker
     * @return Unwired_Event_Broker
     */
    public function setEventBroker(Unwired_Event_Broker $broker)
    {
    	$this->_eventBroker = $broker;

    	return $this;
    }

    /**
     * Get default event broker
     * @return Unwired_Event_Broker
     */
    static public function getDefaultEventBroker()
    {
    	if (null === self::$_defaultEventBroker && Zend_Registry::isRegistered('Unwired_Event_Broker')) {
    		self::$_defaultEventBroker = Zend_Registry::get('Unwired_Event_Broker');
    	}

    	return self::$_defaultEventBroker;
    }

    /**
     * Set default event broker
     * @param Unwired_Event_Broker $broker
     * @return Unwired_Event_Broker
     */
    static public function setDefaultEventBroker(Unwired_Event_Broker $broker)
    {
    	self::$_defaultEventBroker = $broker;
    }
}