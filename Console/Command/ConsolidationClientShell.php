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
    
    protected $services = [
        'netAnnualReturnXirr' => [],
        'netAnnualTotalFundsReturnXirr' => [],
        'netAnnualReturnPastYearXirr' => [],
        'netReturn' => [],
        'netReturnPastYear' => []
    ];
    
    protected $netAnnualReturnXirr = [
        'service' => 'calculateNetAnnualReturnXirr',                            //Service is the function that is needed to call in the worker to calculate this variable
        'gearmanFunction' => 'getFormulaCalculate',                             //This is the gearman function that will be call in order to initiate the service
        'database' => [                                                         //This is the information to save the variable in DB
            'platform' => [                                                     //platform is the variable to save the data by company
                'table' => 'userinvestmentdata',                                                //Table is the table where is going to save the data
                'variable' => 'userinvestmentdata_netAnnualReturnPast12Months',                 //Variable is the name of the service where is going to save the data
                'model' => 'Userinvestmentdata'                                                 //Model is the model to initiate
            ],
            'dashboardOverview' => [                                            //dashboardOverview is the variable to save the data by investor
                'variable' => 'dashboardoverviewdata_netAnnualReturnPast12Months',              //Variable is the name of the service where is going to save the data
            ]
        ]
    ];
    
    protected $netAnnualTotalFundsReturnXirr = [
        'service' => 'calculateNetAnnualTotalFundsReturnXirr',
        'gearmanFunction' => 'getFormulaCalculate',
        'database' => [
            'platform' => [
                'table' => 'userinvestmentdata',
                'variable' => 'userinvestmentdata_netAnnualTotalFundsReturn',
                'model' => 'Userinvestmentdata'
            ],
            'dashboardOverview' => [
                'variable' => 'dashboardoverviewdata_netAnnualTotalFundsReturn',
            ]
            
        ]
    ];
    
    protected $netAnnualReturnPastYearXirr = [
        'service' => 'calculateNetAnnualReturnPastYearXirr',
        'gearmanFunction' => 'getFormulaCalculate',
        'database' => [
            'platform' => [
                'table' => 'userinvestmentdata',
                'variable' => 'userinvestmentdata_netAnnualReturnPastYear',
                'model' => 'Userinvestmentdata'
            ],
            'dashboardOverview' => [
                'variable' => 'dashboardoverviewdata_netAnnualReturnPastYear',
            ]
            
        ]
    ];
    
    protected $netReturn = [
        'service' => 'calculateNetReturn',
        'gearmanFunction' => 'getFormulaCalculate',
        'database' => [
            'platform' => [
                'table' => 'userinvestmentdata',
                'variable' => 'userinvestmentdata_netReturnPast12Months',
                'model' => 'Userinvestmentdata'
            ],
            'dashboardOverview' => [
                'variable' => 'dashboardoverviewdata_netReturnPast12Months',
            ]
            
        ]
    ];
    
    protected $netReturnPastYear = [
        'service' => 'calculateNetReturnPastYear',
        'gearmanFunction' => 'getFormulaCalculate',
        'database' => [
            'platform' => [
                'table' => 'userinvestmentdata',
                'variable' => 'userinvestmentdata_netReturnPastYear',
                'model' => 'Userinvestmentdata'
            ],
            'dashboardOverview' => [
                'variable' => 'dashboardoverviewdata_netReturnPastYear',
            ]
            
        ]
    ];
    
    protected $dashboardOverviewData = [];
    
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
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_CALCULATION_CONSOLIDATION_FINISHED, WIN_QUEUE_STATUS_START_CONSOLIDATION),
                                                  WIN_QUEUE_STATUS_START_CONSOLIDATION,
                                                $jobsInParallel);
            echo "Pending jobs in queue:    ";
            print_r($pendingJobs);
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
            }
            
            if (!empty($pendingJobs)) {
                
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $queueInfo = json_decode($job['Queue2']['queue2_info'], true);
                    $this->queueInfo[$job['Queue2']['id']] = $queueInfo;
                    //We need the companies that are not in progress in order to calculate the Dashboardoverview data
                    $data['companiesNothingInProgress'] = $this->Linkedaccount->getLinkAccountsWithNothingInProcess($job['Queue2']['queue2_userReference']);
                    $data["companies"] = $queueInfo['companiesInFlow'];
                    $this->userLinkaccountIds[$job['Queue2']['id']] = $queueInfo['companiesInFlow'];;
                    $data["queue_userReference"] = $job['Queue2']['queue2_userReference'];
                    $data["queue_id"] = $job['Queue2']['id'];
                    $data["date"] = $queueInfo['date'];
                    $data["originExecution"] = $queueInfo['originExecution'];
                    //Now, we get all the variables Services, in the future it must be from database
                    if (empty($queueInfo['services'])) {
                        $this->getAllServices();
                    }
                    //$this->getConsolidationWorkerFunction();
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
                        $this->GearmanClient->addTask($service['gearmanFunction'], json_encode($data), null, $data["queue_id"] . ".-;" .  $nameServiceKey . ".-;" . $job['Queue2']['queue2_userReference']);
                    }
                }
                
                
                
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the information to Worker\n";
                }

                $this->GearmanClient->runTasks();
                print_r($this->tempArray);
                print_r($this->gearmanErrors);
                print_r($this->userResult);
                // ######################################################################################################
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n";
                }
                $this->saveConsolidationFields();
                
                $this->verifyStatus(WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED, "Data successfuly downloaded", WIN_QUEUE_STATUS_CALCULATION_CONSOLIDATION_FINISHED, WIN_QUEUE_STATUS_CONSOLIDATION_FINISHED);

            }
            else {
                $inActivityCounter++;
                echo __METHOD__ . " " . __LINE__ . " Nothing in queue, so sleeping \n";                
                sleep (WIN_SLEEP_DURATION); 
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
        $this->Investor = ClassRegistry::init('Investor');
        $this->Dashboardoverviewdata = ClassRegistry::init('Dashboardoverviewdata');
        foreach ($this->tempArray as $queueKey => $tempArray) {
            foreach ($tempArray as $linkedaccountId => $tempArrayByCompany) {
                //If linkedaccountId is investor, this data is for the dashboardOverview
                if ($linkedaccountId == 'investor') {
                    $dashboardOverviewData = [];
                    $keyInvestorIdentity = key($tempArrayByCompany);
                    //We search for the array by investorIdentity as per serviceValue (for example, NAR, NARPastYear...)
                    foreach ($tempArrayByCompany[$keyInvestorIdentity] as $key => $serviceValues) {
                        //If the serviceValues is an array, it is the NARPastyear, for now, we only need the last year so we get with an array_shift
                         if (is_array($serviceValues)) {
                            $serviceValues = array_shift($serviceValues);
                        }
                        $dashboardOverviewData[$this->services[$key]['database']['dashboardOverview']['variable']] = $serviceValues;
                    }
                    $investorId = $this->Investor->getData(
                                                        ['investor_identity' => $keyInvestorIdentity],
                                                        ['id']
                                                    );
                    $dashboardOverviewData['date'] = date("Y-m-d", strtotime($this->queueInfo[$queueKey]['date']));
                    $dashboardOverviewData['investor_id'] = $investorId[0]['Investor']['id'];
                    $this->Dashboardoverviewdata->save($dashboardOverviewData);
                    continue;
                }
                //If it is not investor, we are going to save the values by linkedacccountId
                foreach ($tempArrayByCompany as $key => $serviceValues) {
                    //If the serviceValues is an array, it is the NARPastyear, for now, we only need the last year so we get with an array_shift
                    if (is_array($serviceValues)) {
                        $serviceValues = array_shift($serviceValues);
                    }
                    //print_r($this->services);
                    $model = ClassRegistry::init($this->services[$key]['database']['platform']['model']);
                    $dateTime = date("Y-m-d", strtotime($this->queueInfo[$queueKey]['date']));
                    $conditions = array(
                        'date <=' => $dateTime,                                 //We get the last entry taking into account the date we initiate the flow1
                        'linkedaccount_id' => $linkedaccountId);
                    $id = $model->getData($conditions,['id'],'id DESC',null,'first');
                    $model->id = $id;
                    $model->saveField($this->services[$key]['database']['platform']['variable'], $serviceValues);
                }
            }
        }
        //After save all the service, we change the lastAccess of the account as the date of the flow1 started
        foreach ($this->userResult as $queueId => $userResult) {
            $date = $this->queueInfo[$queueId]['date'];
            $lastAccess = date("Y-m-d", strtotime($date-1));
            foreach ($this->queueInfo[$queueId]['companiesInFlow'] as $linkaccountId) {
                $this->Linkedaccount->id = $linkaccountId;
                $this->Linkedaccount->saveField('linkedaccount_lastAccessed', $lastAccess);
                $this->Linkedaccount->saveField('linkedaccount_linkingProcess', WIN_LINKING_NOTHING_IN_PROCESS);
            }
        }
    }
    
    /**
     * Function to get all the services that are needed to calculate in the flow4
     * Services:
     * netAnnualReturnXirr 
     * netAnnualTotalFundsReturnXirr
     * netAnnualReturnPastYearXirr
     * netReturn
     * netReturnPastYear
     */
    public function getAllServices() {
        $services = $this->services;
        foreach ($services as $keyServices => $service) {
            $this->services[$keyServices] = $this->$keyServices;
        }
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
                if ($linkaccountId == 'investor') {
                    $keyForInvestor = key($status[$data[2]]);
                    $dataForInvestor = $status[$data[2]];
                    $this->userResult[$data[0]]['investor'][$data[2]][$keyForInvestor] = $dataForInvestor[$keyForInvestor];
                    if (!empty($dataWorker['errors']['investor'][$data[2]][$keyForInvestor])) {
                        $this->gearmanErrors[$data[0]]['investor'][$data[2]][$keyForInvestor] = $dataWorker['errors']['investor'][$data[2]][$keyForInvestor];
                    }
                }
                else {
                    $keyFunction = key($status);
                    $this->userResult[$data[0]][$linkaccountId][$keyFunction] = $status[$keyFunction];
                    if (!empty($dataWorker['errors'][$linkaccountId][$keyFunction])) {
                        $this->gearmanErrors[$data[0]][$linkaccountId][$keyFunction] = $dataWorker['errors'][$linkaccountId][$keyFunction];
                    }
                }
            }
        }
        if (!empty($dataWorker['tempArray'])) {
            foreach ($dataWorker['tempArray'] as $linkaccountId => $dataArray) {
                if (empty($this->tempArray[$data[0]][$linkaccountId])) {
                    $this->tempArray[$data[0]][$linkaccountId] = $dataArray;
                } 
                else if ($linkaccountId == 'investor') {
                    $keyDataArray = key($dataArray[$data[2]]);
                    $this->tempArray[$data[0]][$linkaccountId][$data[2]][$keyDataArray] = $dataArray[$data[2]][$keyDataArray];
                }
                else {
                    $keyDataArray = key($dataArray);
                    $this->tempArray[$data[0]][$linkaccountId][$keyDataArray] = $dataArray[$keyDataArray];
                }
            }
            
        }
