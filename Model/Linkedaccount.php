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
     * 	Returns an array of the Linkedaccount object and the associated data that fulfill the filterConditions
     *
     * 	@param 		array 	$filterConditions
     * 	@return 	array 	Data of each linkedaccount item as an element of an array
     * 			USERID/PASS SHOULD ALSO BE RETURNED
     */
    public function getLinkedaccountDataList($filterConditions) {
        $linkedaccountResults = $this->find("all", $params = array('recursive' => 1,
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
        else {
            return false;
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

        $linkedAccountsResults[] = $this->getLinkedaccountDataList($filterConditions);
        
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
         * beforeFind, starts a timer for a find operation.
         *   
         * @param array $queryData Array of query data (not modified)
         * @return boolean true
         */
	public function beforeFind($queryData) {
                if(!empty($queryData['conditions']['linkedaccount_status'])){
                    switch ($queryData['conditions']['linkedaccount_status']) {
                        case 'ACTIVE':
                            $queryData['conditions']['linkedaccount_status'] = WIN_LINKEDACCOUNT_ACTIVE;
                            break;
                        case 'SUSPENDED':
                            $queryData['conditions']['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
                            break;
                        default:
                            break;
                    }
                }
		return $queryData;
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
     * 	Callback Function
     * 	Decrypt the sensitive data provided by the investor
     *
     */
    public function afterFind($results, $primary = false) {

        foreach ($results as $key => $val) {
            if (!empty($val['Linkedaccount']['linkedaccount_status'])) {
                switch ($val['Linkedaccount']['linkedaccount_status']) {
                    case WIN_LINKEDACCOUNT_ACTIVE:
                        $results[$key]['Linkedaccount']['linkedaccount_apiStatus'] = 'ACTIVE';
                        break;
                    case WIN_LINKEDACCOUNT_NOT_ACTIVE:
                        $results[$key]['Linkedaccount']['linkedaccount_apiStatus'] = 'SUSPENDED';
                        break;
                    default:
                        $results[$key]['Linkedaccount']['linkedaccount_apiStatus'] = 'UNDEFINED';
                        break;
                }
            }
        }
        return $results;
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
    
    
        /**
     * 	Callback Function
     * 	Check if we can/need to send the Alias
     * Problaby not necessary.
     *
     */
    /*public function afterFind($results, $primary = false) {

        if (!isset($results['Linkedaccount']['linkedaccount_isControlledBy'])) {
            $linkedAccountResult = $this->find('first', $params = array(
                                        'conditions' => array('Linkedaccount.id' => $results[]['id']),
                                        'recursive' => -1,                          //int
                                        'fields' => array('Linkedaccount.linkedaccount_isControlledBy'),
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
    }     */
 
    /**
     * 
     * 
     *          API FUNCTIONS
     */
    
    
    /**
     *
     * Check login, search for multiaccounts in pfp with it and check if you already linked that account/s.
     *  
     * @param int $companyId
     * @param string $username
     * @param string $password
     * @return array   If mono account, return always an array of size 1. Return account and a field that tells if 
     *                  that account is already linked.
     */
    public function api_precheck($investorId, $companyId, $username, $password) {        
       
        $alreadyLinked = false;
        //Begin to check if that account already exist.
        $this->Accountowner = ClassRegistry::init('Accountowner');
        $accountOwner = $this->Accountowner->checkAccountOwner($investorId, $companyId, $username, $password);         //Search for an account owner with same credentials and company
        if (Configure::read('debug')) {
            print_r($accountOwner);
        }
        $this->Company = ClassRegistry::init('Company');
        $multiAccount = $this->Company->getData(array('id' => $companyId), array('company_technicalFeatures', 'company_codeFile'));
        $bitIsSet = $multiAccount[0]['Company']['company_technicalFeatures'] & WIN_MULTI_ACCOUNT_FEATURE;                           //Check if multiaccount bit is set in technical freatures
        if ($bitIsSet == WIN_MULTI_ACCOUNT_FEATURE) {
            $multiAccountCheck = true;
        } 
        else {
            $multiAccountCheck = false;
        }

        if (!empty($accountOwner) && $multiAccountCheck) {
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Linkedaccount is multiaccount and Accountowner already exist, can't link already linked, disabling linking";
            }

            $accountOwnerId = $accountOwner['Accountowner']['id'];
            $accountsLinked = $this->getLinkedaccountDataList(array('accountowner_id' => $accountOwnerId));         //Check for the active linkedaccounts from that accountowner
            } 
        else if (!empty($accountOwner) && !$multiAccountCheck) { //Not multiaccount company and already linked, can't link again
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Linkedaccount not multiaccount and Accountowner already exist, can't link again";
            }
            $alreadyLinked = true;
        }

        //Login process
        $this->Urlsequence = ClassRegistry::init('Urlsequence');
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_LOGIN_SEQUENCE);
        $newComp = $this->companyClass($multiAccount[0]['Company']['company_codeFile']);
        $newComp->setUrlSequence($urlSequenceList);
        
        /*$configurationParameters = array('tracingActive' => true,
            'traceID' => $this->Auth->user('Investor.investor_identity'),
        );
        $newComp->defineConfigParms($configurationParameters);*/

        $newComp->generateCookiesFile();
        if ($multiAccountCheck) {
            $accounts = $newComp->companyUserLoginMultiAccount($username, $password);
            foreach ($accounts as $accountKey => $account) {
                $accounts[$accountKey]['accountCheck'] = false;
                foreach ($accountsLinked as $accountLinked) {
                    //Check the accounts already linked with the accounts to link, if we have them in $accountLinked, mark them in $accounts as already linked.
                    if ($account['linkedaccount_accountIdentity'] == $accountLinked['Linkedaccount']['linkedaccount_accountIdentity']) {        
                        $accounts[$accountKey]['accountCheck'] = true;
                        break;
                    }
                    
                }
            }
        } 
        else {
            $accounts = $newComp->companyUserLogin($username, $password);
            if($accounts){
                $accounts[0]['accountCheck'] = $alreadyLinked;
                $accounts[0]['linkedaccount_accountIdentity'] = $username;
                $accounts[0]['linkedaccount_accountDisplayName'] = $username;
            }
        }      
        if(empty($accounts)){
            //ERROR LOGIN 
            /*$error = array();
            $error['error']['message'] = 'Error at login. User or password incorrect.';*/
            //return $error;
            return false;
        }
        $return['accounts'] = $accounts;
        return $return;
    }

    /**
     * 	Delete a an account that fulfills the filteringConditions
     * 	
     * 	@param 		int 	$linkaccountId	Must indicate at least "investor_id"
     *  @param          int     $originator     WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     * 	@return 	true	record(s) deleted
     * 				false	no record(s) fulfilled $filteringConditions or incorrect filteringConditions
     */
    public function api_deleteLinkedaccount($linkaccountId, $originator = WIN_USER_INITIATED) {
               
        $indexList = $this->find('all', $params = array('recursive' => -1,
            'conditions' => array('linkaccount_id' => $linkaccountId),
            'fields' => array('id', 'accountowner_id'))
        );

        if (empty($indexList)) {
            return false;
        }

        if ($originator == WIN_USER_INITIATED) {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_AND_DELETED_BY_USER;
        } 
        else {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_DELETED_BY_SYSTEM;
        }

        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
        $this->updateAll($newData, $linkaccountId);

        $this->Accountowner = ClassRegistry::init('Accountowner');

        foreach ($indexList as $index) {
            $this->Accountowner->accountDeleted($index['accountowner_id']);
        }

        return true;
    }


    public function addLinkedaccount($accountOwnerId, $linkedaccountIdentity, $linkedaccountPlatformDisplayName, /*$linkedaccountAlias,*/$linkedaccountCurrency = 'EUR') { //[last field is by default €]
            $linkedAccountData['Linkedaccount'] = array('linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE,
                'linkedaccount_statusExtended' => WIN_LINKEDACCOUNT_ACTIVE_AND_CREDENTIALS_VERIFIED,
                'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS,
                'linkedaccount_isControlledBy' => WIN_ALIAS_SYSTEM_CONTROLLED,
                'linkedaccount_accountIdentity' => $linkedaccountIdentity,
                'linkedaccount_accountDisplayName' => $linkedaccountPlatformDisplayName,
                //'linkedaccount_alias' => $linkedaccountAlias,                 
                'accountowner_id' => $accountOwnerId,
                'linkedaccount_currency' => $linkedaccountCurrency,
            );

            if ($this->save($linkedAccountData, $validation = true)) {
                return true;
            } 
            else {
                return false;
            }
    }
    
     public function api_addLinkedaccount($investorId, $companyId, $username, $password, $identity, $displayName, $currency = 'EUR') {
        $accountOwnerId = $this->Accountowner->checkAccountOwner($investorId, $companyId, $username, $password);         //Search for an account owner with same credentials and company
        if (!empty($accountOwnerId)) {
            //Not new Accountowner
            return $this->Linkedaccount->addLinkedaccount($accountOwnerId, $identity, $displayName, $currency);
        } 
        else {
            //New Accountowner
            $newAccountOwnerId = $this->Accountowner->createAccountOwner($this->investorId, $companyId, $username, $password);
            return $this->Linkedaccount->addLinkedaccount($newAccountOwnerId, $identity, $displayName, $currency);
        }
    }

}
