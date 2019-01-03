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
 * 
 * 
 */

class LinkedaccountsController extends AppController {

    var $name = 'Linkedaccounts';
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Linkedaccount','Accountowner', 'Tooltip');
    var $error;

    function beforeFilter() {
        parent::beforeFilter();

//	$this->Security->requireAuth();
        $this->Auth->allow(array('index','pre_check'));
    }

    public function index() {
        $this->accountOwnerFields = array('Accountowner.company_id', 'Accountowner.accountowner_username', 'Accountowner.accountowner_password');
        $this->linkedaccountFields = array('Linkedaccount.id', 'Linkedaccount.linkedaccount_accountIdentity', 'Linkedaccount.linkedaccount_accountDisplayName',
            'Linkedaccount.linkedaccount_alias', 'Linkedaccount.linkedaccount_currency', 'Linkedaccount.linkedaccount_status');

        $tooltips = $this->Tooltip->getTooltip(array(ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME), $this->locale);
        $accounts['tooltip_display_name'] = $tooltips[ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME];
        $accounts['service_status'] = "ACTIVE";
        $accounts['service_status_display_message'] = "You are using the maximum number of linkedaccounts. If you like to link more accounts, please upgrade your subscription";
        $accounts = $accounts + $this->Accountowner->api_readAccountowners($this->investorId, $this->accountOwnerFields, $this->linkedaccountFields, WIN_LINKEDACCOUNT_ACTIVE);
        $this->Accountowner->apiVariableNameOutAdapter($accounts['data']);
        foreach ($accounts['data'] as $key => $account) {
            $this->Accountowner->apiVariableNameOutAdapter($accounts['data'][$key]);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'edit', $accounts['data'][$key]['id']);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'delete', $accounts['data'][$key]['id']);
        }
        $accounts = json_encode($accounts);
        return $accounts;
    }

    public function view($id) {
        
    }

    public function pre_check($investorId, $companyId, $username, $password) {
        $accounts = $this->Linkedaccount->api_precheck($investorId, $companyId, $username, $password);
        foreach ($accounts['accounts'] as $key => $account) {
            $this->Linkedaccount->apiVariableNameOutAdapter($accounts['accounts'][$key]);
        }
        $accounts = json_encode($accounts);
        return $accounts;
    }

}
