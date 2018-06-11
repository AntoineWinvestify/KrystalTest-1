<?php
/**
// +-----------------------------------------------------------------------+
// | Copyright (C) 2018, http://www.winvestify.com                         |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: Antoine de Poorter                                            |
// +-----------------------------------------------------------------------+
//
* @author Antoine de Poorter
* @version 0.3
* @date 2018-08-08
* @package
*/
/*
2016-08-25      version 2016_0.1
function deleteLinkedaccount()					[Not yet OK, test new functionality]
getLinkedaccountDataList()					[OK, tested]
function linkNewAccount()					[not Ok, not tested]


2018-05-08	version 2018_0.2                                [OK, tested]
 support for linkedaccount_status, linkedaccount_statusExtended and linkedaccount_statusExtendedOld fields
 
2018-06-08      version 0.3
 Parent object is now Accountowner and NOT Investor. Reshuffle between this object and 
 Accountowner of some methods 

 
Pending:



*/


class Linkedaccount extends AppModel {

    var $name = 'Linkedaccount';

    public $belongsTo = array(
        'Accountowner' => array(
                'className' => 'Accountowner',
                'foreignKey' => 'accountowner_id'
            ));     
    
    
    
    /**
     * 	Delete a an account that fulfills the filteringConditions
     * 	
     * 	@param 		array 	$filterConditions	Must indicate at least "investor_id"
     *  @param          int     $originator     WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     * 	@return 	true	record(s) deleted
     * 				false	no record(s) fulfilled $filteringConditions or incorrect filteringConditions
     */
    public function deleteLinkedaccount($filterConditions, $originator = WIN_USER_INITIATED) {

        $indexList = $this->find('all', $params = array('recursive' => -1,
                                                        'conditions' => $filterConditions,
                                                        'fields' => array('id', 'accountowner_id'))
                                );

        if (empty($indexList)) {
            return false;
        }

        if ($originator == WIN_USER_INITIATED ) {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_AND_DELETED_BY_USER;
        }
        else {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_DELETED_BY_SYSTEM;
        }
     
        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
        $this->updateAll($newData, $filterConditions);       

        $this->Accountowner = ClassRegistry::init('Accountowner');  
        
        foreach ($indexList as $index) {
            $this->Accountowner->accountDeleted ($index['accountowner_id']);
        }

        return true;
    }

    /**
     * 	Returns an array of the linkedaccount object and the associated data that fulfill the filterConditions
     *
     * 	@param 		array 	$filterConditions
     * 	@return 	array 	Data of each linkedaccount item as an element of an array
     * 			USERID/PASS SHOULD ALSO BE RETURNED
     */
    public function getLinkedaccountDataListxx($filterConditions) {

        $linkedaccountResults = $this->find("all", $params = array('recursive' => -1,
                                                                'conditions' => $filterConditions)
                                            );
        return $linkedaccountResults;
    }

    /**
     * Returns an array of the companies ids depending on the filter Conditions
     * 
     * @param array $filterConditions
     * @return array Each company id
     * USERID/PASS SHOULD ALSO BE RETURNED
     */
    public function getLinkedaccountIdListxx($filterConditions) {

        $linkedaccountResults = $this->find("all", $params = array('recursive' => -1,
                                                                    'fields' => array('company_id'),
                                                                    'conditions' => $filterConditions)
                                           );
        return $linkedaccountResults;
    }

