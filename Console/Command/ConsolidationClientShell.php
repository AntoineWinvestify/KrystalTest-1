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
        
        $inActivityCounter = 0;
        $this->flowName = "GEARMAN_FLOW4";
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));

        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        //$pendingJobs = $this->Queue->getUsersByStatus(FIFO, $queueStatus, $queueAccessType);
        //$pendingJobs[] = $this->Queue->getNextFromQueue(FIFO);
        
        $inActivityCounter++;                                           // Gearman client 
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        //$this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        //$this->date = date("Ymd");
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_AMORTIZATION_TABLE_EXTRACTED, $jobsInParallel);
            print_r($pendingJobs);
            if (!empty($pendingJobs)) {
                $linkedaccountsResults = [];
                foreach ($pendingJobs as $job) {
                    $queueInfo = json_decode($job['Queue']['queue_info'], true);
                    $this->queueInfo[$job['Queue']['id']] = $queueInfo;
                    $date = $queueInfo['date'];
                    $lastAccess = date("Y-m-d", strtotime($date-1));
                    foreach ($queueInfo['companiesInFlow'] as $linkaccountId) {
                        $this->Linkedaccount->id = $linkaccountId;
                        $this->Linkedaccount->saveField('linkedaccount_lastAccessed', $lastAccess);
                    }
                    /*$jobInvestor = $this->Investor->find("first", array('conditions' =>
                        array('Investor.investor_identity' => $job['Queue']['queue_userReference']),
                        'fields' => 'id',
                        'recursive' => -1,
                    ));
                    print_r($jobInvestor);
                    $investorId = $jobInvestor['Investor']['id'];
                    $filterConditions = array(  'investor_id' => $investorId,
                                            );
                    $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                    echo "linkAccount \n";
                    print_r($linkedaccountsResults);*/
                    //$linkedaccountsResults[$job['Queue']['queue_userReference']] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                }
                //$userLinkedaccounts = [];
                /*foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
                    //In this case $key is the number of the linkaccount inside the array 0,1,2,3
                    $i = 0;
                    foreach ($linkedaccountResult as $linkedaccount) {
                        $userLinkedaccounts[$key][$i] = $linkedaccount;
                        //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                        $this->userLinkaccountIds[$pendingJobs[$key]['Queue']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
                        $i++;
                    }
                }*/
                
                //$key is the number of the internal id of the array (0,1,2)
                //$key2 is type of access to company (multicurl, casper, etc)
                /*foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
                    $data["companies"] = $userLinkedaccount;
                    $data["queue_userReference"] = $pendingJobs[$key]['Queue']['queue_userReference'];
                    $data["queue_id"] = $pendingJobs[$key]['Queue']['id'];
                    print_r($data["companies"]);
                    echo "\n";
                    echo "userReference " . $data["queue_userReference"];
                    echo "\n";
                    echo "queueId " . $data["queue_id"];
                    echo "\n";
                    echo json_encode($data);
                    echo "\n";
                    echo $key2;
                    echo "\n aquiiiiiiiiiiiiiii";
                    $this->GearmanClient->addTask('consolidation', json_encode($data), null, $data["queue_id"] . ".-;" . "consolidation" . ".-;" . $pendingJobs[$key]['Queue']['queue_userReference']);
                }*/
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
