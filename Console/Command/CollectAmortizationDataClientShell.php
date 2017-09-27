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
        
        $resultQueue[] = $this->Queue->getUsersByStatus(FIFO, DATA_EXTRACTED, null, 1);
        
        if (empty($resultQueue)) {  // Nothing in the queue
            echo "empty queue<br>";
            echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ . "<br>";
            exit;
        }

        //$this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
        $linkedaccountsResults = [];
        foreach ($resultQueue as $result) {
            /*$resultInvestor = $this->Investor->find("first", array('conditions' =>
                array('Investor.investor_identity' => $result['Queue']['queue_userReference']),
                'fields' => 'id',
                'recursive' => -1,
            ));
            $investorId = $resultInvestor['Investor']['id'];*/
            $queueInfo = $result['Queue']['queue_info'];
            $queueInfo = json_decode($queueInfo, true);
            foreach ($queueInfo['loanIds'] as $key => $loanId) {
                $linkAccountId[] = $key; 
            }
            
            $filterConditions = array('id' => $linkAccountId);
            $linkedaccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
            //$linkedaccountsResults[$result['Queue']['queue_userReference']] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
        }
        
        $companyTypes = $this->Company->find('list', array(
            'fields' => array('Company.company_typeAccess')
        ));
        
    }
    
    
}
