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
 * @version
 * @date
 * @package
 */

App::import('Shell','GearmanClient');

/**
 * Description of ParseAmortizationDataClientShell
 *
 * @author antoiba
 */
class ParseAmortizationDataClientShell extends GearmanClientShell {
    
    public $uses = array('Queue2', 'Amortizationtable');  
    protected $fileName = "amortizationtable";
    
    /**
     * Function to init the process to parse all the user's amortization tables
     *  @param integer $this->args[0]|$queueStatus It is the status we need to use on the search on DB
     *  @param integer $this->args[1]|$queueTypeAccess It is the access type the user used to get the data
     */
    public function initClient() {
        $inActivityCounter = 0;
        $this->flowName = "GEARMAN_FLOW3B";
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $workerFunction = "collectamortizationtablesFileFlow";
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        //$resultQueue = $this->Queue->getUsersByStatus(FIFO, $queueStatus, $queueAccessType);
        //$resultQueue[] = $this->Queue->getNextFromQueue(FIFO);
        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 3B Client\n");
        }
        
        $inActivityCounter++;                                           // Gearman client 
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        $numberOfIteration = 0;
        while ($numberOfIteration == 0) {
            if (Configure::read('debug')) {
                $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n");
            }
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED, WIN_QUEUE_STATUS_EXTRACTING_AMORTIZATION_TABLE_FROM_FILE),
                                                  WIN_QUEUE_STATUS_EXTRACTING_AMORTIZATION_TABLE_FROM_FILE,
                                                $jobsInParallel);            
                        
            
            
            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done");
                    print_r($pendingJobs);
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $params = [];
                    $this->queueInfo[$job['Queue2']['id']] = json_decode($job['Queue2']['queue2_info'], true);
                    print_r($this->queueInfo);
                    $userReference = $job['Queue2']['queue2_userReference'];
                    $directory = Configure::read('dashboard2Files') . $userReference . DS . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories

                    $i = 0;
                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        if (Configure::read('debug')) {
                            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "TempName array");
                            print_r($tempName);
                        }
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        if (!in_array($linkedAccountId, $this->queueInfo[$job['Queue2']['id']]['companiesInFlow'])) {
                            continue;
                        }
                        if (Configure::read('debug')) {
                            $this->out(__FUNCTION__ . " " . __LINE__ . ": queueInfo " . $this->queueInfo[$job['Queue2']['id']]['companiesInFlow'][0]);
                        }
                        $dirs = new Folder($subDirectory);
//                        $nameCompany = $dirs->findRecursive();

                        $allFiles = $dirs->findRecursive(WIN_FLOW_AMORTIZATION_TABLE_FILE . ".*");
                        $tempPfpName = explode("/", $allFiles[0]);

                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        echo "pfp = " . $pfp . "\n";

                        $this->userLinkaccountIds[$job['Queue2']['id']][$i] = $linkedAccountId;
                        $i++;
                        //$files = $this->readFilteredFiles($allFiles, TRANSACTION_FILE + INVESTMENT_FILE);
                        //$listOfActiveLoans = $this->getListActiveLoans($linkedAccountId);
                        $params[$linkedAccountId] = array('queue_id' => $job['Queue2']['id'],
                            'pfp' => $pfp,
                            'userReference' => $job['Queue2']['queue2_userReference'],
                            'files' => $allFiles
                                );
                        
                        echo "PARAM TOTAL";
                    }
                    $data = json_encode($params);
                    $fileName = APP . 'Config/tempDataFile';
                    $file = fopen($fileName, 'w+');
                    fwrite($file, $data);
                    fclose($file);
                    $this->GearmanClient->addTask($workerFunction, $fileName, null, $job['Queue2']['id'] . ".-;" . $workerFunction . ".-;" . $userReference);
                }
                $this->GearmanClient->runTasks();
                
                
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n");
                }
                
                $this->verifyStatus(WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED, "Data succcessfully downloaded", WIN_QUEUE_STATUS_DATA_EXTRACTED, WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED);
                $this->saveAmortizationtablesToDB();
                unset($pendingJobs);
            }
            else {
                if (Configure::read('debug')) {       
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n");
                }     
                sleep (WIN_SLEEP_DURATION); 
            }
            
            $inActivityCounter++;
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                if (Configure::read('debug')) {       
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT \n");
                }                     
                exit;
            }
        }
    }
    
    /**
     * Function to save all the new amortization tables in DB per user and per linked account. It is assumed that these 
     * tables are completely updated until today. Although it seems that Zank takes its time to repay an amortization 
     */
    public function saveAmortizationtablesToDB() {
$timeStart = time();
        foreach ($this->tempArray as $tempArray) {
            foreach ($tempArray as $amortizationData) {
                $this->Amortizationtable->saveAmortizationtable($amortizationData);
            }
        }
$timeStop = time();
echo "\nNUMBER OF SECONDS EXECUTED IN " . __FUNCTION__ . " = " . ($timeStop - $timeStart) ."\n";
    }

    
}
