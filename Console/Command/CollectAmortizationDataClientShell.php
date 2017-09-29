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

/**
 * Description of CollectAmortizationDataClientShell
 *
 */
class CollectAmortizationDataClientShell extends AppShell {
    protected $GearmanClient;
    protected $userResult = [];
    protected $newComp = [];
    public $uses = array('Marketplace', 'Company', 'Urlsequence', 'Marketplacebackup');
    
    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }
    
    public function initClient() {
        //$queueStatus = $this->args[0];
        //$queueAcessType = $this->args[1];
        
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        
        $this->Queue = ClassRegistry::init('Queue');
        
        $resultQueue = $this->Queue->getUsersByStatus(FIFO, DATA_EXTRACTED, null, 1);
        if (empty($resultQueue)) {  // Nothing in the queue
            echo "empty queue<br>";
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
            exit;
        }

        //$this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $linkedaccountsResults = [];
        $queueInfos = [];
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
        
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeAccessAmortization')
        ));
        
        $userLinkedaccounts = [];
        $loandIdLinkedaccounts = [];
        //$i = 0;
        
        foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
            //In this case $key is the number of the linkaccount inside the array 0,1,2,3
            $i = 0;
            foreach ($linkedaccountResult as $linkedaccount) {
                
                $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                $linkedaccountId = $linkedaccount['Linkedaccount']['id'];
                $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                $loandIdLinkedaccounts[$key][$companyType][$i] = $queueInfos[$key][$linkedaccountId];
                $i++;
            }
            //linkedaccount][id]
            
        }
        
        //$key is the number of the internal id of the array (0,1,2)
        //$key2 is type of access to company (multicurl, casper, etc)
        foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
            foreach ($userLinkedaccount as $key2 => $linkedaccountsByType) {
                $data["companies"] = $linkedaccountsByType;
                $data["loandIds"] = $loandIdLinkedaccounts[$key][$key2];
                $data["queue_userReference"] = $resultQueue[$key]['Queue']['queue_userReference'];
                $data["queue_id"] = $resultQueue[$key]['Queue']['id'];
                print_r($data["companies"]);
                print_r($data["loandIds"]);
                echo "\n";
                echo "userReference ". $data["queue_userReference"];
                echo "\n";
                echo "queueId " . $data["queue_id"];
                echo "\n";
                print_r($data["queue_info"]);
                echo json_encode($data);
                echo "\n";
                echo $key2;
                echo "\n aquiiiiiiiiiiiiiii";
                $this->GearmanClient->addTask($key2, json_encode($data), null, $data["queue_id"] . ".-;" . $key2);
            }
        }
        
        $this->GearmanClient->runTasks();
        
        foreach ($this->userResult as $key => $userResult) {
            $statusProcess = $this->consolidationResult($userResult);
            if (!$statusProcess) {
                $statusDelete = $this->safeDelete($key, 1); //1 = $todaydate
            }
            $this->Queue->id = $key;
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
        
        
    }
    
    public function verifyFailTask(GearmanTask $task) {
        $m = $task->data();
        $data = explode(".-;", $task->unique());
        $this->userResult[$data[0]][$data[1]] = "0";
        
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }
    
    public function verifyExceptionTask (GearmanTask $task) {
        $m = $task->data();
        $data = explode(".-;", $task->unique());
        $this->userResult[$data[0]][$data[1]] = "0";
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }
    
    public function verifyCompleteTask (GearmanTask $task) {
        $data = explode(".-;", $task->unique());
        $this->userResult[$data[0]][$data[1]] = $task->data();
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
    
    public function consolidationResult($userResult) {
        $statusProcess = true;
        foreach ($userResult as $key => $result) {
            if (!$result) {
                $statusProcess = false;
                break;
            }
        }
        return $statusProcess;
    }
    
    public function safeDelete($data, $date) {
        echo "Delete all";
    }
    
    
}
