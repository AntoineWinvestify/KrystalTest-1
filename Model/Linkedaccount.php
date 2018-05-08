<?php
/**
// @(#) $Id$
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
* @version 0.2
* @date 2018-05-08
* @package
*/
/*
2016-08-25	  version 2016_0.1
function deleteLinkedaccount()					[Not yet OK, test new functionality]
getLinkedaccountDataList()					[OK, tested]
function linkNewAccount()					[not Ok, not tested]


2018-05-08	  version 2018_0.2                              [OK, tested]
 support for linkedaccount_status, linkedaccount_statusExtended and linkedaccount_statusExtendedOld fields
 
 * 
Pending:



*/


class Linkedaccount extends AppModel {

    var $name = 'Linkedaccount';

    /**
     *
     * 	Delete a record that fulfills the filteringConditions
     * 	
     *
     * 	@param 		array 	$filterConditions	Must indicate at least "investor_id"
     *  @param          int     $originator     WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     * 
     * 	@return 	true	record(s) deleted
     * 				false	no record(s) fulfilled $filteringConditions or incorrect filteringConditions
     *
     */
    public function deleteLinkedaccount($filterConditions, $originator = WIN_USER_INITIATED) {

        if (!array_key_exists('investor_id', $filterConditions)) {
            return false;
        }

        $indexList = $this->find('all', $params = array('recursive' => -1,
                                                        'conditions' => $filterConditions,
                                                        'fields' => array('id', 'investor_id'))
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

        $this->Investor = ClassRegistry::init('Investor');  
        
        foreach ($indexList as $index) {
            $this->Investor->decreaseLinkedAccounts($index['linkedaccount']['investor_id]']);
        }

        return true;
    }

    /**
     *
     * 	Returns an array of the linkedaccount items and their data that fulfill the filterConditions
     *
     * 	@param 		array 	$filterConditions
     * 	@return 	array 	Data of each linkedaccount item as an element of an array
     * 			
     */
    public function getLinkedaccountDataList($filterConditions) {

        $linkedaccountResults = $this->find("all", $params = array('recursive' => -1,
                                                                'conditions' => $filterConditions)
                                            );
        return $linkedaccountResults;
    }

    /**
     * 
     * Returns an array of the companies id depending on the filter Conditions
     * 
     * @param array $filterConditions
     * @return array Each company id
     * 
     */
    public function getLinkedaccountIdList($filterConditions) {

        $linkedaccountResults = $this->find("all", $params = array('recursive' => -1,
                                                                    'fields' => array('company_id'),
                                                                    'conditions' => $filterConditions)
                                           );
        return $linkedaccountResults;
    }

    /**
     *
     * 	Links a new investment account for an investor
     *
     * 	@param 		int 	$companyId		Identifier of company where linked account resides
     * 	@param 		int 	$investorId		Identifier of investor
     * 	@param 		string 	$username		username
     * 	@param 		string 	$password		password
     *
     * 	@return 	boolean	true	Account linked
     * 				false	Error happened, account not linked
     * 						
     */
    public function createNewLinkedAccount($companyId, $investorId, $username, $password) {

        $linkedAccountData['Linkedaccount'] = array('company_id' => $companyId,
                                                    'investor_id' => $investorId,
                                                    'linkedaccount_username' => $username,
                                                    'linkedaccount_password' => $password,
                                                    'linkedaccount_status'   => WIN_LINKEDACCOUNT_ACTIVE,
                                                    'linkedaccount_statusExtended' => WIN_LINKEDACCOUNT_ACTIVE_AND_CREDENTIALS_VERIFIED,
                                                    'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS
        );

        if ($this->save($linkedAccountData, $validation = true)) {
            $this->Investor = ClassRegistry::init('Investor');
            $this->Investor->increaseLinkedAccounts($investorId);
            return true;
        } 
        else {
            return false;
        }
    }
    
    /**
     * Get linkedaccounts id with nothing in process
     * 
     * @param string $queueUserReference It is the user reference
     * @return array
     * 
     */
    public function getLinkAccountsWithNothingInProcess($queueUserReference) {
        $companyNothingInProcess = [];
        $this->Investor = ClassRegistry::init('Investor');
        $jobInvestor = $this->Investor->find("first", array('conditions' =>
            array('Investor.investor_identity' => $queueUserReference),
            'fields' => 'id',
            'recursive' => -1,
        ));

        $investorId = $jobInvestor['Investor']['id'];
        $filterConditions = array(
            'investor_id' => $investorId,
            'linkedaccount_linkingProcess' => WIN_LINKING_NOTHING_IN_PROCESS,
            'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE
        );
        $linkedaccountsResults[] = $this->getLinkedaccountDataList($filterConditions);
        foreach ($linkedaccountsResults as $key => $linkedaccountResult) {
            //In this case $key is the number of the linkaccount inside the array 0,1,2,3
            $i = 0;
            foreach ($linkedaccountResult as $linkedaccount) {
                $companyNothingInProcess[] = $linkedaccount['Linkedaccount']['id'];
            }
        }
        return $companyNothingInProcess;
    }
    
    /**
     * 
     * @param type $linkaccountId id of the linkaccount
     * @param type $newPass new password
     * @return boolean 
     *  
     */
    public function changePasswordLinkaccount($linkaccountId, $newPass){
        $this->save(['id' => $linkaccountId, 'linkedaccount_password' => $newPass]);
    }

    /**
     *
     * 	Callback Function
     * 	Decrypt the sensitive data provided by the investor
     *
     */
    public function afterFind($results, $primary = false) {

        foreach ($results as $key => $val) {
            if (isset($val['Linkedaccount']['linkedaccount_password'])) {
                
                $results[$key]['Linkedaccount']['linkedaccount_password'] = $this->decryptDataAfterFind(
                        $val['Linkedaccount']['linkedaccount_password']);
            }
            if (isset($val['Linkedaccount']['linkedaccount_username'])) {

                $results[$key]['Linkedaccount']['linkedaccount_username'] = $this->decryptDataAfterFind(
                        $val['Linkedaccount']['linkedaccount_username']);
            }
        }
        return $results;
    } 
    
    /**
     * Callback function
     * Add a new request on queue for the company that was linked from a user
     * 
     * @param boolean $created
     * @param array $option
     * @return boolean
     * 
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
     *
     * 	Callback Function
     * 	Encrypt the sensitive fields of the information provided by the investor
     *
     */
    public function beforeSave($options = array()) {

        if (!empty($this->data['Linkedaccount']['linkedaccount_password'])) {
            $this->data['Linkedaccount']['linkedaccount_password'] = $this->encryptDataBeforeSave($this->data['Linkedaccount']['linkedaccount_password']);
        }

        if (!empty($this->data['Linkedaccount']['linkedaccount_username'])) {
            $this->data['Linkedaccount']['linkedaccount_username'] = $this->encryptDataBeforeSave($this->data['Linkedaccount']['linkedaccount_username']);
        }

        return true;
    }

    
    
   /**
     *
     * 	Disables the linked account(s) that fulfill $filterConditions. No action is taken in case account is NOT_ACTIVE 
     *  or already disabled.
     *
     * 	@param 		array   $filterConditions	
     *  @param          int     $originator         WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     *
     * 	@return 	boolean	true	Account(s) disabled
     * 				false	Error happened, account(s) not disabled
     * 						
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
     *
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
    
}
