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

/** Include path **/
require_once(ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'autoload.php');
//require_once(ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'php-bondora-api-master' . DS .  'bondoraApi.php');

/** PHPExcel_IOFactory */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel'.DS.'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS.'IOFactory.php'));
//App::import('Vendor', 'readFilterWinvestify', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS. 'Reader'. DS . 'IReadFilterWinvestify.php'));

use Petslane\Bondora;

/*use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell;*/


class TestsController extends AppController {

    var $name = 'Tests';
    var $helpers = array('Js', 'Text', 'Session');
    var $uses = array('Test', "Data", "Investor");
    var $error;

    
function beforeFilter() {
        parent::beforeFilter();
        

	$this->Security->requireAuth();
        
        $this->Auth->allow(array('convertExcelToArray', "convertPdf", "bondoraTrying"));
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

    $dataResults = $this->Data->find("all", array('conditions'  => $dataFilterConditions,
                                                    'order'     => array('Data.id DESC'),
                                                    'recursive' => -1,
                                    ));

    $absNumber = abs($number);

    if (array_key_exists($absNumber,$dataResults)) {
        $data = array('data_investorReference' => $investorIdentity,
                   'data_JSONdata' => $dataResults[$absNumber]['Data']['data_JSONdata']
                );
        $this->Data->save($data, $validate = true);  
        echo "Data is now available in Dashboard";
    }
    else {
        echo "Nothing found, try again with other data";
        exit;
    }
}

    function showInitialPanel(){
        $this->layout = 'azarus_private_layout';
    }
    function dashboardOverview(){
        $this->layout = 'azarus_private_layout';
    }
    function dashboardMyInvestments(){
        $this->layout = 'azarus_private_layout';
    }
    function dashboardStats(){
        $this->layout = 'azarus_private_layout';
    }
    function modal(){
        $this->layout = 'azarus_private_layout';
    }
    
    
    
    function convertExcelToArray() {

        $objPHPExcel = PHPExcel_IOFactory::load("/var/www/html/compare_local/mintos1.xlsx");
    $this->autoRender = false;
 //ini_set('memory_limit','1024M');
 //  Get worksheet dimensions
  
$sheet = $objPHPExcel->getActiveSheet(); 
$highestRow = $sheet->getHighestRow(); 
$highestColumn = $sheet->getHighestColumn();
echo " high = $highestRow and $highestColumn <br";

/*
for ($row = 1; $row <= $highestRow; $row++){ 
    //  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
    $this->print_r2($rowData);
};*/

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);

        /*$loadedSheetNames = $objPHPExcel->getSheetNames();
        foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
            echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, ' (Raw)</b><br />';
            $objPHPExcel->setActiveSheetIndexByName($loadedSheetName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, true);
            //var_dump($sheetData);
            echo '<br />';
        }*/
     
        
        $values_comunitae = [
            "A" => "date",
            "B" => "debe",
            "C" => "haber",
            "D" => "saldo",
            "E" => [
                [
                    "name" => "cash_deposit",
                    "regex" => "Provisión de fondos"
                ],
                [
                    "name" => "cash_withdraw",
                    "regex" => "Retirada de fondos"
                ],
                [
                    "name" => "primary_market_investment",
                    "regex" => "Participación en préstamo"
                ],
                [
                    "name" => "principal_repayment",
                    "regex" => "Abono rendimientos capital"
                ],
                [
                    "name" => "regular_interest_income",
                    "regex" => "Abono rendimientos intereses"
                ],
                [
                    "name" => "Commission",
                    "regex" => "administración"
                ],
                [
                    "name" => "bank_charges",
                    "regex" => "tarjeta"
                ],
                [
                    "type" => "loanId",
                    "regex" => "CPP_",
                    "initPos" => 0,
                    "finalPos" => "y Nº"
                ]
            ]
        ];
        $values_circulantis = [
            "A" => [
                [
                    "name" => "primary_market_investment",
                    "regex" => "realizada"
                ],
                [
                    "name" => "primary_market_investment",
                    "regex" => "formalizada"
                ],
                [
                    "type" => "loanId",
                    "regex" => "ID Puja",
                    "initPos" => 8,
                    "finalPos" => ","
                ],
                [
                    "type" => "subastaId",
                    "regex" => "ID Subasta",
                    "initPos" => 11,
                    "finalPos" => ","
                ],
                [
                    "type" => "purpose",
                    "regex" => [
                        "init" => "ID Subasta", 
                        "final" => ","],
                    "initPos" => 1,
                    "finalPos" => ","
                ]
            ],
            "B" => "referencia",
            "C" => "importe",
            "D" => "date",
            "E" => "disponible",
            "F" => "ofertado",
            "G" => "invertido",
            "H" => "total"
        ];


