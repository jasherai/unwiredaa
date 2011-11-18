<?php

class Captive_Model_Language extends Unwired_Model_Generic
{
    protected $_languageId = null;

    protected $_name = null;

    protected $_code = null;

    protected $_active = 0;

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
     * @return the $name
     */
    public function getName()
    {
        return $this->_name;
    }

	/**
     * @param field_type $name
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

	/**
     * @return the $code
     */
    public function getCode()
    {
        return $this->_code;
    }

	/**
     * @param field_type $code
     */
    public function setCode($code)
    {
        $this->_code = $code;

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

}