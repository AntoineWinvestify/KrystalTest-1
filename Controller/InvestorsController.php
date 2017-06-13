<?php
/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                         |
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
 * @date 2016-08-25
 * @package
 *

  2016-08-25	  version 0.1
  function linkAccount									[OK]
  function acceptConditionsTempPanel                            			[OK]
  removed acceptConditionsTempPanel after finishing Beta testing period                 [OK, tested]


  Pending:
  Generate a miniview for the extended notification of the event "newAccountLinked"     [OK, not yet tested]

 */


class InvestorsController extends AppController {

    var $name = 'Investors';
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Investor', 'Linkedaccount', 'Company', 'Urlsequence');
    var $error;

    
function beforeFilter() {
        parent::beforeFilter();

//	$this->Security->requireAuth();
//	$this->Auth->allow(array('cronAnalyzeUserDatas'));
}

   

    
    
/**
 *
 * 	Delete a linked account
 * 	
 * 	return list of currently linked accounts and a alert message about result of successfull delete
 * 	
 */
function deleteLinkedAccount() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        $error = false;
        $this->layout = 'ajax';
        $this->disableCache();

        $investorId = $this->Auth->user('Investor.id');
        $companyFilterConditions = array('id >' => 0);
        $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

        $linkedaccountFilterConditions = array('investor_id' => $investorId,
            'id' => $_REQUEST['index']);

        $this->Linkedaccount->deleteLinkedaccount($linkedaccountFilterConditions, $multiple = false); // Delete 1 account

        $linkedaccountFilterConditions = array('investor_id' => $investorId);

        $linkedAccountResult = $this->Linkedaccount->getLinkedaccountDataList($linkedaccountFilterConditions);

        $this->set('linkedAccountResult', $linkedAccountResult);
        $this->set('error', $error);
        $this->set('companyResults', $companyResults);
        $this->set('action', "delete");        
        $this->render('linkedaccountsList');
}

    
    
    
    
/**
 *
 * 	The investor can modify his personal data. This function will show the panel
 *
 */
public function editUserProfileData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        $error = false;
        $this->layout = 'ajax';
        $this->disableCache();

        Configure::load('countryCodes.php', 'default');
        $countryData = Configure::read('countrycodes');
        $this->set('countryData', $countryData);
        
        $investorId = $this->Auth->user('Investor.id');
        $userId = $this->Auth->user('id');

        foreach ($_REQUEST as $key => $value) {
            $receivedData[$key] = $_REQUEST[$key];
        }

        if (empty($this->request->data)) {  // screen is loaded for first time, i.e. in "read mode"
            $resultInvestor = $this->Investor->find('all', array('conditions' => array('id' => $investorId),
                'recursive' => -1,
            ));

            $this->set('initialLoad', true);
            $this->set('resultUserData', $resultInvestor);
            return;
        }

// The user has changed one or more data-items
        if (!empty($receivedData['password'])) {
            $this->User = ClassRegistry::init('User');
            $tempreceivedData['User'] = $receivedData;
            $this->User->set($tempreceivedData);
            if ($this->User->validates()) {
                
            } else {
                $this->set('userValidationErrors', $this->User->validationErrors);
                $userValidationErrors = $this->User->validationErrors;
            }
        }
        $tempreceivedData1['Investor'] = $receivedData;
        $this->Investor->set($tempreceivedData1);

        if ($this->Investor->validates()) {
            
        } else {
            $this->set('investorValidationErrors', $this->Investor->validationErrors);
            $investorValidationErrors = $this->Investor->validationErrors;
        }

// Validation passed, so time to save the data
        if (($investorValidationErrors == NULL) AND ( $userValidationErrors == NULL)) {
            if (!empty($receivedData['password'])) {
                $this->User->id = $userId;
                $this->User->save(array('password' => $receivedData['password']), $validate = false);
            }

            $this->Investor->id = $investorId;
            $this->Investor->save($receivedData, $validate = false);
            // UPDATE THE SESSION DATA
        }

        $receivedDataTemp[0]['Investor'] = $receivedData;
        $this->set('resultUserData', $receivedDataTemp);
    }

    
    
    
    
/**
 *
 * 	Manage linked accounts, i.e. store new pair of userid/password for newly linked account 
 * 	Return list of currently linked accounts and an alert message about result of successfull linking of account
 * 	
 */