        $values_mintos = [     // All types/names will be defined as associative index in array
            "A" =>  [
                "name" => "transaction_id"
             ],
            "B" => [
                [
                    "type" => "date",                           // Winvestify standardized name 
                    "inputData" => [
				"input2" => "Y-m-d",		// Input parameters. The first parameter
                                                                // is ALWAYS the contents of the cell
                                  // etc etc  ...
                                ],
                    "functionName" => "normalizeDate",         
                ]
            ],
            "C" => [
                [
                    "type" => "loanId",                         // Complex format, calling external method
                    "inputData" => [
                                "input2" => "Loan ID: ",        // May contain trailing spaces
                                "input3" => ",",
                            ],
                    "functionName" => "extractDataFromString",  
                ],
                [
                    "type" => "transactionType",                // Complex format, calling external method
                    "inputData" => [                            // List of all concepts that the platform can generate
                                                                // format ["concept string platform", "concept string Winvestify"]
                                   "input2" => [["Incoming client payment", "Cash_deposit"],
                                                ["Investment principal increase", "Primary_market_investment"],
                                                ["Investment principal repayment", "Principal_repayment"],
                                                ["Investment principal rebuy","Principal_buyback"],
                                                ["Interest income", "Regular_interest_income"],
                                                ["Delayed interest income", "Delayed_interest_income"],
                                                ["Late payment fee income","Late_payment_fee_income"],
                                                ["Interest income on rebuy", "Interest_income_buyback"],
                                                ["Delayed interest income on rebuy", "Delayed_interest_income_buyback"],
                                    ]   
                            ],
                    "functionName" => "getTransactionType",  
                ],
                [
                    "type" => "transactionDetail",              // Complex format, calling external method
                    "inputData" => [                            // List of all concepts that the platform can generate
                                                                // format ["concept string platform", "concept string Winvestify"]
                                   "input2" => [["Incoming client payment", "Cash_deposit"],
                                                ["Investment principal increase", "Primary_market_investment"],
                                                ["Investment principal repayment", "Principal_repayment"],
                                                ["Investment principal rebuy","Principal_buyback"],
                                                ["Interest income", "Regular_interest_income"],
                                                ["Delayed interest income", "Delayed_interest_income"],
                                                ["Late payment fee income","Late_payment_fee_income"],
                                                ["Interest income on rebuy", "Interest_income_buyback"],
                                                ["Delayed interest income on rebuy", "Delayed_interest_income_buyback"],
                                        
                                    ]   
                            ],
                    "functionName" => "getTransactionDetail",  
                ]
            ],
            "D" => [                                            // Simply changing name of column to the Winvestify standardized name
                    "name" => "turnover",                      
                ],
            "E" => [
                    "name" => "balance",
                ],
            "F" => [
                [
                    "type" => "currency",                       // Complex format, calling external method
                    "functionName" => "getCurrency",  
                ]
            ],          
        ];       
 
            
        $offset = 3;
   
