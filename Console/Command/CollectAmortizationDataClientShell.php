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
 * @version 0.2
 * @date
 * @package
 */

App::import('Shell','GearmanClient');

/**
 * Class CollectAmortizationDataClientShell to init the process to collect all
 * the amortization tables of various investors
 */
class CollectAmortizationDataClientShell extends GearmanClientShell {
    
    protected $fileName = WIN_FLOW_AMORTIZATION_TABLE_FILE;
    
    /**
     * Process to initiate the process to collect all the amortization tables
     */
    public function initClient() {
        //$queueStatus = $this->args[0];
        //$queueAcessType = $this->args[1];
        $inActivityCounter = 0;
        $this->flowName = "GEARMAN_FLOW3A";
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeAccessAmortization')
        ));
        $inActivityCounter++;  
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_DATA_EXTRACTED, WIN_QUEUE_STATUS_DOWNLOADING_AMORTIZATION_TABLES),
                                                  WIN_QUEUE_STATUS_DOWNLOADING_AMORTIZATION_TABLES,
                                                $jobsInParallel);            

            $linkedaccountsResults = [];
            $queueInfos = [];
            print_r($pendingJobs);
            if (!empty($pendingJobs)) {
                foreach ($pendingJobs as $job) {
                    $queueInfoJson = $job['Queue2']['queue2_info'];
                    $queueInfo = json_decode($queueInfoJson, true);
                    $this->queueInfo[$job['Queue2']['id']] = $queueInfo;
                    $linkAccountIds = [];
                    
                    $baseDirectory = Configure::read('dashboard2Files') . $job['Queue2']['queue2_userReference'] . DS . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
                    $dir = new Folder($baseDirectory);
                    $pathToJsonFile = $dir->findRecursive(WIN_FLOW_NEW_LOAN_FILE . ".*");
                    foreach ($pathToJsonFile as $key => $path) {
                        $tempName = explode("/", $path);
                        print_r($tempName);
                        if (Configure::read('debug')) {
                            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "TempName array");                            
                        }
                        
                        $linkedAccountId = $tempName[count($tempName) - 3];
                        if (!in_array($linkedAccountId, $queueInfo['companiesInFlow'])) {
                            continue;
                        }
                        $file = new File($path);                  
                        $jsonLoanIds = $file->read(true, 'r');
                        $loanIds = json_decode($jsonLoanIds, true);
                        $finalLoanIds = $loanIds;

                        $linkAccountIds[] = $linkedAccountId;
                        $loanIdsPerCompany[$linkedAccountId] = $finalLoanIds;
                    }
                    $filterConditions = array('id' => $queueInfo['companiesInFlow']); //, 'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);
                    $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                    //foreach ($queueInfo['loanIds'] as $key => $loanId) {
                    /*foreach ($loanIds as $key => $loanId) {
                        if (!in_array($key, $linkAccountId)) {
                             
                        }
                    }
                    */
                }
                $userLinkedaccounts = [];
                $loandIdLinkedaccounts = [];

                foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
                    //In this case $key is the number of the linkaccount inside the array 0,1,2,3
                    $i = 0;
                    foreach ($linkedaccountResult as $linkedaccount) {
                        $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                        $folderExist = $this->verifyCompanyFolderExist($pendingJobs[$key]['Queue2']['queue2_userReference'], $linkedaccount['Linkedaccount']['id'], "amortizationTable");
                        if (!$folderExist) {
                            $linkedaccountId = $linkedaccount['Linkedaccount']['id'];
                            $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                            //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                            $this->userLinkaccountIds[$pendingJobs[$key]['Queue2']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
                            $loandIdLinkedaccounts[$key][$companyType][$i] = $loanIdsPerCompany[$linkedaccountId];
                            $i++;
                        }
                    }
                }

                //$key is the number of the internal id of the array (0,1,2)
                //$typeAccessKey is type of access to company (multicurl, casper, etc)
                foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
                    foreach ($userLinkedaccount as $typeAccessKey => $linkedaccountsByType) {
                        $data["companies"] = $linkedaccountsByType;
                        $data["loanIds"] = $loandIdLinkedaccounts[$key][$typeAccessKey];
                        $data["queue_userReference"] = $pendingJobs[$key]['Queue2']['queue2_userReference'];
                        $data["queue_id"] = $pendingJobs[$key]['Queue2']['id'];
                        $data["date"] = $this->queueInfo[$data["queue_id"]]["date"];
                        print_r($data["companies"]);
                        print_r($data["loanIds"]);
                        if (Configure::read('debug')) {
                            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Showing data sent to worker \n");
                            print_r($data["companies"]);
                            echo "userReference ". $data["queue_userReference"] . "\n";
                            echo "queueId " . $data["queue_id"] . "\n";
                            echo "Type of access for company" . $typeAccessKey . "\n";
                            echo "All information \n";
                            print_r($data);
                        }                       
                        $this->GearmanClient->addTask($typeAccessKey, json_encode($data), null, $data["queue_id"] . ".-;" . $typeAccessKey . ".-;" . $pendingJobs[$key]['Queue2']['queue2_userReference']);
                        }
                }

                $this->GearmanClient->runTasks();

                // ######################################################################################################           
                foreach ($pendingJobs as $jobToDeleteSlice) {
                    $queue2JsonDecoded = json_decode($jobToDeleteSlice['Queue2']["queue2_info"], true);
                    foreach($queue2JsonDecoded['companiesInFlow'] as $linkAccountId){
                        $path = Configure::read('dashboard2Files') . $jobToDeleteSlice['Queue2']['queue2_userReference'] . DS . $queue2JsonDecoded['date'] . DS . $linkAccountId . DS;
                        $folder = new Folder($path);
                        $pathToJsonFile = $folder->findRecursive("finishedToday" . ".*");
                        if(isset($pathToJsonFile[0])){
                            echo "Finished investment finded, deleting fron slice db \n";                      
                            $jsonFile = fopen($pathToJsonFile[0], "r");
                            $jsonInfo = fread($jsonFile, filesize($pathToJsonFile[0]));
                            $jsonInfo = json_decode($jsonInfo, true);
                            echo "Slice to delete: ";
                            print_r($jsonInfo);
                            
                            $this->Investmentslice = ClassRegistry::init('Investmentslice');
                            foreach($jsonInfo as $sliceId => $loanID){
                                echo "Deleting $sliceId \n";
                                $this->Investmentslice->delete($sliceId);
                            }
                            
                        }
                    }
                }
                //Delete the slice of investment that finisehd today.
               /* if(isset($baseDirectory)){
                    
                }*/
                
                
                $this->verifyStatus(WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED, "Data successfuly downloaded", WIN_QUEUE_STATUS_DATA_EXTRACTED, WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED);
                $numberOfIteration++;
            }
            else {
                $inActivityCounter++;
                echo __METHOD__ . " " . __LINE__ . " Nothing in queue, so sleeping \n";                
                sleep (WIN_SLEEP_DURATION); 
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                echo __METHOD__ . " " . __LINE__ . "Maximum Waiting time expired, so EXIT \n";
                $this->killShellCommand("collectAmortizationDataWorker");
                exit;
            }
        }
        $this->killShellCommand("collectAmortizationDataWorker");       
    }
    
    
}
