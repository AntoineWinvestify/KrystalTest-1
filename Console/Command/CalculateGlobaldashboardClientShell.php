<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author Antoine
 * @version 0.2.0
 * @date 2018-06-18
 * @package
 */
/*
 * This Client starts analyzing the data of the amortization tables and writes the data-elements 
 * 'investment_nextPaymentDate', 'investment_dateForPaymentDelayCalculation' and 
 * 'investment_paymentStatus' to the corresponding database tables.
 * Encountered errors are stored in the database table "applicationerrors".
 *
 *
 * 2017-12-23		version 0.1
 * Basic version
 *
 * 2018-06-18           version 0.2
 * Added "netPaymentDate"
 * 
 * 
 * PENDING:
 * get paidInstalments
 *
 * 
 */
App::import('Shell', 'GearmanClient');
App::import('Shell', 'UserData');

class CalculateGlobaldashboardClientShell extends GearmanClientShell {

    public $uses = array('Queue2', 'Investor', 'Userinvestmentdata', 'Globaldashboard', 'Linkedaccount', 'Investment', 'Globaldashboarddelay');
    public $today;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
        return;
    }

    public function initClient() {
        $this->today = date('Y-m-d', time());

        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $this->flowName = "GEARMAN_FLOW3C";
        $inActivityCounter = 0;
        $workerFunction = "parseFileFlow";

        echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 3C Client\n";
        }

        $inActivityCounter++;

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');

        $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED, WIN_QUEUE_STATUS_START_GLOBAL_CALCULATION), WIN_QUEUE_STATUS_START_GLOBAL_CALCULATION, $jobsInParallel);

        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
        }

        if (!empty($pendingJobs)) {

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
            }

            foreach ($pendingJobs as $keyjobs => $job) {
                $userReference = $job['Queue2']['queue2_userReference'];
                $queueId = $job['Queue2']['id'];
                $this->queueInfo[$job['Queue2']['id']] = json_decode($job['Queue2']['queue2_info'], true);
                $directory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
                $dir = new Folder($directory);
                $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
                $i = 0;

                foreach ($subDir[0] as $subDirectory) {
                    $tempName = explode("/", $subDirectory);
                    $linkedAccountId = $tempName[count($tempName) - 1];

                    if (!in_array($linkedAccountId, $this->queueInfo[$job['Queue2']['id']]['companiesInFlow'])) {
                        continue;
                    }

                    $this->userLinkaccountIds[$job['Queue2']['id']][$i] = $linkedAccountId;
                    $i++;

                    $params = array(
                        'userReference' => $job['Queue2']['queue2_userReference'],
                        'actionOrigin' => $job['Queue2']['queue2_type'], // this was WIN_ACTION_ORIGIN_ACCOUNT_LINKING,
                        'finishDate' => $this->queueInfo[$queueId]['date'],
                        'queueInfo' => json_decode($job['Queue2']['queue2_info'], true));
                }

                $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue2']['id'] . ".-;" .
                        $workerFunction . ".-;" . $job['Queue2']['queue2_userReference']);
            }


            echo "Calling consolidateData\n";
            $this->calculateGlobals($params);
            $this->calculateDelays($params);
            //?
            foreach ($this->queueInfo as $queueIdKey => $info) {
                foreach ($info['companiesInFlow'] as $companieInFlow) {
                    $this->userResult[$queueIdKey][$companieInFlow] = 1;
                }
            }

            $this->verifyStatus(WIN_QUEUE_STATUS_GLOBAL_CALCULATION_FINISHED, "Global dashboard calculated correctly", WIN_QUEUE_STATUS_START_GLOBAL_CALCULATION, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
        }
        else {
            $inActivityCounter++;
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n";
            }
            sleep(4);                                                       // Just wait a short time and check again
        }
        if ($inActivityCounter > MAX_INACTIVITY) {                          // system has dealt with ALL request for tonight, so exit "forever"
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT\n";
            }
            exit;
        }
    }

    /**
     * Calculate or update globalDashboard for all active linkedaccount of the user.
     * We calculate all historical if the user link a new account.
     * 
     * @param array $params                                                     //Array with the queue info, we need the companiesInFlow to get the delay ranges of the linkedaccounts
     * @return boolean
     */
    public function calculateGlobals($params) {

        $userinvestmentdataIds = array();
        $investorId = $this->Investor->getData(array('investor_identity' => $params['userReference']), 'id', null, null, 'first')['Investor']['id'];
        $finishDate = strtotime($params['finishDate']);
        $conditions = array(
            'Accountowner.investor_id' => $investorId,
            'Linkedaccount.linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);
        if ($params['queueInfo']['originExecution'] != WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
            $conditions = array_merge($conditions, array('Linkedaccount.linkedaccount_linkingProcess' => WIN_LINKING_NOTHING_IN_PROCESS));
        }
        $linkedAccountList = $this->Linkedaccount->getData($conditions, 'Linkedaccount.id', null, null, 'list', 0);

        //Get the lowest date
        if ($params['queueInfo']['originExecution'] == WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
            $startDate = $this->Userinvestmentdata->getData(
                            array('userinvestmentdata_investorIdentity' => $params['userReference']), 'date', 'date ASC', null, 'first')['Userinvestmentdata']['date'];
        }
        else {
            $startDate = min($params['queueInfo']['startDate']);
        }
        $date = strtotime($startDate);
        //Get all userinvestmentdatas

        while ($date < $finishDate) {
            $searchDate = date('Ymd', $date);
            $i = 1;
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
                echo "Calculating global data for $searchDate";
            }
            foreach ($linkedAccountList as $key => $linkedaccountId) {

                $data[$key] = $this->Userinvestmentdata->getData(
                        array('Userinvestmentdata.userinvestmentdata_investorIdentity' => $params['userReference'],
                    'Linkedaccount.linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE,
                    'Userinvestmentdata.date' => $searchDate,
                    'Linkedaccount.id' => $linkedaccountId), null, null, null, 'first', 0);

                if ($params['queueInfo']['originExecution'] == WIN_ACTION_ORIGIN_REGULAR_UPDATE) {
                    while (empty($data[$key])) {
                        $tmpdate = strtotime("$searchDate -$i days");
                        $searchTmpDate = date('Ymd', $tmpdate);
                        $data[$key] = $this->Userinvestmentdata->getData(
                                array('Userinvestmentdata.userinvestmentdata_investorIdentity' => $params['userReference'],
                            'Linkedaccount.linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE,
                            'Userinvestmentdata.date' => $searchTmpDate,
                            'Linkedaccount.id' => $linkedaccountId), null, null, null, 'all', 0);
                        $i++;
                    }
                }
                else if (empty($data[$key]) && $params['queueInfo']['originExecution'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) {
                    unset($data[$key]);
                }
            }

            $globalTotal['date'] = $searchDate;
            $globalTotal['investor_id'] = $investorId;
            $globalTotal['globaldashboard_investorIdentity'] = $params['userReference'];
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
                echo "Data of the linked accounts for $searchDate";
                print_r($data);
            }
            foreach ($data as $dashboardKey => $dashboard) {
                $dashboardData = $dashboard['Userinvestmentdata'];
                foreach ($dashboardData as $fieldKey => $field) {
                    switch ($fieldKey) {
                        case 'id':
                            $userinvestmentdataIds[$dashboardKey] = $field;
                            break;
                        case 'userinvestmentdata_capitalRepayment':
                            $globalTotal['globaldashboard_capitalRepayment'] = $globalTotal['globaldashboard_capitalRepayment'] + $field;
                            break;
                        case 'userinvestmentdata_partialPrincipalRepayment':
                            $globalTotal['globaldashboard_partialPrincipalRepayment'] = $globalTotal['globaldashboard_partialPrincipalRepayment'] + $field;
                            break;
                        case 'userinvestmentdata_receivedPrepayments':
                            $globalTotal['globaldashboard_receivedPrepayments'] = $globalTotal['globaldashboard_receivedPrepayments'] + $field;
                            break;
                        case 'userinvestmentdata_totalGrossIncome':
                            $globalTotal['globaldashboard_totalGrossIncome'] = $globalTotal['globaldashboard_totalGrossIncome'] + $field;
                            break;
                        case 'userinvestmentdata_outstandingPrincipal':
                            $globalTotal['globaldashboard_outstandingPrincipal'] = $globalTotal['globaldashboard_outstandingPrincipal'] + $field;
                            break;
                        case 'userinvestmentdata_interestGrossIncome':
                            $globalTotal['globaldashboard_interestGrossIncome'] = $globalTotal['globaldashboard_interestGrossIncome'] + $field;
                            break;
                        case 'userinvestmentdata_cashInPlatform':
                            $globalTotal['globaldashboard_cashInPlatform'] = $globalTotal['globaldashboard_cashInPlatform'] + $field;
                            break;
                        case 'userinvestmentdata_numberActiveInvestments':
                            $globalTotal['globaldashboard_numberActiveInvestments'] = $globalTotal['globaldashboard_numberActiveInvestments'] + $field;
                            break;
                        case 'userinvestmentdata_reservedAssets':
                            $globalTotal['globaldashboard_reservedAssets'] = $globalTotal['globaldashboard_reservedAssets'] + $field;
                            break;
                        case 'userinvestmentdata_totalNetDeposits':
                            $globalTotal['globaldashboard_totalNetDeposits'] = $globalTotal['globaldashboard_totalNetDeposits'] + $field;
                            break;
                        case 'userinvestmentdata_totalLoansCost':
                            $globalTotal['globaldashboard_totalLoansCost'] = $globalTotal['globaldashboard_totalLoansCost'] + $field;
                            break;
                        case 'userinvestmentdata_numberActiveInvestmentsdecrements':
                            $globalTotal['globaldashboard_numberActiveInvestmentsdecrements'] = $globalTotal['globaldashboard_numberActiveInvestmentsdecrements'] + $field;
                            break;
                        case 'userinvestmentdata_numberActiveInvestmentsincrements':
                            $globalTotal['globaldashboard_numberActiveInvestmentsincrements'] = $globalTotal['globaldashboard_numberActiveInvestmentsincrements'] + $field;
                            break;
                        case 'userinvestmentdata_writtenOff':
                            $globalTotal['globaldashboard_writtenOff'] = $globalTotal['globaldashboard_writtenOff'] + $field;
                            break;
                        case 'userinvestmentdata_defaultInterestIncome':
                            $globalTotal['globaldashboard_defaultInterestIncome'] = $globalTotal['globaldashboard_defaultInterestIncome'] + $field;
                            break;
                        case 'userinvestmentdata_cashDrag':
                            $total = bcadd(bcadd($globalTotal['globaldashboard_outstandingPrincipal'], $globalTotal['globaldashboard_cashInPlatform'], 16), $globalTotal['globaldashboard_reservedAssets'], 16);
                            $globalTotal['globaldashboard_cashDrag'] = bcdiv($globalTotal['globaldashboard_cashInPlatform'], $total, 16);
                            break;
                        //globaldashboard_exposure
                    }
                }
            }
            $globaId = $this->Globaldashboard->getData(array('date' => $searchDate, 'investor_id' => $investorId), 'id', null, null, 'first')['Globaldashboard']['id'];
            if (!empty($globaId)) {
                $globalTotal['id'] = $globaId;
            }
             if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . " To save\n";
                print_r($globalTotal);
            }
            $this->Globaldashboard->save($globalTotal);

            foreach ($userinvestmentdataIds as $userinvestmentdataId) {
                $this->Userinvestmentdata->save(array('id' => $userinvestmentdataId, 'globaldashboard_id' => $this->Globaldashboard->id));
                $this->Userinvestmentdata->clear();
            }

            $this->Globaldashboard->clear();
            unset($globalTotal);
            $date = strtotime("+1 day", $date);
        }

        $lastAccess = date("Y-m-d", strtotime($searchDate));
        $companiedInFlow = $params['queueInfo']['companiesInFlow'];
        //UPDATE LASTACCESS IN LINKEDACCOUNT;
        foreach ($companiedInFlow as $linkedaccountId) {
            $this->Linkedaccount->id = $linkedaccountId;
            $this->Linkedaccount->saveField('linkedaccount_lastAccessed', $lastAccess);
            $this->Linkedaccount->saveField('linkedaccount_linkingProcess', WIN_LINKING_NOTHING_IN_PROCESS);
        }
    }

    /**
     * Calculate global delays for the investor on the day.
     * 
     * @param array $params                                                     //Array with the queue info, we need the companiesInFlow to get the delay ranges of the linkedaccounts
     * @return boolean
     */
    public function calculateDelays($params) {

        $date = strtotime($params['finishDate'] . " -1 days");
        $date = date('Y-m-d', $date);
        $investorId = $this->Investor->getData(array('investor_identity' => $params['userReference']), 'id', null, null, 'first')['Investor']['id'];
        $conditions = array(
            'Accountowner.investor_id' => $investorId,
            'Linkedaccount.linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);
        if ($params['queueInfo']['originExecution'] != WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
            $conditions = array_merge($conditions, array('Linkedaccount.linkedaccount_linkingProcess' => WIN_LINKING_NOTHING_IN_PROCESS));
        }
        $companiedInFlow = $this->Linkedaccount->getData($conditions, 'Linkedaccount.id', null, null, 'list', 0);

        $range = $this->Investment->getDefaultedByOutstanding($companiedInFlow);
        $range2 = $this->Investment->getDefaultedByInvestmentNumber($companiedInFlow);

        $globaldashboardId = $this->Globaldashboard->getData(array('globaldashboard_investorIdentity' => $params['userReference'], 'date' => $date), 'id', null, null, 'first')['Globaldashboard']['id'];

        $this->Globaldashboarddelay->create();
        $this->Globaldashboarddelay->save(array(
            'globaldashboarddelay_1-7OOutstanding' => $range['1-7'],
            'globaldashboarddelay_delay8-30Outstanding' => $range['8-30'],
            'globaldashboarddelay_delay31-60Outstanding' => $range['31-60'],
            'globaldashboarddelay_delay61-90Outstanding' => $range['61-90'],
            'globaldashboarddelay_delay>90Outstanding' => $range['+90'],
            'globaldashboarddelay_outstandingDebts' => $range['outstandingDebt'],
            'globaldashboarddelay_currentOutstanding' => $range['current'],
            'globaldashboarddelay_delay1-7Active' => $range2['1-7'],
            'globaldashboarddelay_delay8-30Active' => $range2['8-30'],
            'globaldashboarddelay_delay31-60Active' => $range2['31-60'],
            'globaldashboarddelay_delay61-90Active' => $range2['61-90'],
            'globaldashboarddelay_delay>90Active' => $range2['+90'],
            'globaldashboarddelay_activeDebts' => $range2['+90Number'],
            'globaldashboarddelay_currentActive' => $range2['current'],
            'globaldashboard_id' => $globaldashboardId
        ));

        return true;
    }

}
