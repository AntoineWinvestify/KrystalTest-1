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
    function dashboardCompanyOverview(){
        $this->layout = 'azarus_private_layout';
    }
    function modal(){
        $this->layout = 'azarus_private_layout';
    }
    

    
    function convertExcelToArray() {
/*
 * use command to be able to read spanish chars
 * iconv -f cp1250 -t utf-8 Finanzarel.csv > Finanzarel1-csv

 */
   
  //      $inputFileType = 'CSV';
        $inputFileName = '/var/www/html/compare_local/twino-investments.xlsx';
  //      $objReader = PHPExcel_IOFactory::createReader($inputFileType);  
//        $objReader->setDelimiter(";");
//        $objPHPExcel = $objReader->load($inputFileName);
 
        //    exit;

        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);            
   //     $objPHPExcel = PHPExcel_IOFactory::load("/var/www/html/compare_local/extracto-movimientos Circulantis-User 1.xlsx");
    $this->autoRender = false;
 //ini_set('memory_limit','1024M');
 //  Get worksheet dimensions
  
$sheet = $objPHPExcel->getActiveSheet(); 
$highestRow = $sheet->getHighestRow(); 
$highestColumn = $sheet->getHighestColumn();
echo " high = $highestRow and $highestColumn <br>";

/*
for ($row = 1; $row <= $highestRow; $row++){ 
    //  Read a row of data into an array
    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                    NULL,
                                    TRUE,
                                    FALSE);
    $this->print_r2($rowData);
};*/


/**
     * Create array from worksheet
     *
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param boolean $calculateFormulas Should formulas be calculated?
     * @param boolean $formatData  Should formatting be applied to cell values?
     * @param boolean $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                               True - Return rows and columns indexed by their actual row and column IDs
     * @return array
     */
 //   public function toArray($nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false)



        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
//    $this->print_r2($sheetData);
  
        /*$loadedSheetNames = $objPHPExcel->getSheetNames();
        foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
            echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, ' (Raw)</b><br />';
            $objPHPExcel->setActiveSheetIndexByName($loadedSheetName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, true);
            //var_dump($sheetData);
            echo '<br />';
        }*/
     
 
        $datas = $this->saveExcelArrayToTemp($sheetData, $values_twino_investment, $offset);

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
       
        $objPHPExcel = $objReader->load("/var/www/html/compare_local/Comunitae.xlsx");
      
        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $this->print_r2($sheetData);


        $datas = $this->saveExcelArrayToTemp($sheetData, $values);
        $this->print_r2($datas);
    }
    
    
    
    /**
     * 
     * Function to get the final position to get the variable from a string
     * @param string $rowDatas  the excel data in an array
     * @param string $values     the array with configuration data for parsing
     * @param int $offset       the number of indices at beginning of array which are NOT to be parsed
     * @return array $temparray the data after the parsing process
     * 
     */
    function saveExcelArrayToTemp($rowDatas, $values, $offset) {
        $tempArray = [];
  
        $i = 0;
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $offset) {
                break;
            }
            unset($rowDatas[$key]);  
            $i++;
        }

        
        $i = 0;
        $outOfRange = false;
        $this->print_r2($rowDatas);
        
        foreach ($rowDatas as $keyRow => $rowData) {
            echo "Reading a NEW ROW<br>";
            foreach ($values as $key => $value) {
                $previousKey = $i - 1;
                $currentKey = $i;
                // check for subindices and construct them
                if (array_key_exists("name", $value)) {
                    $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $value['name']) . "']"; 
                    $tempString = $finalIndex  . "= '" . $rowData[$key] .  "'; ";
                    eval($tempString);
                }
                else { 
                    foreach ($value as $userFunction ) {
                        echo "---------------------------------------------------------------------<br>";
                        if (!array_key_exists('inputData',$userFunction)) {
                            $userFunction['inputData'] = [];
                        }
                        else {  // input parameters are defined in config file
                        // check if any of the input parameters require data from
                        // another cell in current row, or from the previous row
                            foreach ($userFunction["inputData"] as $keyInputData => $input) {   // read "input data from config file                      
                                if (stripos ($input, "#previous.") !== false) {
                                    if ($previousKey == -1) {
                                        $outOfRange = true;
                                        break;
                                    }
                                    $temp = explode(".", $input);
                                    $userFunction["inputData"][$keyInputData] = $tempArray[$previousKey][$temp[1]];
                                }
                                if (stripos ($input, "#current.") !== false) {
                                    $this->print_r2($tempArray);
                                    $temp = explode(".", $input);
                                    $userFunction["inputData"][$keyInputData] = $tempArray[$currentKey][$temp[1]];    
                                }                                         
                            }  
                        }
                        array_unshift($userFunction['inputData'], $rowData[$key]);       // Add cell content to list of input parameters

                        if ($outOfRange == false) {
                            $tempResult = call_user_func_array(array(__NAMESPACE__ .'\TestsController',  
                                $userFunction['functionName']), $userFunction['inputData']);
                            if (!empty($tempResult)) {
                                $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $userFunction["type"]) . "']"; 
                                $tempString = $finalIndex  . "= '" . $tempResult .  "';  ";
                                eval($tempString);
                            }
                        }
                        else {
                            $outOfRange = false;        // reset
                        }
                    }
                }
            }

            if (array_key_exists("loanId", $tempArray[$i]) ){
                 $tempArray[ $tempArray[$i]['loanId'] ][]  = $tempArray[$i];
            }
            else {      // move to the global index
                $tempArray['global'][] = $tempArray[$i];
            }
                    
     //       unset($tempArray[$i]);
            $i++; 
        }