        $datas = $this->saveExcelArrayToTemp($sheetData, $values_mintos, $offset);
        $this->print_r2($datas);
    }
    
    function convertExcelByParts($chunkInit, $chunkSize, $inputFileType, $values) {
        if (empty($inputFileType)) {
            $inputFileType = "Excel2007";
        }
        if (empty($chunkInit)) {
            $chunkInit = 1;
        }
        if (empty($chunkSize)) {
            $chunkSize = 500;
        }
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        
        /**  Create a new Instance of our Read Filter  **/
        $chunkFilter = new readFilterWinvestify();
        /**  Tell the Read Filter, the limits on which rows we want to read this iteration  **/
        $chunkFilter->setRows($chunkInit,$chunkSize);
        /**  Tell the Reader that we want to use the Read Filter that we've Instantiated  **/
        $objReader->setReadFilter($chunkFilter);
       
        $objPHPExcel = $objReader->load("/var/www/html/compare_local/mintos2.xlsx");
      
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $this->print_r2($sheetData);
   
        $datas = $this->saveExcelArrayToTemp($sheetData, $values);
        echo "ANTOINE";
        $this->print_r2($datas);
    }
    
    
    
    /**
     * Function to get the final position to get the variable from a string
     * @param string $rowDatas  the excel data in an array
     * @param string $value     the array with configuration data for parsing
     * @param int $offset       the number of indices at beginning of array which are NOT to be parsed
     * @return array $temparray the data after the parsing process
     * 
     */
    function saveExcelArrayToTemp($rowDatas, $values, $offset) {
        echo __FILE__ . " " . __LINE__ ."<br>";
        $tempArray = [];
$offset = 11152;
        $i = 0;
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $offset) {
                break;
            }
            unset($rowDatas[$key]);  
            $i++;
        }

        
