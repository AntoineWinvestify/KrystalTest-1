<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2019, http://www.winvestify.com                         |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.1
 * @date 2019-01-02
 * @package
 */
/*
 * 2019-01-02	  version 0.1
 * function index
 * 
 * 2019-01-03	  version 0.2
 * function pre_check
 * function edit
 * function add
 */

class LinkedaccountsController extends AppController {

    var $name = 'Linkedaccounts';
    var $uses = array('Linkedaccount', 'Accountowner', 'Tooltip');
    var $error;

    function beforeFilter() {
        parent::beforeFilter();

//	$this->Security->requireAuth();
        $this->Auth->allow(array('v1_index', 'v1_pre_check', 'v1_edit'));
    }
    
    /** 
     * This methods terminates the HTTP GET.
     * Format GET /v1/linkedaccounts?[status]
     * 
     * 
     * @return string
     * 
     */
    public function v1_index() {
        
        $linkedaccountStatus = $this->listOfQueryParams;
        
        $tooltips = $this->Tooltip->getTooltip(array(ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME), $this->language);
        $accounts['tooltip_display_name'] = $tooltips[ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME];
        $accounts['service_status'] = "ACTIVE";
        $accounts['service_status_display_message'] = "You are using the maximum number of linkedaccounts. If you like to link more accounts, please upgrade your subscription";
        $accounts = $accounts + $this->Accountowner->api_readAccountowners($this->investorId, $linkedaccountStatus);
        $this->Accountowner->apiVariableNameOutAdapter($accounts['data']);
        foreach ($accounts['data'] as $key => $account) {
            $this->Accountowner->apiVariableNameOutAdapter($accounts['data'][$key]);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'edit', $accounts['data'][$key]['id']);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'delete', $accounts['data'][$key]['id']);
            //HOW GET POLLING RESOURCE ID?
        }
        $accounts = json_encode($accounts);
        return $accounts;
    }
    
    /**
     * This methods terminates the HTTP GET.
     * Format GET /v1/linkedaccounts/[linkedaccountId]
     * Example GET /v1/linkedaccounts/945
     * 
     * @param integer $id The database identifier of the requested 'Linkedaccount' resource
     * @return string
     */
    public function v1_view($id) {
        $fields = $this->listOfQueryParams['fields'];
    }


    /**
     * This methods terminates the HTTP PATCH/PUT
     * Format PATCH /v1/linkedaccounts/[linkedaccountId]?[accountowner_password]
     * Example PATCH /v1/linkedaccounts/945?accountowner_password=123456;
     *  
     * @param integer $id The database identifier of the requested 'Linkedaccount' resource
     * @return string
     */
    public function v1_edit($id) {
        
        
        $newPass = $this->listOfQueryParams['accountowner_password'];
        $data = $this->Linkedaccount->getData(array('Linkedaccount.id' => $id), array('Linkedaccount.accountowner_id'), null, null, 'first');
        $accountownerId = $data['Linkedaccount']['accountowner_id'];
        $feedback = $this->Accountowner->api_changeAccountPassword($this->investorId, $accountownerId, $newPass);
        return json_enconde($feedback);     
    }
    
    /**
     * This methods terminates the HTTP POST
     * Format POST /v1/linkedaccounts?['company_id']&['accountowner_username']&['accountowner_password']&['linkedaccount']=[['linkedaccount_identity']&['linkedaccount_accountDisplayName']&['linkedaccount_currency']]
     * Example POST /v1/linkedaccounts?company_id=25&linkedaccount_username=pfpaccount&linkedaccount_password=pfppassword&linkedaccount=[linkedaccount_username=978&linkedaccount_platform_display_name=Klaus[EUR]&linkedaccount_currency=EUR]
     * 
     * @return string
     */
    public function v1_add() {
        $companyId = $this->listOfQueryParams['company_id'];
        $username = $this->listOfQueryParams['accountowner_username'];
        $password = $this->listOfQueryParams['accountowner_password'];
        $identity = $this->listOfQueryParams['linkedaccount']['linkedaccount_identity'];
        $displayName = $this->listOfQueryParams['linkedaccount']['linkedaccount_accountDisplayName'];
        if ($this->listOfQueryParams['linkedaccount']['linkedaccount_currency']) {
            $currency = $this->listOfQueryParams['linkedaccount']['linkedaccount_currency'];
        }
        
        if(!empty($currency)){
            $result = $this->Linkedaccount->api_addLinkedaccount($this->investorId, $companyId, $username, $password, $identity, $displayName, $currency);
        } 
        else {
            $result = $this->Linkedaccount->api_addLinkedaccount($this->investorId, $companyId, $username, $password, $identity, $displayName);
        }      
        
        if (!empty($result)) { //Link OK
            $this->accountOwnerFields = array('Accountowner.company_id', 'Accountowner.accountowner_username', 'Accountowner.accountowner_password');
            $this->linkedaccountFields = array('Linkedaccount.id', 'Linkedaccount.linkedaccount_accountIdentity', 'Linkedaccount.linkedaccount_accountDisplayName',
                'Linkedaccount.linkedaccount_alias', 'Linkedaccount.linkedaccount_currency', 'Linkedaccount.linkedaccount_status');
            $accounts['data']['feedback_message_user'] = 'Account succefully linked.';
            $accounts = $accounts + $this->Accountowner->api_readAccountowners($this->investorId, $this->accountOwnerFields, $this->linkedaccountFields, WIN_LINKEDACCOUNT_ACTIVE);
            $accounts = $this->Accountowner->apiVariableNameOutAdapter($accounts['data']);
            foreach ($accounts['data'] as $key => $account) {
                $this->Accountowner->apiVariableNameOutAdapter($accounts['data'][$key]);
                $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'edit', $accounts['data'][$key]['id']);
                $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'delete', $accounts['data'][$key]['id']);
                $accounts = json_encode($accounts);
                return $accounts;
            }
        } 
        else{ //Link fail
            //ERROR LINQUEAR CUENTA
        }
    }
    
    /**
     * 
     * @param int $id
     * @return string
     */
    public function v1_delete($id){
        
    }
    
    
    /**
     * This methods terminates the HTTP POST
     * Format POST api/1.0/linkedaccounts?['company_id']&['accountowner_username']&['accountowner_password']
     * Example POST api/1.0/linkedaccounts?company_id=25&linkedaccount_username=pfpaccount&linkedaccount_password=pfppassword
     *  
     * @return string
     */
    public function v1_pre_check() {
        $companyId = $this->listOfQueryParams['company_id'];
        $username = $this->listOfQueryParams['accountowner_username'];
        $password = $this->listOfQueryParams['accountowner_password'];           
                
        $accounts = $this->Linkedaccount->api_precheck($this->investorId, $companyId, $username, $password);
        foreach ($accounts['accounts'] as $key => $account) {
            $this->Linkedaccount->apiVariableNameOutAdapter($accounts['accounts'][$key]);
        }
        $accounts = json_encode($accounts);
        return $accounts;
    }

}
