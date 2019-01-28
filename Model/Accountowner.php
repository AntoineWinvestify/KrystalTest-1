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
 * @version 0.1
 * @date 2018-05-08
 * @package
 */
/*
  This model holds the credentials of the account(s) on a P2P

  2018-05-08	  version 2018_0.1
  First version



  Pending:



 */


class Accountowner extends AppModel {

    var $name = 'Accountowner';
    public $hasMany = array(
        'Linkedaccount' => array(
            'className' => 'Linkedaccount',
            'foreignKey' => 'accountowner_id',
    ));
    public $belongsTo = array(
        'Investor' => array(
            'className' => 'Investor',
            'foreignKey' => 'investor_id'
    ));

    /**
     * Delete an object of type Accountowner
     * Happens automatically when accountowner_linkedAccountCounter = 0
     * 
     * 	@param 		int 	$accountOwnerId         The database reference of accountowner object to 'delete'
     *  @param          int     $originator             WIN_USER_INITIATED or WIN_SYSTEM_INITIATED
     * 	@return 	true	record(s) deleted
     * 				false	no record(s) fulfilled $filteringConditions or incorrect filteringConditions
     */
    function deleteAccountOwner($accountOwnerId) {
        // Check if accountowner really exists.
        $filterConditions = ['id' => $accountOwnerId];
        $resultCounter = $this->find('first', array('conditions' => $filterConditions,
            'recursive' => -1,
        ));

        if (!empty($resultCounter)) {
            $data = array('id' => $accountOwnerId,
                'accountowner_status' => WIN_LINKEDACCOUNT_NOT_ACTIVE,
                'accountowner_username' => "FINISHED",
                'accountowner_password' => "FINISHED");

            if ($this->save($data, $validation = true)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Create a new accountowner for a linked account of an investor. If accountowner already exists then its 
     * reference is returned
     *
     * @param int $companyId            Database Identifier of PFP company where the linked account resides
     * @param int $investorId           Database Identifier of investor
     * @param string $username          username
     * @param string $password          password
     * @return boolean|\bigint
     */
    public function createAccountOwner($investorId, $companyId, $username, $password) {
        $data['company_id'] = $companyId;
        $data['investor_id'] = $investorId;
        $data['accountowner_username'] = $username;
        $data['accountowner_password'] = $password;
        $data['accountowner_status'] = WIN_ACCOUNTOWNER_ACTIVE;
          if ($this->save($data, $validation = true)) {
          return $this->id;
          }
          return false;
    }

    /**
     * Add a linked account to the accountowner. Also the number of linked accounts tied to an accountowner object
     * is incremented
     * 
     * @param int $accountownerId    It is the reference of the accountowner object
     * @return boolean
     */
    public function accountAdded($accountownerId) {

        $filterConditions = array('id' => $accountownerId);
        $resultCounter = $this->find('first', array('conditions' => $filterConditions,
            'recursive' => -1,
            'fields' => array('id', 'accountowner_linkedAccountCounter', 'investor_id'),
        ));
        if (empty($resultCounter)) {
            return false;
        }

        $newCounterValue = $resultCounter['Accountowner']['accountowner_linkedAccountCounter'] + 1;
        $data = array('id' => $accountownerId, 'accountowner_linkedAccountCounter' => $newCounterValue);
        if ($this->save($data, $validate = true)) {
            $this->Investor = ClassRegistry::init('Investor');
            $this->Investor->increaseLinkedAccounts($resultCounter['Accountowner']['id']);

            $this->Linkedaccount = ClassRegistry::init('Linkedaccount');

            ($newCounterValue > 1) ? $newAliasState = WIN_ALIAS_SYSTEM_CONTROLLED : $newAliasState = WIN_ALIAS_USER_CONTROLLED;

            $this->Linkedaccount->updateAll(array('Linkedaccount.linkedaccount_isControlledBy' => $newAliasState), array('Linkedaccount.accountowner_id' => $accountownerId)
            );
            return true;
        }
        return false;
    }

    /**
     * Remove a linked account from the accountowner. This is ONLY
     * required if the account is deleted. Disabling an account
     * has no effect. Also the number of linked accounts tied to an accountowner object
     * is decremented.
     * 
     * @param int $accountownerId   It is the reference of the accountowner object
     * @return boolean
     */
    public function accountDeleted($accountownerId) {

        $filterConditions = array('id' => $accountownerId);
        $resultCounter = $this->find('first', array('conditions' => $filterConditions,
            'recursive' => -1,
            'fields' => array('id', 'accountowner_linkedAccountCounter', 'investor_id')
        ));
        if (empty($resultCounter)) {
            return false;
        }

        $newCounterValue = $resultCounter['Accountowner']['accountowner_linkedAccountCounter'] - 1;
        if ($newCounterValue == 0) {
            $data = array('id' => $accountownerId, 'accountowner_linkedAccountCounter' => $newCounterValue, 'accountowner_status' => WIN_ACCOUNTOWNER_NOT_ACTIVE);
        } else {
            $data = array('id' => $accountownerId, 'accountowner_linkedAccountCounter' => $newCounterValue);
        }
        if ($this->save($data, $validate = true)) {
           /*$this->Investor = ClassRegistry::init('Investor');
            $this->Investor->decreaseLinkedAccounts($resultCounter['Accountowner']['id']);

            $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
            if ($newCounterValue == 0) {
                $this->deleteAccountOwner($accountownerId);
                return true;
            }

            ($newCounterValue > 1) ? $newAliasState = WIN_ALIAS_SYSTEM_CONTROLLED : $newAliasState = WIN_ALIAS_USER_CONTROLLED;

            $this->Linkedaccount->updateAll(
                    array('Linkedaccount.linkedaccount_isControlledBy' => $newAliasState), array('Linkedaccount.accountowner_id' => $accountownerId)
            );*/
            return true;
        }
        return false;
    }

    /**
     * Change the password on a PFP for a USER
     * 
     * @param type $accountownerId  id of the accountowner object
     * @param type $newPass         new password
     * @return boolean        
     */
    public function changeAccountPassword($accountownerId, $newPass) {
        // Check if accountowner really exists.
        $filterConditions = ['id' => $accountownerId];
        $resultCounter = $this->find('first', array('conditions' => $filterConditions,
            'recursive' => -1,
        ));

        if (!empty($resultCounter)) {
            if ($this->save(['id' => $accountownerId, 'accountowner_password' => $newPass])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the investor already have linked an account owner.
     * 
     * @param int $investorId
     * @param int $companyId
     * @param string $username
     * @param string $password
     * @return boolean/array
     */
    public function checkAccountOwner($investorId, $companyId, $username, $password) {
        $accountFinded = null;

        $filterConditions = array('Accountowner.investor_id' => $investorId, 'Accountowner.company_id' => $companyId, 'Accountowner.accountowner_status' => WIN_ACCOUNTOWNER_ACTIVE);
        $accounts = $this->find("all", $params = array('recursive' => -1,
            'conditions' => array($filterConditions)
                )
        );
        foreach ($accounts as $account) {
            if ($account['Accountowner']['accountowner_username'] == $username && $account['Accountowner']['accountowner_password'] == $password) {
                $accountFinded = $account;
                break;
            }
        }

        if (!empty($accountFinded)) {
            return $accountFinded;
        } else {
            return false;
        }
    }

    /**
     * 	Callback Function
     * 	Decrypt the sensitive data provided by the investor
     *
     */
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['Accountowner']['accountowner_password'])) {
                $results[$key]['Accountowner']['accountowner_password'] = $this->decryptDataAfterFind(
                        $val['Accountowner']['accountowner_password']);
            }

            if (isset($val['Accountowner']['accountowner_username'])) {

                $results[$key]['Accountowner']['accountowner_username'] = $this->decryptDataAfterFind(
                        $val['Accountowner']['accountowner_username']);
            }
        }
        return $results;
    }

    /**
     *
     * 	Callback Function
     * 	Encrypt the sensitive fields of the information provided by the investor
     *
     */
    public function beforeSave($options = array()) {

        if (!empty($this->data['Accountowner']['accountowner_password'])) {
            $this->data['Accountowner']['accountowner_password'] = $this->encryptDataBeforeSave($this->data['Accountowner']['accountowner_password']);
        }

        if (!empty($this->data['Accountowner']['accountowner_username'])) {
            $this->data['Accountowner']['accountowner_username'] = $this->encryptDataBeforeSave($this->data['Accountowner']['accountowner_username']);
        }
        return true;
    }

    /**
     * 
     * 
     *          API FUNCTIONS
     * 
     */

  
    /**
     * Return the accountOwners of an given investor with the related linkedaccounts.
     * 
     * @param int $investorId                                                  Id of the investor
     * @param int/array $linkedaccountStatus                                   Active or/and suspended
     * @param int/array $accountOwnerStatus                                    Active or/and suspended
     * @return array
     */
    public function api_readAccountowners($investorId, $linkedaccountStatus = WIN_LINKEDACCOUNT_ACTIVE, $accountOwnerStatus = WIN_ACCOUNTOWNER_ACTIVE) {
        $accountsResult = array();
        $accountOwnerFields = array('Accountowner.id', 'Accountowner.company_id', 'Accountowner.accountowner_username', 'Accountowner.accountowner_password');
        $linkedaccountFields = array('Linkedaccount.id', 'Linkedaccount.linkedaccount_accountIdentity', 'Linkedaccount.linkedaccount_accountDisplayName',
            'Linkedaccount.linkedaccount_currency', 'Linkedaccount.linkedaccount_status');
        $filterConditions = array('Accountowner.investor_id' => $investorId, 'Accountowner.accountowner_status' => $accountOwnerStatus);
        

        $accounts = $this->find("all", array('recursive' => -1,
            'conditions' => $filterConditions,
            'fields' => $accountOwnerFields,
        ));

        foreach($linkedaccountStatus as $status){
            $linkedaccountSearchStatus[] = $status;
        }
        
        foreach ($accounts as $key => $accountResult) {
            $linkedaccountsResult = $this->Linkedaccount->find("all", array('recursive' => -1,
                'conditions' => array('Linkedaccount.linkedaccount_status' => $linkedaccountSearchStatus,
                    'Linkedaccount.accountowner_id' => $accountResult['Accountowner']['id']),
                'fields' => $linkedaccountFields
            ));

            foreach($linkedaccountsResult as $linkedaccountResult){
                $accounts[$key]['Linkedaccount'][] = $linkedaccountResult;
            }
        }

        $i = 0;
        foreach($accounts as $accountKey => $account){
            unset($account['Accountowner']['id']);
            foreach($account['Linkedaccount'] as $linkedaccount){
                foreach($account['Accountowner'] as $key => $accountOwnerData){
                    $accountsResult['data'][$i][$key] = $accountOwnerData;  
                }
                foreach($linkedaccount as $key => $field){
                    if('accountowner_id' == $key){
                        continue;
                    }
                    $accountsResult['data'][$i][$key] = $field;  
                }
                $accountsResult['data'][$i]['linkedaccount_visual_state'] = 'ANALYZING';
                $i++;
            }
        }  

        return $accountsResult;
    }

     /**
     * Change the password on a PFP for a USER
     * 
     * @param int $accountownerId                                              Id of the accountowner object
     * @param type $newPass                                                    New password
     * @return array        
     */
    public function api_changeAccountPassword($investorId, $accountownerId, $newPass) {
        // Check if accountowner really exists.
        $filterConditions = ['Accountowner.id' => $accountownerId];
        $result = $this->find('first', array('conditions' => $filterConditions,
            'recursive' => -1,
        ));
        //Get credentials to try the login
        $username = $result['Accountowner']['accountowner_username'];
        $companyId  = $result['Accountowner']['company_id'];
        
        //Login
        $accounts = $this->Linkedaccount->precheck($investorId, $companyId, $username, $newPass);

        if (!empty($result) && $accounts != false && empty($accounts['error'])) {
            if ($this->save(['id' => $accountownerId, 'accountowner_password' => $newPass])) {
                $feedback['feedback_message_user'] = 'Your password has been succesfully changed';
                $feedback = json_encode($feedback);
                return $feedback;
            }
        }
        $feedback['feedback_message_user'] = "Your password couldn't be succesfully changed, try later or check your password.";
        $feedback = json_encode($feedback);
        return $feedback;
    }
    

    
}
