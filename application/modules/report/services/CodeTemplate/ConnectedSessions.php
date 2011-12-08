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

class Report_Service_CodeTemplate_ConnectedDevicesUsers extends Report_Service_CodeTemplate_Abstract {
    protected $_garden_sess = 'acct_garden_session';
    protected $_network_user = 'network_user';
    protected $_group = 'group';
    protected $_internet_sess = 'acct_internet_session';

    protected function getTemplate($groupIds, $data) {
        $result = $data['data'];
        $totals = $data['totals'];
	$nullstr="-";
        $html = '';

        foreach ($result as $k => $v) {

	        $html .= '<table class="listing">';
	        $html .= '<tr>
<th rowspan=2 style="vertical-align: bottom;">Group Name</th>
<th style="text-align: center;">Offline</th>
<th colspan=3 style="text-align: center;">Online</th>
<th style="text-align: center; vertical-align: top;" rowspan=2>Total</th>
</tr><tr>
<th style="text-align: center;">Garden</th>
<th style="text-align: center;">Guest</th>
<th style="text-align: center;">MAC Auth.</th>
<th style="text-align: center;">Password A.</th>
</tr>';

		$totals_garden=$totals['garden'][$k]-($totals['guest'][$k]+$totals['macauth'][$k]+$totals['auth'][$k]);
	        $html .= '<tr><td><strong>Total</td>
<td style="text-align:right;"><strong>'.(((int)$totals_garden > 0)?$totals_garden:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['guest'][$k] > 0)?$totals['guest'][$k]:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['macauth'][$k] > 0)?$totals['macauth'][$k]:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['auth'][$k] > 0)?$totals['auth'][$k]:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['garden'][$k] > 0)?$totals['garden'][$k]:$nullstr).'</strong></td>
</tr>';
	        foreach ($v as $key => $value) {
//<td></td>
			$value_garden=($value['garden']*1)-(($value['guest']*1)+($value['macauth']*1)+($value['auth']*1));
	        	$html .= '<tr><td>'.$value['name'].'</td>
<td style="text-align:right;">'.(((int)$value_garden > 0)?$value_garden:$nullstr).'</td>
<td style="text-align:right;">'.(((int)$value['guest'] > 0)?$value['guest']:$nullstr).'</td>
<td style="text-align:right;">'.(((int)$value['macauth'] > 0)?$value['macauth']:$nullstr).'</td>
<td style="text-align:right;">'.(((int)$value['auth'] > 0)?$value['auth']:$nullstr).'</td>
<td style="text-align:right;">'.(((int)$value['garden'] > 0)?$value['garden']:$nullstr).'</td>
</tr>';
	        }
	        $html .= '<tr><td><strong>Total</td>
<td style="text-align:right;"><strong>'.(((int)$totals_garden > 0)?$totals_garden:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['guest'][$k] > 0)?$totals['guest'][$k]:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['macauth'][$k] > 0)?$totals['macauth'][$k]:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['auth'][$k] > 0)?$totals['auth'][$k]:$nullstr).'</strong></td>
<td style="text-align:right;"><strong>'.(((int)$totals['garden'][$k] > 0)?$totals['garden'][$k]:$nullstr).'</strong></td>
</tr>';
        }

        return $html;
    }

    protected function getData($groupIds, $dateFrom, $dateTo) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $result = array();

        foreach ($groupIds as $k => $v) {

        	$groupRel = $this->_getGroupRelations(array($v));

		/*all users (garden)*/
        	$select = $db->select()
	        	->from(array('a' => 'acct_garden_session'), array('COUNT(*) as cnt_by_group'))
	        	->join(array('b' => 'acct_garden_roaming'), 'a.session_id = b.session_id')
	        	->join(array('c' => 'node'), 'b.node_id = c.node_id')
	        	->join(array('d' => 'group'), 'c.group_id = d.group_id', array('d.name as group_name','d.group_id'))
	        	->where('d.group_id IN (?)', $groupRel)
		        ->where('DATE(a.start_time) BETWEEN ? AND ?', $dateFrom, $dateTo)
	        	->where('NOT ISNULL(a.stop_time)')
	        	->group('d.group_id');

        	$garden_totals[$v] = 0;
        	foreach ($db->fetchAll($select) as $key => $value) {
        		$garden_totals[$v] += $value['cnt_by_group'];
        		$result[$v][$value['group_id']]['garden'] = $value['cnt_by_group'];
        		$result[$v][$value['group_id']]['name'] = $value['group_name'];
        	}

		/*guests*/
	        $select = $db->select()
		        ->from(array('a' => 'acct_internet_session'), array('COUNT(*) as cnt_by_group'))
		        ->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
		        ->join(array('c' => 'node'), 'b.node_id = c.node_id')
		        ->join(array('d' => 'group'), 'c.group_id = d.group_id')
		        ->where('d.group_id IN (?)', $groupRel)
		        ->where('DATE(a.start_time) BETWEEN ? AND ?', $dateFrom, $dateTo)
		        ->where('NOT ISNULL(a.stop_time)')
		        ->where('a.groupname = "Guest"')
		        ->group('d.group_id');

	    	$guest_totals[$v] = 0;
	        foreach ($db->fetchAll($select) as $key => $value) {
	        	$guest_totals[$v] += $value['cnt_by_group'];
	        	$result[$v][$value['group_id']]['guest'] = $value['cnt_by_group'];
	        }

		/*mac auth*/
	        $select = $db->select()
		        ->from(array('a' => 'acct_internet_session'), array('COUNT(*) as cnt_by_group'))
		        ->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
		        ->join(array('c' => 'node'), 'b.node_id = c.node_id')
		        ->join(array('d' => 'group'), 'c.group_id = d.group_id')
		        ->where('d.group_id IN (?)', $groupRel)
		        ->where('DATE(a.start_time) BETWEEN ? AND ?', $dateFrom, $dateTo)
		        ->where('NOT ISNULL(a.stop_time)')
		        ->where('a.groupname = "MACAuthenticated"')
		        ->group('d.group_id');

	   	$macauth_totals[$v] = 0;
	        foreach ($db->fetchAll($select) as $key => $value) {
	        	$macauth_totals[$v] += $value['cnt_by_group'];
	        	$result[$v][$value['group_id']]['macauth'] = $value['cnt_by_group'];
	        }
		/*password auth*/
	        $select = $db->select()
		        ->from(array('a' => 'acct_internet_session'), array('COUNT(*) as cnt_by_group'))
		        ->join(array('b' => 'acct_internet_roaming'), 'a.session_id = b.session_id')
		        ->join(array('c' => 'node'), 'b.node_id = c.node_id')
		        ->join(array('d' => 'group'), 'c.group_id = d.group_id')
		        ->where('d.group_id IN (?)', $groupRel)
		        ->where('DATE(a.start_time) BETWEEN ? AND ?', $dateFrom, $dateTo)
		        ->where('NOT ISNULL(a.stop_time)')
		        ->where('a.groupname = "Authenticated"')
		        ->group('d.group_id');

	   	$auth_totals[$v] = 0;
	        foreach ($db->fetchAll($select) as $key => $value) {
	        	$auth_totals[$v] += $value['cnt_by_group'];
	        	$result[$v][$value['group_id']]['macauth'] = $value['cnt_by_group'];
	        }
        }

        return array ('data' => $result, 'totals' => array('garden' => $garden_totals, 'guest' => $guest_totals, 'macauth' => $macauth_totals, 'auth' => $auth_totals) );
    }
}
