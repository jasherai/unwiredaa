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

class Report_View_Helper_ArrayToCsv extends Zend_View_Helper_Abstract
{
    protected $_fp = null;

    protected $_titles = true;

    public function arrayToCSV($data, $separator = ',', $titles = true)
    {
        $this->_titles = $titles;

        $csv = $this->_convertToCsvString($data, $separator, true);
        fclose($this->_getFp());

        return $csv;
    }

    protected function _convertToCsvString($data, $separator = ',')
    {
        $csv = '';

        $firstElem = current($data);

        $outstream = $this->_getFp();

        if (!is_array(current($firstElem)) && $this->_titles) {
            $titleData = array();
            foreach (array_keys($firstElem) as $title) {
                $titleData[] = $this->view->translate('report_result_title_' . $title);
            }

            $this->_titles = false;

            array_unshift($data, $titleData);
        }

        foreach ($data as $line) {
            if (is_array(current($firstElem))) {
                $csv .= $this->_convertToCsvString($line);
            } else {
                fputcsv($outstream, $line, ',', '"');
                rewind($outstream);
                $csv .= fgets($outstream);
                ftruncate($outstream, 0);
            }
        }

        return $csv;
    }

    protected function _getFp()
    {
        if (null === $this->_fp) {
            $this->_fp = fopen("php://temp", 'r+');;
        }

        return $this->_fp;
    }
}