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

class Unwired_View_Helper_PaginatorToXml extends Unwired_View_Helper_ArrayToXml
{


    public function paginatorToXml(Zend_Paginator $paginator, $listName = 'items', $itemName = 'item', $replacements = array(), array $exclude = array())
    {
        $this->_xml = new DOMDocument();

        $api = $this->_xml->createElement('api');

        $node = $this->_xml->createElement($listName);

        foreach ($paginator as $item) {
            $newNode = $this->_xml->createElement($itemName);
            $this->_iterateChildren($item->toArray(), $newNode, $replacements, $exclude);
            $node->appendChild($newNode);
        }

        $api->appendChild($node);

        $api->appendChild($this->_xml->createElement('start',
                                                     (($paginator->getCurrentPageNumber()-1) * $paginator->getItemCountPerPage())));

        $api->appendChild($this->_xml->createElement('count', $paginator->getCurrentItemCount()));

        $api->appendChild($this->_xml->createElement('total', $paginator->getTotalItemCount()));

        $this->_xml->appendChild($api);

        return $this;
    }
}