    /**
     * 	Links a new investment account for an investor
     *
     * 	@param 		int 	$companyId		Identifier of company where linked account resides
     * 	@param 		int 	$investorId		Identifier of investor
     * 	@param 		string 	$username		username
     * 	@param 		string 	$password		password
     *
     * 	@return 	boolean	true	Account linked
     * 				false	Error happened, account not linked					
     */
    public function createNewLinkedAccount($companyId, $investorId, $username, $password) {

        $accountOwnerId = $this->createAccountOwner($companyId, $investorId, $username, $password);
        if ($accountOwnerId > 0) {    
            $linkedAccountData['Linkedaccount'] = array('linkedaccount_status'   => WIN_LINKEDACCOUNT_ACTIVE,
                                                        'linkedaccount_statusExtended' => WIN_LINKEDACCOUNT_ACTIVE_AND_CREDENTIALS_VERIFIED,
                                                        'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS,
                                                        'accountowner_id' => $accountOwnerId,
                                                        'linkedaccount_isControlledBy' => WIN_ALIAS_SYSTEM_CONTROLLED
                                                        );    
            
            if ($this->save($linkedAccountData, $validation = true)) {
                $this->Accountowner->accountAdded ($accountOwnerId);
                return true;
            } 
        }
        return false;
    }
    