function linkAccount() {
        $error = false;

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        $this->layout = 'ajax';
        $this->disableCache();

        $investorId = $this->Auth->user('Investor.id');
        $companyFilterConditions = array('id' => $_REQUEST['companyId']);
        $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

        if (empty($companyResults)) {
            $error = true;
            return;
        }

        $companyId = $_REQUEST['companyId'];
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGIN_SEQUENCE);

        $newComp = $this->companyClass($companyResults[$companyId]['company_codeFile']);
        $newComp->setUrlSequence($urlSequenceList);

        $configurationParameters = array('tracingActive' => true,
            'traceID' => $this->Auth->user('Investor.investor_identity'),
        );
        $newComp->defineConfigParms($configurationParameters);

        $userInvestment = $newComp->companyUserLogin($_REQUEST['userName'], $_REQUEST['password']);

        if (!$userInvestment) {                                                 // authentication error
            // load the list of all companies for display purposes
            $companyFilterConditions = array('id >' => 0);  // Load ALL company data as array
            $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

            $linkedaccountFilterConditions = array('investor_id' => $investorId);
            $linkedAccountResult = $this->Linkedaccount->getLinkedaccountDataList($linkedaccountFilterConditions);
            
            $this->set('linkedAccountResult', $linkedAccountResult);
            $this->set('companyResults', $companyResults);
            $this->set('action', "error");      // add a new account

            $this->render('linkedaccountsList');
            //$this->render('accountLinkingError');

        } else {
            if ($this->Linkedaccount->createNewLinkedAccount($_REQUEST['companyId'], $this->Auth->user('Investor.id'), $_REQUEST['userName'], $_REQUEST['password'])) {
                $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, LOGOUT_SEQUENCE);
                $newComp->companyUserLogout();

// load the list of all companies for display purposes
                $companyFilterConditions = array('id >' => 0);  // Load ALL company data as array
                $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

                $linkedaccountFilterConditions = array('investor_id' => $investorId);
                $linkedAccountResult = $this->Linkedaccount->getLinkedaccountDataList($linkedaccountFilterConditions);

                $this->set('linkedAccountResult', $linkedAccountResult);
                $this->set('companyResults', $companyResults);
                $this->set('error', $error);
                $this->set('action', "add");      // add a new account
  
  
  
                
                
                
                
                
 /* Provide information for a notification of this event */               
                /*$this->Notification = ClassRegistry::init('Notification');   
                $companyName = $companyResults[$companyId]['company_name'];
 
                $filterCondition = array("investor_id" => $investorId);
                $text = __('New account linked');
                $icon = "";
                $extendedInfo = __('You linked your account of platform ') . $newLinkedAccount; 
                $notificationDateTime = "";
                $response = $this->render('newAccountLink');        // Generate the view html for the body of the notifications modal
                $extendedInfo = $response->body();                
                $this->Notification->addNotification($filterConditions, $text, $icon, $extendedInfo, $notificationDateTime); */
                
  
                
     
                
                
                
                
                
                
                //$this->render('accountLinkingOk');
                $this->render('linkedaccountsList');
            } else {
                $this->set('error', true);
                $this->set('action', "authenticate");      // add a new account
                $this->render('accountLinkingError');
            }
        }
    }

    
    
    
    
/**
 *
 * 	Reads all the linked accounts of a user
 *
 */
function readLinkedAccounts() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }

        $error = false;
        $this->layout = 'ajax';
        $this->disableCache();

        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');    // Load the "Company" model

        $investorId = $this->Auth->user('Investor.id');
        $conditions = array('investor_id' => $this->Auth->user('Investor.id'));

        $linkedAccountResult = $this->Linkedaccount->find("all", $params = array('recursive' => -1,
            'conditions' => $conditions)
        );

        $conditions = array('Company.company_state' => ACTIVE);         // Any company anywhere will be possible

        $this->Company = ClassRegistry::init('Company');
        $companyResult = $this->Company->find("all", $params = array('recursive' => -1,
            'conditions' => $conditions)
        );

        foreach ($companyResult as $index => $result) {
            if (($result['Company']['company_featureList'] & ALLOW_LINKED_ACCOUNTS) == 0) {
                unset($companyResult[$index]);
            }
        }

// normalize the array
        foreach ($companyResult as $value) {
            $companyResults[$value['Company']['id']] = $value['Company'];
            $companyList[$value['Company']['id']] = $value['Company']['company_name'];
        }

        $this->set("linkedAccountResult", $linkedAccountResult);
        $this->set("companyResults", $companyResults);
        $this->set("companyList", $companyList);
    }

    
    
    
    
/**
 *
 * 	Generates the basic panel for accessing investor's data, like personal data, linked
 * 	account data and social network data
 * 	
 * 	
 */
function userProfileDataPanel() {
        $this->layout = 'azarus_private_layout';
    }

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    function ocrUserData(){
        echo " ";
    }
    
    function ocrDataPanel(){
        echo " ";
    }
    
    
    
}
