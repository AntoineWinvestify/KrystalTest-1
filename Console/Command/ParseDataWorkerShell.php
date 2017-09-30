<?php
/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the               |
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 * 
 * 
 * Each user has queueId, investorID
 * number of child processes, each childprocess takes a full sequence of a user
 * and writes the new job status for the user
 * Start instance of parser with configfile
 * Errors are taken care of in the worker and will spark the exception Callback with
 * some extra user data
 * Normal return also include some basic return data, like queue_id and user_reference
 * 
 * @author 
 * @version
 * @date
 * @package
 * 
 * 
 * TO BE DONE:
 * CHECK THE STRUCTURE OF A XLS/XLSX/CSV FILE BY CHECKING THE NAMES OF THE HEADERS. 
 * 
 * 
 */

class ParseDataWorkerShell extends AppShell {
    
    protected $GearmanWorker;
    
    var $uses = array('Investment', 'Userinvestmentdata', 'Queue');
    
    public function startup() {
            $this->GearmanWorker = new GearmanWorker();
    }
    
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');
        $this->GearmanWorker->addFunction('multicurlFiles', array($this, 'getDataMulticurlFiles'));
        $this->GearmanWorker->addFunction('casperFiles', array($this, 'getDataCasperFiles'));
        $this->GearmanWorker->addFunction('testFail', function(GearmanJob $job) {
            try {
                throw new Exception('Boom');
            } catch (Exception $e) {
                $job->sendException($e->getMessage());
                $job->sendFail();

            }
        });
        
