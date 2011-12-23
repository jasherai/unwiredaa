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

class Default_Service_Chilli
{
    protected static $_defaultOptions = array();

    protected $_options = array();

    public function __construct()
    {
        $this->_options = self::$_defaultOptions;
    }

    public static function setDefaultOptions(array $options)
    {
        self::$_defaultOptions = $options;
    }

    public function setOptions(array $options)
    {
        $this->_options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (empty($this->_options)) {
            $this->_options = self::$_defaultOptions;
        }

        return $this->_options;
    }

    public function getOption($key)
    {
        return isset($this->_options[$key]) ? $this->_options[$key] : null;
    }

    public function getDeviceStatistics($device)
    {
        if ($device instanceof Nodes_Model_Node) {
            $device = $device->getMac();
        }

        $client = new Zend_Http_Client();

        $client->setUri($this->getOption('statsUrl'))
               ->setParameterGet('location', $device)
               ->setParameterGet('json', 1)
               ->setParameterGet('interval', 0);

        $result = $client->request();

        if ($result->getStatus() != 200) {
            return array();
        } else {
            return json_decode($result->getBody());
        }
    }

    public function getInterfaceStatistics($interface)
    {
        $client = new Zend_Http_Client();

        $client->setUri($this->getOption('statsUrl'))
               ->setParameterGet('interface', $interface)
               ->setParameterGet('json', 1)
               ->setParameterGet('interval', 0);

        $result = $client->request();

        if ($result->getStatus() != 200) {
            return array();
        } else {
            return json_decode($result->getBody());
        }
    }
}