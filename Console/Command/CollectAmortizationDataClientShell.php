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
    
    protected $newComp = [];
    public $uses = array('Marketplace', 'Company', 'Urlsequence', 'Marketplacebackup');
    
    /**
     * Process to initiate the process to collect all the amortization tables
     */
    public function initClient() {
        //$queueStatus = $this->args[0];
        //$queueAcessType = $this->args[1];
        $inActivityCounter = 0;
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
            $resultQueue = $this->checkJobs(DATA_EXTRACTED, $jobsInParallel);
            $linkedaccountsResults = [];
            $queueInfos = [];
            print_r($resultQueue);
            if (!empty($resultQueue)) {
                foreach ($resultQueue as $result) {
                    $queueInfoJson = $result['Queue']['queue_info'];
                    $queueInfo = json_decode($queueInfoJson, true);
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
                        $folderExist = $this->verifyCompanyFolderExist($resultQueue[$key]['Queue']['queue_userReference'], $linkedaccount['Linkedaccount']['id'], "amortizationTable");
                        if (!$folderExist) {
                            $linkedaccountId = $linkedaccount['Linkedaccount']['id'];
                            $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                            //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                            $this->userLinkaccountIds[$resultQueue[$key]['Queue']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
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
                        $data["queue_userReference"] = $resultQueue[$key]['Queue']['queue_userReference'];
                        $data["queue_id"] = $resultQueue[$key]['Queue']['id'];
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
                        $this->GearmanClient->addTask($key2, json_encode($data), null, $data["queue_id"] . ".-;" . $key2 . ".-;" . $resultQueue[$key]['Queue']['queue_userReference']);
                    }
                }

                $this->GearmanClient->runTasks();

                foreach ($this->userResult as $queueId => $userResult) {
                    $statusProcess = $this->consolidationResult($userResult, $queueId);
                    $this->Queue->id = $queueId;
                    if ($statusProcess) {
                        $newState = AMORTIZATION_TABLES_DOWNLOADED;
                        echo "Data succcessfully download";
                    }
                    else {
                        $newState = START_COLLECTING_DATA;
                        echo "There was an error downloading data";
                    }
                    $this->Queue->save(array('queue_status' => $newState), $validate = true);
                }
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
