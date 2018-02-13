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
 * Description of PreprocessClientShell
 *
 * @author antoiba
 */
class PreprocessClientShell extends GearmanClientShell {
    
    /**
     * Function to init the process to recollect all the user investment data
     *  @param integer $this->args[0]|$queueStatus It is the status we need to use on the search on DB
     *  @param integer $this->args[1]|$queueTypeAccess It is the access type the user used to get the data
     */
    public function initClient() {
        
        $inActivityCounter = 0;
        $this->flowName = "GEARMAN_FLOW0";
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));

        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        //$pendingJobs = $this->Queue->getUsersByStatus(FIFO, $queueStatus, $queueAccessType);
        //$pendingJobs[] = $this->Queue->getNextFromQueue(FIFO);
        
        $inActivityCounter++;                                           // Gearman client 
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeAccessPreprocess')
        ));
        $this->date = date("Ymd");
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_START_PREPROCESS, WIN_QUEUE_STATUS_STARTING_PREPROCESS),
                                                  WIN_QUEUE_STATUS_STARTING_PREPROCESS,
                                                $jobsInParallel);
            print_r($pendingJobs);         
            
            
            print_r($pendingJobs);
            if (!empty($pendingJobs)) {
                $linkedaccountsResults = [];
                foreach ($pendingJobs as $job) {
                    $queueInfo = json_decode($job['Queue2']['queue2_info'], true);
                    $this->queueInfo[$job['Queue2']['id']] = $queueInfo;
                    $jobInvestor = $this->Investor->find("first", array('conditions' =>
                        array('Investor.investor_identity' => $job['Queue2']['queue2_userReference']),
                        'fields' => 'id',
                        'recursive' => -1,
                    ));
                    print_r($jobInvestor);
                    $investorId = $jobInvestor['Investor']['id'];
                    $filterConditions = array(  'investor_id' => $investorId,
                                                'company_id' => 10                
                                            );
                    $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                    echo "linkAccount \n";
                    print_r($linkedaccountsResults);
                    //$linkedaccountsResults[$job['Queue2']['queue2_userReference']] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                }
                
                $userLinkedaccounts = [];
                foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
                    //In this case $key is the number of the linkaccount inside the array 0,1,2,3
                    $i = 0;
                    foreach ($linkedaccountResult as $linkedaccount) {
                        $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                        $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                        //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                        $this->userLinkaccountIds[$pendingJobs[$key]['Queue2']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
                        $i++;
                    }
                }
                
                //$key is the number of the internal id of the array (0,1,2)
                //$key2 is type of access to company (multicurl, casper, etc)
                foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
                    foreach ($userLinkedaccount as $typeAccessKey => $linkedaccountsByType) {
                        $data["companies"] = $linkedaccountsByType;
                        $data["queue_userReference"] = $pendingJobs[$key]['Queue2']['queue2_userReference'];
                        $data["queue_id"] = $pendingJobs[$key]['Queue2']['id'];
                        $data["date"] = $this->date;
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
                
                //$this->verifyStatus(WIN_QUEUE_STATUS_START_COLLECTING_DATA, "Data succcessfully downloaded", WIN_QUEUE_STATUS_START_PREPROCESS, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
                unset($pendingJobs);
                unset($jobInvestor);
                unset($linkedaccountsResults); 
                unset($linkedaccountsResults);        
                unset($userLinkedaccounts);
                $numberOfIteration++;
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
        }
    }
    
    
}
