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
 * @author 
 * @version 0.1
 * @date 2017-10-13
 * @package
 */

App::import('Shell','GearmanClient');

/**
 * Description of ConsolidationClientShell
 *
 * @author antoiba
 */
class ConsolidationClientShell extends GearmanClientShell {
    
    /**
     * Function to init the process to recollect all the user investment data
     *  @param integer $this->args[0]|$queueStatus It is the status we need to use on the search on DB
     *  @param integer $this->args[1]|$queueTypeAccess It is the access type the user used to get the data
     */
    public function initClient() {
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $this->flowName = "GEARMAN_FLOW4";
        $inActivityCounter = 0;
        $workerFunction = "consolidation";
        echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 4 Client\n";
        }
        
        $inActivityCounter++;                                           // Gearman client 
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        //$this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        //$this->date = date("Ymd");
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED, $jobsInParallel);
            print_r($pendingJobs);
            
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
            }
            
            if (!empty($pendingJobs)) {
                
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
                }
                
                $linkedaccountsResults = [];
                foreach ($pendingJobs as $keyjobs => $job) {
                    
                    $queueInfo = json_decode($job['Queue']['queue_info'], true);
                    $this->queueInfo[$job['Queue']['id']] = $queueInfo;
                    
                    $data["companies"] = $queueInfo['companiesInFlow'];
                    $this->userLinkaccountIds[$job['Queue']['id']] = $queueInfo['companiesInFlow'];;
                    $data["queue_userReference"] = $job['Queue']['queue_userReference'];
                    $data["queue_id"] = $job['Queue']['id'];
                    $data["date"] = $queueInfo['date'];
                    $services = $this->getConsolidationWorkerFunction();
                    foreach ($services as $service) {
                        $data['service'] = $service;
                        if (Configure::read('debug')) {
                            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Showing data sent to worker \n");
                            print_r($data["companies"]);
                            echo "userReference ". $data["queue_userReference"] . "\n";
                            echo "queueId " . $data["queue_id"] . "\n";
                            echo "the date is " . $data["date"] . "\n";
                            echo "All information \n";
                            print_r($data);
                        }
                        $this->GearmanClient->addTask($service['gearmanFunction'], json_encode($data), null, $data["queue_id"] . ".-;" .  $service['gearmanFunction'] . ".-;" . $job['Queue']['queue_userReference']);
                    }
                }
                
                
                
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the information to Worker\n";
                }

                $this->GearmanClient->runTasks();

                // ######################################################################################################

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n";
                }
                
                $this->saveConsolidationFields();
                
                //$this->verifyStatus(WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED, "Data successfuly downloaded", WIN_QUEUE_STATUS_DATA_EXTRACTED, WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED);
                foreach ($this->userResult as $queueId => $userResult) {
                    $date = $this->queueInfo[$queueId]['date'];
                    $lastAccess = date("Y-m-d", strtotime($date-1));
                    foreach ($this->queueInfo[$queueId]['companiesInFlow'] as $linkaccountId) {
                        $this->Linkedaccount->id = $linkaccountId;
                        $this->Linkedaccount->saveField('linkedaccount_lastAccessed', $lastAccess);
                    }
                }
               
                
            }
            else {
                $inActivityCounter++;
                echo __METHOD__ . " " . __LINE__ . " Nothing in queue, so sleeping \n";                
                sleep (4); 
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                echo __METHOD__ . " " . __LINE__ . "Maximum Waiting time expired, so EXIT \n";                  
                exit;
            }
            $numberOfIteration++;
        }
    }
    
    /**
     * Function to save all the amortization tables on DB per user and per linkaccount
     */
    public function saveConsolidationFields() {
        $result = [];
        foreach ($this->tempArray as $queuekey => $tempArray) {
            foreach ($tempArray as $linkedaccountId => $tempArrayByCompany) {
                foreach ($tempArrayByCompany as $key => $formulaValues) {
                    $model = ClassRegistry::init($formulaValues['table']);
                    $this->model->saveDataByType($linkedaccountId, $this->date, $formulaValues);
                }
            }
        }
        $this->model = ClassRegistry::init('Investment');
        $investmentIds = $this->Investment->getInvestmentIdByLoanId($loanIds);
        foreach ($this->tempArray as $queuekey => $tempArray) {
            foreach ($tempArray as $linkaccount => $linkaccountData) {
                $this->Amortizationtable->saveAmortizationtable($linkaccountData, $investmentIds);
            }
        }
    }
    
    public function getConsolidationWorkerFunction() {
        //Future implementation
        //$formulaByInvestor = $this->getFormulasFromDB();
        ///////////////* THIS IS TEMPORAL
        $services = [];
        
        $services[0]['service'] = "calculateNetAnnualReturnXirr";
        $services[0]['gearmanFunction'] = 'getFormulaCalculate';
        $services[1]['service'] = "calculateNetAnnualTotalFundsXirr";
        $services[1]['gearmanFunction'] = 'getFormulaCalculate';
        $services[2]['service'] = "calculateNetAnnualPastReturnXirr";
        $services[2]['gearmanFunction'] = 'getFormulaCalculate';
        /////////////////////
        return $services;
    }
}
