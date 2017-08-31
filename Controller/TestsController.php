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
require_once(ROOT . DS . 'app' . DS .  'Vendor' . DS  . 'php-bondora-api-master' . DS .  'bondoraApi.php');
/** PHPExcel_IOFactory */
App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel'.DS.'PHPExcel.php'));
App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS.'IOFactory.php'));
App::import('Vendor', 'readFilterWinvestify', array('file' => 'PHPExcel'.DS.'PHPExcel'.DS. 'Reader'. DS . 'IReadFilterWinvestify.php'));

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
        $objPHPExcel = PHPExcel_IOFactory::load("/var/www/html/cake_branch/mintos.xlsx");
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
                    "finalPos" => "..."
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
        $datas = $this->saveExcelArrayToTemp($sheetData, $values_mintos);
        var_dump($datas);
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
        
        $objPHPExcel = $objReader->load("/var/www/html/cake_branch/mintos.xlsx");
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $datas = $this->saveExcelArrayToTemp($sheetData, $values);
        var_dump($datas);
    }
    
    function saveExcelArrayToTemp($rowDatas, $values) {
        $i = 0;
        $tempArray = [];
        foreach ($rowDatas as $rowData) {
            foreach ($values as $key => $value) {
                if (is_array($value)) {
                    if (empty($tempArray[$i])) {
                        $tempArray[$i] = $this->getValueExcelFromArray($rowData[$key], $value);
                    }
                    else {
                        $tempArray[$i] = $this->getValueExcelFromArray($rowData[$key], $value, $tempArray[$i]);
                    }
                }
                else {
                    $tempArray[$i][$value] = $rowData[$key];
                }
            }
            $i++;
        }
        return $tempArray;
    }
    
    
    
    function getValueExcelFromArray($rowData, $values, $tempArray = null) {
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
