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

class Report_View_Helper_ReportToCsv extends Zend_View_Helper_Abstract
{
    protected $_fp = null;


    public function reportToCSV($data, $separator = ';')
    {

        $csv = '';

        foreach ($data as $table) {
            $csv .= $this->_tableToCsvString($data, $separator);
        }

        fclose($this->_getFp());

        return $csv;
    }

    protected function _tableToCsvString($data, $separator = ',')
    {
        $csv = '';

        foreach ($data['colDefs'] as $colDefRow) {
            foreach ($colDefRow as $titleCol) {
                $titles = array();

                if (is_array($titleCol)) {
                    $titles[] = $this->translate($titleCol['name']);
                } else {
                    $titles[] = $titleCol;
                }

                $csv .= $this->_getCsvLine($titles, $separator);
            }
        }

        foreach ($data['rows'] as $row) {
            $csv .= $this->_getCsvLine($row, $separator);
        }

        return $csv;

    }

    protected function _getCsvLine($data, $separator = ';')
    {
        $outstream = $this->_getFp();

        fputcsv($outstream, $data, $separator, '"');
        rewind($outstream);
        $csv = fgets($outstream);
        ftruncate($outstream, 0);

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