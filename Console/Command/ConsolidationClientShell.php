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
    
    protected $services;
    
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
                    $data["originExecution"] = $queueInfo['originExecution'];
                    $this->getConsolidationWorkerFunction();
                    foreach ($this->services as $nameServiceKey => $service) {
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
                        $this->GearmanClient->addTask($service['gearmanFunction'], json_encode($data), null, $data["queue_id"] . ".-;" .  $nameServiceKey . ".-;" . $job['Queue']['queue_userReference']);
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
        foreach ($this->tempArray as $queueKey => $tempArray) {
            foreach ($tempArray as $linkedaccountId => $tempArrayByCompany) {
                foreach ($tempArrayByCompany as $key => $serviceValues) {
                    if (is_array($serviceValues)) {
                        $serviceValues = array_shift($serviceValues);
                    }
                    $model = ClassRegistry::init($this->services[$key]['database']['model']);
                    $id = $model->find('first',
                        array( 'conditions' => array('date' => date("Y-m-d", strtotime($this->queueInfo[$queueKey]['date']))),
                               'recursive' => -1,
                               'fields' => array('id')
                        )  
                    ); 
                    $model->id = $id;
                    $model->saveField($this->services[$key]['database']['variable'], $serviceValues);
                }
            }
        }
        foreach ($this->userResult as $queueId => $userResult) {
            $date = $this->queueInfo[$queueId]['date'];
            $lastAccess = date("Y-m-d", strtotime($date-1));
            foreach ($this->queueInfo[$queueId]['companiesInFlow'] as $linkaccountId) {
                $this->Linkedaccount->id = $linkaccountId;
                $this->Linkedaccount->saveField('linkedaccount_lastAccessed', $lastAccess);
            }
        }
    }
    
    public function getConsolidationWorkerFunction() {
        //Future implementation
        //$formulaByInvestor = $this->getFormulasFromDB();
        ///////////////* THIS IS TEMPORAL
        $this->services = [];
        
        $this->services['netAnnualReturnXirr']['service'] = "calculateNetAnnualReturnXirr";
        $this->services['netAnnualReturnXirr']['gearmanFunction'] = 'getFormulaCalculate';
        $this->services['netAnnualReturnXirr']['database']['table'] = 'userinvestmentdata';
        $this->services['netAnnualReturnXirr']['database']['variable'] = 'userinvestmentdata_netAnualReturnPast12Months';
        $this->services['netAnnualReturnXirr']['database']['model'] = 'Userinvestmentdata';
        $this->services['netAnnualTotalFundsReturnXirr']['service'] = "calculateNetAnnualTotalFundsReturnXirr";
        $this->services['netAnnualTotalFundsReturnXirr']['gearmanFunction'] = 'getFormulaCalculate';
        $this->services['netAnnualTotalFundsReturnXirr']['database']['table'] = 'userinvestmentdata';
        $this->services['netAnnualTotalFundsReturnXirr']['database']['variable'] = 'userinvestmentdata_netAnualTotalFundsReturn';
        $this->services['netAnnualTotalFundsReturnXirr']['database']['model'] = 'Userinvestmentdata';
        $this->services['netAnnualReturnPastYearXirr']['service'] = "calculateNetAnnualReturnPastYearXirr";
        $this->services['netAnnualReturnPastYearXirr']['gearmanFunction'] = 'getFormulaCalculate';
        $this->services['netAnnualReturnPastYearXirr']['database']['table'] = 'userinvestmentdata';
        $this->services['netAnnualReturnPastYearXirr']['database']['variable'] = 'userinvestmentdata_netAnualReturnPastYear';
        $this->services['netAnnualReturnPastYearXirr']['database']['model'] = 'Userinvestmentdata';
        //$services[1]['service'] = "calculateNetAnnualTotalFundsXirr";
        //$services[1]['gearmanFunction'] = 'getFormulaCalculate';
        //$services[2]['service'] = "calculateNetAnnualPastReturnXirr";
        //$services[2]['gearmanFunction'] = 'getFormulaCalculate';
        /////////////////////
    }
    
    /**
     * Function that runs after a task was complete on the Gearman Worker
     * @param GearmanTask $task It is a Gearman::Client's representation of a task done.
     *          string $task->unique Returns the unique identifier for this task. This is assigned by the GearmanClient
     *                  $data[0] It is the queueId of the task
     *                  $data[1] It is the function name on the Gearman Worker
     *                  $data[2] It is the userReference  
     *          string $task->data Returns data being returned for a task by a worker
     *                  $data["statusCollect"] It is the status of the request by linkaccount Id
     *                  $data["errors"] If the statusCollect is 0, the error is saved on it
     *                  $data["tempArray"] The information to save on database by linkaccount id
     */
    public function verifyCompleteTask (GearmanTask $task) {
        echo "Received data from Worker \n";
        $data = explode(".-;", $task->unique());
        if (empty($this->userReference[$data[0]])) {
            $this->userReference[$data[0]] = $data[2];
        }
        $dataWorker = json_decode($task->data(), true);
        
        if (!empty($dataWorker['statusCollect'])) {
            foreach ($dataWorker['statusCollect'] as $linkaccountId => $status) {
                $this->userResult[$data[0]][$linkaccountId] = $status;
                $this->gearmanErrors[$data[0]][$linkaccountId] = $dataWorker['errors'][$linkaccountId];
            }
        }
        if (!empty($dataWorker['tempArray'])) {
            foreach ($dataWorker['tempArray'] as $linkaccountId => $dataArray) {
                if (empty($this->tempArray[$data[0]][$linkaccountId])) {
                    $this->tempArray[$data[0]][$linkaccountId] = $dataArray;
                } 
                else {
                    $keyDataArray = key($dataArray);
                    $this->tempArray[$data[0]][$linkaccountId][$keyDataArray] = $dataArray[$keyDataArray];
                }
                $this->userResult[$data[0]][$linkaccountId] = 1;
            }
            
        }

//        print_r($this->userResult);
//        print_r($this->userReference);
        echo "ID Unique: " . $task->unique() . "\n";
//        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
}