    /**
     * Get linkedaccounts ids with 'nothing in process' for an investor
     * 
     * @param string $queueUserReference It is the user reference
     * @return array        List of linkedaccount ids of Investor
     */
    public function getLinkAccountsWithNothingInProcess($queueUserReference) {
        $companyNothingInProcess = [];
        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
// Find all the Accountowner objects of Investor   
	$filterConditions = array('Investor.investor_identity' => $queueUserReference);

	$this->Investor->Behaviors->load('Containable');
	$this->Investor->contain('Accountowner');  							// Own model is automatically included
	$resultInvestorsData = $this->Investor->find("all", $params = array('recursive'     => 1,
                                                                            'conditions'    => $filterConditions));
 // The result should contain information about Accountowner and LinkedAccount models      
        if (!isset($resultInvestorsData['Accountowner'])) {
            return [];
        }
        
        $accountOwnerIds = array();
 // search through the obtained data array
        foreach ($resultInvestorsData['Linkedaccount'] as $account) {
            if ($account['accountowner_status'] == WIN_ACCOUNTOWNER_ACTIVE) {
                $accountOwnerIds[] = ['id' => $account['id']];
            }
        }
        if (empty($accountOwnerIds)) {
            return [];
        }
        
        $filterConditions = array(
            "OR" => $accountOwnerIds,
            "AND" => array (array('linkedaccount_linkingProcess' => WIN_LINKING_NOTHING_IN_PROCESS,
                            'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE),
                            ));
        
        $linkedAccountsResults[] = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
        
        $companyNothingInProcess = array();
        foreach ($linkedAccountsResults as $linkedAccountResult) {
            $companyNothingInProcess[] = $linkedAccountResult['Linkedaccount']['id'];
        }
        return $companyNothingInProcess;
    }
    
    /**
     * Change the password on a PFP of a user's account
     * 
     * @param type $linkaccountId id of the linkaccount
     * @param type $newPass new password
     * @return boolean 
     */
    public function changePasswordLinkaccount($linkedAccountId, $newPass){
        $filterConditions = array('id' => $linkedAccountId);
        $accountOwnerId = $this->find("first", $params = array('recursive' => -1,
                                                               'fields'    => array('accountowner_id'),
                                                               'conditions' => $filterConditions)
                                           );       
        $this-> changeAccountPassword($accountOwnerId, $newPass);
    }

    
    /**
     * Callback function
     * Add a new request on queue for the company that was linked from a user
     * 
     * @param boolean $created
     * @param array $option
     * @return boolean
     */
    public function afterSave($created, $option = array()) {
        
        if ($created) {
            $this->Investor = ClassRegistry::init('Investor');
            $this->Queue2 = ClassRegistry::init('Queue2');
            $data = [];
            $linkaccountId = $this->id;
            $investorId = $this->data['Linkedaccount']['investor_id'];
            $data["companiesInFlow"][0] = $linkaccountId;
            $data["originExecution"] = WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT;
            $userReference = $this->Investor->getInvestorIdentityByInvestorId($investorId);
            $result = $this->Queue2->addToQueueDashboard2($userReference, json_encode($data));
            return $result;
        }
    }
 
    
   /**
     * 	Disables the linked account(s) that fulfill $filterConditions. No action is taken in case account is NOT_ACTIVE 
     *  or already disabled.
     *  Note that "disabled" is not the same as "deleted"
     *
     * 	@param 		array   $filterConditions	
     *  @param          int     $originator         WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     *
     * 	@return 	boolean	true	Account(s) disabled
     * 				false	Error happened, account(s) not disabled					
     */
    public function disableLinkedAccount($filterConditions, $originator = WIN_USER_INITIATED) {

        $indexList = $this->find('all', $params = array('recursive' => -1,
                                                         'fields'    => array('id', 'linkedaccount_status','linkedaccount_statusExtended'),
                                                        'conditions' => $filterConditions)
                                );

        if (empty($indexList)) {
            return false;
        }

        if ($originator == WIN_USER_INITIATED ) {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_USER;
        }
        else {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_SYSTEM;
        } 

        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
    
        foreach ($indexList as $index) {
            if ($index['Linkedaccount']['linkedaccount_status'] == WIN_LINKEDACCOUNT_ACTIVE) {
                $newData['linkedaccount_statusExtendedOld'] = $index['Linkedaccount']['linkedaccount_statusExtended'];
                $newData['id'] =  $index['Linkedaccount']['id'];
                $changedData[] = $newData;
            }
            else {
                continue;
            }   
        }
       
        $this->saveMany($changedData, array('validate' => true));        
        return true;
    }   
    
    
   /**
     * 	Enables the linked account(s) that fulfill $filterConditions. 
     *  Only accounts that are disabled can be enabled. Note that if no disabled account(s) fulfill the 
     *  filteringConditions, a true is returned.
     *
     * 	@param 		array   $filterConditions	
     *  @param          int     $originator         WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     *
     * 	@return 	boolean	true	Account(s) enabled
     * 				false	Error happened, account(s) could not be enabled
     * 						
     */
    public function enableLinkedAccount($filterConditions, $originator = WIN_USER_INITIATED) {

        $indexList = $this->find('all', $params = array('recursive' => -1,
                                                         'fields'    => array('id', 'linkedaccount_status', 
                                                             'linkedaccount_statusExtended', 'linkedaccount_statusExtendedOld',
                                                                                ),
                                                        'conditions' => $filterConditions)
                                );

        if (empty($indexList)) {
            return false;
        }

        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_ACTIVE;

        foreach ($indexList as $index) {
            if ($index['Linkedaccount']['linkedaccount_statusExtended'] == WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_SYSTEM ||
                    $index['Linkedaccount']['linkedaccount_statusExtended'] == WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_USER) {
                $newData['linkedaccount_statusExtended'] = $index['Linkedaccount']['linkedaccount_statusExtendedOld'];
                $newData['id'] =  $index['Linkedaccount']['id'];
                $changedData[] = $newData;
            }
            else {
                continue;
            }   
        }

        $this->saveMany($changedData, array('validate' => true));       
        return true;
    }     
  
    
    /**
     * 	Callback Function
     * 	Check if we can/need to send the Alias
     *
     */
    public function afterFind($results, $primary = false) {

        if (!isset($results['Linkedaccount']['linkedaccount_isControlledBy'])) {
            $linkedAccountResult = $this->find('first', $params = array(
                                        'conditions' => array('Linkedaccount.id' => $result['id']),
                                        'recursive' => -1,                          //int
                                        'fields' => array('Linkedaccount.linkedaccountisControlledBy'),
                                        'callbacks' => false,
                                        ));
            $isControlledBy = $linkedAccountResult['Linkedaccount']['linkedaccount_isControlledBy'];
        }
        else {
            $isControlledBy = $results['Linkedaccount']['linkedaccount_isControlledBy'];
        }
        
        if ($isControlledBy == WIN_ALIAS_SYSTEM_CONTROLLED) {
            if (isset($results['Linkedaccount']['linkedaccount_alias'])) {
                unset($results['Linkedaccount']['linkedaccount_alias']);  
            }
        }
        return $results;
    }     
  
 
}
