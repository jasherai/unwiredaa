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

class Unwired_View_Helper_ArrayToXml extends Zend_View_Helper_Abstract
{
    /**
     * @var DOMDocument
     */
    protected $_xml = null;

    public function arrayToXml($data = array(), $name = 'node', array $replacements = array(), array $exclude = array())
    {
        $this->_xml = new DOMDocument();

        $api = $this->_xml->createElement('api');

        $node = $this->_xml->createElement($name);

        $this->_iterateChildren($data, $node, $replacements, $exclude);

        $api->appendChild($node);

        $this->_xml->appendChild($api);

        return $this;
    }

    /**
     *
     * @return DOMDocument
     */
    public function getDom()
    {
        return $this->_xml;
    }

    public function __toString()
    {
        $this->_xml->formatOutput = true;

        return $this->_xml->saveXML();
    }

    protected function _iterateChildren($data, DOMElement $node, array $replacements = array(), array $exclude = array())
    {
        foreach ($data as $name => $value) {
            if (in_array($name, $exclude)) {
                continue;
            }

            $targetNode = $node;
            $attribs = array();

            $subreplacements = array();
            $subexclude = array();

            if (array_key_exists($name, $exclude)) {
                $subexclude = $exclude[$name];
            }

            if (array_key_exists($name, $replacements)) {
                if (!is_array($replacements[$name])) {
                    $name = $replacements[$name];
                } else {
                    $subreplacements = $replacements[$name];
                }
            }

            if (is_numeric($name)) {
                $attribs['key'] = $name;
                $name = 'value';
            }

            if (null === $value || is_string($value) || is_numeric($value)) {
                if (is_string($value) && !is_numeric($value)) {
                    $newNode = $this->_xml->createElement($name);
                    $newNode->appendChild($this->_xml->createCDATASection($value));
                } else {
                    $newNode = $this->_xml->createElement($name, $value);
                }
            } else {
                $newNode = $this->_xml->createElement($name);
                $this->_iterateChildren($value, $newNode, $subreplacements, $subexclude);
            }

            foreach ($attribs as $key => $value) {
                $newNode->setAttribute($key, $value);
            }

            $node->appendChild($newNode);
        }

    }
}