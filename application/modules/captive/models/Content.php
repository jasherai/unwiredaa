<?php

class Captive_Model_Content extends Unwired_Model_Generic
{
    protected $_contentId = null;

    protected $_languageId = null;

    protected $_splashId = null;

    protected $_templateId = null;

    protected $_title = null;

    protected $_content = null;

    protected $_type = null;

    protected $_orderWeb = 1;

    protected $_orderMobile = 1;

    protected $_column = 1;

    protected $_widget = null;

    protected $_templateContent = null;

	/**
     * @return the $contentId
     */
    public function getContentId()
    {
        return $this->_contentId;
    }

	/**
     * @param field_type $contentId
     */
    public function setContentId($contentId)
    {
        $this->_contentId = $contentId;

        return $this;
    }

	/**
     * @return the $languageId
     */
    public function getLanguageId()
    {
        return $this->_languageId;
    }

	/**
     * @param field_type $languageId
     */
    public function setLanguageId($languageId)
    {
        $this->_languageId = $languageId;

        return $this;
    }

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

	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->_title;
    }

	/**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;

        return $this;
    }
	/**
     * @return the $content
     */
    public function getContent()
    {
        return $this->_content;
    }

	/**
     * @param field_type $content
     */
    public function setContent($content)
    {
        $this->_content = $content;

        return $this;
    }

	/**
     * @return the $type
     */
    public function getType()
    {
        return $this->_type;
    }

	/**
     * @param field_type $type
     */
    public function setType($type)
    {
        $this->_type = $type;

        return $this;
    }

	/**
     * @return the $orderWeb
     */
    public function getOrderWeb()
    {
        return $this->_orderWeb;
    }

	/**
     * @param field_type $orderWeb
     */
    public function setOrderWeb($orderWeb)
    {
        $this->_orderWeb = $orderWeb;

        return $this;
    }

	/**
     * @return the $orderMobile
     */
    public function getOrderMobile()
    {
        return $this->_orderMobile;
    }

	/**
     * @param field_type $orderMobile
     */
    public function setOrderMobile($orderMobile)
    {
        $this->_orderMobile = $orderMobile;

        return $this;
    }

	/**
     * @return the $column
     */
    public function getColumn()
    {
        return $this->_column;
    }

	/**
     * @param field_type $column
     */
    public function setColumn($column)
    {
        $this->_column = $column;

        return $this;
    }

	/**
     * @return the $widget
     */
    public function getWidget()
    {
        return $this->_widget;
    }

	/**
     * @param field_type $widget
     */
    public function setWidget($widget)
    {
        $this->_widget = $widget;

        return $this;
    }

	/**
     * @return the $templateContent
     */
    public function getTemplateContent()
    {
        return $this->_templateContent;
    }

	/**
     * @param field_type $templateContent
     */
    public function setTemplateContent($templateContent)
    {
        $this->_templateContent = $templateContent;

        return $this;
    }

    /**
     * Render content
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->getWidget()) {
             return $this->getContent();
        }

        $widgetClass = 'Widget_' . ucfirst($this->getWidget());
        $widget = new $widgetClass;

        return $widget->render($this);
    }

}