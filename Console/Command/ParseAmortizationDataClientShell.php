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
    
    public $uses = array('Queue', 'Payment', 'Investment');  
    
    /**
     * Function to init the process to parse all the user's amortization tables
     *  @param integer $this->args[0]|$queueStatus It is the status we need to use on the search on DB
     *  @param integer $this->args[1]|$queueTypeAccess It is the access type the user used to get the data
     */
    public function initClient() {
        $inActivityCounter = 0;
        $this->flowName = "GEARMAN_FLOW1";
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $fileName = "amortizationtable";
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
        $this->date = date("Ymd");
        $numberOfIteration = 0;
        while ($numberOfIteration == 0) {
            if (Configure::read('debug')) {
                $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n");
            }
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED, $jobsInParallel);
            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done");
                    print_r($pendingJobs);
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $this->queueInfo[$job['Queue']['id']] = json_decode($job['Queue']['queue_info'], true);
                    $userReference = $job['Queue']['queue_userReference'];
                    $directory = Configure::read('dashboard2Files') . $userReference . DS . $this->date . DS;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
                    echo "Subdiiiiiiir";
                    print_r($subDir);
                    $i = 0;
                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        if (Configure::read('debug')) {
                            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "TempName array");
                            print_r($tempName);
                        }
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        $dirs = new Folder($subDirectory);
                        $nameCompany = $dirs->findRecursive();
                        $allFiles = $dirs->findRecursive($fileName . ".*");
                        $tempPfpName = explode("/", $nameCompany[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        echo "pfp = " . $pfp . "\n";
                        print_r($allFiles);
                        $this->userLinkaccountIds[$job['Queue']['id']][$i] = $linkedAccountId;
                        $i++;
                        //$files = $this->readFilteredFiles($allFiles, TRANSACTION_FILE + INVESTMENT_FILE);
                        //$listOfActiveLoans = $this->getListActiveLoans($linkedAccountId);
                        $params[$linkedAccountId] = array('queue_id' => $job['Queue']['id'],
                            'pfp' => $pfp,
                            'userReference' => $job['Queue']['queue_userReference'],
                            'files' => $allFiles);
                    }
                    $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue']['id'] . ".-;" . $workerFunction . ".-;" . $userReference);
                }
                $this->GearmanClient->runTasks();
                    
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n");
                }
                $this->verifiedStatus(WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, "Data succcessfully downloaded", WIN_QUEUE_STATUS_START_COLLECTING_DATA, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
                unset($pendingJobs);
                $numberOfIteration++;
            }
            else {
                $inActivityCounter++;
                if (Configure::read('debug')) {       
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n");
                }     
                sleep (4); 
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                if (Configure::read('debug')) {       
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT \n");
                }                     
                exit;
            }
        }
    }
    
}
