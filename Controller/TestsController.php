<?php
/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
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
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2017-04-07
 * @package
 *

  2017-04-07	  version 2017_0.1
  function to copy a userdata photo to admin user.				[OK]

 */

/** Include path * */
require_once(ROOT . DS . 'app' . DS . 'Vendor' . DS . 'autoload.php');
//require_once(ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'php-bondora-api-master' . DS .  'bondoraApi.php');

/** PHPExcel_IOFactory */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
App::uses('CakeEventListener', 'Event');

//App::import('Vendor', 'readFilterWinvestify', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS. 'Reader'. DS . 'IReadFilterWinvestify.php'));

use Petslane\Bondora;

class TestsController extends AppController {

    var $name = 'Tests'; 
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Tooltipincompany', 'Tooltip', 'Test', 'Queue2', 'Data', 'Investor', 'Userinvestmentdata', 'Company',
        'Linkedaccount', 'Accountowner');
    var $error;

    public $components = array('ApiAdapter'); 

    function beforeFilter() {
        parent::beforeFilter();

        Configure::write('debug', 2);        
        $this->autoRender = false; 
        
        //$this->Security->requireAuth();
        $this->Auth->allow(array('convertExcelToArray', "bondoraTrying", "editCheck", "precheck",
            "testAddPayment", "pdfTest", "linkedaccount",
            "hashTest", "testDateDiff",
            "memoryTest3", "memoryTest2", "hashTest", 'tooltip'));       
    }

    
    
    public function multilogin() {

        $companyId = 25;
        
        $this->Company = ClassRegistry::init('Company');
        $multiAccount = $this->Company->getData(array('id' => $companyId), array('company_technicalFeatures', 'company_codeFile'));
        //Login process
        $urlSequenceList = $this->Urlsequence->getUrlsequence($companyId, WIN_LOGIN_SEQUENCE);
        $newComp = $this->companyClass($multiAccount[0]['Company']['company_codeFile']);
        $newComp->setUrlSequence($urlSequenceList);
        $configurationParameters = array('tracingActive' => true,
            'traceID' => $this->Auth->user('Investor.investor_identity'),
        );
        $newComp->defineConfigParms($configurationParameters);
        $newComp->generateCookiesFile();
        $accounts = $newComp->companyUserLoginMultiAccount($username, $password);
    }

    public function linkedaccount() {

        $companyId = 25;
        /*Configure::write('Investor_id',290);
        
        $tooltips = $this->Tooltip->getTooltip(array(ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME), $locale = 'en');
        $accounts['tooltip_display_name'] = $tooltips[ACCOUNT_LINKING_TOOLTIP_DISPLAY_NAME];
        $accounts['service_status'] = "ACTIVE";
        $accounts['service_status_display_message'] = "You are using the maximum number of linkedaccounts. If you like to link more accounts, please upgrade your subscription";
        $accounts = $accounts + $this->Accountowner->api_readAccountowners(WIN_LINKEDACCOUNT_ACTIVE);
        $this->Accountowner->apiVariableNameOutAdapter($accounts['data']);
        foreach($accounts['data'] as $key => $account){
            $this->Accountowner->apiVariableNameOutAdapter($accounts['data'][$key]);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'edit', $accounts['data'][$key]['id']);
            $accounts['data'][$key]['links'][] = $this->generateLink('linkedaccounts', 'delete', $accounts['data'][$key]['id']);

        }*/
        $accounts = $this->Linkedaccount->api_precheck(290, $companyId, $username, $password);
        
        foreach ($accounts['accounts'] as $key => $account){
            $this->Linkedaccount->apiVariableNameOutAdapter($accounts['accounts'][$key]);
        }
        $accounts = json_encode($accounts);
        echo $accounts;
        return $accounts;

        /*$accounts = $this->Linkedaccount->api_precheck($companyId, $username, $password);
        print_r($accounts);
exit;*/
        
        
        
        /*$data['Accountowner'] = array('company_id' => 25,
            'investor_id' => 1,
            'accountowner_username' => $username,
            'accountowner_password' => $password,
            'accountowner_status' => WIN_ACCOUNTOWNER_ACTIVE,
        );

        $this->Accountowner->save($data);*/
    } 

    
    
    
    
    
    
    public function editCheck() {
    // Generate an event

//echo $this->Investor->createInvestorReference('+443344556688', 'myNewEmailAddress891@gmail.com'); 
//exit; 
    $event = new CakeEvent("Model.Email.SendMessage", $this, 
                            array(
                                'model' => "Email",
                                'isFinalEvent' => false,
                                'investor_userReference' => "39048098ab409be490A", 
                                'userIdentification' => 8831445566,
                                'messageContent'        => __('Your account on platform XXXX1'),
                                'modelData' => ['Email' => ['id' => 2147,
                                    'email_senderEmail' => "antoine@winvestify.com",
                                    'email_senderName' => 'Antoine',
                                    'email_senderSurname' => 'de Poorter',
                                    'email_senderSubject' => 'feature',
                                    'email_senderText' => "this is a dummy test",
                                    'email_senderTelephone' => "+3344556677",
                                    'id' => 2147
                                    ]],
                           //     'id' => 1
                                )
                                    );
    
 echo __FILE__ . " " . __LINE__ . " \n<br>";
    $this->getEventManager()->dispatch($event);
 echo __FILE__ . " " . __LINE__ . " \n<br>";     
exit;                                
                                
                                
    $fields = get_class_vars('DATABASE_CONFIG');
 echo __FILE__ . " " . __LINE__ . " \n<br>";
    var_dump($fields);
    
    $fields1 = get_class_vars($this->request);
echo __FILE__ . " " . __LINE__ . " \n<br>";
    var_dump($fields1);   
    
    
    App::Import('ConnectionManager');
    $ds = ConnectionManager::getDataSource('default');
    $dsc = $ds->config;
echo __FILE__ . " " . __LINE__ . " \n<br>";
    var_dump($dsc);
    
    App::uses('ConnectionManager', 'Model');
    $dataSource = ConnectionManager::enumConnectionObjects();
echo __FILE__ . " " . __LINE__ . " \n<br>";
    var_dump($dataSource);
    
    
    exit;
    
    
    
    
    
    
    
    
    
    
        Configure::load('endpointsConfig.php', 'default'); 
        $endpoints = Configure::read("endpoints");         
        
    echo __FILE__ . " " . __LINE__ . "\n";   
    $this->print_r2($endpoints);

    

    exit;
    }

    
    
    public function tooltip() {

        $tooltip = $this->Tooltip->getTooltip(array(15, 16, 17, 18, 19, 20, 49, 50, 51, 52, 54, 55), 'en', 25);
        $this->print_r2($tooltip);

        $tooltip = $this->Tooltip->getTooltip(array(15, 16, 17, 18, 19, 20, 49, 50, 51, 52, 54, 55), 'en', 24);
        $this->print_r2($tooltip);

        $tooltip = $this->Tooltip->getTooltip(array(15, 16, 17, 18, 19, 20, 49, 50, 51, 52, 54, 55), 'es', 25);
        $this->print_r2($tooltip);
        $tooltip = $this->Tooltip->getTooltip(array(15, 16, 17, 18, 19, 20, 49, 50, 51, 52, 54, 55), 'es', 24);
        $this->print_r2($tooltip);

        $tooltip = $this->Tooltip->getTooltip(array(38, 48, 39, 40, 43), 'en');
        $this->print_r2($tooltip);
        
        $tooltip = $this->Tooltip->getTooltip(array(38, 48, 39, 40, 43), 'es');
        $this->print_r2($tooltip);
    }
    
 

    
 
  
    
    public function recursiveSearchIncoming() {   
    Configure::write('debug', 2);        
    $this->autoRender = false;   
 
    $jsonString = '{
  "service_status": "ACTIVE",
  "data": [
    {
      "id": 325938,
      "service_status": "NOT_ACTIVE",
      "linkedaccount_status": "ACTIVE",
      "linkedaccount_visual_state": "ANALYZING",
      "polling_type": "NOTIFICATION_CHECK",
      "links": [
        {
          "metadata_type_of_document": "DNI_FRONT",
          "linkedaccount_status": "NON_EXISTENT_VALUE"
        }
      ]
    },
    {
      "id": 432456,
      "metadata_type_of_document": "DNI_BACK",
      "service_status": "SUSPENDED",
      "polling_type": "LINKEDACCOUNT_CHECK",
      "linkedaccount_status": "NOT_ACTIVE",
      "linkedaccount_visual_state": "QUEUED",
      "linkedaccount_username": "antoine@gmail.com"
    },
    {
      "id": 432458,
      "metadata_type_of_document": "BANK_CERTIFICATE",
      "polling_type": "PMESSAGE_CHECK",
      "linkedaccount_status": "UNDEFINED",
      "linkedaccount_visual_state": "MONITORED"
    }
  ]
}';
echo "Using a component<br>";     
    $jsonArray = json_decode($jsonString, true);
    pr($jsonArray);    
    $this->ApiAdapter->normalizeIncomingJson($jsonArray); 
    pr($jsonArray);   
   
     

    $jsonString = '{
  "service_status": 10,
  "data": [
    {
      "id": 325938,
      "service_status": 20,
      "linkedaccount_status": 1,
      "linkedaccount_visual_state": 10,
      "polling_type": 10,
      "links": [
        {
          "metadata_type_of_document": 10,
          "linkedaccount_status": 10
        }
      ]
    },
    {
      "id": 432456,
      "metadata_type_of_document": 20,
      "service_status": 30,
      "polling_type": 10,
      "linkedaccount_status": 2,
      "linkedaccount_visual_state": 20,
      "linkedaccount_username": "antoine@gmail.com"
    },
    {
      "id": 432458,
      "metadata_type_of_document": 30,
      "polling_type": 30,
      "linkedaccount_status": 0,
      "linkedaccount_visual_state": 30
    }
  ]
}';

    
 echo "Using a component<br>";     
    $jsonArray = json_decode($jsonString, true);
    pr($jsonArray);    
    $this->ApiAdapter->normalizeOutgoingJson($jsonArray); 
    pr($jsonArray);   
       
