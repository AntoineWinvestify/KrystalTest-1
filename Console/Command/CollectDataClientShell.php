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



class CollectDataClientShell extends AppShell {
    protected $GearmanClient;
    protected $userResult = [];
    protected $newComp = [];
    public $uses = array('Marketplace', 'Company', 'Urlsequence', 'Marketplacebackup');

    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }
    
    public function main() {
        echo "Nothing to do here";
    }

    /**
     * Function to init the process to recollect all the user investment data
    *  @param integer $this->args[0]|$queueStatus It is the status we need to use on the search on DB
    *  @param integer $this->args[1]|$queueTypeAccess It is the access type the user used to get the data
     */
    public function initClient() {
        $queueStatus = $this->args[0];
        $queueAcessType = $this->args[1];
        
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(function(GearmanTask $task) {
            $m = $task->data();
            echo "ID Unique: " . $task->unique() . "\n";
            echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
            //return GEARMAN_WORK_EXCEPTION;
        });
        
        $this->GearmanClient->setFailCallback(function(GearmanTask $task) {
            $m = $task->data();
            echo "ID Unique: " . $task->unique() . "\n";
            echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
            //echo GEARMAN_WORK_FAIL;
        });
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        
        $this->Queue = ClassRegistry::init('Queue');
        //$resultQueue = $this->Queue->getUsersByStatus(FIFO, $queueStatus, $queueAccessType);
        $resultQueue[] = $this->Queue->getNextFromQueue(FIFO);
        
        if (empty($resultQueue)) {  // Nothing in the queue
            echo "empty queue<br>";
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
            exit;
        }

        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $linkedaccountsResults = [];
        foreach ($resultQueue as $result) {
            $resultInvestor = $this->Investor->find("first", array('conditions' =>
                array('Investor.investor_identity' => $result['Queue']['queue_userReference']),
                'fields' => 'id',
                'recursive' => -1,
            ));
            $investorId = $resultInvestor['Investor']['id'];
            $filterConditions = array('investor_id' => $investorId);
            $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
            //$linkedaccountsResults[$result['Queue']['queue_userReference']] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
        }
        
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeOfAccess')
        ));
        
        $userLinkedaccounts = [];
        //$i = 0;
        
        foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
            //In this case $key is the number of the linkaccount inside the array 0,1,2,3
            $i = 0;
            foreach ($linkedaccountResult as $linkedaccount) {
                $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                $i++;
            }
            
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
                $this->GearmanClient->addTask($key2, json_encode($data), null, $data["queue_userReference"] . ".-;" . $key2);
            }
        }
        
        $this->GearmanClient->runTasks();
        
        
        
    }
    
    public function verifyFailTask(GearmanTask $task) {
        $m = $task->data();
        $data = explode(".-;", $task->unique());
        $this->userResult[$data[0]][$data[1]] = $task->data();
        print_r($this->userResult);
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }
    
    public function verifyExceptionTask (GearmanTask $task) {
        $m = $task->data();
        $data = explode(".-;", $task->unique());
        $this->userResult[$data[0]][$data[1]] = $task->data();
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
}