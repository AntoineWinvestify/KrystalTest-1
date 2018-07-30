
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
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

//App::import('Vendor', 'readFilterWinvestify', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS. 'Reader'. DS . 'IReadFilterWinvestify.php'));

use Petslane\Bondora;

/* use PhpOffice\PhpSpreadsheet\IOFactory;
  use PhpOffice\PhpSpreadsheet\Cell; */

class TestsController extends AppController {

    var $name = 'Tests';
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Test', "Queue2", "Data", "Investor", "Userinvestmentdata", "Company", "Urlsequence", "Globalcashflowdata", "Linkedaccount");
    var $error;

    function beforeFilter() {
        parent::beforeFilter();

    Configure::write('debug', 2); 

        //$this->Security->requireAuth();
        $this->Auth->allow(array('convertExcelToArray', "convertPdf", "bondoraTrying",
            "analyzeFile", 'getAmount', "dashboardOverview", "arrayToExcel", "insertDummyData", "downloadTimePeriod",
            "testLocation", "mytest", "mytest1", "readSize", "testReadFullAmortizationTable", "testAddPayment", "testAddPayment",
            "testDateDiff", "xlsxConvert", "read", "pdfTest", "testLocation", "testChildModel", "mytest", "mytest1"));
    }
    
    
      public function readSize(){
          echo 'patata ';
          $size = filesize( APP .  "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_6.xlsx");
          echo 'hola';
          echo $size;
                    echo 'patata ';
          $size = filesize( APP .  "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_7.xlsx");
          echo 'hola';
          echo $size;
                    echo 'patata ';
          $size = filesize( APP .  "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_8.xlsx");
          echo 'hola';
          echo $size;
                    echo 'patata ';
          $size = filesize( APP .  "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_9.xlsx");
                    echo 'patata ';
          $size = filesize( APP .  "files/dashboard2/39048098ab409be490A/20180508/899/twino/transaction_1_10.xlsx");
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
    
    
    public function mytest1(){
    $this->autoRender = false;
    Configure::write('debug', 2);    
    $this->Investor->Behaviors->load('Containable');
    $this->Investor->contain('Linkedaccount');
    $result = $this->Investor->find("all", 
                     array ("recursive" => 2, 
                            "conditions" => array("Investor.investor_identity" => "39048098ab409be490A"), 
                     array( 'Linkedaccount' => array('Linkedaccount.linkedaccount_statusExtended' => 10) 
                     ))
            );   

    $companyNothingInProcess = [];
        $this->Investor = ClassRegistry::init('Investor');
        $this->Linkedaccount = ClassRegistry::init('Linkedaccount');
// Find all the objects of Investor   
	$filterConditions = array('Investor.investor_identity' => "39048098ab409be490A");       // Me

	$this->Investor->Behaviors->load('Containable');
	$this->Investor->contain('Linkedaccount');  							// Own model is automatically included
	$resultInvestorsData = $this->Investor->find("all", $params = array('recursive'     => 2,
                                                                            'conditions'    => $filterConditions));

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
            "AND" => array( array ('linkedaccount_linkingProcess' => 1, 
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
    
    
    public function mytest(){
    $this->autoRender = false;
    Configure::write('debug', 2);     
    $this->Investmentslice = ClassRegistry::init('Investmentslice');      
    $this->Globalamortizationtable = ClassRegistry::init('Globalamortizationtable');  
                
    $filterConditions = array ("date" => "2018-01-11",
                               "investment_id" => 1022);
                
    $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');  
                if ($this->Userinvestmentdata->deleteAll($filterConditions, $cascade = false, $callbacks = false)) {
                echo __FILE__ . " " . __LINE__ . " Userinvestmentdata deleted ";                 
            }

    
    
    
 //       $result = $this->Globalamortizationtable->saveAmortizationtable($amortizationData, 3);   
    

   
    $result = $this->Globalamortizationtable->find("all", 
                                            array ("recursive" => 2, 
                                                    "conditions" => array("Globalamortizationtable.id" => 17 ))
            );
    $this->print_r2($result);    
    
    echo "ssss<br>";
        $this->Investmentslice->Behaviors->load('Containable');
        $this->Investmentslice->contain('Globalamortizationtable');
        $result1 = $this->Investmentslice->find("all", 
                                            array ("recursive" => 2,
                                                     "conditions" => array("Investmentslice.id" => 100 )));
    $this->print_r2($result1); 

    foreach ($result1[0]['Globalamortizationtable'] as $GlobalamortizationtableIndex) {
        echo "id = " . $GlobalamortizationtableIndex['id'] . "<br/>";
        echo "scheduledDate " . $GlobalamortizationtableIndex['globalamortizationtable_scheduledDate'] . "<br/>";
    }
    

    echo "Finished<br/>";   
    }
   
    
    
    
    
    
    
     public function mytestOld(){
         
     $this->autoRender = false;
         
         
   
        $this->Queue2->addToQueueDashboard2("39048098ab409be490A" , 
                                    $queueInfo= null, 
                                    $queueStatus = 11, 
                                    $queueId = null, 
                                    $queueType = 1);       
         
         
         
 //        WIN_ACTION_ORIGIN_ACCOUNT_LINKING', 1);
//  define('WIN_ACTION_ORIGIN_REGULAR_UPDATE'
         
     
           
 //           $this->Linkedaccount->deleteLinkedaccount($filterConditions, WIN_USER_INITIATED);



//   $this->Linkedaccount->createNewLinkedAccount(3, 1, "myUserName", "myPassword");

//$this->Linkedaccount->disableLinkedAccount(array('investor_id' => 1), WIN_USER_INITIATED);
//$this->Linkedaccount->enableLinkedAccount(array('investor_id' => 1, 'company_id' => 3), WIN_USER_INITIATED);

    }   
    

        private function extractDataFromString($input, $search, $separator, $mandatory = 0) {

        $position = stripos($input, $search);
        if ($position !== false) {  // == TRUE
            if ($mandatory == 2){    
                return "global_" . mt_rand();
            }
            $start = $position;
            $length = strlen($search);
        }
        else { // FALSE
            $start = 0;
            $length = 0;
            
            if ($mandatory == 1){    
                return "global_" . mt_rand();
            }
        }       

        $position1 = stripos($input, $separator);
        if ($position1 !== false) {  // == TRUE
            $length1 = $position1;
        }
        else { // FALSE
            $length1 = 100;                 // ficticious value
        }       
        $start = $start + $length;
        $finish = $length1 - $start;
        return substr($input, $start, $finish) ;
    }

    public function xlsxConvert() {
        echo 'Inicio';

        $unoconv = Unoconv\Unoconv::create();
        echo APP .  "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS . 'test.xlsx';
        $meh = fopen (APP .  "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS . 'test.xlsx', "r+");
        echo fread($meh);
         //$unoconv->transcode('\home ' . DS . 'eduardo' . DS . 'Downloads' . DS . 'testmenor.xlsx', 'pdf','\home' . DS . 'eduardo' . DS . 'Downloads' . DS . 'testmenorConvertido.pdf');
        $unoconv->transcode(APP . "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS  . 'test.xlsx', 'pdf', APP . "files" . DS ."investors" . DS . "39048098ab409be490A" .DS  . "20180116" . DS  . 'testConvertido.pdf');

        echo 'Fin';
    }

    public function read() {
        $file = "test.xlsx";
        $finfo = finfo_open();
        $fileinfo = finfo_file($finfo, $file, FILEINFO_MIME);
        finfo_close($finfo);
        print_r($fileinfo);
        
        
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));
        
        $inputFileType = 'Excel2007';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
         $objPHPExcel = $objReader->load($file);
        $worksheet = $objPHPExcel->getActiveSheet();
        
        foreach ($worksheet->getRowIterator() as $row) {
            echo 'Row number: ' . $row->getRowIndex() . "\r\n";
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (!is_null($cell)) {
                    echo 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
                }
            }
        }
        
    }

    public function xlsxRead() {
        //$file =  APP  . "files" . DS . "investors" . DS . "39048098ab409be490A" . DS . "20171217" . DS . "898" . DS . "bondora" . DS . "investment_1_1.xlsx";

        $file = APP . "investment_1.csv";

        $finfo = finfo_open();
        $fileinfo = finfo_file($finfo, $file, FILEINFO_MIME);
        finfo_close($finfo);
        echo "///////////////////////////////////" . $fileinfo;

        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        //WE MUST CLEAR CSV OF SPECIAL CHARACTERS
        $csv = fopen($file, "r");
        $csvString = mb_convert_encoding(fread($csv, filesize($file)), "UTF-8"); //Convert special characters
        fclose($csv);
        $csv = fopen($file, "w+");   //Rewrite old csv
        fwrite($csv, $csvString);
        fclose($csv);


        $inputFileType = 'CSV';
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setDelimiter(';');
        $objPHPExcel = $objReader->load($file);
        $worksheet = $objPHPExcel->getActiveSheet();
        foreach ($worksheet->getRowIterator() as $row) {
            echo 'Row number: ' . $row->getRowIndex() . "\r\n";
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
            foreach ($cellIterator as $cell) {
                if (!is_null($cell)) {
                    echo 'Cell: ' . $cell->getCoordinate() . ' - ' . $cell->getValue() . "\r\n";
                }
            }
        }
    }

    function clearCsv($string) {
        $not_permited = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permited = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        return str_replace($not_permited, $permited, $string);
    }

    public function downloadTimePeriod($dateMin, $datePeriod) {
        $dateMin = "20090101";
        $datePeriod = 120;
        echo "start" . HTML_ENDOFLINE;
        do {
            if ($this->numberOfFiles == 0) {
                $this->dateInit = date("Ymd", strtotime($this->dateFinish . " " . -$datePeriod . " days")); //First init date must be Finish date - time period
                echo "Empiezo en " . $this->dateInit . " Termino en " . $this->dateFinish . " ";
                $this->numberOfFiles++;
                echo $this->numberOfFiles . HTML_ENDOFLINE;
            } else {
                $this->dateFinish = date("Ymd", strtotime($this->dateInit . " " . -1 . " days")); //Next finish date will we the previous day of the last Init date
                $this->dateInit = date("Ymd", strtotime($this->dateInit . " " . -$datePeriod . " days"));
                if (date($this->dateInit) < date($dateMin)) {
                    $this->dateInit = date($dateMin); //Condition for dont go a previus date than $dateMin;
                }
                echo "Otro Empiezo en " . $this->dateInit . " Termino en " . $this->dateFinish . " ";
                $this->numberOfFiles++;
                echo $this->numberOfFiles . HTML_ENDOFLINE;
            }
        } while (date($this->dateInit) > date($dateMin));


        /* if($this->dateInit <= date("Ymd", $dateMin)){
          return false; //End period download
          }
          return true; //Continue period download */
    }

    function arrayToExcel($array, $excelName) {
        /*$array = array("market" => 1, "q" => 2, "a" => 3, "s" => 4, "d" => 5, "f" => 6, "e" => 7, "r" => 8, "t" => 9, "y" => 11, "u" => 12, "i" => 13, "o" => 14, "p" => 15, "l" => 16);
        $excelName = "prueba";*/
        $keyArray = array();
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        foreach ($array as $key => $val) {
            $keyArray[] = $key;
        }

        $filter = null;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle($excelName);

        $objPHPExcel->setActiveSheetIndex(0)
                ->fromArray($keyArray, NULL, 'A1')
                ->fromArray($array, NULL, 'A2');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($excelName);
        exit;
    }

    /**
     * syntax;  {DOMAIN}/tests/showUserData/2898786785624/-1
     * 
     * 	Show the user's data in a dashboard of Admin user
     * 
     *  @param 	string	$userIdentity   The unique identification of the user
     *  @param      $integer "photonumber. 0 = most recent photo, -1 one earlier, -2 two earlier etc.
     *  @return 	boolean true:  photo has been copied to current user's dashboard
     *  Another way is to "impersonate the user" copy his "auth profile and access his dashboard page. Advantage: no screwup 
     *  potential statistics functions
     */
    function showUserData($userIdentity, $number) {

        $this->autoRender = false;

        $this->layout = 'ajax';
        $this->disableCache();
        /*
         * THIS DOES NOT WORK
          $userIdentity = "41d0934670r943aed954932f";

          $investorFilterConditions = array('Investor.investor_identity' => $userIdentity);
          $investorResults = $this->Investor->find("first", array('conditions'  => $investorFilterConditions,
          'recursive' => 0,
          ));
          unset($investorResults['User']['password']);
          $temp = array();
          $temp['User'] = $investorResults['User'];
          $temp['User']['Investor'] = $investorResults['Investor'];

          $this->Session->write('AuthOriginal', $this->Session->read('Auth'));
          $this->Session->write('Auth', $temp);
          $this->print_r2($this->Session->read());
          exit;
         */

        $investorIdentity = $this->Auth->user('Investor.investor_identity');
        $dataFilterConditions = array('data_investorReference' => $userIdentity);

        $dataResults = $this->Data->find("all", array('conditions' => $dataFilterConditions,
            'order' => array('Data.id DESC'),
            'recursive' => -1,
        ));

        $absNumber = abs($number);

        if (array_key_exists($absNumber, $dataResults)) {
            $data = array('data_investorReference' => $investorIdentity,
                'data_JSONdata' => $dataResults[$absNumber]['Data']['data_JSONdata']
            );
            $this->Data->save($data, $validate = true);
            echo "Data is now available in Dashboard";
        } else {
            echo "Nothing found, try again with other data";
            exit;
        }
    }

  
    /**
     *  to test the new API for location
     * 
     * 
     */
    function testLocation() {
        $this->autoRender = false;
        Configure::write('debug', 2); 
        $accessKey = "40d49470983cedfb136010af6c2c9d4ePPPPP";
        $ipAddress = "88.12.243.232";
        
        // Initialize CURL:
        $ch = curl_init('http://api.ipstack.com/'.$ipAddress.'?access_key='.$accessKey.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $apiResult = json_decode($json, true);
        if (isset($apiResult['error'])) {
            echo "Errorcode = " . $apiResult['error']['code'];
            debug($apiResult['error']);     
            
        }

        debug($apiResult);
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

    public function insertDummyData() {
        //$model = ClassRegistry::init("Userinvestmentdata");

        /* for ($i = 0; $i < 600; $i++) {
          $random = rand(500, 15000);
          $random2 = rand(100, 5000);
          $random3 = rand(300, 5000);
          $date = date("Y-m-d",strtotime("-$i days"));

          $data['Userinvestmentdata']['linkedaccount_id'] = 35705;
          $data['Userinvestmentdata']['userinvestmentdata_investorIdentity'] = "39048098ab409be490A";
          $data['Userinvestmentdata']['userinvestmentdata_totalGrossIncome'] = $random;
          $data['Userinvestmentdata']['userinvestmentdata_totalLoansCost'] = $random2;
          $data['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'] = $random3;
          $data['Userinvestmentdata']['date'] = $date;
          round(bcmul(bcdiv($global['cash'], $global['totalVolume'],16), 100, 16), 2, PHP_ROUND_HALF_UP);
          //echo $random . "<br>";
          //echo $date . "<br>";
          $model->create();
          $model->save($data);
          } */

        $model = ClassRegistry::init("Paymenttotal");
        $model->virtualFields = array('paymenttotal_totalCost' . '_sum' => 'sum(paymenttotal_myInvestment + paymenttotal_secondaryMarketInvestment)');
        $sumValue = $model->find('list', array(
            'fields' => array('date', 'paymenttotal_totalCost' . '_sum'),
            'group' => array('date')
                /* 'conditions' => array(
                  "date >=" => $dateInit,
                  "date <=" => $dateFinish,
                  "linkedaccount_id" => $linkedaccountId
                  ) */
                )
        );
        print_r($sumValue);
        exit;

        /* $total = $this->Model->find('all', array(
          'fields' => array(
          'SUM(Model.price + OtherModel.price) AS total'
          ),
          'group' => 'Model.id'
          )); */

        /* $model->virtualFields = array('paymenttotal_regularGrossInterestIncome' . '_sum' => 'sum(paymenttotal_regularGrossInterestIncome)');
          $sumValue2  =  $model->find('list',array(
          'fields' => array('date', 'paymenttotal_regularGrossInterestIncome' . '_sum'),
          'group' => array('date')
          /*'conditions' => array(
          "date >=" => $dateInit,
          "date <=" => $dateFinish,
          "linkedaccount_id" => $linkedaccountId
          ) */
        /* )
          ); */

        /* foreach ($sumValue as $key => $value) {
          $totalSum[$key] = $value + $sumValue2[$key];
          }

          print_r($totalSum);
          /*$sumValue  =  $model->find('list',array(
          'fields' => array('linkedaccount_id', $value . '_sum'),
          'conditions' => array(
          $modelName .  ".created >=" => $dateInit,
          $modelName .  ".created <=" => $dateFinish
          )
          );

          /* $model->virtualFields = array('paymenttotal_regularGrossInterestIncome' . '_sum' => 'sum(paymenttotal_regularGrossInterestIncome)');
          $sumValue2  =  $model->find('list',array(
          'fields' => array('date', 'paymenttotal_regularGrossInterestIncome' . '_sum'),
          'group' => array('date')
          /*'conditions' => array(
          "date >=" => $dateInit,
          "date <=" => $dateFinish,
          "linkedaccount_id" => $linkedaccountId
          ) */
        /* )
          ); */

        /* foreach ($sumValue as $key => $value) {
          $totalSum[$key] = $value + $sumValue2[$key];
          }

          print_r($totalSum);
          /*$sumValue  =  $model->find('list',array(
          'fields' => array('linkedaccount_id', $value . '_sum'),
          'conditions' => array(
          $modelName .  ".created >=" => $dateInit,
          $modelName .  ".created <=" => $dateFinish
          )
          )
          ); */

        $model2 = ClassRegistry::init("Userinvestmentdata");

        $idByDate = $model2->find('list', array(
            'fields' => array('date', 'id'),
            'group' => array('date')
                /* 'conditions' => array(
                  "date >=" => $dateInit,
                  "date <=" => $dateFinish,
                  "linkedaccount_id" => $linkedaccountId
                  ) */
                )
        );


        foreach ($sumValue as $key => $sum) {
            //$data['Userinvestmentdata']['userinvestmentdata_totalGrossIncome'] = $sum;
            $model2->id = $idByDate[$key];
            $model2->saveField('userinvestmentdata_totalLoansCost', $sum);
        }

        /* $sumValue  =  $model->find('list',array(
          'fields' => array('linkedaccount_id', $value . '_sum'),
          'conditions' => array(
          $modelName .  ".created >=" => $dateInit,
          $modelName .  ".created <=" => $dateFinish
          )
          )
          ); */
    }

    public function testDateDiff() {
        $date1 = new DateTime("2013-03-24");
        $date2 = new DateTime("2017-06-26");
        $interval = $date1->diff($date2);
        echo "difference " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        echo "<br>";
        $resultDate1 = 20170626 - 20130324;
        $resultDate2 = 20170000 - 20130000;
        echo 20170626 - 20130324 . "<br>";
        echo 20170000 - 20130000 . "<br>";
        if ($resultDate1 <= $resultDate2) {
            echo $years = 2017 - 2013;
        }
        // shows the total amount of days (not divided into years, months and days like above)
        echo "difference " . $interval->days . " days ";
        echo "<br>";

        $date1 = new DateTime("2013-03-24");
        $date2 = new DateTime("2017-03-24");
        $interval = $date1->diff($date2);
        echo "difference " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        echo "<br>";
        $resultDate1 = 20170324 - 20130324;
        $resultDate2 = 20170000 - 20130000;
        if ($resultDate1 <= $resultDate2) {
            echo $years = 2017 - 2013 . "<br>";
        }
        // shows the total amount of days (not divided into years, months and days like above)
        echo "difference " . $interval->days . " days ";
        echo "<br>";
        $date1 = new DateTime("2013-03-24");
        $date2 = new DateTime("2017-01-26");
        $interval = $date1->diff($date2);
        echo "difference " . $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
        echo "<br>";
        $resultDate1 = 20170126 - 20130324;
        $resultDate2 = 20170000 - 20130000;
        if ($resultDate1 <= $resultDate2) {
            echo $resultDate1 . "<br>";
            echo $years = 2017 - 2013 . "<br>";
        }
        // shows the total amount of days (not divided into years, months and days like above)
        echo "difference " . $interval->days . " days ";
        echo "<br>";
        echo 20170326 - 20130324 . "<br>";
        echo 20170323 - 20130324 . "<br>";
        echo 20170000 - 20130000 . "<br>";
    }

   
    public function testAddPayment()  {
        $this->Globalamortizationtable = ClassRegistry::init('Globalamortizationtable');
        $this->Amortizationtable = ClassRegistry::init('Amortizationtable');
        Configure::write('debug', 2);         
        echo "start of method " . __METHOD__ . "<br/>";
        $this->autoRender = false;
        

        include APP . "Console/Command/ParseDataClientShell.php";
       
        $transactionData = ['transactionId' => 3242454534,
                            'date'  => "2018-07-05",
                            'investment_loanId' => "1581870-01", 
                           ];
 
        $resultData = 
            ['payment' =>
                [
                    'payment_principalAndInterestPayment'  => "9434.8",
                    'payment_capitalRepayment' => "6000.40",
             //       'payment_regularGrossInterestIncome' => "434.40",
                ],
            
            'investment' =>
                [
                    'investment_loanId' => "1581870-01",                                                  
                    'id'  =>  5529,      
                    'investment_dateForPaymentDelayCalculation' => "2018-06-00",
                ],

            ];
        
        $companyData[0]['Company']['id']  = 24; 
        
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
    
    
    
    
    function testAddPayment66()  {
        echo "start of method " . __METHOD__ . "<br/>";
        $this->autoRender = false;
        Configure::write('debug', 2); 
        $this->Amortizationtable = ClassRegistry::init('Amortizationtable');
        $sliceId = 1373;     
        $nextPendingInstalmentDate = $this->Amortizationtable->getNextPendingPaymentDate($sliceId);     
        echo "nextPendingInstalmentData = $nextPendingInstalmentDate";    
    
/* 
        $globalAmortizationTable = $this->Globalamortizationtable->readFullAmortizationTable($sliceId);  
        $this->print_r2($globalAmortizationTable);   
        echo __FUNCTION__ . " " . __LINE__ . " <br/>";       
        
 */   
       
        $companyId = 25;
        $investmentId = 5467;
        $sliceIdentifier = 3546;
        $data = ['paymentDate' => "2018-10-22", 
            'capitalRepayment' => "1093.3",
                     'interest' => "1.2",
            ];
        $result = $this->Amortizationtable->addPayment($companyId, $investmentId, $sliceIdentifier, $data) ;
        $this->print_r2($result);
        
    
        $nextPendingInstalmentDate = $this->Amortizationtable->getNextPendingPaymentDate($sliceId);     
        echo "nextPendingInstalmentData = $nextPendingInstalmentDate";    
    
        
        
        $this->Company = ClassRegistry::init('Company');
        $pfp = "zank";
        $this->companyData = $this->Company->getData($filter = ['company_codeFile' => $pfp ]);
    //    $this->print_r2($this->companyData);        
        if ($this->companyData[0]['Company']['company_technicalFeatures'] &&  WIN_PROVIDE_UP_TO_DATE_FILES == WIN_PROVIDE_UP_TO_DATE_FILES) { 
            echo "WIN_PROVIDE_UP_TO_DATE_FILES flag is set for $pfp\n";         
        }  
        else {
            echo "WIN_PROVIDE_UP_TO_DATE_FILES flag is not set for $pfp\n";  
        }
        
        $pfp = "finanzarel";
        $this->companyData = $this->Company->getData($filter = ['company_codeFile' => $pfp ]);        
    //    $this->print_r2($this->companyData);
        if ($this->companyData[0]['Company']['company_technicalFeatures'] &&  WIN_PROVIDE_UP_TO_DATE_FILES == WIN_PROVIDE_UP_TO_DATE_FILES) { 
            echo "WIN_PROVIDE_UP_TO_DATE_FILES flag is set for $pfp\n";         
        }  
        else {
            echo "WIN_PROVIDE_UP_TO_DATE_FILES flag is not set for $pfp\n";  
        }        
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
                                                        'fields' => array('id', 'linkedaccount_id' ))
                                         );        
        
        $this->print_r2($result);
        exit;
        
      
        $filterConditions = array('id' => 2105);
        print_r($filterConditions);
     	$resultInvestmentData = $this->Investment->find("all", $params = array('recursive'     => 1,
                                                                             'conditions'    => $filterConditions));              
            
        $myInstance = $resultInvestmentData[0]['Investment']['id'];  
        $myInstance = 55;
echo $myInstance;
//        $this->print_r2($resultInvestmentData);

    
        $result = $this->Investment->hasChildModel($myInstance, 'Investmentslice');
        echo "FINAL";
        $this->print_r2($result);
    } 
    
    
    
}
