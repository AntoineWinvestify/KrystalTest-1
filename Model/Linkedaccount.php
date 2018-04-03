<?php
/**
// @(#) $Id$
// +-----------------------------------------------------------------------+
// | Copyright (C) 2016, http://yoursite                                   |
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
* @date 2016-08-25
* @package
*

2016-08-25	  version 2016_0.1
function deleteLinkedaccount()					[Not yet OK, test new functionality]
getLinkedaccountDataList()					[OK, tested]
function linkNewAccount()					[not Ok, not tested]



Pending:



*/


class Linkedaccount extends AppModel {

    var $name = 'Linkedaccount';

    /**
     *
     * 	Delete a record that fullfils the filteringConditions
     * 	
     *
     * 	@param 		array 	$filteringConditions	Must indicate at least "investorId"
     * 	@param 		bool	$multiple	true 	delete all if more then one record is found
     * 									false	delete only *first* record if more found [i.e the one with the lowest index]
     *
     * 	@return 	true	record(s) deleted
     * 				false	no record(s) fullfilled $filteringConditions
     *
     */
    public function deleteLinkedaccount($filteringConditions, $multiple = false) {

        $indexList = $this->find('list', $params = array('recursive' => -1,
            'conditions' => $filteringConditions,
        ));

        if (empty($indexList)) {
            return false;
        }

        $numberInList = count($indexList);
        $this->Investor = ClassRegistry::init('Investor');

        if ($numberInList == 1) {
            $this->delete($indexList);
            $this->Investor->decreaseLinkedAccounts($filteringConditions['investor_id']);
            return true;
        } else {
            if ($multiple) {
                $this->deleteAll($filteringConditions);

                for ($i = 0; $i < $numberInList; $i++) {
                    $this->Investor->decreaseLinkedAccounts($filteringConditions['investor_id']);
                }
                return true;
            } else {
                list($key, $val) = each($indexList);
                $this->delete($key);
                $this->Investor->decreaseLinkedAccounts($filteringConditions['investor_id']);
            }
        }
        return true;
    }

    /**
     *
     * 	Returns an array of the linkedaccount items and their data that fullfil the filterConditions
     *
     * 	@param 		array 	$filteringConditions
     * 	@return 	array 	Data of each linkedaccount item as an element of an array
     * 			
     */
    public function getLinkedaccountDataList($filterConditions) {

        $linkedaccountResults = $this->find("all", $params = array('recursive' => -1,
            'conditions' => $filterConditions
        ));
        return $linkedaccountResults;
    }

    /**
     * Returns an array of the companies id depending on the filter Conditions
     * @param array $filterConditions
     * @return array Each company id
     */
    public function getLinkedaccountIdList($filterConditions) {

        $linkedaccountResults = $this->find("all", $params = array('recursive' => -1,
            'fields' => array('company_id'),
            'conditions' => $filterConditions
        ));
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
     * 						false	Error happened, account not linked
     * 						
     */
    public function createNewLinkedAccount($companyId, $investorId, $username, $password) {

        $linkedAccountData['Linkedaccount'] = array('company_id' => $companyId,
            'investor_id' => $investorId,
            'linkedaccount_username' => $username,
            'linkedaccount_password' => $password,
            'linkedaccount_linkingProcess' => WIN_LINKING_WORK_IN_PROCESS
        );

        if ($this->save($linkedAccountData, $validation = true)) {
            $this->Investor = ClassRegistry::init('Investor');
            $this->Investor->increaseLinkedAccounts($investorId);
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Get linkedaccounts id with nothing in process
     * @param string $queueUserReference It is the user reference
     * @return array
     */
    public function getLinkAccountsWithNothingInProcess($queueUserReference) {
        $companyNothingInProcess = [];
        $this->Investor = ClassRegistry::init('Investor');
        $jobInvestor = $this->Investor->find("first", array('conditions' =>
            array('Investor.investor_identity' => $queueUserReference),
            'fields' => 'id',
            'recursive' => -1,
        ));
        //print_r($jobInvestor);
        $investorId = $jobInvestor['Investor']['id'];
        $filterConditions = array(
            'investor_id' => $investorId,
            'linkedaccount_linkingProcess' => WIN_LINKING_NOTHING_IN_PROCESS
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
            $userReference =  $this->Investor->getInvestorIdentityByInvestorId($investorId);
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

}
