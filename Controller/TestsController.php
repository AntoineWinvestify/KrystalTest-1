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
        $objPHPExcel = PHPExcel_IOFactory::load("/var/www/html/compare_local/mintos2.xlsx");
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
        
        $values_mintos = [
            "A" => "TransactionId",
            "B" => "date",
            "C" => [
                [
                    "name" => "primary_market_investment",
                    "regex" => "Investment principal increase"
                ],
                [
                    "name" => "principal_repayment",
                    "regex" => "Investment principal repayment"
                ],
                [
                    "name" => "principal_buyback",
                    "regex" => "Investment principal rebuy"
                ],
                [
                    "name" => "regular_interest_income",
                    "regex" => "Interest income"
                ],
                [
                    "name" => "delayed_interest_income",
                    "regex" => "Delayed interest income"
                ],
                [
                    "name" => "late_payment_fee_income",
                    "regex" => "Late payment fee income"
                ],
                [
                    "name" => "interest_income_buyback",
                    "regex" => "Interest income on rebuy"
                ],
                [
                    "name" => "delayed_interest_income_buyback",
                    "regex" => "Delayed interest income on rebuy"
                ],
                [
                    "type" => "loanId",
                    "regex" => "Loan ID",
                    "initPos" => 9,
                    "finalPos" => null
                ]
            ],
            "D" => "turnover",
            "E" => "balance",
            "F" => "currency"
        ];
        
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


        $values_example = [     // All types/names will be defined as associative index in array
            "A" =>  [
                "name" => "transaction_id"
             ],
 // OperaciÃ³n realizada ID Puja: 181325, ID Subasta: 1908,Ahumados Sabal, S.L....           

            "B" => [
                [
                    "type" => "date",                           // Winvestify standardized name 
                    "inputData" => [
				"input2" => "mdY",		// Input parameters. The first parameter
				//"input3" => "ssss"            // is always the contents of the cell
                                  // etc etc  ...
                                ],
                    "functionName" => "normalizeDate",         
                ]
                
    /*            [
                    "type" => "date1",                           // Winvestify standardized name 
                    "inputData" => [
				"input2" => "mddddY",		// Input parameters. The first parameter
				"input3" => "sssTTs"              // is always the contents of the cell
                                  // etc etc  ...
                                ],
                    "functionName" => "formatDate",         
                 ]
     */
            ],
            "C" => [
                [
                    "type" => "purpose",                        // Complex format, calling external method
                    "inputData" => [
                                "input2" => "ID Subasta", 
                                "input3" => ",",
                                "input4" => 1,
                                "input5" => ","
                            ],
                    "functionName" => "extractDataFromString",  
                ]
            ],
            "D" => [                                            // Simply changing name of column to Winvestify
                    "name" => "turnover",                   // standardized name, which is also the name of index 
                ],
            "E" => [
                    "name" => "balance",
                ],
   //         "E" => "primary_market_investment",
    /*
            "F" => "secundary_market_investment",
            "G" => "principal_repayment",
            "H" => "partial_principal_repayment",
            "I" => "regular_interest_income",
            "J" => "delayed_interest_income",
            "K" => "late_payment_fee_income",
            "L" => "interest_income_buyback",
            "M" => "other_income",
            "N" => "other_income_1",
            "O" => "other_income_2",
            "P" => "other_income_3",
            "Q" => "commission",           
            "R" => "recoveries",
            "S" => "bank_charges",
            "T" => "premium_paid_secondary_market",
            "U" => "interest_payment_secondary_market_purchase",
            "V" => "tax_VAT",
            "W" => "tax_income_withholding_tax",
            "X" => "write-off"
     */ 
     
        ];       
 echo "B___________________________B<br><br>";
  $this->print_r2($values_example);
  echo "Anto";

  
        $transactionDetails = [
            "cash_deposit" => [
                "cash" => 1,         // 1 = income, 2 = cost
                "transactionType" => "Deposit",
                ],
            "cash_withdrawal" => [
                "cash" => 2,         
                "transactionType" => "Withdraw",               
                ], 
            "primary_market_investment" => [
                "cash" => 2,         
                "transactionType" => "Investment",
                ],
            "secundary_market_investment" => [
                "cash" => 2,         
                "transactionType" => "Investment",               
                ], 
            "principal_repayment" => [
                "cash" => 1,        
                "transactionType" => "Repayment",
                ],
            "partial_principal_repayment" => [
                "cash" => 1,         
                "transactionType" => "Repayment",               
                ], 
            "principal_buyback" => [
                "cash" => 1,        
                "transactionType" => "Repayment",
                ],
            "regular_interest_income" => [
                "cash" => 1,         
                "transactionType" => "Income",               
                ], 
            "delayed_interest_income" => [
                "cash" => 1,         
                "transactionType" => "Income",
                ],
            "late_payment_fee_income" => [
                "cash" => 1,         
                "transactionType" => "Income",               
                ], 
            "cash_deposit" => [
                "cash" => 1,        
                "transactionType" => "Income",
                ],
            "interest_income_buyback" => [
                "cash" => 1,         
                "transactionType" => "Income",               
                ], 
            "delayed_interest_income_buyback" => [
                "cash" => 1,        
                "transactionType" => "Income",
                ],
            "cash_withdrawal" => [
                "cash" => 1,         
                "transactionType" => "Income",               
                ], 
            "cash_deposit" => [
                "cash" => 1,       
                "transactionType" => "Income",
                ],
            "cash_withdrawal" => [
                "cash" => 1,         
                "transactionType" => "Income",               
                ],  
            "recoveries" => [
                "cash" => 1,         
                "transactionType" => "Income",               
                ],  
            "commission" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ], 
            "bank_charges" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ], 
            "premium_paid_secondary_market" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ], 
            "interest_payment_secondary_market_purchase" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ],            
            "tax_VAT" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ], 
            "tax_income_withholding_tax" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ],            
            "write-off" => [
                "cash" => 2,         
                "transactionType" => "Costs",               
                ],    
         ];  
            
            
            
            
            
        
        $offset = 3;
   
        $datas = $this->saveExcelArrayToTemp($sheetData, $values_example, $offset);
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
       
        $objPHPExcel = $objReader->load("/var/www/html/compare_local/mintos.xlsx");
      
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $this->print_r2($sheetData);
   
        $datas = $this->saveExcelArrayToTemp($sheetData, $values);
        echo "ANTOINE";
        $this->print_r2($datas);
    }
    
    
    
    /**
     * Function to get the final position to get the variable from a string
     * @param string $rowDatas  the excel data in an array
     * @param string $value     the array with cofiguration data for parsing
     * @param int $offset       the number of indices which are NOT to be parsed
     * @return array $temparray the data after the parsing process
     * 
     */
    function saveExcelArrayToTemp($rowDatas, $values, $offset) {
        echo __FILE__ . " " . __LINE__ ."<br>";
        $tempArray = [];
$offset = 113;
        $i = 0;
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $offset) {
                break;
            }
            unset($rowDatas[$key]);  
            $i++;
        }
        
   //     $this->print_r2($rowDatas);
