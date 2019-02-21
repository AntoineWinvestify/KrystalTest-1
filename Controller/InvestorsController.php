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
 */
/*

  2016-08-25	  version 0.1
  function linkAccount									[OK]
  function acceptConditionsTempPanel                            			[OK]
  removed acceptConditionsTempPanel after finishing Beta testing period                 [OK, tested]

  2017-09-06        version 0.2
  Updated data saving to do again a query to show correctly the data 
  
 * 
 * 2018-02-20
 * Change password in link account  changePasswordLinkedAccount() function
 * 
  Pending:
  Generate a miniview for the extended notification of the event "newAccountLinked"     [OK, not yet tested]
 * A more consistent andpermanent solution will be implemented with 1CR for all "confirmed" data items,
 * like name, surname, telephone, dni, ....
 * 
 */

App::uses('AppController', 'Controller');

class InvestorsController extends AppController {

    var $name = 'Investors';
    var $helpers = array('Text');
    var $uses = array('Investor', 'Linkedaccount', 'Company', 'Urlsequence');
    var $error;

    
    function beforeFilter() {
        parent::beforeFilter();
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

        $this->Linkedaccount->deleteLinkedaccount($linkedaccountFilterConditions); // Delete 1 account

        $linkedaccountFilterConditions = array('investor_id' => $investorId, 
                                    'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);

        $linkedAccountResult = $this->Linkedaccount->getLinkedaccountDataList($linkedaccountFilterConditions);

        $this->set('linkedAccountResult', $linkedAccountResult);
        $this->set('error', $error);
        $this->set('companyResults', $companyResults);
        $this->set('action', "delete");        
        $this->render('linkedaccountsList');
}

    
    /**
     * Read the check data
     * 
     * @param type $investorId
     * @return type
     */
    public function readCheckData($investorId) {
        $checkData = $this->Investor->Check->find('all', array('conditions' => array('investor_id' => $investorId)));
        return $checkData;
    }
    
    
    
/**
 *
 * 	The investor can modify his personal data. This function will show the panel
 *
 */
public function editUserProfileData() {

        $this->layout = 'azarus_private_layout';


        Configure::load('countryCodes.php', 'default');
        $countryData = Configure::read('countrycodes');
        $this->set('countryData', $countryData);
        
        $investorId = $this->Auth->user('Investor.id');
        $userId = $this->Auth->user('id');

         //We do again the query to get correctly the data on plugins.
        $resultInvestor = $this->Investor->find('all', array('conditions' => array('id' => $investorId),
            'recursive' => -1,
        ));
        $this->set('userValidationErrors', 1);
        $this->set('resultUserData', $resultInvestor);
        //}
    }

    /**
     *
     * 	The investor can modify his personal data. 
     *
     */
    public function saveNewUserProfileData() {

        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }


        $this->layout = 'ajax';
        $this->disableCache();

        $investorId = $this->Auth->user('Investor.id');
        $userId = $this->Auth->user('id');
      
        foreach ($_REQUEST as $key => $value) {
            $receivedData[$key] = $_REQUEST[$key];
        }
        

        // The user has changed one or more data-items
        if (!empty($receivedData['password'])) {
            $this->User = ClassRegistry::init('User');
            $tempreceivedData['User'] = $receivedData;
            $this->User->set($tempreceivedData);
            if (!$this->User->validates()) {  
                
                $validationErrors[0] = $this->User->validationErrors;
            }
        }
        $tempreceivedData1['Investor'] = $receivedData;
        $this->Investor->set($tempreceivedData1);
        if (!$this->Investor->validates() || !$this->User->validates()) {
            $validationErrors[1] = $this->Investor->validationErrors;
            $this->set('validationErrors', json_encode($validationErrors));
        }
        else {
            // Validation passed, so time to save the data

                if (!empty($receivedData['password'])) {
                    $this->User->id = $userId;
                    $this->User->save(array('password' => $receivedData['password']), $validate = false);
                }

                $this->Investor->id = $investorId;
                $this->Investor->save($receivedData, $validate = false);
                // UPDATE THE SESSION DATA
            

            //We do again the query to get correctly the data on plugins.
            $resultInvestorTemp = $this->Investor->find('all', array('conditions' => array('id' => $investorId),
                'recursive' => -1,
            ));
            $this->set('resultUserData', json_encode($resultInvestorTemp));
        }
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
        $companyId = $this->request->data['companyId'];
        $investorId = $this->Auth->user('Investor.id');
        $companyFilterConditions = array('id' => $companyId);
        $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

        if (empty($companyResults)) {
            $error = true;
            return;
        }

        
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_LOGIN_SEQUENCE);