// CIRCULANTIS
// MOVIMIENTO                                                                   REFERENCIA IMPORTE â‚¬	FECHA	   DISPONIBLE â‚¬   OFERTADO â‚¬    INVERTIDO â‚¬    TOTAL â‚¬
// OperaciÃ³n formalizada ID Puja: 180626, ID Subasta: 1893,Mayentis S.L....	F180626     0          7/31/2017    572.18          66.34           15,049.39	     15,687.91

        
// ECROWD
// Fecha        Nombre del proyecto                                                     Cuota	Amortización de capital(€)	Intereses brutos(€) Retención IRPF(€)  Total(€)
// 25-07-2017	Ampliación de la red de fibra óptica de l'Ametlla de Mar - Fase 5 -	2	0,00                              1,09               0,21                0,88
 
        
// FINANZAREL        
// Id           A�o	Trimestre	Fecha           Subasta     Descripci�n                 Importe         Saldo
// 20171678450	2017	2017T3          21/07/17	2817        Intereses                   �0,97           �55.314,02
// 20171678440	2017	2017T3          21/07/17	2817        Amortizaci�n de efecto	-�153,94	�55.313,06
  
        
// COMUNITAE
// Fecha de Operacion	Debe	Haber	Saldo	Concepto
// 8/1/2017             0.50€	0.00€	49.61€	Cargo por comisión de administración
// 7/25/2017            0.58€	0.00€	50.11€	Cargo por comisión de administración
// 7/25/2017            0.00€	50.00€	50.69€	Abono rendimientos capital   ptmo. CPP_016231  y Nº de recibo 342097

        $i = 0;
        foreach ($rowDatas as $keyRow => $rowData) {
            foreach ($values as $key => $value) {
                if (array_key_exists("name", $value)) {
                    $tempArray[$i][$value["name"]] = $rowData[$key];
                }
                else {
                    foreach ($value as $userFunction ) {
                        if (!array_key_exists('inputData',$userFunction)) {
                            $userFunction['inputData'] = [];
                        }                        
                        array_unshift($userFunction['inputData'], $rowData[$key]);       // Add cell content to list of input parameters
                        $tempArray[$i][$userFunction["type"]] = call_user_func_array(array(__NAMESPACE__ .'\TestsController',  
                                $userFunction['functionName']), $userFunction['inputData']);
                    }
                }
            }
/*
            if (array_key_exists("loanId", $tempArray[$i]) ){
                echo "Adding entry for loanId " . $tempArray[$i]['loanId'] . ", TransactionId =  " . $tempArray[$i]['transaction_id'] . "<br>";
                $tempArray[ $tempArray[$i]['loanId'] ][]  = $tempArray[$i];
            }
            else {      // move to the global index
                $tempArray['global'][] = $tempArray[$i];
            }
            unset($tempArray[$i]);
 */           
          $i++; 
     //   continue;       // short cut
        }
 //       $this->print_r2($tempArray);
        return $tempArray;
    }
    
    
    /**
     * Converts any type of date format to internal format yyyy-mm-dd
     * 
     * @param string $date  
     * @param string $currentFormat:  Y = 4 digit year, y = 2 digit year
     *                                M = 2 digit month, m = 1 OR 2 digit month (no leading 0)
     *                                D = 2 digit day, d = 1 OR 2 digit day (no leading 0)
     *                         
     * @return string 
     * 
     */
   function normalizeDate($date, $currentFormat) {
       $internalFormat = $this->multiexplode(array(".", "-", "/"), $currentFormat);
       
       ((count($internalFormat) == 1 ) ? $dateFormat = $currentFormat : $dateFormat = $internalFormat[0] . $internalFormat[1] . $internalFormat[2]);
       
       $tempDate = $this->multiexplode(array(":", " ", ".", "-"), $date);
       $finalDate = array();

       $length = strlen($dateFormat);
       for ($i = 0; $i < $length; $i++) {
            switch ($dateFormat[$i]) {
                case "d":
                    $finalDate[2] = $tempDate[$i];
                break;
                case "D":
                    $finalDate[2] = $tempDate[$i];
                break;              
                case "m":
                    $finalDate[1] = $tempDate[$i];
                break;
                case "M":
                    $finalDate[1] = $tempDate[$i]; 
                break;  
                case "y":
                    $finalDate[0] = $tempDate[$i]; 
                break;
                case "Y":
                    $finalDate[0] = $tempDate[$i]; 
                break;              
            }
        }    
        return $finalDate[0] . "-" . $finalDate[1] . "-" . $finalDate[2];   
   }  
  
 
    /**
     * Translates the currency to internal representation
     * 
     * @param string $loanCurrency  
     * @return integer  constant representing currency 
     * 
     */
    function getCurrency($loanCurrency) {
        $details = new Parser();
        $currencyDetails = $details->getCurrencyDetails();

        foreach ($currencyDetails as $currencyIndex => $currency) {
            if ($loanCurrency == $currency[0]) {
              return $currencyIndex;
            }   
        } 
    }
   
    /**
     * Extracts data from a string. The returned string is the part
     * from the input string after the searchString, until the seperator character
     * 
     * @param string    $input
     * @param string    $search
     * @param string    $seperator   The seperator character
     * @return string   $extractedString
     *       
     */
    function extractDataFromString($input, $search, $seperator ) {
        $position = stripos($input, $search) + strlen($search);
        $substrings = explode($seperator, substr($input, $position));
        return $substrings[0];
    }  
   
   
    /**  "MIX" ACCORDING TO FLOW DATA IS STILL MISSING
     * Reads the transaction type of the cashflow operation
     * 
     * @param string   +$input
     * @return array   $parameter2  List of all concepts of the platform
     *       
     */
    function getTransactionDetail($input, $config) {
        foreach ($config as $key => $configItem) {
            $position = stripos($input, $configItem[0]);
            if ($position !== false) {
                return $configItem[1];
            }
        }
    }     
    
    
   
    /**  "MIX" ACCORDING TO FLOW DATA IS STILL MISSING
     * 
     * Reads the transaction detail of the cashflow operation
     * 
     * @param string   +$input
     * @return array   $parameter2  List of all concepts of the platform
     *       
     */
    function getTransactionType($input, $config) {    
        $details = new Parser();
        $transactionDetails = $details->getTransactionDetails();

        foreach ($config as $key => $configItem) {
            $position = stripos($input, $configItem[0]);
            if ($position !== false) {  // value is in $configItem[1];
                foreach ($transactionDetails as $key => $detail) {
                    if ($detail['detail'] == $configItem[1]) {
                        return $detail['transactionType'];
                    }
                }
            }
        }         
    }  

    
    
    function multiexplode ($delimiters,$string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    } 
    
    
    
    
    
    
    
    
    
    function convertPdf () {
        // Parse pdf file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile('/var/www/html/cake_branch/comunitae.pdf');
        // Retrieve all pages from the pdf file.
        $pages  = $pdf->getPages();
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
        
        /*$investments = explode("%", $page_string[0]);
        
        foreach ($investments as $investment) {
            echo $investment;
            echo "<br>";
        }*/
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
}
