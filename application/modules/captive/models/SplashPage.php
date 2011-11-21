<?php

class Captive_Model_SplashPage extends Unwired_Model_Generic implements Zend_Acl_Resource_Interface
{
    protected $_splashId = null;

	protected $_title = null;

	protected $_active = 0;

	protected $_templateId = null;

	protected $_template = null;

	protected $_isMobile = 0;

	protected $_groupsAssigned = array();

	protected $_settings = array();

	/**
     * @return the $splashId
     */
    public function getSplashId()
    {
        return $this->_splashId;
    }

	/**
     * @param field_type $splashId
     */
    public function setSplashId($splashId)
    {
        $this->_splashId = $splashId;

        return $this;
    }

	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->_title;
    }

	/**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }

	/**
     * @return the $active
     */
    public function getActive()
    {
        return $this->_active;
    }

	/**
     * @param field_type $active
     */
    public function setActive($active)
    {
        $this->_active = $active;

        return $this;
    }

	/**
     * @return the $templateId
     */
    public function getTemplateId()
    {
        return $this->_templateId;
    }

	/**
     * @param field_type $templateId
     */
    public function setTemplateId($templateId)
    {
        $this->_templateId = $templateId;

        return $this;
    }

    public function getTemplate()
    {
        return $this->_template;
    }

    public function setTemplate(Captive_Model_Template $template)
    {
        $this->_template = $template;

        return $this;
    }

    public function isMobile()
    {
        return $this->_isMobile;
    }

    public function setIsMobile($mobile = 1)
    {
        $this->_isMobile = (int) (bool) $mobile;

        return $this;
    }

    public function getSettings()
    {
        if (empty($this->_settings) && $this->_template) {
            $this->_settings = $this->getTemplate()->getSettings();
        }

        return $this->_settings;
    }

    public function setSettings($settings = array())
    {
        if (!is_array($settings)) {
            $settings = array();
        }

        if ($this->_template) {
            $settings = array_merge($this->getTemplate()->getSettings(), $settings);
        }

        $this->_settings = $settings;

        return $this;
    }

    public function getGroupsAssigned()
    {
        return $this->_groupsAssigned;
    }

    public function setGroupsAssigned($groups = array())
    {
        if (!is_array($groups)) {
            $groups = array();
        }
        $this->_groupsAssigned = $groups;

        return $this;
    }

    public function getResourceId()
    {
        return 'captive_splashpage';
    }

}