//        print_r($this->userResult);
//        print_r($this->userReference);
        echo "ID Unique: " . $task->unique() . "\n";
//        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
    
    /**
     * Function to verify that the job was successful per user and per company
     * @param int $status It is the Id of the next queue status
     * @param string $message It is the message to show on console
     * @param int $restartStatus It is the Id of the queue if something fail
     * @param int $errorStatus It is the Id of the queue if the error repeats and it is irrecoverable
     * @param array $this->userResult This array is a variable class where we save the completion status of a pfp
     *                                The variable is created as: this->userResult[$queueId][$linkedaccountId]
     */
    public function verifyStatus($status, $message, $restartStatus, $errorStatus) {       
        foreach ($this->userResult as $queueId => $userResult) {
            $globalDestruction = false;
            unset($this->queueInfo[$queueId]['companiesInFlow']);
            /*
             * foreach to verify if there was an error in the worker collecting the data
             * $linkaccountId if it's defined as global, there was a crash error in the worker
             * $result it is true if everything was correct and false if there was an error
             */
            foreach ($userResult as $linkaccountId => $result) {
                if ($linkaccountId == WIN_STATUS_COLLECT_GLOBAL_ERROR) {
                    $globalDestruction = true;
                    break;
                }
                if ($linkaccountId == 'investor') {
                    $keyUserReference = key($result);
                    foreach ($result[$keyUserReference] as $keyFunction => $resultFunctionStatus) {
                        if (is_array($resultFunctionStatus)) {
                            foreach ($resultFunctionStatus as $keyDate => $dateResult) {
                                if (!$dateResult) {
                                    $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction][$keyDate]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
                                    $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['typeOfError'] = "ERROR on flow " . 
                                            $this->flowName . " and linkAccountId " . $linkaccountId ;
                                    $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                                            . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction][$keyDate]['typeErrorId'] . 
                                            " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction][$keyDate]['subtypeErrorId'];
                                    $this->saveGearmanError($this->gearmanErrors[$queueId]['investor'][$keyUserReference][$keyFunction][$keyDate]);
                                }
                            }
                        }
                        else if (!$resultFunctionStatus) {
                            //$this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]);
                            $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
                            $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['typeOfError'] = "ERROR on flow " . $this->flowName . 
                                    " and linkAccountId " . $linkaccountId .
                                    " " .  $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]["typeOfError"];
                            $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                                    . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['typeErrorId'] . 
                                    " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]['subtypeErrorId'] . 
                                    " with " . $this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]["detailedErrorInformation"];
                            print_r($this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]);
                            $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId][$keyUserReference][$keyFunction]);
                        }
                    }
                    continue;
                }
                else {
                    $keyFunction = key($result);
                    if (is_array($result[$keyFunction])) {
                        foreach ($result[$keyFunction] as $keyDate => $dateResult) {
                            if (!$dateResult) {
                                $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction][$keyDate]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
                                $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction][$keyDate]['typeOfError'] = "ERROR on flow " . $this->flowName . 
                                        " and linkAccountId " . $linkaccountId ;
                                $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction][$keyDate]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                                        . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction][$keyDate]['typeErrorId'] . 
                                        " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction][$keyDate]['subtypeErrorId'] ;
                                $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId][$keyFunction][$keyDate]);
                            }
                        }
                    }
                    if (!$result[$keyFunction]) {
                        $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]['typeErrorId'] = constant("WIN_ERROR_" . $this->flowName);
                        $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]['typeOfError'] = "ERROR on flow " . $this->flowName . 
                                " and linkAccountId " . $linkaccountId . 
                                " " .  $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]["typeOfError"];
                        $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]['detailedErrorInformation'] = "ERROR on " . $this->flowName
                                . " with type of error: " . $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]['typeErrorId'] . 
                                " AND subtype " . $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]['subtypeErrorId'] . 
                                " with " . $this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]["detailedErrorInformation"];
                        $this->saveGearmanError($this->gearmanErrors[$queueId][$linkaccountId][$keyFunction]);
                    }
                }
                $this->queueInfo[$queueId]['companiesInFlow'][] = $linkaccountId; 
            }
            /*
             * When there is a globalDestruction, all content of the files are deleted
             */
            if ($globalDestruction) {
                foreach ($this->userLinkaccountIds[$queueId] as $key => $userLinkaccountId) {
                    $this->queueInfo[$queueId]['companiesInProcess'][] = $userLinkaccountId;
                    if (!empty($this->tempArray)) {
                        unset($this->tempArray[$queueId][$linkaccountId]);
                    }
                }
            }
            if (!empty($this->queueInfo[$queueId]['companiesInFlow'])) {
                    $this->Queue2->id = $queueId;
                if (!$globalDestruction) {
                    $newState = $status;
                    $this->queueInfo[$queueId]['numberTries'] = 0;
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . $message;
                    }
                } 
                else {
                    $data = $this->getFailStatus($queueId, $restartStatus, $errorStatus);
                    $newState = $data["newStatus"];
                    $this->queueInfo[$queueId]["numberTries"] = $data["numberTries"];
                }
                $this->Queue2->save(array(
                            'queue2_status' => $newState,
                            'queue2_info' => json_encode($this->queueInfo[$queueId])
                        ),
                        $validate = true
                );
            }
            //$statusProcess = $this->consolidationResult($userResult, $queueId);
        }
    }
    
}
