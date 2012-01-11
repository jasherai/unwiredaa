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
class Reports_Service_CodeTemplate_TopTLD extends Reports_Service_CodeTemplate_Abstract
{

	public function getData($groupIds, $dateFrom, $dateTo) {
	        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
			/*$groupRel = $this->_getGroupRelations($groupIds);*/

			$select = $db->select()
					->from(array('dns_log'),array('count(*) as cnt','tld','sld'))
			                ->where('tld NOT IN (?)', array('arpa','lan','local','mobi','home','_TCP','office'))
			                ->where('sld != ""')
			                ->where('DATE(time) >= ?', $dateFrom)
			                ->where('DATE(time) <= ?', $dateTo)
			                ->group(array('tld', 'sld'))
					->order('cnt DESC');

			$items = $db->fetchAll($select);
			$result = array();
			$total=0;


			foreach ($items as $key => $value) {
				$total+=$value['cnt'];
				if (!isset($result[$value['tld']])) {
				    $result[$value['tld']] = array('sld' => array(),
				                                   'count' => 0);
				}


				$result[$value['tld']]['count']+=$value['cnt'];

				if ($value['cnt']>100) {
					$result[$value['tld']]['sld'][$value['sld']] = $value['cnt'];
				} else {
					if (!isset($result[$value['tld']]['sld']['report_result_other_domain'])) {
					    $result[$value['tld']]['sld']['report_result_other_domain']=$value['cnt'];
					}
					else  {
					    $result[$value['tld']]['sld']['report_result_other_domain']+=$value['cnt'];
					}
				}
			}

			uasort($result, function($a, $b) {
				if ($a['count'] == $b['count']) {
					return 0;
				}
				return ($a['count'] > $b['count']) ? -1 : 1;
			});

//			Zend_Debug::dump($result); die();

			$report = array('graphics' => array(),
			                'tables' => array('tld' => array('colDefs' => array(array('report_result_tld',
			                                                                    	  array('name' =>'report_result_request_count',
			                                                                    	        'width' => '20%'))),
			                                                 'rows' => array())));

    	    $graphics = array();

            foreach ($result as $tld => $values) {
                if (count($report['tables']['tld']['rows'])) {
                    $report['tables']['tld']['rows'][] = array('data' => array('&nbsp;','&nbsp;'),
                                                               'class' => array('bold', 'bold right'));
                }
                $report['tables']['tld']['rows'][] = array('data' => array('.' . $tld, $values['count']),
                                                           'class' => array('bold', 'bold right'));

                $graphics[] = array($tld, $values['count']);

                foreach ($values['sld'] as $sld => $count) {
                    if ($sld == 'report_result_other_domain') {
                        $report['tables']['tld']['rows'][] = array('data' => array(array('data' => 'report_result_other_domain', 'translatable' => true), $count),
                                                              'class' => array('', 'right'));
                    } else {
                    $report['tables']['tld']['rows'][] = array('data' => array('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $sld . '.' . $tld, $count),
                                                              'class' => array('', 'right'));
                    }
                }
            }

            $report['graphics'] = array('tld' => array('name' => 'report_result_tld',
			                                           'type' => 'piechart',
													   'headers' => array('report_result_tld', 'report_result_request_count'),
			                                           'rows' => $graphics));

	        return $report;
	}
}

