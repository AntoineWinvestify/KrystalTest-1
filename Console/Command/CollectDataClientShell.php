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
 * Class CollectDataClientShell to init the process to collect all
 * the files of various investors using Gearman
 */
class CollectDataClientShell extends GearmanClientShell {

    /**
     * Function to init the process to recollect all the user investment data
     *  @param integer $this->args[0]|$queueStatus It is the status we need to use on the search on DB
     *  @param integer $this->args[1]|$queueTypeAccess It is the access type the user used to get the data
     */
    public function initClient() {
        //$queueStatus = $this->args[0];
        //$queueAcessType = $this->args[1];
        $inActivityCounter = 0;
        $this->flowName = "GEARMAN_FLOW1";
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));

        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        //$resultQueue = $this->Queue->getUsersByStatus(FIFO, $queueStatus, $queueAccessType);
        //$resultQueue[] = $this->Queue->getNextFromQueue(FIFO);
        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 2 Client\n");
        }

        $inActivityCounter++;                                           // Gearman client 
        $jobsInParallel = 1;
        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeAccessTransaction')
        ));
        $this->date = date("Ymd");
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            if (Configure::read('debug')) {
                $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n");
            }
            $resultQueue  = $this->checkJobs(WIN_QUEUE_STATUS_START_COLLECTING_DATA, $jobsInParallel);
            if (!empty($resultQueue)) {
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done");
                    print_r($resultQueue);
                }
                $linkedaccountsResults = [];
                foreach ($resultQueue as $result) {
                    $queueInfo = json_decode($result['Queue']['queue_info'], true);
                    $this->queueInfo[$result['Queue']['id']] = $queueInfo;
                    $resultInvestor = $this->Investor->find("first", array('conditions' =>
                        array('Investor.investor_identity' => $result['Queue']['queue_userReference']),
                        'fields' => 'id',
                        'recursive' => -1,
                    ));
                    print_r($resultInvestor);
                    $investorId = $resultInvestor['Investor']['id'];
                    $filterConditions = array('investor_id' => $investorId);
                    $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                    echo "linkAccount \n";
                    print_r($linkedaccountsResults);
                    //$linkedaccountsResults[$result['Queue']['queue_userReference']] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                }
                $userLinkedaccounts = [];
                foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
                    //In this case $key is the number of the linkaccount inside the array 0,1,2,3
                    $i = 0;
                    foreach ($linkedaccountResult as $linkedaccount) {
                        $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                        $folderExist = $this->verifyCompanyFolderExist($resultQueue[$key]['Queue']['queue_userReference'], $linkedaccount['Linkedaccount']['id']);
                        if (!$folderExist) {
                            $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                            //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                            $this->userLinkaccountIds[$resultQueue[$key]['Queue']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
                            $i++;
                        }
                    }

                }
                
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Sending the previous information to Worker\n");
                }


                //$key is the number of the internal id of the array (0,1,2)
                //$key2 is type of access to company (multicurl, casper, etc)
                foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
                    foreach ($userLinkedaccount as $key2 => $linkedaccountsByType) {
                        $data["companies"] = $linkedaccountsByType;
                        $data["queue_userReference"] = $resultQueue[$key]['Queue']['queue_userReference'];
                        $data["queue_id"] = $resultQueue[$key]['Queue']['id'];
                        print_r($data["companies"]);
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

                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n");
                }
                $this->verifiedStatus(WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, "Data succcessfully downloaded");
                unset($resultQueue);
                unset($resultInvestor);
                unset($linkedaccountsResults); 
                unset($linkedaccountsResults);        
                unset($userLinkedaccounts);
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
