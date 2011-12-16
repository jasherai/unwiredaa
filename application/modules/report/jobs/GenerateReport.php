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

/**
 *
 * @author B. Krastev <bkrastev@web-teh.net>
 */

class Report_Job_GenerateReport {

    public function run()
    {
        $reports = $this->getPendingReports();

        foreach ($reports as $report) {
            $this->generateReport($report);
        }
    }

    public function getPendingReports()
    {
        $reportGroupMapper = new Report_Model_Mapper_Group();

        $periodicalReports = $reportGroupMapper->findBy(array('report_type' => 1));

        $pendingReports = array();

        foreach ($periodicalReports as $report) {
            $report = new Report_Model_Group();
            $interval = $report->getReportInterval();

            $now = new Zend_Date();

            $toDate = new Zend_Date($report->getDateTo());
            $fromDate = new Zend_Date($report->getDateFrom());

            $period = $period->sub($fromDate);

            switch ($interval) {
                case 4:
                    $fromDate->setYear($now->getYear());
                break;

                case 3:
                    $fromDate->setYear($now->getYear());
                    $fromDate->setMonth($now->getMonth());
                break;

                case 2:
                    $diffStamp = $now->getDate()
                                          ->subDate($fromDate->getDate())
                                               ->getTimestamp();

                    if (fmod($diffStamp, (7 * 24 * 3600)) == 0) {
                        $fromDate->setDate($now);
                    }
                ;
                break;

                case 1:
                default:
                    $fromDate = $now;
                break;
            }

            if (!$fromDate->isToday()) {
                continue;
            }

            $toDate->add($period);


        }
    }

    public function generateReport(Report_Model_Group $report)
    {
        try {
            $codeTemplateMapper = new Report_Model_Mapper_CodeTemplate();
    		$codeTemplate = $codeTemplateMapper->find($report->getCodetemplateId());

    		$className = $codeTemplate->getClassName();
    		$reportGenerator = new $className;

    		$result = $reportGenerator->getReport(array_keys($report->getGroupsAssigned()), $report->getDateFrom(), $report->getDateTo());

    		$resultMapper = new Report_Model_Mapper_Result();
    		$entity = $resultMapper->getEmptyModel();

    		$entity->setDateAdded(date('Y-m-d H:i:s'));
    		$entity->setData($result['data']);
    		$entity->setHtmldata($result['html']);
    		$entity->setReportGroupId($report->getReportGroupId());
    		$resultMapper->save($entity);

    		return true;
        } catch (Exception $e) {
            return false;
        }
    }
}