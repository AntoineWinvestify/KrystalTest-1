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
        //$pendingJobs = $this->Queue->getUsersByStatus(FIFO, $queueStatus, $queueAccessType);
        //$pendingJobs[] = $this->Queue->getNextFromQueue(FIFO);
        if (Configure::read('debug')) {
            $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 1 Client\n");
        }

        $inActivityCounter++;                                           // Gearman client 
 
        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');     
        
        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeAccessTransaction')
        ));
        $this->date = date("Ymd", strtotime("-1 day"));
        $numberOfIteration = 0;
        while ($numberOfIteration == 0){
            if (Configure::read('debug')) {
                $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n");
            } 
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_START_COLLECTING_DATA, WIN_QUEUE_STATUS_DOWNLOADING_GLOBAL_DATA),
                                                  WIN_QUEUE_STATUS_DOWNLOADING_GLOBAL_DATA,
                                                $jobsInParallel);
            echo "printing pendingJobs\n";
            print_r($pendingJobs);            
            
            
            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done");
                    print_r($pendingJobs);
                }
                $linkedaccountsResults = [];
                $companiesInFlowExist = [];
                foreach ($pendingJobs as $job) {
                    $queueInfo = json_decode($job['Queue']['queue_info'], true);
                    $this->queueInfo[$job['Queue']['id']] = $queueInfo;
                    $this->getFinishDate($job);
                    $jobInvestor = $this->Investor->find("first", array('conditions' =>
                        array('Investor.investor_identity' => $job['Queue']['queue_userReference']),
                        'fields' => 'id',
                        'recursive' => -1,
                    ));
                    print_r($jobInvestor);
                    $investorId = $jobInvestor['Investor']['id'];
                    $companiesInFlowExist[$job['Queue']['id']] = false;
                    $filterConditions = array('investor_id' => $investorId);
                    //We verify that companiesInFlow exists and if exists, 
                    //we only get that companies information from database
                    echo "\n" . __LINE__ . __FILE__ . "\n Verifying that companies from queue exists in DB for that investor \n";
                    if (!empty($queueInfo['companiesInFlow'])) {
                        $companiesInFlowExist[$job['Queue']['id']] = true;
                        foreach ($queueInfo['companiesInFlow'] as $key => $linkaccountIdInFlow) {
                            $linkAccountId[] = $linkaccountIdInFlow;
                        }
                        $filterConditions = array(
                                'investor_id' => $investorId,
                                'id' => $linkAccountId
                            );
                    }
                    $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                    //$linkedaccountsResults[$job['Queue']['queue_userReference']] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
                }
                $userLinkedaccounts = [];
                foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
                    //In this case $key is the number of the linkaccount inside the array 0,1,2,3
                    $i = 0;
                    foreach ($linkedaccountResult as $linkedaccount) {
                        $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                        $folderExist = $this->verifyCompanyFolderExist($pendingJobs[$key]['Queue']['queue_userReference'], $linkedaccount['Linkedaccount']['id']);
                        //We verify that a company doesn't have a folder with information, 
                        //if the folder exists, it means that we get than company previously on that day
                        if (!$folderExist) {
                            //After verify that a folder doesn't exist, 
                            //we verify that companiesInFlow doesn't exist neither
                            if (!$companiesInFlowExist[$job['Queue']['id']]) {
                                //If not exists, we put all the linkaccounts of the companies 
                                //that we are going to collect inside the variables companiesInFlow
                                $this->queueInfo[$job['Queue']['id']]['companiesInFlow'][] = $linkedaccount['Linkedaccount']['id'];
                            }
                            $this->getStartDate($linkedaccount, $job['Queue']['id']);
                            
                            $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                            //We need to save all the accounts id in case that a Gearman Worker fails,in order to delete all the folders
                            $this->userLinkaccountIds[$pendingJobs[$key]['Queue']['id']][$i] = $linkedaccount['Linkedaccount']['id'];
                            $i++;
                        }
                        else {
                            echo "\n" . __LINE__ . "   " . __FILE__;
                            echo "\n company in flow with linkedAccountId $linkedaccount has a created folder \n Not including company $linkedaccount in flow \n";
                        }
                    }
                    if (Configure::read('debug')) {
                        $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "The companies in flow are");
                        print_r($this->queueInfo[$job['Queue']['id']]['companiesInFlow']);
                    }
                    
                }
                
                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Sending the previous information to Worker\n");
                }


                //$key is the number of the internal id of the array (0,1,2)
                //$typeAccessKey is type of access to company (multicurl, casper, etc)
                foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
                    foreach ($userLinkedaccount as $typeAccessKey => $linkedaccountsByType) {
                        $data["companies"] = $linkedaccountsByType;
                        $data["queue_userReference"] = $pendingJobs[$key]['Queue']['queue_userReference'];
                        $data["queue_id"] = $pendingJobs[$key]['Queue']['id'];
                        if (!empty($this->queueInfo[$job['Queue']['id']]['originExecution'])) {
                            $data["originExecution"] = $this->queueInfo[$job['Queue']['id']]['originExecution'];
                        }
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
                        $this->GearmanClient->addTask($typeAccessKey, json_encode($data), null, $data["queue_id"] . ".-;" . $typeAccessKey . ".-;" . $pendingJobs[$key]['Queue']['queue_userReference']);
                    }
                }

                $this->GearmanClient->runTasks();

                if (Configure::read('debug')) {
                    $this->out(__FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n");
                }
                $this->verifyStatus(WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, "Data succcessfully downloaded", WIN_QUEUE_STATUS_START_COLLECTING_DATA, WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
                unset($pendingJobs);
                unset($jobInvestor);
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
    
    /**
     * Function to initiate startDate in queueInfo variable
     * @param array $linkedaccount Array that contains everything about the linkedaccount
     */
    public function getStartDate($linkedaccount, $queueId) {
        //We set null startDate
        $this->queueInfo[$queueId]['startDate'][$linkedaccount['Linkedaccount']['id']] = null;
        $startDate = date("Ymd", strtotime($linkedaccount['Linkedaccount']['linkedaccount_lastAccessed']));
        //If lastAccessed is null, strtotime will put 19700101 so we don't want that date
        if ($startDate != "19700101") {
            $this->queueInfo[$queueId]['startDate'][$linkedaccount['Linkedaccount']['id']] = $startDate;
        }
    }
    
    /**
     * Function to initiate finishDate in queueInfo variable
     * @param array $job Array that contains everything about the request
     */
    public function getFinishDate($job) {
        if (empty($this->queueInfo[$job['Queue']['id']]['date'] )) {
            $this->queueInfo[$job['Queue']['id']]['date'] = $this->date;
        }
        else {
            $this->date = $this->queueInfo[$job['Queue']['id']]['date'];
        }
    }
    
}