        $this->GearmanWorker->addFunction('parseFileFlow', array($this, 'parseFileFlow'));   
        echo "Entering loop\n";
        while($this->GearmanWorker->work());
        
    }
            
  
    
    
    
    /**
     * Parse the content of a file (xls, xlsx, csv) into an array 
     * The $job->workload() function reads the input data as sent by the Gearman client
     * This is json_encoded data with the following structure:
     * 
     *      $data['linkedAccountId']['userReference']
     *      $data['linkedAccountId']['activeInvestments'][]     => list of all active investments 
     *                                                             and reserved investments?
     *      $data['linkedAccountId']['queue_id']
     *      $data['linkedAccountId']['pfp']
     *      $data['linkedAccountId']['files'][filename1']       array of filenames, FQDN's
     *      $data['linkedAccountId']['files'][filename2']  
     *                                        ... ... ... 
     * 
     * 
     * @return array queue_id, userreference, también en el exception error
     * el worker genera tb los applicationerrors
     * 
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                      array with all errorData related to occurred error
     * 
     */   
    public function parseFileFlow($job) {

        $platformData = json_decode($job->workload(), true);

        $collectLoanIds = array();
        
        foreach ($platformData as $linkedAccountKey => $data) {
            if ($data['pfp'] <> "mintos") { 
    //            continue;
            }
            
    //        $companyHandle = $this->companyClass($data['pfp']);
            
            echo "CURRENT PLATFORM = " . $data['pfp'] . "\n";
            // Deal first with the transaction file(s)
            print_r($data);
            $files = $data['files']; 
            // First analyze the transaction file(s)
            $approvedFiles = $this->readFilteredFiles($files,  TRANSACTION_FILE);
                
    //        $myParser = new Fileparser();       // We are dealing with an XLS file so no special care needs to be taken

    //        $parserConfig = $companyHandle->getParserConfigTransactionFile();
            print_r($parserConfig);
            echo __FILE__ . " " . __LINE__ . "\n";
            
            $tempResult = array();
            foreach ($approvedFiles as $approvedFile){          // probably done only once
    //            $myParser->setConfig['sortParameter'] = "loanId";
    //            $tempResult = $myParser->analyzeFile($approvedFile, $parserConfig);// if successfull analysis, result is an array with loanId's as index 
                if (empty($tempResult)) {                // error occurred while analyzing a file. Report it 
    //               $error[$linkedAccountKey][] = $myParser->getLastError();
                   // GENERATE appplicationerror 
                } 
                else {       // all is OK 
                  
                    $totalParsingresult = $tempResult;    // add $result, combine the arrays
                    echo __FILE__ . " " . __LINE__ . " \n";
                    print_r($totalParsingresult);
                    if ($myCompany->fileanalyzed($fileName, $typeOfFile, $fileContent)) {                   // Generate the callback function
                        // continue 
                    }
                    else {
                        // an error has occurred or been detected by companycode file. Exit gracefully
                        return false;
                    }
                    //run through the array and search for new loans.
                    foreach ($totalParsingresult as $loanKey => $loan) {
                        if ($loanKey == "global") {
                            continue;
                        }
                        if (!$loanExists($loanKey)) {       // Check if new investments have appeared
                            if (array_search($loanKey, $data['activeInvestments'] !== false)); 
                            $newLoans[] = $loanKey;
                        }
                    }
                    // store everything so we can return the final result to client
                    $returnData[$linkedAccountKey]['newLoans'] = $newLoans;
                    $returnData[$linkedAccountKey][$parsingResult] = $totalParsingresult;
                    unset($newLoans);
                } 
                $returnData[$linkedAccountKey]['investor_investorReference'] = $data['userReference'];
                $returnData[$linkedAccountKey]['queue_id'] = $data['queue_id'];   
            }
        }
        return json_encode($returnData); 
    }
}        
        
        
 /*
                    $myParser->analysisErrors();
                    $this->Applicationerror = ClassRegistry::init('Applicationerror');
                    $par1 = "ERROR Parsing error of downloaded file";
                    $par2 = $data;
                    $par3 = __LINE__;
                    $par4 = __FILE__;
                    $par5 = "";
                    $this->applicationerror->saveAppError($par1, $par2, $par3, $par4, $par5);
                    // do cleaning up of all files which have been generated so far
    //echo error back to Gearman client
              

            if (empty($collectLoanIds)) {
                $newState = AMORTIZATION_TABLES_DOWNLOADED;                        // Do not collect amortization tables
            }
            else {
                $newState = DATA_EXTRACTED;
            }

            // write new status
            $resultQueue = $this->Queue->find('all', array('conditions' => array('queue_userReference' => $data['queue_id']),
                    'recursive' => -1,
                ));
            $this->Queue->id = $resultQueue[0]['id'];

            if ($this->Queue->save(array('queue_status' => $newState), $validate = true)) {
                $params = array('investor_investorReference' => $data['userReference'],
                                'queue_id'  => $data['queue_id']
                                );
                return json_encode($params);                                        // normal end of execution of worker         
            }

            // generate exception
            // return
*/

            
            
            


    /**
     * 
     * Class that can analyze a xls/csv/pdf file and put the information in an array
     * 
     * 
     */
    class Fileparser {
        protected $config = array ('OffsetStart' => 0,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => ""   // used to "sort" the array and use $sortParameter as prime index. 
                                 );                     // if array does not have $sortParameter then "global" index is used
                                                        // Typically used for sorting by loanId index

        protected $errorData = array();                 // Contains the information of the last occurred error


        protected $currencies = array(EUR => ["EUR", "€"], 
                                        GBP => ["GBP", "£"], 
                                        USD => ["USD", "$"],
                                        ARS => ["ARS", "$"],
                                        AUD => ["AUD", "$"],
                                        NZD => ["NZD", "$"],                                           
                                        BYN => ["BYN", "BR"],       
                                        BGN => ["BGN", "лв"], 
                                        CZK => ["CZK", "Kč"],                                        
                                        DKK => ["DKK", "Kr"],                                       
                                        CHF => ["CHF", "Fr"],                                        
                                        MXN => ["MXN", "$"], 
                                        RUB => ["RUB", "₽"],              
                                        );        
    
        public $numberOfDecimals = 5;

        public  $transactionDetails = [
                0 => [
                    "detail" => "Cash_deposit",
                    "cash" => 1,                                    // 1 = in, 2 = out
                    "account" => "CF",                           
                    "transactionType" => "Deposit"
                    ],
                1 => [
                    "detail" => "Cash_withdrawal",
                    "cash" => 2,   
                    "account" => "CF", 
                    "transactionType" => "Withdraw"               
                    ], 
                2 => [
                    "detail" => "Primary_market_investment",
                    "cash" => 2,  
                    "account" => "Capital",                 
                    "transactionType" => "Investment"
                    ],
                3 => [
                    "detail" => "Secundary_market_investment",
                    "cash" => 2,
                    "account" => "Capital",                 
                    "transactionType" => "Investment"              
                    ], 
                4 => [
                    "detail" => "Principal_repayment",
                    "cash" => 1,  
                    "account" => "Capital",                 
                    "transactionType" => "Repayment"
                    ],
                5 => [
                    "detail" => "Partial_principal_repayment",
                    "cash" => 1, 
                    "account" => "Capital",                
                    "transactionType" => "Repayment"               
                    ], 
                6 => [
                    "detail" => "Principal_buyback",
                    "cash" => 1, 
                    "account" => "Capital",                 
                    "transactionType" => "Repayment"
                    ],

                7 => [
                    "detail" => "Principal_and_interest_payment",
                    "cash" => 1, 
                    "account" => "Mix",                 
                    "transactionType" => "Mix"
                    ],       

                8 => [
                    "detail" => "Regular_interest_income",
                    "cash" => 1, 
                    "account" => "PL",                
                    "transactionType" => "Income"               
                    ], 
                9 => [
                    "detail" => "Delayed_interest_income",
                    "cash" => 1,  
                    "account" => "PL",                 
                    "transactionType" => "Income"
                    ],
                10 => [
                    "detail" => "Late_payment_fee_income",
                    "cash" => 1,  
                    "account" => "PL",                 
                    "transactionType" => "Income"               
                    ], 
                11 => [
                    "detail" => "Cash_deposit",
                    "cash" => 1,  
                    "account" => "PL",                 
                    "transactionType" => "Income"
                    ],
                12 => [
                    "detail" => "Interest_income_buyback",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income"               
                    ], 
                13 => [
                    "detail" => "Delayed_interest_income_buyback",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income"
                    ],
                14 => [
                    "detail" => "Cash_withdrawal",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income"              
                    ], 
                15 => [
                    "detail" => "Cash_deposit",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income"
                    ],
                16 => [
                    "detail" => "Cash_withdrawal",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income"               
                    ],  
                17 => [
                    "detail" => "Recoveries",
                    "cash" => 1, 
                    "account" => "PL", 
                    "transactionType" => "Income"               
                    ],  
                18 => [
                    "detail" => "Commission",
                    "cash" => 2,
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ], 
                19 => [
                    "detail" => "Bank_charges",
                    "cash" => 2,
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ], 
                20 => [
                    "detail" => "Premium_paid_secondary_market",
                    "cash" => 2,    
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ], 
                21 => [
                    "detail" => "Interest_payment_secondary_market_purchase",
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ],            
                22 => [
                    "detail" => "Tax_VAT",
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ], 
                23 => [
                    "detail" => "Tax_income_withholding_tax",
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ],            
                24 => [
                    "detail" => "Write-off", 
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs"               
                    ]    
            ];

   
    function __construct() {
        echo "starting parser\n";
 //       parent::__construct();    

//  Do whatever is needed for this subsclass
    }        
        

        
    /**
     * Starts the process of analyzing the file and returns the results as an array
     *  @param  $file           Name(s) of the file to analyze
     *  @param  $referenceFile  Name of the file that contains configuration data of a specific "document"/PFP
     *  @return array   $analyzedData
     *          false in case an error occurred
     */
    public function analyzeFile($file, $referenceFile) {
        echo "INPUT FILE = $file, and referenceFile = \n";
 //       print_r($referenceFile);
       // determine first if it csv, if yes then run command
        $fileNameChunks = explode(DS, $file);
        if (stripos($fileNameChunks[count($fileNameChunks) - 1], "CSV")) {
    //        $command = "iconv -f cp1250 -t utf-8 " . $file " > " $file ";
            $inputFileType = 'CSV';
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);    
            $objReader->setDelimiter($this->Config['separatorChar']);
            $objPHPExcel = $objReader->load($file);            
            //execute command php has a function for this which works on a string
        }
        else {      // xls/xlsx file
            $objPHPExcel = PHPExcel_IOFactory::load($file);            
        }  
        
        ini_set('memory_limit','1024M');
        $sheet = $objPHPExcel->getActiveSheet(); 
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();
        echo " high = $highestRow and $highestColumn \n";

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $datas = $this->saveExcelToArray($sheetData, $referenceFile, $offset);
        return $datas;
        
        }
   
        
  /**
     * Analyze the received data using the configuration data and store the result
     * in an array
     *
     * @param string $rowDatas  the excel data in an array
     * @param string $values     the array with configuration data for parsing
     * @param int $offset       the number of indices at beginning of array which are NOT to be parsed
     * @return array $temparray the data after the parsing process
     * 
     */
    private function saveExcelToArray($rowDatas, $values, $offset) {
        $tempArray = [];
  
        $i = 0;
        foreach ($rowDatas as $key => $rowData) {
            if ($i == $this->offsetStart) {
                break;
            }
            unset($rowDatas[$key]);  
            $i++;
        }

        
        $i = 0;
        $outOfRange = false;
 //       print_r($rowDatas);
        
        foreach ($rowDatas as $keyRow => $rowData) {
            echo "Reading a NEW ROW\n";
            foreach ($values as $key => $value) {
                $previousKey = $i - 1;
                $currentKey = $i;
                // check for subindices and construct them
                if (array_key_exists("name", $value)) {
//                    echo "ANTOINE\n";
                    $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $value['name']) . "']"; 
                    $tempString = $finalIndex  . "= '" . $rowData[$key] .  "'; ";
                    eval($tempString);
                }
                else { 
//                    echo "CHARO\n";
                    foreach ($value as $userFunction ) {
                        echo "---------------------------------------------------------------------\n";
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
                                    print_r($tempArray);
                                    $temp = explode(".", $input);
                                    $userFunction["inputData"][$keyInputData] = $tempArray[$currentKey][$temp[1]];    
                                }                                         
                            }  
                        }
                        array_unshift($userFunction['inputData'], $rowData[$key]);       // Add cell content to list of input parameters

                        if ($outOfRange == false) {
                            $tempResult = call_user_func_array(array(__NAMESPACE__ .'Fileparser',  
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
            if (!empty($config['sortParameter'])) {
                if (array_key_exists($config['sortParameter'], $tempArray[$i]) ){
                     $tempArray[ $tempArray[$i][$config['sortParameter']] ][]  = $tempArray[$i];
                }
                else {      // move to the global index
                    $tempArray['global'][] = $tempArray[$i];
                }
            }  

     //        unset($tempArray[$i]);
            $i++; 
        }
echo "END OF LOOP \n"; 

// Delete the numeric indices. This should not be necesary but the code above does
// NOT work, the bad line is "unset($tempArray[$i]);".
// So below is a stupid work-around
        for ($i; $i >= 0; $i--) {
            unset($tempArray[$i]);
            echo "delete index $i  \n";
        }
        
        print_r($tempArray);
        echo __FUNCTION__ . " " . __LINE__ . " \n";       
        return $tempArray;
    }
    
   
    /**
     * Returns information of the last occurred error
     *
     *  @return array   $analyzedData
     *          false in case an error occurred
     */
    public function getLastError()  {
        return $this->errorData;
    }
    
    /**
     * 
     * 
     * the following parameters can be configured:
     * offsetStart
     * offsetEnd
     * sortParameter
     * separatorChar
     * 
     */
    public function setConfig($configurations)  { 
        foreach ($configurations as $configurationKey => $configuration) {
            $this->$configurationKey = $configuration;          // avoid deleting already specified config parameters
        }
    }


    
    /**
     *
     * 	Read the normalized transaction details
     * 
     * 	@return array $transactionDetails
     *
     */
    private function getTransactionDetails() {
        return $this->transactionDetails;
    }

    
    /**
     *
     * 	Read the supported currencies and their properties
     * 
     * 	@return array $currencies
     *
     */
    private function getCurrencyDetails() {
        return $this->currencies;
    }


    /**
     *
     * 	Read number of decimals to be used for amounts
     * 
     * 	@return int $numberOfDecimals
     *
     */
    public function getNumberofDecimals() {
        return $this->numberofDecimals;
    }   
    
       
   
    
    /**
     * Converts any type of date format to internal format yyyy-mm-dd
     * 
     * @param string $date  
     * @param string $currentFormat:  Y = 4 digit year, y = 2 digit year
     *                                M = 2 digit month, m = 1 OR 2 digit month (no leading 0)
     *                                D = 2 digit day, d = 1 OR 2 digit day (no leading 0)
     *                         
     * @return string   date in format yyyy-mm-dd
     * 
     */
    private function normalizeDate($date, $currentFormat) {
        $internalFormat = $this->multiexplode(array(":", " ", ".", "-", "/"), $currentFormat);
        (count($internalFormat) == 1 ) ? $dateFormat = $currentFormat : $dateFormat = $internalFormat[0] . $internalFormat[1] . $internalFormat[2];
        $tempDate = $this->multiexplode(array(":", " ", ".", "-", "/"), $date);
        print_r($tempDate);     
        if (count($tempDate) == 1) {
           return;
        }
       
        $finalDate = array();
    
        $length = strlen($dateFormat);
        for ($i = 0; $i < $length; $i++) {
            switch ($dateFormat[$i]) {
                case "d":
                    $finalDate[2] = $this->norm_date_element($tempDate[$i]);
                break;
                case "D":
                    $finalDate[2] = $tempDate[$i];
                break;              
                case "m":
                    $finalDate[1] = $this->norm_date_element($tempDate[$i]);
                break;
                case "M":
                    $finalDate[1] = $tempDate[$i]; 
                break;  
                case "y":
                    $finalDate[0] = "20" . $tempDate[$i]; 
                break;
                case "Y":
                    $finalDate[0] = $tempDate[$i]; 
                break;              
            }
        }  
        
        $returnDate = $finalDate[0] . "-" . $finalDate[1] . "-" . $finalDate[2];   
        list($y, $m, $d) = array_pad(explode('-', $returnDate, 3), 3, 0);
        
        if (ctype_digit("$y$m$d") && checkdate($m, $d, $y)) {                           // check if date is a real date according to internal format
            return $returnDate;
        }
        return;
    }  


    /**
     * normalize a day or month element of a date to two (2) characters, adding a 0 if needed
     * 
     * @param string $val  Value to be normalized to 2 digits 
     * @return string 
     * 
     */   
    private function norm_date_element($val) {
	if ($val < 10) {
		return (str_pad($val, 2, "0", STR_PAD_LEFT));
	}
	return $val;
    }
 
    /**  STILL TO DO (scientific) exponential notation
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
    private function getAmount($input, $thousandsSep, $decimalSep, $decimals) {
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
     * Translates the currency to internal representation. 
     * The currency can be the ISO code or the currency symbol.
     * Not full-proof as many currencies share the $ sign
     * 
     * @param string $loanCurrency  
     * @return integer  constant representing currency 
     * 
     */
    private function getCurrency($loanCurrency) {
        $details = new Parser();
        $currencyDetails = $details->getCurrencyDetails();
        unset($details);
        
        $filter = array(".", ",", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $currencySymbol = str_replace($filter, "", $loanCurrency);
        
        foreach ($currencyDetails as $currencyIndex => $currency) {
            if ($loanCurrency == $currency[0]) {                // check the ISO code
              return $currencyIndex;
            }   
            if ($currencySymbol == $currency[1]) {              // check the symbol
              return $currencyIndex;
            }  
        } 
    }
   
    
    /**
     * get hash of a string
     * 
     * @param string    $input
     * @return string   $extractedString
     *       
     */
    private function getHash($input) {
        return  hash ("md5", $input, false);
    }  
   
   
    /** 
     * Reads the transaction type of the cashflow operation
     * 
     * @param string   $input
     * @return array   $parameter2  List of all concepts of the platform
     *       
     */
    private function getTransactionDetail($input, $config) {
        foreach ($config as $key => $configItem) {
            $position = stripos($input, $configItem[0]);
            if ($position !== false) {
                return $configItem[1];
            }
        }
    }     
    
    /**
     * Search for a something within a string, starting after $search
     * and ending when $seperator is found
     * 
     * @param string    $input
     * @param string    $search
     * @param string    $separator   The separator character
     * @return string   $extractedString
     *       
     */
    private function extractDataFromString($input, $search, $separator ) {
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
    private function getTransactionType($input, $config) {  
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
    private function getRowData($input, $field, $overwrite) {  

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
    
    
    private function multiexplode ($delimiters,$string) {
        $ready = str_replace($delimiters, $delimiters[0], $string);
        $launch = explode($delimiters[0], $ready);
        return  $launch;
    } 
  
}
