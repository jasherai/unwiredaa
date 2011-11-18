<?php

class Captive_Model_UserAgent
{
    protected $_isMobile = false;

    protected $_device = null;

    protected $_browser = null;


    static protected $_instance;

    protected function __construct()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        if (!$userAgent) {
            return;
        }

        switch ($userAgent) {
            case (stripos($userAgent, 'iphone') !== false):
            case (stripos($userAgent, 'ipad') !== false):
                $this->_isMobile = true;
                $this->_device = 'ios';
                $this->_browser = 'safari';
            break;

            case (stripos($userAgent, 'android') !== false):
                $this->_isMobile = true;
                $this->_device = 'android';
                /**
                 * @todo This is wrong
                 */
                $this->_browser = 'chrome';
            break;

            case (stripos($userAgent, 'blackberry') !== false) :
                $this->_isMobile = true;
                $this->_device = 'blackberry';
            break;

            case (stripos($userAgent, 'nokia') !== false):
            case (stripos($userAgent, 'symbian') !== false):
            case (stripos($userAgent, 'j2me') !== false):
            case (stripos($userAgent, 'series60') !== false):
                $this->_isMobile = true;
                $this->_device = 'symbian';
            break;

            default:
                $this->_isMobile = false;
                $this->_device = 'desktop';

            break;
        }
    }

    /**
     * Get singleton instance
     *
     * @return Captive_Model_UserAgent
     */
    static public function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function isMobile()
    {
        return $this->_isMobile;
    }

    public function isSymbian()
    {
        return ($this->_device == 'symbian') ? true : false;
    }

    public function isBlackberry()
    {
        return ($this->_device == 'blackberry') ? true : false;
    }

    public function isAndroid()
    {
        return ($this->_device == 'android') ? true : false;
    }

    public function isIos()
    {
        return ($this->_device == 'ios') ? true : false;
    }

    public function isDesktop()
    {
        return ($this->_device == 'desktop') ? true : false;
    }

    public function getDevice()
    {
        return $this->_device;
    }

    public function getBrowser()
    {
        return $this->_browser;
    }
}