echo "END OF LOOP <br>";   
// Delete the numeric indices. This should not be necesary but the code above does
// NOT work, the bad line is "unset($tempArray[$i]);".
// So below is a stupid work-around
        for ($i; $i >= 0; $i--) {
            unset($tempArray[$i]);
            echo "delete index $i <br>";
        }
        
        $this->print_r2($tempArray);
        echo __FUNCTION__ . " " . __LINE__ . " <br>";       
        return $tempArray;
    }
    
  
   
    
    
    /** FUNCTIONALITY TO KEEP IN MIND
     * Tries to "guess" what the unknown concept could be. The algorithm is based on a
     * limited dictionary search, combined with an analysis of the current and previous cashflow line
     * so we can conclude if it is a cost, or income, and act accordingly.
     * 
     * @param string $concept       The concept which is unknown to the system
     *                         
     * @return array    $conceptDefinition['name']
     *                                    ['typeOfAction']
     *                                    
     * 
     */
    function unknownConceptHandler($concept) {
/*
    search in a dictionay  input word, output income/cost
    localDictionay = array (
    read some other data of the current atçrray (if it already exists) come in 
 *   handy due to lack of time 
 * check if it has been an adition to the 
  
  
    );
  
 
   
  
  
  
 */
        
        
        
    }   
    
    
    
 
    /**-
     * Gets an amount. The "length" of the number is determined by the required number
     * of decimals. If there are more decimals then required, the number is truncated and rounded
     * else 0's are added.
     * Examples:
     * getAmount("1.234,56789€", ".", ",", 3) => 1234568
     * getAmount("1234.56789€", "", ".", 7) => 12345678900
     * getAmount("1,234.56 €", ",", ".", 2) => 123456
     * 
     * @param string  $thousandsSep character that separates units of 1000 in a number
     * @param string  $decimalSep   character that separates the decimals 
     * @param int     $decimals     number of required decimals in the amount to be returned
     * @return int    represents the amount including its decimals
     * 
     */
    function getAmount($input, $thousandsSep, $decimalSep, $decimals) {
        if ($decimalSep == ".") {
            $seperator = "\.";
        }
        else {                                                              // seperator =>  ","
            $seperator = ",";
        }
        $allowedChars =  "/[^0-9" . $seperator . "]/";
        $normalizedInput = preg_replace($allowedChars, "", $input);         // only keep digits, and decimal seperator
        $normalizedInputFinal = preg_replace("/,/", ".", $normalizedInput); 

        // determine how many decimals are actually used
        $position = strpos($input, $decimalSep);
        $decimalPart = preg_replace('/[^0-9]+/' ,"", substr($input, $position + 1, 100));
        $numberOfDecimals = strlen($decimalPart);

        $digitsToAdd = $decimals - $numberOfDecimals;

        if ($digitsToAdd <= 0) {
            $amount = round($normalizedInputFinal, $decimals);
        }
        if ($digitsToAdd == 0) {
            $amount = preg_replace("/[^0-9]/", "", $input);  
        }
        if ($digitsToAdd > 0) {
            $amount = preg_replace('/[^0-9]+/', "", $input) . str_pad("", ($decimals - $numberOfDecimals), "0");
        }       
        return preg_replace('/[^0-9]+/' ,"", $amount);
    }        
 

  
    
    /**
     * get hash of a string
     * 
     * @param string    $input
     * @return string   $extractedString
     *       
     */
    function getHash($input) {
        return  hash ("md5", $input, false);
    }  
   
   
    /** 
     * Reads the transaction type of the cashflow operation
     * 
     * @param string   $input
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
    
    /**
     * Get the amount of a column. The currency is omitted.
     * The number of decimals is defined in the Parser class.
     * 
     * @param string    $input
     * @param string    $search
     * @param string    $separator   The separator character
     * @return string   $extractedString
     *       
     */
    function extractDataFromString($input, $search, $separator ) {
        $position = stripos($input, $search) + strlen($search);
        $substrings = explode($separator, substr($input, $position));
        return $substrings[0];
    }  

   
    /**
     * 
     * Reads the transaction detail of the cashflow operation
     * 
     * @param string   $input
     * @return array   $parameter2  List of all concepts of the platform
     *       
     */
    function getTransactionType($input, $config) {  
        $details = new Parser();
        $transactionDetails = $details->getTransactionDetails();
        unset($details);
        
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

    /**
     * 
     * Reads a field from a row. Note that the field must be
     * a "calculated" field, i.e it must be defined in the config file 
     * 
     * @param string    $input   cell data
     * @param array     $field   field to read
     * @param boolean   overwrite     overwrite current value of the $input
     *       
     */
    function getRowData($input, $field, $overwrite) {  

        if (empty($input)) {
            return $field;
        }    
        else {
            if ($overwrite) {
                return $field;
            }
        }      
         return "";
    }    
    
    
    
    
    function multiexplode ($delimiters,$string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    } 
    
    
    
    
    
    
    
    
    
    function convertPdf () {
        // Parse pdf file and build necessary objects.
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile('/var/www/html/compare_local/OSchedule.pdf');
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
