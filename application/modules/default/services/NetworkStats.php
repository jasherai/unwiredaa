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

class Default_Service_NetworkStats
{
    public function getStatistics()
    {
        $db = Zend_Db_Table::getDefaultAdapter();

        $data = array(
                      'usersTotal' => 0,
                      'usersOnline' => 0,
                      'usersGarden' => 0,
                      'devicesOnline' => 0,
                      'devicesOffline' => 0
                      );

        try {
            $data['usersTotal'] = $db->fetchOne("SELECT count(DISTINCT `session_id`) from `acct_garden_interim` WHERE `time` > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");

            $data['usersOnline'] = $db->fetchOne("SELECT count(DISTINCT session_id) from acct_internet_interim WHERE time > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");

            $data['usersGarden'] = $data['usersTotal'] - $data['usersOnline'];

            $nodeTable = new Nodes_Model_DbTable_Node();
            $nodeTableName = $nodeTable->info(Zend_Db_Table::NAME);
            $nodeTable = null;

            $data['devicesOnline'] = $db->fetchOne("select count(*) from `{$nodeTableName}` where `online_status` = 1 AND `deleted` = 0;");
            $data['devicesOffline'] = $db->fetchOne("select count(*) from `{$nodeTableName}` where `online_status` = 0 AND `deleted` = 0;");
        } catch (Exception $e) {
            // recover
        }

        $networkStats = new Default_Model_NetworkStats();

        $networkStats->fromArray($data);

        return $networkStats;
    }
}