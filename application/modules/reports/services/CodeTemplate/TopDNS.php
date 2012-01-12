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
class Reports_Service_CodeTemplate_TopDNS extends Reports_Service_CodeTemplate_Abstract
{

	public function getData($groupIds, $dateFrom, $dateTo)
	{
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

			$result = array('graphics' => array(),
			                'tables' => array('sld' => array('colDefs' => array(array('report_result_domain',
			                                                                    	  array('name' => 'report_result_request_count',
			                                                                    	        'width' => '20%'))),
			                                                 'rows' => array()),
			                                  'tld' => array('colDefs' => array(array('report_result_tld',
			                                                                    	  array('name' =>'report_result_request_count',
			                                                                    	        'width' => '20%'))),
			                                                 'rows' => array())));

			$tlds = array();
			$total = 0;

			foreach ($items as $key => $value) {
				$total+=$value['cnt'];
				if (!isset($result['tables']['sld']['rows'][$value['sld'].'.'.$value['tld']])) {
				    $result['tables']['sld']['rows'][$value['sld'].'.'.$value['tld']] = array('data' => array('name' => $value['sld'].'.'.$value['tld'],
				                                                                              				  'value' => 0));
				}
				$result['tables']['sld']['rows'][$value['sld'].'.'.$value['tld']]['data']['value'] = $value['cnt'];

			    if (!isset($result['tables']['tld']['rows'][$value['tld']])) {
				    $result['tables']['tld']['rows'][$value['tld']] = array('data' => array('name' => $value['tld'],
	                                                                        				'value' => 0));
				}
				$result['tables']['tld']['rows'][$value['tld']]['data']['value'] += $value['cnt'];
			}

			$items = null;

			$topSldRows = array_slice($result['tables']['sld']['rows'], 0, 50, true);

			$otherSldRows = array_slice($result['tables']['sld']['rows'], 50, null, true);

			$other = array('data' => array('name' => array('data' => 'report_result_other_domain', 'translatable' => true),
                        				   'value' => 0));

			foreach ($otherSldRows as $data) {
			    $other['data']['value'] += $data['data']['value'];
			}

			$topSldRows['other'] = $other;
			$result['tables']['sld']['rows'] = $topSldRows;

			$otherSldRows = null;

			$topSldRows = null;


			$topTldRows = array_slice($result['tables']['tld']['rows'], 0, 20, true);

			$otherTldRows = array_slice($result['tables']['tld']['rows'], 20, null, true);

			$other = array('data' => array('name' => array('data' => 'report_result_other_tld', 'translatable' => true),
                        				   'value' => 0));

			foreach ($otherTldRows as $data) {
			    $other['data']['value'] += $data['data']['value'];
			}

			$topTldRows['other'] = $other;
			$result['tables']['tld']['rows'] = $topTldRows;

			$otherTldRows = null;

			$topTldRows = null;

			$result['graphics'] = array('sld' => array('name' => 'report_result_domain',
			                                           'type' => 'piechart',
			                                           'headers' => array('report_result_domain', 'report_result_request_count'),
			                                           'rows' => array()),
			                            'tld' => array('name' => 'report_result_tld',
			                                           'type' => 'piechart',
													   'headers' => array('report_result_tld', 'report_result_request_count'),
			                                           'rows' => array()));

			foreach ($result['tables']['sld']['rows'] as $data) {
			    $result['graphics']['sld']['rows'][] = array(is_array($data['data']['name']) ? $data['data']['name']['data'] : $data['data']['name'],
			                                                 $data['data']['value']);
			}

	        foreach ($result['tables']['tld']['rows'] as $data) {
			    $result['graphics']['tld']['rows'][] = array(is_array($data['data']['name']) ? $data['data']['name']['data'] : $data['data']['name'],
			                                                 $data['data']['value']);
			}
	        return $result;
	}
}
