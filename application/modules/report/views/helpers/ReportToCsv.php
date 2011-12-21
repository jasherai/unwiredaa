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
//Zend_Debug::dump($data); die();
        foreach ($data as $table) {
            $csv .= $this->_tableToCsvString($table, $separator);
            $csv .= $this->_getCsvLine(array(''));
        }

        fclose($this->_getFp());

        return $csv;
    }

    protected function _tableToCsvString($data, $separator = ',')
    {
        $csv = '';
        foreach ($data['colDefs'] as $colDefRow) {
            $titles = array();

            foreach ($colDefRow as $titleCol) {

                if (is_array($titleCol)) {
                    $titles[] = $this->view->translate($titleCol['name']);
                } else {
                    $titles[] = $this->view->translate($titleCol);
                }

            }
            $csv .= $this->_getCsvLine($titles, $separator);
        }

        foreach ($data['rows'] as $row) {
            $rowData = array();

            if (isset($row['data'])) {
                $row = $row['data'];
            }
            foreach ($row as $field) {
                if (is_array($field)) {
                    if ($field['translatable'] == true) {
                        $rowData[] = $this->view->translate($field['data']);
                    } else {
                        $rowData[] = str_replace('  ', '', html_entity_decode($field['data']));
                    }
                } else {
                    $rowData[] = $this->view->translate(str_replace('  ', '', html_entity_decode($field)));
                }
            }

            $csv .= $this->_getCsvLine($rowData, $separator);
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