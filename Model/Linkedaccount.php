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
    public $hasone = array(
        'Userinvestmentdata' => array(
            'className' => 'Userinvestmentdata',
            'ForeignKey' => 'linkedaccount_id',
        )
    );


    var $defaultFields = [ 
        'investor' => ['id',            //causes problems with PATCH, is never r/w
                        'company_id', 
                        'investor_id',
                        'accountowner_username',
                        'accountowner_linkedAccountCounter',
                        'accountowner_id',
                        'linkedaccount_status', 
                        'linkedaccount_alias', 
                        'linkedaccount_accountDisplayName',
                        'linkedaccount_currency',
                        'linkedaccount_visualStatus', 
                        'linkedaccount_links'
                      ],

        'winAdmin' => ['id', 
                        'company_id', 
                        'investor_id',
                        'accountowner_username',
                        'accountowner_linkedAccountCounter',
                        'accountowner_id',
                        'linkedaccount_status', 
                        'linkedaccount_statusExtended', 
                        'linkedaccount_statusExtendedOld',
                        'linkedaccount_alias', 
                        'linkedaccount_accountDisplayName',
                        'linkedaccount_accountIdentity',
                        'linkedaccount_currency',
                        'linkedaccount_visualStatus',    
                        'linkedaccount_links',
                        'modified',
                        'created'
            ],              
        'superAdmin' => ['id', 
                        'company_id', 
                        'investor_id',
                        'accountowner_username',
                        'accountowner_linkedAccountCounter',
                        'accountowner_id',
                        'linkedaccount_status', 
                        'linkedaccount_statusExtended', 
                        'linkedaccount_statusExtendedOld',
                        'linkedaccount_alias', 
                        'linkedaccount_accountDisplayName',
                        'linkedaccount_accountIdentity',
                        'linkedaccount_currency',
                        'linkedaccount_visualStatus',
                        'linkedaccount_links',
                        'modified',
                        'created'            
                      ],                
    ];  
 
    
    
    
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

        if ($originator == WIN_USER_INITIATED) {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_AND_DELETED_BY_USER;
        }
        else {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_DELETED_BY_SYSTEM;
        }

        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
        $this->updateAll($newData, $filterConditions);

        $this->Accountowner = ClassRegistry::init('Accountowner');

        foreach ($indexList as $index) {
            $this->Accountowner->accountDeleted($index['accountowner_id']);
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
            $linkedAccountData['Linkedaccount'] = array('linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE,
                'linkedaccount_statusExtended' => WIN_LINKEDACCOUNT_ACTIVE_AND_CREDENTIALS_VERIFIED,
                'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS,
                'accountowner_id' => $accountOwnerId,
                'linkedaccount_isControlledBy' => WIN_ALIAS_SYSTEM_CONTROLLED
            );

            if ($this->save($linkedAccountData, $validation = true)) {
                $this->Accountowner->accountAdded($accountOwnerId);
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
        $this->Investor->contain('Accountowner');         // Own model is automatically included
        $resultInvestorsData = $this->Investor->find("all", $params = array('recursive' => 1,
            'conditions' => $filterConditions));
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
            "AND" => array(array('linkedaccount_linkingProcess' => WIN_LINKING_NOTHING_IN_PROCESS,
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
     * @param type $linkedaccountId id of the linkedaccount
     * @param type $newPass new password
     * @return boolean 
     */
    public function changePasswordLinkaccount($linkedaccountId, $newPass) {
        $filterConditions = array('id' => $linkedaccountId);
        $accountOwnerId = $this->find("first", $params = array('recursive' => -1,
            'fields' => array('accountowner_id'),
            'conditions' => $filterConditions)
        );
        $this->changeAccountPassword($accountOwnerId, $newPass);
    }

    /**
     * beforeFind, starts a timer for a find operation.
     *   
     * @param array $queryData Array of query data (not modified)
     * @return boolean true
     */
    public function beforeFind($queryData) {
        if (!empty($queryData['conditions']['Linkedaccount.linkedaccount_status'])) {
            foreach ($queryData['conditions']['Linkedaccount.linkedaccount_status'] as $key => $value) {
                switch ($queryData['conditions']['Linkedaccount.linkedaccount_status'][$key]) {
                    case 'ACTIVE':
                        $queryData['conditions']['Linkedaccount.linkedaccount_status'][$key] = WIN_LINKEDACCOUNT_ACTIVE;
                        break;
                    case 'NOT_ACTIVE':
                        $queryData['conditions']['Linkedaccount.linkedaccount_status'][$key] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
                        break;
                    default:
                        break;
                }
            }
        }
        return $queryData;
    }

    /**
     * Callback function
     * Add a new request in Queue2 for the company that was by a user
     * 
     * @param boolean $created True if a new record was created (rather than an update).
     * @param array $options
     * @return boolean
     */
    public function afterSave($created, $options = array()) {

        if ($created) {
            $this->Investor = ClassRegistry::init('Investor');
            $this->Queue2 = ClassRegistry::init('Queue2');
            $data = [];
            $linkedaccountId = $this->id;
            $accountowner = $this->Accountowner->getData(array('id' => $this->data['Linkedaccount']['accountowner_id']), array('investor_id'), null, null, 'first');
            $investorId = $accountowner['Accountowner']['investor_id'];
            $data["companiesInFlow"][0] = $linkedaccountId;
            $data["originExecution"] = WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT;
            $userReference = $this->Investor->getInvestorIdentityByInvestorId($investorId);
            $result = $this->Queue2->addToQueueDashboard2($userReference, json_encode($data));
            return $result;
        }
        else {          // update of already existing object
            if (isset($this->data['Linkedaccount']['linkedaccount_visualStatus'])) {
                $investorId = $this->getInvestorFromLinkedaccount($this->data['Linkedaccount']['id']);
                $event = new CakeEvent("Model.Linkedaccount.Analyzing", $this, array(
                    'model' => "Linkedaccount",
                    'isFinalEvent' => false,
                    'userIdentification' => $investorId,
                    'modelData' => $this->data,
                    'id' => $this->data['Linkedaccount']['id'],
                ));
                $this->getEventManager()->dispatch($event);
                return true;
            }
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
                        $results[$key]['Linkedaccount']['linkedaccount_apiStatus'] = 'NOT_ACTIVE';
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
     * 	Disables the linked account(s) that fulfill $filterConditions. No action is taken in case account is NOT_ACTIVE 
     *  or already disabled.
     *
     * 	@param 		array   $filterConditions	
     *  @param          int     $originator         WIN_USER_INITIATED OR WIN_SYSTEM_INITIATED
     * 	@return 	boolean	true	Account(s) disabled
     * 				false	Error happened, account(s) not disabled
     */
    public function disableLinkedAccount($filterConditions, $originator = WIN_USER_INITIATED) {

        $indexList = $this->find('all', $params = array('recursive' => -1,
            'fields' => array('id', 'linkedaccount_status', 'linkedaccount_statusExtended'),
            'conditions' => $filterConditions)
        );

        if (empty($indexList)) {
            return false;
        }

        if ($originator == WIN_USER_INITIATED) {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_USER;
        }
        else {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_TEMPORARILY_DISABLED_BY_SYSTEM;
        }

        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;

        foreach ($indexList as $index) {
            if ($index['Linkedaccount']['linkedaccount_status'] == WIN_LINKEDACCOUNT_ACTIVE) {
                $newData['linkedaccount_statusExtendedOld'] = $index['Linkedaccount']['linkedaccount_statusExtended'];
                $newData['id'] = $index['Linkedaccount']['id'];
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
     * 	@return 	boolean	true	Account(s) enabled
     * 				false	Error happened, account(s) could not be enabled						
     */
    public function enableLinkedAccount($filterConditions, $originator = WIN_USER_INITIATED) {

        $indexList = $this->find('all', $params = array('recursive' => -1,
            'fields' => array('id', 'linkedaccount_status',
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
                $newData['id'] = $index['Linkedaccount']['id'];
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
    /* public function afterFind($results, $primary = false) {

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
      } */

    /**
     * Get the company id given by the linkedaccount id
     * 
     * @param int $id
     * @return int
     */
    public function getCompanyFromLinkedaccount($id) {
        $linkedAccountResult = $this->find("first", $param = ['conditions' => ['Linkedaccount.id' => $id],
            'fields' => ['Accountowner.company_id'], 'recursive' => 0]);
        return $linkedAccountResult['Accountowner']['company_id'];
    }

    /**
     * Get the investor id given by the linkedaccount id
     * 
     * @param int $id The id of the linkedaccount
     * @return int
     */
    public function getInvestorFromLinkedaccount($id) {
        $linkedAccountResult = $this->find("first", $param = ['conditions' => ['Linkedaccount.id' => $id],
            'fields' => ['Accountowner.investor_id'], 'recursive' => 0]);
        return $linkedAccountResult['Accountowner']['investor_id'];
    }

    /**
     * Get the list of the linkedaccount from an investor
     * 
     * @param type $id
     * return array
     */
    public function getListFromInvestorId($id) {
        $linkedAccountResult = $this->find('list', array(
            'conditions' => array('Accountowner.investor_id' => $id),
            'fields' => 'Linkedaccount.id',
            'recursive' => 0,
        ));
        return $linkedAccountResult;
    }

    /**
     * Get the linkedaccount_currency of a linkedaccount given the id
     * 
     * @param int $id
     * @return int
     */
    public function getCurrency($id) {
        $linkedAccountResult = $this->find("first", $param = ['conditions' => ['Linkedaccount.id' => $id],
            'fields' => ['Linkedaccount.linkedaccount_currency'], 'recursive' => -1]);
        return $linkedAccountResult['Linkedaccount']['linkedaccount_currency'];
    }

    /**
     * 
     *          API FUNCTIONS
     * 
     */

    /**
     * Check login, search for multiaccounts in pfp with it and check if you already linked that account/s.
     *  
     * @param int $companyId
     * @param string $username
     * @param string $password
     * @return array   If mono account, return always an array of size 1. Return account and a field that tells if 
     *                  that account is already linked.
     */
    public function precheck($investorId, $companyId, $username, $password) {

        $alreadyLinked = false;
        //Begin to check if that account already exist.
        $this->Accountowner = ClassRegistry::init('Accountowner');
        $accountOwner = $this->Accountowner->checkAccountOwner($investorId, $companyId, $username, $password);         //Search for an account owner with same credentials and company

        $this->Company = ClassRegistry::init('Company');
        $multiAccount = $this->Company->getData(array('id' => $companyId), array('company_technicalFeatures', 'company_codeFile'));
        $bitIsSet = $multiAccount[0]['Company']['company_technicalFeatures'] & WIN_MULTI_ACCOUNT_FEATURE;                           //Check if multiaccount bit is set in technical features

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
            $accountsLinked = $this->getLinkedaccountDataList(array('accountowner_id' => $accountOwnerId, 'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE));         //Check for the active linkedaccounts from that accountowner
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

        /* $configurationParameters = array('tracingActive' => true,
          'traceID' => $this->Auth->user('Investor.investor_identity'),
          );
          $newComp->defineConfigParms($configurationParameters); */

        $newComp->generateCookiesFile();
        if ($multiAccountCheck) {
            $accounts = $newComp->companyUserLoginMultiAccount($username, $password);
            foreach ($accounts as $accountKey => $account) {
                $accounts[$accountKey]['accountCheck'] = false;                 //This field is not in db, is only for the frontend, it tell if the account is already linked or not.
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
            if ($accounts == true) {
                unlink($accounts);
                $accounts = array();
                $accounts[0]['accountCheck'] = $alreadyLinked;
                $accounts[0]['linkedaccount_accountIdentity'] = $username;
                $accounts[0]['linkedaccount_accountDisplayName'] = $username;
            }
        }

        if (empty($accounts)) {
            //ERROR LOGIN 
            $error = array();
            $error['code'] = 403;
            return $error;
        }
        $returnResult['code'] = 200;
        $returnResult['accounts'] = $accounts;
        return $returnResult;
    }

    /**
     * Delete a linkedaccount 
     * 
     * @param type $investorId          Id of the investor that deletes the account
     * @param type $linkaccountId       Id of the linkedaccount to delete
     * @param type $roleName            Role of the user that wants to delete the account
     * @return string|int               Feedback and http code
     */
    public function api_deleteLinkedaccount($investorId, $linkaccountId, $roleName = 'Investor') {

        $indexList = $this->find('all', $params = array('recursive' => -1,
            'conditions' => array('Linkedaccount.id' => $linkaccountId),
            'fields' => array('Linkedaccount.id', 'Linkedaccount.accountowner_id', 'Linkedaccount.linkedaccount_status'))
        );

        if (empty($indexList)) {
            $return['data']['feedback_message_user'] = 'Account removal failed.';
            $return['code'] = 403;
            return $return;
        }
        else if ($indexList[0]['Linkedaccount']['linkedaccount_status'] == WIN_LINKEDACCOUNT_NOT_ACTIVE) {
            $return['code'] = 404;
            $return['data']['feedback_message_user'] = 'Account not found.';
            return $return;
        }

        //Check if the investor is the owner of the account.  
        $idCheck = $this->getInvestorFromLinkedaccount($linkaccountId);
        if($idCheck !== $investorId){
            $return['code'] = 403;
            $return['data']['feedback_message_user'] = 'Account removal failed.';
            return $return;
        }

        if ($roleName == 'Investor') {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_AND_DELETED_BY_USER;
        }
        else {
            $newData['linkedaccount_statusExtended'] = WIN_LINKEDACCOUNT_NOT_ACTIVE_DELETED_BY_SYSTEM;
        }

        $newData['id'] = $linkaccountId;
        $newData['linkedaccount_status'] = WIN_LINKEDACCOUNT_NOT_ACTIVE;
        $this->save($newData);

        foreach ($indexList as $index) {
            $this->Accountowner = ClassRegistry::init('Accountowner');
            $this->Accountowner->accountDeleted($index['Linkedaccount']['accountowner_id']);
        }
        $return['code'] = 200;
        $return['data']['feedback_message_user'] = 'The account has been sucessfully been removed from your Dashboard.';
        return $return;
    }

    /**
     * Add the linked account to the db.
     * 
     * @param int $accountOwnerId  Id from accountOwner
     * @param string $linkedaccountIdentity Identity  from the linkedaccount, necessary for multiaccounts.
     * @param string $linkedaccountPlatformDisplayName How we will show the linkedaccount name to the user.
     * @param string $linkedaccountCurrency Currency from the linkedaccouts(In mintos, different linkedaccount from the same accountOwner have different currency).
     * @return boolean
     */
    public function addLinkedaccount($accountOwnerId, $linkedaccountIdentity, $linkedaccountPlatformDisplayName, /* $linkedaccountAlias, */ $linkedaccountCurrency = 'EUR') { //[last field is by default €]
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
            $id = $this->id;
            $result = $this->Accountowner->getData(array('id' => $accountOwnerId), array('accountowner_linkedAccountCounter'));
            $linkedaccountCount = $result[0]['Accountowner']['accountowner_linkedAccountCounter'] + 1;
            $this->Accountowner->save(array('id' => $accountOwnerId, 'accountowner_linkedAccountCounter' => $linkedaccountCount));
            return $id;
        }
        else {
            return false;
        }
    }

    /**
     * Add a new linkaccount for an investor. If accountOwner(same company and credentials) exited before, link the new linkedaccount with it, if not, create a new account owner.
     * 
     * @param int $investorId  Id from investor.
     * @param int $companyId   Company from the accountOwner
     * @param string $username
     * @param string $password Username and password are the credentials to login in the pfp site.
     * @param string $identity Identity  from the linkedaccount, necessary for multiaccounts.
     * @param string $displayName How we will show the linkedaccount name to the user.
     * @param string $currency Currency from the linkedaccouts(In mintos, different linkedaccount from the same accountOwner will have different currency).
     * @return boolean
     */
    public function api_addLinkedaccount($investorId, $companyId, $username, $password, $identity, $displayName, $currency = 'EUR') {
        $accountOwnerId = $this->Accountowner->checkAccountOwner($investorId, $companyId, $username, $password);         //Search for an account owner with same credentials and company

        if (!empty($accountOwnerId) && !empty($investorId) && !empty($companyId) && !empty($username) && !empty($password) && !empty($identity) && !empty($displayName)) {
            //Not new Accountowner
            return $this->addLinkedaccount($accountOwnerId['Accountowner']['id'], $identity, $displayName, $currency);
        }
        else if(!empty($investorId) && !empty($companyId) && !empty($username) && !empty($password) && !empty($identity) && !empty($displayName)){
            //New Accountowner
            $newAccountOwnerId = $this->Accountowner->createAccountOwner($investorId, $companyId, $username, $password);
            return $this->addLinkedaccount($newAccountOwnerId, $identity, $displayName, $currency);
        }
        else{
            $result['code'] = 400;
            return $result;
        }
    }
    
    
    
    /** NOT TESTED
     * Determines if the current user (by means of its $investorId) is the direct or indirect owner
     * of the current Model. 
     * This functionality determines if a webclient may access the data of another investor
     * with proper R/W permissions.
     * 
     * @param $investorId The internal reference of the investor Object
     * @param $id The internal reference of the Linkedaccount object to be checked
     * @return int (WIN_ACL_INVESTOR_IS_OWNER, WIN_ACL_INVESTOR_IS_NOT_OWNER, WIN_ACL_RESOURCE_DOES_NOT_EXIST)
     */
    public function isOwner($investorId, $id) { 
        
        $realOwner = getInvestorFromLinkedaccount($id);       

        if (empty($realOwner)) {
            return WIN_ACL_RESOURCE_DOES_NOT_EXIST;
        }        
        if ($id == $investorId) {
            return WIN_ACL_INVESTOR_IS_OWNER;
        }
        return WIN_ACL_INVESTOR_IS_NOT_OWNER;   
    }
    
    
    /** NOT TESTED
     * Reads the list of defaultFields to read in case the webclient has not indicated any fields
     * in its GET request
     * 
     * @param $roleName The name of the role for whom the list of defaults fields is read
     * @return array  An array with the names of the fields. The names are the *internal* names of the fields
     */
    public function getDefaultFields($roleName) {
        return $this->defaultFields[$roleName];        
    }   
    

}