/*  
5c2de18a-d924-4c20-a398-0b7f6d15f83e
5c2de18a-2858-4e5b-8742-0b7f6d15f83e
5c2de18a-dc00-4551-9860-0b7f6d15f83e
*/    

  
    
    echo "MM";
    if( array_key_exists($key, $array) ){
        print("<br> ----------------- FOUND <u>{$key}</u> with value: {$array[$key]}");

        return array( $key => $array[$key] );

    }
    else if( !array_key_exists($key, $array) ){
        foreach ($array as $index   =>  $subarray){
                if( is_array($subarray) ){
                    print("<br> ************* <u>{$index}</u> is an ARRAY");
                    print("<br> ************* RE-SEACHING <u>{$index}</u> FOR : <u>{$key}</u>");
                    return search2($subarray, $key);
                }
        }
    }
}    
    
    
   

    public function deleteFromUser($investorId = null, $linkaccountsId = null) {

        $Prefilter = array('investor_id' => 290);                      //Find all linkaccount of the investor
        $Idlist = $this->Linkedaccount->getData($Prefilter, array('id'));
        print_r($Idlist);
        /* foreach($Idlist[''] as $id){ */
    }

    function hashTest() {

        $telephone = 615091091;
        $username = "eduardo@winvestify.com";

        $hashTelephone = hash("crc32", $telephone);
        $hashUsername = hash("crc32", $username);
        $uuid = $hashTelephone . $hashUsername;
        echo strlen($uuid);
        echo "    " . $uuid;
    }

    function memoryTest3() {
        $timeInit = microtime(true);
        foreach ($this->pruebaYield() as $y) {
            if ($y == 100000 || $y == 200000 || $y == 300000 || $y == 400000 || $y == 500000) {
                echo "!!!";
                echo memory_get_usage();
            }
        }
        echo "!!!";
        echo memory_get_usage();
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeInit;
        echo " time" . $time;
    }

    public function memoryTest2() {
        $timeInit = microtime(true);
        for ($i = 0; $i <= 600000; $i++) {
            $arrayprueba[] = $i;
            if ($i == 100000 || $i == 200000 || $i == 300000 || $i == 400000 || $i == 500000) {
                echo "!!!";
                echo memory_get_usage();
            }
        }
        echo "!!!";
        echo memory_get_usage();
        $timeEnd = microtime(true);
        $time = $timeEnd - $timeInit;
        echo " time" . $time;
    }

    public function readSize() {
        echo 'patata ';
        $size = filesize(APP . "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_6.xlsx");
        echo 'hola';
        echo $size;
        echo 'patata ';
        $size = filesize(APP . "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_7.xlsx");
        echo 'hola';
        echo $size;
        echo 'patata ';
        $size = filesize(APP . "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_8.xlsx");
        echo 'hola';
        echo $size;
        echo 'patata ';
        $size = filesize(APP . "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_9.xlsx");
        echo 'patata ';
        $size = filesize(APP . "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_10.xlsx");
        echo 'hola';
        echo $size;
        echo 'hola';
        echo $size;
    }

    var $dateFinish = "20171129";
    var $numberOfFiles = 0;

    public function pdfTest() {

// Include Composer autoloader if not already done.
        include 'Vendor/autoload.php';

// Parse pdf file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile('/home/eduardo/Downloads/inversion 3.pdf');
        $text = $pdf->getText();
        $data['A'] = trim($this->extractDataFromString($text, 'PROJECT NAME:', 'CONTRACT NUMBER:'));
        $data['B'] = trim($this->extractDataFromString($text, 'INTEREST RATE:', 'LOAN PURPOSE:'));
        echo $text;
        print_r($data);
    }

    public function mytest1() {
        $this->autoRender = false;
        Configure::write('debug', 2);
        $this->Investor->Behaviors->load('Containable');
        $this->Investor->contain('Linkedaccount');
        $result = $this->Investor->find("all", array("recursive" => 2,
            "conditions" => array("Investor.investor_identity" => "39048098ab409be490A"),
            array('Linkedaccount' => array('Linkedaccount.linkedaccount_statusExtended' => 10)
            ))
        );

        $companyNothingInProcess = [];
        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
// Find all the objects of Investor   
        $filterConditions = array('Investor.investor_identity' => "39048098ab409be490A");       // Me

        $this->Investor->Behaviors->load('Containable');
        $this->Investor->contain('Linkedaccount');         // Own model is automatically included
        $resultInvestorsData = $this->Investor->find("all", $params = array('recursive' => 2,
            'conditions' => $filterConditions));

        if (!isset($resultInvestorsData['Accountowner'])) {
            echo "No linked accounts<br/>";
            return [];
        }

        $accountOwnerIds = array();
        foreach ($resultInvestorsData[0]['Linkedaccount'] as $account) {
            if ($account['linkedaccount_status'] == 2) {
                $accountOwnerIds[] = ['id' => $account['id']];
            }
        }
        $this->print_r2($accountOwnerIds);
        if (empty($accountOwnerIds)) {
            echo "empty result to quit\n";
            return;
        }

        $filterConditions = array(
            "OR" => $accountOwnerIds,
            "AND" => array(array('linkedaccount_linkingProcess' => 1,
                    'linkedaccount_statusExtended' => 10
                )),
        );

        $linkedAccountsResults = $this->Linkedaccount->getLinkedaccountDataList($filterConditions);
        $this->print_r2($linkedAccountsResults);

        $companyNothingInProcess = array();
        foreach ($linkedAccountsResults as $key => $linkedAccountResult) {
            //In this case $key is the number of the linkaccount inside the array 0,1,2,3
            $companyNothingInProcess[] = $linkedAccountResult['Linkedaccount']['id'];
        }
        $this->print_r2($companyNothingInProcess);
    }


 
    function convertPdf() {
        // Parse pdf file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile('/var/www/html/compare_local/OSchedule.pdf');
        // Retrieve all pages from the pdf file.
        $pages = $pdf->getPages();
        // Loop over each page to extract text.
        $page_string = [];

        foreach ($pages as $page) {
            echo "<br>";
            $page_string[] = $page->getText();
            echo "<br>";
        }

        foreach ($page_string as $page) {
            echo $page;
            echo "<br>";
        }

        /* $investments = explode("%", $page_string[0]);

          foreach ($investments as $investment) {
          echo $investment;
          echo "<br>";
          } */
        //echo $investments[0];
        //echo $page_string;
        //$text = $pdf->getText();
        //echo $text;
    }

    function bondoraTrying() {
        $config = array(
            'auth' => array(
                'url_base' => 'https://www.bondora.com',
                'client_id' => 'ff77b8a268b7437db6ada6cc4542a2ca',
                'secret' => 'AodG9hU9nsYyNgBku3z503wcejJLK9DrN07pm7fnEbWjuZRw',
                'scope' => 'BidsEdit BidsRead Investments SmBuy SmSell',
            ),
            'api_base' => 'https://api.bondora.com',
        );
        $api = new Bondora\Api($config);

        // Get login url
        $url = $api->getAuthUrl();
        echo $url;

        if (empty($_GET["code"])) {
            echo "patata";
            header("Location: " . $url);
            echo $url;
        }
        $code = $_GET["code"];
        echo $code;
        // redirect user to $url. After login, user will be redirected back with get parameter 'code'
        // get token from 'code' provided after user successful login. Store access_token and refresh_token
        //$token_object = $api->getToken($code);
    }

 
    public function testAddPayment() {
        $this->Globalamortizationtable = ClassRegistry::init('Globalamortizationtable');
        $this->Amortizationtable = ClassRegistry::init('Amortizationtable');
        Configure::write('debug', 2);
        echo "start of method " . __METHOD__ . "<br/>";
        $this->autoRender = false;


        include APP . "Console/Command/ParseDataClientShell.php";

        $transactionData = ['transactionId' => 3242454534,
            'date' => "2018-07-05",
            'investment_loanId' => "1581870-01",
        ];

        $resultData = ['payment' =>
            [
                'payment_principalAndInterestPayment' => "9434.8",
                'payment_capitalRepayment' => "6000.40",
            //       'payment_regularGrossInterestIncome' => "434.40",
            ],
            'investment' =>
            [
                'investment_loanId' => "1581870-01",
                'id' => 5529,
                'investment_dateForPaymentDelayCalculation' => "2018-06-00",
            ],
        ];

        $companyData[0]['Company']['id'] = 24;

        $this->print_r2($transactionData);
        $this->print_r2($resultData);

        $myInd = new ParseDataClientShell();

        $result = $myInd->repaymentReceived($transactionData, $resultData);

        echo "result = $result\n";
        /*
          $sliceId = 1373;
          $nextPendingInstalmentDate = $this->Amortizationtable->getNextPendingPaymentDate($sliceId);
          echo "nextPendingInstalmentData = $nextPendingInstalmentDate";


         */
    }


    /**
     *  to test the new API for location
     * 
     * 
     */
    function testChildModel() {
        $this->autoRender = false;
        Configure::write('debug', 2);

        $this->Investment = ClassRegistry::init('Investment');


        $filteringConditions = array('id >' => 1);
        echo "filter = ";
        print_r($filteringConditions);
        $result = $this->Investment->find("all", array('conditions' => $filteringConditions,
            'recursive' => -1,
            'fields' => array('id', 'linkedaccount_id'))
        );

        $this->print_r2($result);
        exit;


        $filterConditions = array('id' => 2105);
        print_r($filterConditions);
        $resultInvestmentData = $this->Investment->find("all", $params = array('recursive' => 1,
            'conditions' => $filterConditions));

        $myInstance = $resultInvestmentData[0]['Investment']['id'];
        $myInstance = 55;
        echo $myInstance;
//        $this->print_r2($resultInvestmentData);


        $result = $this->Investment->hasChildModel($myInstance, 'Investmentslice');
        echo "FINAL";
        $this->print_r2($result);
    }

    
}
