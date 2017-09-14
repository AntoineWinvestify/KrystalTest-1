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
    private $newComp = [];
    private $uses = array('Marketplace', 'Company', 'Urlsequence', 'Marketplacebackup');

    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }

    public function main() {
        $this->GearmanClient->addServers();
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));
        
        $this->Queue = ClassRegistry::init('Queue');
        $resultQueue = $this->Queue->getUsersByStatus(FIFO, START_COLLECTING);
        
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
            'fields' => array('Company.company_type')
        ));
        
        $userLinkedaccounts = [];
        $i = 0;
        
        foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
            //In this case $key is the number of the linkaccount inside the array 0,1,2,3
            foreach ($linkedaccountResult as $linkedaccount) {
                $companyType = $companyTypes[$linkedaccount['Linkedaccount']['company_id']];
                $userLinkedaccounts[$key][$companyType][$i] = $linkedaccount;
                /*
                 * This code it is not good
                foreach (COMPANY_TYPES as $key2 => $companyType) {
                    if (in_array($linkedaccount['Linkedaccount']['company_id'], $companyType)) {
                        $userLinkedaccounts[$key][$key2][$i] = $linkedaccount;
                        break;
                    }
                }*/
                //$i is the number of each company per user and per type of company
                $i++;
            }
        }
        
        //$key is the number of each linkedaccounts
        //$key is type of access to company (multicurl, casper, etc)
        foreach ($userLinkedaccounts as $key => $userLinkedaccount) {
            foreach ($userLinkedaccount as $key2 => $linkedaccountsByType) {
                $data["companies"] = $linkedaccountsByType;
                $data["queue_userReference"] = $resultQueue[$key]['Queue']['queue_userReference'];
                $data["queue_id"] = $resultQueue[$key]['Queue']['id'];
                $this->GearmanClient->addTask($key2, json_encode($data), null, $data["queue_userReference"] . ".-;" . $key2);
            }
        }
        
        $this->GearmanClient->runTasks();
        
        
        
        
        
    }
    
    private function verifyFailTask(GearmanTask $task) {
        $m = $task->data();
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }
    
    private function verifyExceptionTask (GearmanTask $task) {
        $m = $task->data();
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }
    
    private function verifyCompleteTask (GearmanTask $task) {
        echo "COMPLETE: " . $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;
    }
}