        $newComp = $this->companyClass($companyResults[$companyId]['company_codeFile']);
        $newComp->setUrlSequence($urlSequenceList);
        $configurationParameters = array('tracingActive' => true,
            'traceID' => $this->Auth->user('Investor.investor_identity'),
        );
        $newComp->defineConfigParms($configurationParameters);
        $newComp->generateCookiesFile();
        $userInvestment = $newComp->companyUserLogin($this->request->data['userName'], $this->request->data['password']);

        if (!$userInvestment) {                                                 // authentication error
            // load the list of all companies for display purposes
            $companyFilterConditions = array('id >' => 0);  // Load ALL company data as array
            $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

            $linkedaccountFilterConditions = array('investor_id' => $investorId, 
                                        'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);
            $linkedAccountResult = $this->Linkedaccount->getLinkedaccountDataList($linkedaccountFilterConditions);
            $newComp->deleteCookiesFile();
            $this->set('linkedAccountResult', $linkedAccountResult);
            $this->set('companyResults', $companyResults);
            $this->set('action', "error");      // add a new account

            $this->render('linkedaccountsList');
            //$this->render('accountLinkingError');

        } else {
            if ($this->Linkedaccount->createNewLinkedAccount($companyId, $this->Auth->user('Investor.id'), $this->request->data['userName'], $this->request->data['password'])) {
                $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_LOGOUT_SEQUENCE);
                $newComp->setUrlSequence($urlSequenceList);
                $newComp->companyUserLogout();
                $newComp->deleteCookiesFile();
// load the list of all companies for display purposes
                $companyFilterConditions = array('id >' => 0);  // Load ALL company data as array
                $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);

                $linkedaccountFilterConditions = array('investor_id' => $investorId, 
                                                'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);
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
        $error = false;
        if (!$this->request->is('ajax')) {
            $this->layout = "azarus_private_layout";
            /*throw new
            FatalErrorException(__('You cannot access this page directly'));*/
        }
        else {
            $this->layout = 'ajax';
            $this->disableCache();
        }

        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');    // Load the "Company" model

        $investorId = $this->Auth->user('Investor.id');
        $conditions = array('investor_id' => $this->Auth->user('Investor.id'), 
                        'linkedaccount_status' => WIN_LINKEDACCOUNT_ACTIVE);

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

    
    function changePasswordLinkedAccount() {
        if (!$this->request->is('ajax')) {
            throw new
            FatalErrorException(__('You cannot access this page directly'));
        }
        else {
            $this->layout = 'ajax';
            $this->disableCache();
 
            $linkaccountId = $this->request->data['id'];
            $newPass = $this->request->data['password'];
            $user = $this->request->data['username'];       
            
           
            $linkaccountData = $this->Linkedaccount->getData(['id' => $linkaccountId], ['company_id']);
            $companyId = $linkaccountData[0]['Linkedaccount']['company_id'];
            
          
            //try login for thew new password
            $companyFilterConditions = array('id' => $companyId);
            $companyResults = $this->Company->getCompanyDataList($companyFilterConditions);
            $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_LOGIN_SEQUENCE);
            $newComp = $this->companyClass($companyResults[$companyId]['company_codeFile']);
            $newComp->setUrlSequence($urlSequenceList);
            $configurationParameters = array('tracingActive' => true,
                'traceID' => $this->Auth->user('Investor.investor_identity'),
            );
            $newComp->defineConfigParms($configurationParameters);
            $newComp->generateCookiesFile();
            $userLogin = $newComp->companyUserLogin($user, $newPass);
            //echo $userLogin;
            //If we can login, change the password
            if($userLogin){
                $this->Linkedaccount->changePasswordLinkaccount($linkaccountId, $newPass);
                $this->set('changePasswordResponse', '1');
            } 
            else {
                $this->set('changePasswordResponse', '0');
            }
        }
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


    
    
    /** THIS METHOD SHALL NOT BE ACCESIBLE TO INVESTOR PROFILE. DEFINE USING CONFIG DATA
     * This methods terminates the HTTP GET.
     * Format GET /api/1.0/investors.json&_fields=x,y,z
     * Example GET /api/1.0/investors.json&investor_country=SPAIN&_fields=investor_name,investor_surname
     * 
     * @param -
     * 
     */
    public function v1_index() {
 echo "AAAAAAAAAAAAAAAAAAAAA";

        if (empty($this->listOfFields)) {
            $this->listOfFields =   ['Investor.investor_name', 'Investor.investor_surname',      
                                     'Investor.investor_DNI', 'Investor.investor_dateOfBirth', 
                                     'Investor.investor_address2',  'Investor.investor_address1',
                                     'Investor.investor_city',  'Investor.investor_telephone',
                                     'Investor.investor_postCode',  'Investor.investor_email',
                                     'Investor.investor_country', 'Investor.investor_language'
                                    ];
        } 
echo "bbbb";
        foreach ($this->listOfFields as $field) {
            
$tempField = explode("_", $field);
echo "field = $field<br>";
$this->print_r2($tempField);
$p = count($tempField);
echo "p = $p<br>";

            if (count($tempField) == 2)) {
                echo "Added<br>2";
                $this->listOfFields[] = "Check.check_" . $tempField[1]; 
            }  
        }  
  var_dump($this->listOfFields);    
        $this->Investor->contain('Investor', 'Check');
        $results = $this->Investor->find("all", $params = ['conditions' => $this->listOfQueryParams,
                                                          'fields' => $this->listOfFields,
                                                          'recursive' => 0]);

        $numberOfResults = count($results);    
echo "ccc";
        $j = 0;
        foreach ($results as $resultItem) { 
            foreach ($resultItem['Investor'] as $key => $value) {
                if ($key === 'id') {   
                    continue;
                } 
                $rootName = explode("_", $key, 2);

                if ($numberOfResults == 1) {
                    $apiResult[$key]['value'] = $value;  
                    $apiResult[$key]['read-only'] = $resultItem['Check']['check_' . $rootName[1]];    
                }
                else {
                    $apiResult[$j][$key]['value'] = $value;  
                    $apiResult[$j][$key]['read-only'] = $resultItem['Check']['check_' . $rootName[1]];   
                } 
            }
            $j++;
        }
 
        $this->Investor->apiVariableNameOutAdapter($apiResult);
        $this->set(['data' => $apiResult,
                  '_serialize' => ['data']]
                   ); 
    }     
    
    /**
     * This methods terminates the HTTP GET.
     * Format GET api/v1/investors/[investorId]&fields=x,y,z
     * Example GET api/v1/investors/1.json&_fields=investor_name,investor_surname
     * 
     * @param integer $id The database identifier of the requested 'Investor' resource
     * 
     */
    public function v1_view($id){
        // somehow, $id is not loaded
        if ($this->investorId <> $this->request->params['id']) {        
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }
        $id = $this->request->params['id'];
        if (empty($this->listOfFields)) {
            $this->listOfFields =   ['Investor.investor_name', 'Investor.investor_surname',      
                                     'Investor.investor_DNI',  'Investor.investor_dateOfBirth', 
                                     'Investor.investor_address1',  'Investor.investor_address1', 
                                     'Investor.investor_city', 'Investor.investor_telephone',
                                     'Investor.investor_postCode', 'Investor.investor_email',
                                     'Investor.investor_country', 'Investor.investor_language'
                                    ];
        }

        foreach ($this->listOfFields as $field) {
            $tempField = explode("_", $field);
            if (count($tempField) == 2) {
                $this->listOfFields[] = "Check.check_" . $tempField[1];
            }       
        } 

        $this->Investor->contain('Investor', 'Check');
        $result = $this->Investor->findById($id, $fields = $this->listOfFields, $recursive = 0);

        if (!empty($result)) {
            foreach ($result['Investor'] as $key => $value) {
                $apiResult[$key]['value'] = $value;                 
                if ($key === 'id') {
                     continue;
                } 
                $rootName = explode("_", $key, 2); 
                $apiResult[$key]['read-only'] = $result['Check']['check_' . $rootName[1]];    
            } 
        }
        
        $this->Investor->apiVariableNameOutAdapter($apiResult);
        $this->set(['data' => $apiResult,
                  '_serialize' => ['data']]
                   );          
    }     
    

   
    /** 
     * This methods handles the HTTP PATCH message
     * Format PATCH /api/1.0/investors/[investorId].json ....
     * Example PATCH /api/1.01/investors/1.json
     * fields are conveyed in the message body as json
     * 
     * @param integer $id The database identifier of the requested 'Investor' object
     * 
     */
    public function v1_edit($id) { 
        // somehow, $id is not loaded
        if ($this->investorId <> $this->request->params['id']) {        
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }
        
        $data = $this->request->data;
        
        if (!empty($data)) {
            $dataNew = $data['data'];

            $dataNew['id'] = $this->request->params['id']; 

            $this->Investor->apiVariableNameInAdapter($dataNew);
            $result = $this->Investor->save($dataNew, $validate = true);
        }
        
        if (!($result)) {
            $validationErrors = $this->Investor->validationErrors;
            $this->Investor->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('NO_WRITE_ACCESS', 
                                                        "It is not allowed to modify read-only fields", 
                                                        $validationErrors);
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(403);                                   // 403 Forbidden  
        }
        else {
            if (!empty($result['Investor']['requireNewAccessToken'])) {
                $apiResult = ['requireNewAccessToken' => true];
                $this->Investor->apiVariableNameOutAdapter($apiResult);
                $resultJson = json_encode($apiResult);
            }
            else {
                $this->response->statusCode(204);
            }
        }
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;               
    }     

    /** 
     * Simple version is OK, ie. for defining *manually* new investors
     * This methods terminates the HTTP POST for defining a new investor.
     * Format POST /api/1.0/investors.json
     * Example POST /api/1.0/investors.json
     * All the userdata is located in the POST body as a json 
     * 
     * @return mixed false or the database identifier of the new 'Investor' object
     */
    public function v1_add() { 
 
        if ($this->roleName <> "superAdmin") {        
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }   
        
        $this->AppModel = ClassRegistry::init('AppModel');
        $data = $this->request->data;                                           // holds all the new investor data
        $newData = $data['data'];
 
        $this->AppModel->apiVariableNameInAdapter($newData);    
        $result = $this->Investor->api_addInvestor($newData);
        
        if (!($result)) {
            $validationErrors = $this->Investor->validationErrors;              // Cannot retrieve all validation errors
            $this->Investor->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('CANNOT_CREATE_INVESTOR_OBJECT', 
                                                        "The system encountered an undefined error, try again later on");
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(500);                                    
        }
        else { // create the links
            $account['feedback_message_user'] = 'Account successfully created.';
            $account['data']['links'][] = $this->generateLink("investors", "edit", $result . '.json'); 
            $account['data']['links'][] = $this->generateLink("investors", "delete" , $result . '.json');             
            $resultJson = json_encode($account);           
            $this->response->statusCode(201);
        }
        
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;               
    }          
    
  
    /** 
     * Simple version is OK, i.e. for deling *manually* an investor
     * This methods terminates the HTTP POST for defining a new investor.
     * Format DELETE /api/1.0/investors.[investorId].json
     * Example DELETE /api/1.0/investors/23.json
     * 
     * @return boolean
     * @throws UnauthorizedException
     */
    public function v1_delete($id) { 

        if ($this->roleName <> "superAdmin") {        
            throw new UnauthorizedException('You are not authorized to access the requested resource');      
        }         
        $id = $this->request->params['id'];
        $result = $this->Investor->api_deleteInvestor($id);
        
        if (!($result)) {
            $validationErrors = $this->Investor->validationErrors;              // Cannot retrieve all validation errors
            $this->Investor->apiVariableNameOutAdapter($validationErrors);

            $formattedError = $this->createErrorFormat('CANNOT_DELETE_INVESTOR_OBJECT', 
                                                        "The system encountered an undefined error, try again later on");
            $resultJson = json_encode($formattedError);
            $this->response->statusCode(500);                                    
        }
        else { // create the links
            $this->response->statusCode(200);
            $return['feedback_message_user'] = "Account has been deleted";
            $resultJson = json_encode($return);
        }
        
        $this->response->type('json');
        $this->response->body($resultJson); 
        return $this->response;               
    }          
    
}