// Transaction ID	Date            Details                                 Turnover            	Balance	Currency
// 126162844	2017-01-01 23:30:00	Interest income Loan ID: 19550-01	0.000891587669667	9.6354150112017	EUR
// 126162002	2017-01-01 23:30:00	Interest income Loan ID: 16706-01	5.1373503120617         5.1458167810378	EUR
   
        $i = 0;
        foreach ($rowDatas as $keyRow => $rowData) {
            foreach ($values as $key => $value) {
                $this->print_r2($value);
                if (array_key_exists("name", $value)) {
                    $tempArray[$i][$value["name"]] = $rowData[$key];
                }
                else {
                    foreach ($value as $userFunction ) {
                        array_unshift($userFunction['inputData'], $rowData[$key]);       // Add cell content to list of input parameters
                        $tempArray[$i][$userFunction["type"]] = call_user_func_array(array(__NAMESPACE__ .'\TestsController',  
                                $userFunction['functionName']), $userFunction['inputData']);
                    }
                }
            }

            
            if (array_key_exists("loanId", $tempArray[$i]) ){
                $tempArray[$tempArray[$i]['loanId']]  = $tempArray[$i];
            }
            else {      // move to the global index
                $tempArray['global'] = $tempArray[$i];
            }
            unset($tempArray[$i]);
            
            $i++; 
        continue;       // short cut
        }
        echo "AAAAAAAAAAAAAAAAAAA";
        $this->print_r2($tempArray);
        exit;
        return $tempArray;
    }
    
    
    /**
     * Converts any type of date format to internal format dd-mm-yyyy
     * 
     * @param string $date  
     * @param string $currentFormat
     * @return string 
     * 
     */
   function normalizeDate($date, $currentFormat) {
       $tempDate = $this->multiexplode(array(":", " ", ".", "-"), $date);
       echo "par2 = $currentFormat <br>";
       return $tempDate[0] . "-" . $tempDate[1] . "-" . $tempDate[2];
   }  
    
    /**
    * Extracts data from a string
    * 
    * @param type $string
    * @param type $parameter2
    * @return type
    * 
    */
    function extractDataFromString($string, $parameter2) {
       $tempDate = $this->multiexplode(array(":", " ", ".", "-"), $date);
       echo "par2 = $currentFormat <br>";
       return $tempDate[0] . "-" . $tempDate[1] . "-" . $tempDate[2];
   }  
   
   

    function multiexplode ($delimiters,$string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    } 
    
    function getValueExcelFromArray($rowData, $values) {
 //       echo __FILE__ . " " . __LINE__ ."<br>";
        foreach ($values as $key => $value) {
            $pos = $this->getPosInit($rowData, $value["regex"]);
            //$pos = strpos($rowData, $value["regex"]);
            if ($pos !== false) {
                // " found after position X
                //$tempArray["loanId"] = substr($value, $pos + $variable["initPos"], $variable["finalPos"]);
                if (!empty($value["name"])) {
                    $tempArray["type"] = $value["name"];
                }
                else {
                    $tempArray[$value["type"]] = $this->getValueBySubstring($rowData, $value, $pos);
                }
            }
        }
        return $tempArray;
    }
    
    function getValueBySubstring($rowData, $value, $pos) {
        $posFinal = $this->getPosFinal($rowData, $value, $pos);
        if (empty($posFinal)) {
            $data = substr($rowData, $pos + $value["initPos"]);
        }
        else {
            $data = substr($rowData, $pos + $value["initPos"], $posFinal);
        }
        return trim($data);
    }
    
    function getPosInit($rowData, $regex) {
        if (is_array($regex)) {
            $posStart = strpos($rowData, $regex["init"]);
            $pos = strpos($rowData, $regex["final"], $posStart);
        }
        else {
            $pos = strpos($rowData, $regex);
        }
        return $pos;
    }
    
    function getPosFinal($rowData, $value, $pos) {
        $posFinal = null;
        if (!is_int($value["finalPos"])) {
            $positionFinal = strpos($rowData, $value["finalPos"], $pos);
            if ($positionFinal !== false) {
                $posFinal = $positionFinal-$pos-$value["initPos"];
            }
        }
        else if (is_int($value["finalPos"])) {
            $posFinal = $value["finalPos"];
        }
        return $posFinal;
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
