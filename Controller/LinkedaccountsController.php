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
 * 
 * 2019-01-14	  version 0.3
 * function delete
 */

class LinkedaccountsController extends AppController {

    var $name = 'Linkedaccounts';
    var $uses = array('Linkedaccount', 'Accountowner', 'Tooltip', 'Pollingresource');
    var $error;

    function beforeFilter () {
        parent::beforeFilter();
    }

    /**
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/linkedaccounts?[status]
     * Example GET /api/1.0/linkedaccounts?linkedaccount_status=ACTIVE
     *             /api/1.0/linkedaccounts?linkedaccount_status=ACTIVE,SUSPENDED
     * @return string
     * 
     */
    public function v1_index () {
        $linkedaccountStatus = $this->listOfQueryParams;

        $tooltips = $this->Tooltip->getTooltip(array(ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME), $this->language);
        $accounts['tooltip_display_name'] = $tooltips[ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME];
        $accounts['service_status'] = "ACTIVE";
        $accounts['service_status_display_message'] = "You are using the maximum number of linkedaccounts. If you like to link more accounts, please upgrade your subscription";
        $accounts = $accounts + $this->Accountowner->api_readAccountowners($this->investorId, $linkedaccountStatus);

        $this->Accountowner->apiVariableNameOutAdapter($accounts['data']);
        foreach ($accounts['data'] as $key => $account) {
            $this->Accountowner->apiVariableNameOutAdapter($accounts['data'][$key]);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'edit', $accounts['data'][$key]['Linkedaccount']['id'] . '.json');
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'delete', $accounts['data'][$key]['Linkedaccount']['id'] . '.json');
            if ($account['linkedaccount_visualStatus'] !== 'MONITOR') {
                $resourceId = $this->Pollingresource->getData(array(
                            'pollingresource_userIdentification' => $this->investorId,
                            'pollingresource_status' => ACTIVE,
                            'pollingresource_interval >' => 0,
                            'pollingresource_resourceId' => $accounts['data'][$key]['Linkedaccount']['id']), 'id', null, null, 'first')['Pollingresource']['id'];

                $accounts['data'][$key]['links'][] = $this->generateLink('pollingresources', 'delete', $resourceId . '.json');
                $accounts['data'][$key]['links'][] = $this->generateLink('pollingresources', 'monitor', $resourceId . '.json');
            }
        }
        $accounts = json_encode($accounts);
        $this->response->type('json');
        $this->response->body($accounts);
        return $this->response;
    }

    /**
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/linkedaccounts/[linkedaccountId]
     * Example GET /api/1.0/linkedaccounts/945
     * 
     * @param integer $id The database identifier of the requested 'Linkedaccount' resource
     * @return string
     */
    public function v1_view () {
        $id = $this->request->params['id'];
        $linkedaccount = $this->Linkedaccount->getData(array('Linkedaccount.id' => $id), null, null, null, 'first');
        $linkedaccount = $this->Linkedaccount->apiVariableNameOutAdapter($linkedaccount);
        $linkedaccount = json_encode($linkedaccount);
        $this->response->type('json');
        $this->response->body($linkedaccount);
        return $this->response;
    }

    /**
     * This methods terminates the HTTP PATCH/PUT
     * Format PATCH /api/1.0/linkedaccounts/[linkedaccountId]?[accountowner_password]
     * Example PATCH /api/1.0/linkedaccounts/945?accountowner_password=123456;
     *  
     * @param integer $id The database identifier of the requested 'Linkedaccount' resource
     * @return string
     */
    public function v1_edit () {
        $id = $this->request->params['id'];
        $RequestData = $this->request->data;
        $this->Linkedaccount->apiVariableNameInAdapter($RequestData);
        $newPass = $RequestData['accountowner_password'];
        $data = $this->Linkedaccount->getData(array('Linkedaccount.id' => $id), array('Linkedaccount.accountowner_id'), null, null, 'first');
        $accountownerId = $data['Linkedaccount']['accountowner_id'];
        $returnData = $this->Accountowner->api_changeAccountPassword($this->investorId, $accountownerId, $newPass);
        $this->response->statusCode($returnData['code']);
        $returnJson = json_encode($returnData['data']);
        $this->response->type('json');
        $this->response->body($returnJson);
        return $this->response;
    }

    /**
     * This methods terminates the HTTP POST
     * Format POST /api/1.0/linkedaccounts?['company_id']&['accountowner_username']&['accountowner_password']&['linkedaccount']=[['linkedaccount_identity']&['linkedaccount_accountDisplayName']&['linkedaccount_currency']]
     * Example POST /api/1.0/linkedaccounts?company_id=25&linkedaccount_username=pfpaccount&linkedaccount_password=pfppassword&linkedaccount=[linkedaccount_username=978&linkedaccount_platform_display_name=Klaus[EUR]&linkedaccount_currency=EUR]
     * 
     * @return string
     */
    public function v1_add () {
        //$this->print_r2($this->request);
        $data = $this->request['data'];
        $this->Linkedaccount->apiVariableNameInAdapter($data);
        $this->Linkedaccount->apiVariableNameInAdapter($data['linkedaccount'][0]);

        $companyId = $data['company_id'];
        $username = $data['accountowner_username'];
        $password = $data['accountowner_password'];
        $identity = $data['linkedaccount'][0]['linkedaccount_accountIdentity'];
        $displayName = $data['linkedaccount'][0]['linkedaccount_accountDisplayName'];
        if (!empty($this->$data['linkedaccount'][0]['linkedaccount_currency'])) {
            $currency = $this->$data['linkedaccount'][0]['linkedaccount_currency'];
        }

        if (!empty($currency)) {
            $result = $this->Linkedaccount->api_addLinkedaccount($this->investorId, $companyId, $username, $password, $identity, $displayName, $currency);
        }
        else {
            $result = $this->Linkedaccount->api_addLinkedaccount($this->investorId, $companyId, $username, $password, $identity, $displayName);
        }

        if ($result !== false) { //Link OK       
            $account = $this->Accountowner->api_readAccountowner($result);


            $resourceId = $this->Pollingresource->getData(array(
                        'pollingresource_userIdentification' => $this->investorId,
                        'pollingresource_status' => ACTIVE,
                        'pollingresource_interval >' => 0,
                        'pollingresource_resourceId' => $account['data']['Linkedaccount']['id']), 'id', null, null, 'first')['Pollingresource']['id'];

            $account['feedback_message_user'] = 'Account successfully linked.';
            $this->Accountowner->apiVariableNameOutAdapter($account['data']);
            $this->Accountowner->apiVariableNameOutAdapter($account['data']['linkedaccount']);

            $account['data']['linkedaccount']['links'][] = $this->generateLink('linkedaccounts', 'edit', $account['data']['linkedaccount']['id']);
            $account['data']['linkedaccount']['links'][] = $this->generateLink('linkedaccounts', 'delete', $account['data']['linkedaccount']['id']);
            $account['data']['linkedaccount']['links'][] = $this->generateLink('pollingresources', 'delete', $resourceId . '.json');
            $account['data']['linkedaccount']['links'][] = $this->generateLink('pollingresources', 'monitor', $resourceId . '.json');
            
            $account = json_encode($account);
            $this->response->type('json');
            $this->response->body($account);
            return $this->response;
        }
        else { //DB save fail
            $this->response->type('json');
            $this->response->body($error);
            return $this->response;
        }
    }

    /**
     * Format DELETE api/1.0/linkedaccounts/[linkedaccountId]
     * Example DELETE api/1.0//linkedaccounts/945
     * 
     * @param int $id
     * @return string
     */
    public function v1_delete () {
        $id = $this->request->params['id'];
        $returnData = $this->Linkedaccount->api_deleteLinkedaccount($this->investorId, $id, $this->roleName);
        $this->response->statusCode($returnData['code']);
        $returnJson = json_encode($returnData['data']);
        $this->response->type('json');
        $this->response->body($returnJson);
        return $this->response;
    }

    /**
     * This methods terminates the HTTP POST
     * Format POST api/1.0/linkedaccounts?['company_id']&['accountowner_username']&['accountowner_password']??????
     * Example POST api/1.0/linkedaccounts?company_id=25&linkedaccount_username=pfpaccount&linkedaccount_password=pfppassword?????????
     *  
     * @return string
     */
    public function v1_precheck () {
        $data = $this->request['data'];
        $this->Linkedaccount->apiVariableNameInAdapter($data);

        $companyId = $data['company_id'];
        $username = $data['accountowner_username'];
        $password = $data['accountowner_password'];

        $accounts = $this->Linkedaccount->precheck($this->investorId, $companyId, $username, $password);
        foreach ($accounts['accounts'] as $key => $account) {
            $this->Linkedaccount->apiVariableNameOutAdapter($accounts['accounts'][$key]);
        }
        
        $this->response->statusCode($accounts['code']);
        unset($accounts['code']);
        $accounts = json_encode($accounts);
        $this->response->type('json');
        $this->response->body($accounts);
        return $this->response;
    }

}
