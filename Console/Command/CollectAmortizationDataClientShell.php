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
        $this->date = date("Ymd");
        $inActivityCounter++;  
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_DATA_EXTRACTED, $jobsInParallel);
            $linkedaccountsResults = [];
            $queueInfos = [];
            print_r($pendingJobs);
            if (!empty($pendingJobs)) {
                foreach ($pendingJobs as $job) {
                    $queueInfoJson = $job['Queue']['queue_info'];
                    $queueInfo = json_decode($queueInfoJson, true);
                    $this->queueInfo[$job['Queue']['id']] = $queueInfo;
                    $linkAccountId = [];
                    foreach ($queueInfo['loanIds'] as $key => $loanId) {
                        if (!in_array($key, $linkAccountId)) {
                            $linkAccountId[] = $key; 
                        }
                    }
                    $filterConditions = array('id' => $linkAccountId);
                    $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                    $queueInfos[] = $queueInfo['loanIds'];
                }
                $userLinkedaccounts = [];
                $loandIdLinkedaccounts = [];

                foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
                    //In this case $key is the number of the linkaccount inside the array 0,1,2,3
                    $i = 0;
                    foreach ($linkedaccountResult as $linkedaccount) {
                        $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                        $folderExist = $this->verifyCompanyFolderExist($pendingJobs[$key]['Queue']['queue_userReference'], $linkedaccount['Linkedaccount']['id'], "amortizationTable");
                        if (!$folderExist) {
                            $linkedaccountId = $linkedaccount['Linkedaccount']['id'];
                            $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                            //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                            $this->userLinkaccountIds[$pendingJobs[$key]['Queue']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
                            $loandIdLinkedaccounts[$key][$companyType][$i] = $queueInfos[$key][$linkedaccountId];
                            $i++;
                        }
                    }
                }

                //$key is the number of the internal id of the array (0,1,2)
                //$key2 is type of access to company (multicurl, casper, etc)
                foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
                    foreach ($userLinkedaccount as $key2 => $linkedaccountsByType) {
                        $data["companies"] = $linkedaccountsByType;
                        $data["loanIds"] = $loandIdLinkedaccounts[$key][$key2];
                        $data["queue_userReference"] = $pendingJobs[$key]['Queue']['queue_userReference'];
                        $data["queue_id"] = $pendingJobs[$key]['Queue']['id'];
                        $data["date"] = $this->queueInfo[$data["queue_id"]]["date"];
                        print_r($data["companies"]);
                        print_r($data["loanIds"]);
                        echo "\n";
                        echo "userReference ". $data["queue_userReference"];
                        echo "\n";
                        echo "queueId " . $data["queue_id"];
                        echo "\n";
                        echo json_encode($data);
                        echo "\n";
                        echo $key2;
                        echo "\n aquiiiiiiiiiiiiiii";
                        $this->GearmanClient->addTask($key2, json_encode($data), null, $data["queue_id"] . ".-;" . $key2 . ".-;" . $pendingJobs[$key]['Queue']['queue_userReference']);
                    }
                }

                $this->GearmanClient->runTasks();
                
                $this->verifyStatus(WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED, "Data successfuly downloaded", WIN_QUEUE_STATUS_DATA_EXTRACTED, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_AMORTIZATION_TABLE);
                $numberOfIteration++;
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
        }
        
        
    }
    
    
}
