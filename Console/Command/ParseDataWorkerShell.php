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
 * Normal return also include some basic return data, like queue_id and user_reference
 * The worker will parse the data for each and every platform for which data has been
 * supplied by the worker.
 * 
 * Errors are initially taken care of in the worker and will spark eventually the exception 
 * Callback with some extra user data.
 * If an error is encountered then the respective error data is stored in an internal array 
 * (per PFP). 
 * If possible, the worker will deal with *all* the PFP's as instructed by the client
 * even if an error is found in one of the PFP's.
 * 
 *  
 * @author 
 * @version
 * @date
 * @package
 * 
 * 
 *
 * 
 * TO BE DONE:
 * CHECK THE STRUCTURE OF A XLS/XLSX/CSV FILE BY CHECKING THE NAMES OF THE HEADERS.  
 * detecting "unknown concept" 
 * 
 */

class ParseDataWorkerShell extends AppShell {
 
    protected $GearmanWorker;
    
 //   var $uses = array();      // No models used
    
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
     *      $data['linkedAccountId']['queue_id']
     *      $data['linkedAccountId']['pfp']
     *      $data['linkedAccountId']['files'][filename1']           => array of filenames, FQDN's
     *      $data['linkedAccountId']['files'][filename2']  
     *                                        ... ... ... 
     *      $data['linkedAccountId']['listOfCurrentActiveLoans']    => list of all active loans BEFORE this analysis
     * 
     * 
     * 
     * 
     * @return array queue_id, userReference, exception error
     *  The worker provides all error information to the Client
     * 
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                      array with all errorData related to occurred error
     *
     * 
     * 
     * The investment_* parsing will parse the whole contents, but afterwards a consolidation 
     * takes that will filter out only the investments with some kind of change during the current
     * reading period.
     * 
     */   
    public function parseFileFlow($job) {

        $platformData = json_decode($job->workload(), true);

        foreach ($platformData as $linkedAccountKey => $data) {
            if ($data['pfp'] <> "mintos") { // TO BE REMOVED           TO BE REMOVED
                continue;
            }
            $platform = $data['pfp'];
            $companyHandle = $this->companyClass($data['pfp']);
            
            echo "CURRENT PLATFORM = " . $data['pfp'] . "\n";
            // Deal first with the transaction file(s)
            print_r($data);
            $files = $data['files']; 
            // First analyze the transaction file(s)
            $myParser = new Fileparser();       // We are dealing with an XLS file so no special care needs to be taken
            
// do this first for the transaction file and then for investmentfile(s)
            $fileTypesToCheck = array (0 => TRANSACTION_FILE, 
                                       1 => INVESTMENT_FILE);
            
            foreach ($fileTypesToCheck as $actualFileType) {               
                $approvedFiles = $this->readFilteredFiles($files,  $actualFileType);
                if ($actualFileType == INVESTMENT_FILE) {
                    $parserConfig = $companyHandle->getParserConfigInvestmentFile();
                }
                else {
                    $parserConfig = $companyHandle->getParserConfigTransactionFile();
                }

                $tempResult = array();
                foreach ($approvedFiles as $approvedFile) { 
                    unset($errorInfo);
                    
                    $myParser->setConfig(array('sortParameter' => "investment.investment_loanId"));
                    echo __FILE__ . " " . __LINE__ . "\n";
                    $tempResult = $myParser->analyzeFile($approvedFile, $parserConfig);     // if successfull analysis, result is an array with loanId's as index 
echo __FILE__ . " " . __LINE__ . "\n";

                    echo "Writing File $approvedFile\n";
                    if (empty($tempResult)) {                // error occurred while analyzing a file. Report it back to Client
                        $errorInfo = array( "typeOfError"   => "parsingError",
                                            "errorDetails"  => $myParser->getLastError(),
                                            ); 
                        $returnData[$linkedAccountKey]['error'][] = $errorInfo;  
                    } 
                    else {       // all is OK 
                        if ($actualFileType == INVESTMENT_FILE) {
                            $totalParsingresultInvestments = $tempResult;    // add $result, combine the arrays                         
                        }
                        if ( $actualFileType == TRANSACTION_FILE) {
                            $totalParsingresultTransactions = $tempResult;                       
                        } 
                        
                        try {
                            $callBackResult = $companyHandle->fileAnalyzed($approvedFile, $actualFileType, $tempResult);       // Generate callback
                        }
                        catch (Exception $e){
                            $errorInfo = array( "typeOfError"   => "callBackExceptionError",
                                                "callBackResultCode" => $callBackResult,
                                                "exceptionResult"   => $e,
                                                "fileName"      => $fileName,
                                                "typeOfFile"    => $typeOfFile,
                                                "fileContents"  => json_encode($file,$fileContent)
                                                );
                            $returnData[$linkedAccountKey]['error'][] = $errorInfo;
                        }
echo __FILE__ . " " . __LINE__ . "\n";
                        //run through the array and search for new loans.
/*
                        foreach ($totalParsingresult as $loanKey => $loan) {
                            if ($loanKey == "global") {
                                continue;
                            }
                        }
                        // store everything in an array so we can return it to the Client
                        $returnData[$linkedAccountKey]['newLoans'] = $newLoans;
                        $returnData[$linkedAccountKey]['parsingResult'] = $totalParsingresult;
                        unset($newLoans);
 */
                    } 
                }
            }

            foreach ($totalParsingresultTransactions as $loanIdKey => $transaction) {
                $totalParsingresultInvestmentsTemp[$loanIdKey] = $totalParsingresultInvestments[$loanIdKey][0];
      
                if ( !array_key_exists ($loanIdKey , $totalParsingresultInvestments ))  { 
                    echo "NO found match for loanId = $loanIdKey  \n"; // THIS IS NEVER POSSIBLE
                }
            }
        
 echo __FILE__ . " " . __LINE__ . "   \n"; 

            $returnData[$linkedAccountKey]['parsingResultTransactions'] = $totalParsingresultTransactions;
            $returnData[$linkedAccountKey]['parsingResultInvestments'] = $totalParsingresultInvestmentsTemp;       
            $returnData[$linkedAccountKey]['userReference'] = $data['userReference'];
            $returnData[$linkedAccountKey]['queue_id'] = $data['queue_id']; 
            $returnData[$linkedAccountKey]['pfp'] = $platform;

            foreach ($totalParsingresultTransactions as $loanIdKey => $transaction) {    
                echo ".";
                if (array_search($loanIdKey, $listOfCurrentActiveLoans) !== false) {         // Check if new investments have appeared
                    $newLoans[] = $loanIdKey;
                }       
            }
            $returnData[$linkedAccountKey]['newLoans'] = $newLoans;
            unset( $newLoans);
        }
        print_r($returnData);
        return json_encode($returnData); 
    }     
}   
    
            









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

        protected $transactionDetails = [  // CHECK THIS Table againsT TYPE OF STATEMENTS  OF FLOWDATA
                0 => [
                    "detail" => "Cash_deposit",
                    "cash" => 1,                                    // 1 = in, 2 = out
                    "account" => "CF",                           
                    "transactionType" => "Deposit",
                    "type" => "userdatainvestment.userdatainvestment_deposits"
                    ],
                1 => [
                    "detail" => "Cash_withdrawal",
                    "cash" => 2,   
                    "account" => "CF", 
                    "transactionType" => "Withdraw",
                    "type" => "userdatainvestment.userdatainvestment_deposits"           
                    ], 
                2 => [
                    "detail" => "Primary_market_investment",
                    "cash" => 2,  
                    "account" => "Capital",                 
                    "transactionType" => "Investment",
                    "type" => "investment.1",
                    ],
                3 => [
                    "detail" => "Secundary_market_investment",
                    "cash" => 2,
                    "account" => "Capital",                 
                    "transactionType" => "Investment",
                    "type" => "investment.2"                       
                    ], 
                4 => [
                    "detail" => "Principal_repayment",
                    "cash" => 1,  
                    "account" => "Capital",                 
                    "transactionType" => "Repayment",
                    "type" => "investment.4"                        
                    ],
                5 => [
                    "detail" => "Partial_principal_repayment",
                    "cash" => 1, 
                    "account" => "Capital",                
                    "transactionType" => "Repayment",
                    "type" => "investment.5"                        
                    ], 
                6 => [
                    "detail" => "Principal_buyback",
                    "cash" => 1, 
                    "account" => "Capital",                 
                    "transactionType" => "Repayment",
                    "type" => "investment.6"                        
                    ],
                7 => [
                    "detail" => "Principal_and_interest_payment",
                    "cash" => 1, 
                    "account" => "Mix",                 
                    "transactionType" => "Mix",
                    "type" => "investment.7"                        
                    ],       
                8 => [
                    "detail" => "Regular_gross_interest_income",
                    "cash" => 1, 
                    "account" => "PL",                
                    "transactionType" => "Income",
                    "type" => "investment.8"                        
                    ], 
                9 => [
                    "detail" => "Delayed_interest_income",
                    "cash" => 1,  
                    "account" => "PL",                 
                    "transactionType" => "Income",
                    "type" => "investment.9"                       
                    ],
                10 => [
                    "detail" => "Late_payment_fee_income",
                    "cash" => 1,  
                    "account" => "PL",                 
                    "transactionType" => "Income",
                    "type" => "investment.10"                        
                    ], 
                11 => [
                    "detail" => "Cash_deposit",
                    "cash" => 1,  
                    "account" => "PL",                 
                    "transactionType" => "Income",
                    "type" => "investment.11"                        
                    ],
                12 => [
                    "detail" => "Interest_income_buyback",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income",
                    "type" => "investment.12"                        
                    ], 
                13 => [
                    "detail" => "Delayed_interest_income_buyback",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income",
                    "type" => "investment.13" 
                    ],
                14 => [
                    "detail" => "Cash_withdrawal",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income",
                    "type" => "investment.14"                        
                    ], 
                15 => [
                    "detail" => "Cash_deposit",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income",
                    "type" => "investment.15"                        
                    ],
                16 => [
                    "detail" => "Cash_withdrawal",
                    "cash" => 1,
                    "account" => "PL", 
                    "transactionType" => "Income",
                    "type" => "investment.16"                        
                    ],  
                17 => [
                    "detail" => "Recoveries",
                    "cash" => 1, 
                    "account" => "PL", 
                    "transactionType" => "Income",
                    "type" => "investment.17"                       
                    ],  
                18 => [
                    "detail" => "Commission",
                    "cash" => 2,
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment.18"                        
                    ], 
                19 => [
                    "detail" => "Bank_charges",
                    "cash" => 2,
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment.19"                        
                    ], 
                20 => [
                    "detail" => "Premium_paid_secondary_market",
                    "cash" => 2,    
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment.20"                        
                    ], 
                21 => [
                    "detail" => "Interest_payment_secondary_market_purchase",
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment.21"                        
                    ],            
                22 => [
                    "detail" => "Tax_VAT",
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment."                        
                    ], 
                23 => [
                    "detail" => "Tax_income_withholding_tax",
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment.22"                        
                    ],            
                24 => [
                    "detail" => "Write-off", 
                    "cash" => 2,  
                    "account" => "PL", 
                    "transactionType" => "Costs",
                    "type" => "investment.23"    
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
     *  @param  $referenceFile  Name of the file that contains configuration data of a specific "document"
     *  @param  $offset_top     The number of lines (=rows) from the TOP OF THE FILE which are not to be included in parser
     *  @param  $offset_bottom  The number of lines (=rows) from, counted from the BOTTOM OF THE FILE which are not to be included in parser                         
     *  @return array   $parsedData
     *          false in case an error occurred
     */
    public function analyzeFile($file, $referenceFile) {
        echo "INPUT FILE = $file \n";
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
        
        ini_set('memory_limit','2048M');
        $sheet = $objPHPExcel->getActiveSheet(); 
        $highestRow = $sheet->getHighestRow(); 
        $highestColumn = $sheet->getHighestColumn();
        echo " Number of rows = $highestRow and number of Columns = $highestColumn \n";

        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        $offset = 5;
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

        foreach ($rowDatas as $keyRow => $rowData) {
//            echo "Reading a NEW ROW\n";
            foreach ($values as $key => $value) {
                $previousKey = $i - 1;
                $currentKey = $i;
                // check for subindices and construct them
                if (array_key_exists("name", $value)) {      // "name" => .......
                    $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $value['name']) . "']"; 
                    $tempString = $finalIndex  . "= '" . $rowData[$key] .  "'; ";
                    eval($tempString);
                }
                else {          // "type" => .......
//                    echo "---------------------------------------------------------------------\n";
                    foreach ($value as $userFunction ) {
                        if (!array_key_exists('inputData',$userFunction)) {
                            $userFunction['inputData'] = [];
                        }
                        else {  // input parameters are defined in config file
                        // check if any of the input parameters require data from
                        // another cell in current row, or from the previous row
                            foreach ($userFunction["inputData"] as $keyInputData => $input) {   // read "input data from config file      
                                if (!is_array($input)) {        // Only check if it is a "string" value, i.e. not an array
                                    if (stripos ($input, "#previous.") !== false) {
                                        if ($previousKey == -1) {
                                            $outOfRange = true;
                                            break;
                                        }
          //                              echo __FUNCTION__ . " " . __LINE__ . "\n";
                                        $temp = explode(".", $input);
                                        $userFunction["inputData"][$keyInputData] = $tempArray[$previousKey][$temp[1]];
                                    }
                                    if (stripos ($input, "#current.") !== false) {
                                        $temp = explode(".", $input);
                                        $userFunction["inputData"][$keyInputData] = $tempArray[$currentKey][$temp[1]];    
                                    }               
                                }                         
                            }             
                        }

//echo "rowdata = " . $rowData[$key] . "\n";
                        array_unshift($userFunction['inputData'], $rowData[$key]);       // Add cell content to list of input parameters
                        if ($outOfRange == false) {
                            $tempResult = call_user_func_array(array(__NAMESPACE__ .'Fileparser',  
                                                                       $userFunction['functionName']), 
                                                                       $userFunction['inputData']);

if (is_array($tempResult)) {
    $userFunction = $tempResult;
//    print_r($userFunction);
//    echo "YES\n";
    $tempResult = $tempResult[0];
}


                            // Write the result to the array with parsing result. The first index is written 
                            // various variables if $tempResult is an array
                            if (!empty($tempResult)) {
                                $finalIndex = "\$tempArray[\$i]['" . str_replace(".", "']['", $userFunction["type"]) . "']"; 
                                $tempString = $finalIndex  . "= '" . $tempResult .  "';  ";
  //                              echo "tempString = $tempString\n";
                                eval($tempString);

                            }
                        }
                        else {
                            $outOfRange = false;        // reset
                        }                   
                    }    
                }
            }

            if (!empty($this->config['sortParameter'])) {
                if (!empty($this->config['sortParameter'])) {
                    $temp = "\$tempArray[\$tempArray[\$i]['" . str_replace(".", "']['", $this->config['sortParameter']) . "']][] = \$tempArray[\$i];"; 
                    eval($temp);
       //             $tempArray[ $tempArray[$i]  ]
                }
                else {      // move to the global index
                    $tempArray['global'][] = $tempArray[$i];
                }
            }  
     //        unset($tempArray[$i]);
        $i++; 
    }

// Delete the numeric indices. This should not be necesary but the code above does
// NOT work, the bad line is "unset($tempArray[$i]);".// So below is a stupid work-around
        for ($i; $i >= 0; $i--) {
            unset($tempArray[$i]);
        }
        return $tempArray;
    }
    
    
    
    
  
    /**
     * Returns the quotient * 100 of a division. This represents the %
     * an unknown "payment" concept was found.
     * @param   string  $input   Content of row
     * @param   int     $divident
     * @param   int     $divisor
     * @param   int     $precision Number of decimals                   
     * @return  
     * 
     * example:  DivisionInPercentage(12,27,0)   => 44
     *           DivisionInPercentage(12,27,1)   => 44.4
     */
    public function divisionInPercentage($input, $divident, $divisor, $precision)  { 
        return round(($divident * 100 )/$divisor, $precision, PHP_ROUND_HALF_UP);
    } 
    
    
    /**
     * Returns information of the last occurred error. Can also detect if
     * an unknown "payment" concept was found.
     *  @return array   $analyzedData
     *          false in case an error occurred
     */
    public function getLastError()  {
        return $this->errorData;
    }
    
    /**
     * Sets one or more configuration parameters. 
     * The following parameters can be configured:
     * 
     *  sortParameter   The name of variable by which the array is to be sorted. The contents of the variable is used as index key
     *                  No default value defined
     *  separatorChar   default value = ";". This parameter is only useful for "csv" files
     *  offset_top      The number of lines (=rows) from the TOP OF THE FILE which are not to be included in parser
     *                  Default value = 1
     *  offset_bottom   The number of lines (=rows) from, counted from the BOTTOM OF THE FILE which are not to be included in parser  
     *                  Default value = 0
     * @param   array   $configurations     list of configuration parameter             
     * @return  boolean OK
     * 
     */
    public function setConfig($configurations)  {
        foreach ($configurations as $configurationKey => $configuration) {
            $this->config[$configurationKey] = $configuration;          // avoid deleting already specified config parameters
        }
        return; 
    }
    
    
     /**
     * Reads the current configuration parameter(s).
     * 
     */
    public function getConfig()  {
        return($this->config);
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
     * Analyze and determine the (preliminary) scope of an undefined payment concept.
     * The algorithm uses a combination of a dictionary search and checking of transaction data 
     * to determine (at least) if it is an income or a cost.
     * 
     * @param string   
     * @param string  
     *                         
     * @return string  
     * also check for the presence of loanId, and + or - sign of field
     */
    private function analyzeUnknownConcept($input, $config) {

    //    read the unknown concept
        $result = 0;
        $dictionaryWords = array('tax'          => COST,
                                'instalment'    => INCOME,
                                'installment'   => INCOME,
                                'payment'       => COST,
                                'back fee'      => COST,
                                'back tax'      => COST,
                                'cost'          => COST,
                                'purchase'      => COST,
                                'bid'           => COST,
                                'auction'       => COST,
                                'sale'          => INCOME,
                                'swap'          => INCOME,
                                'loan'          => COST,
                                'buy'           => INCOME,
                                'sell'          => INCOME,
                                'sale'          => INCOME,
                                'earning'       => INCOME

                            );
        foreach ($dictionaryWords as $wordKey => $word) {
            $position = stripos($input, $wordKey);           
            if ($position !== false) {      // A match was found 
                $result = $word;
                break;
            }
        }
        
        switch($result) {
            case COST:                                  // A result was found.
                return "Unknown_cost";
                break;
            
            case INCOME:                                // A result was found
                return "Unknown_income";
            
            default:                                    // Nothing found, so do some maths to 
                return "Unknown_concept";               // see if it is an income or a cost.
        }
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
//        print_r($tempDate);     
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
     * 
     * Reads the transaction detail of the transaction operation and the variable where to store
     * the result of this function
     * 
     * @param string   $input
     * @return array    [0] => Winvestify standardized concept
     *                  [1] => array of parameter, i.e. list of variables in which the result
     *                         of this function is to be stored. In practice it is normally 
     *                         only 1 variable, but the same value could be replicated in many
     *                         variables.
     *                  The variable name is read from "internal variable" $this->transactionDetails.
     */
    private function getTransactionDetail($input, $config) {
        foreach ($config as $configKey => $configItem) {
            $position = stripos($input, $configKey);
            if ($position !== false) {
                foreach ($this->transactionDetails as $key => $detail) {
                    if ($detail['detail'] == $configItem) {
                        $result = array($configItem,"type" => $detail['type']); 
                        return $result;
                    }
                } 
            }
        } 
        echo "getTransactionDetail => unknown concept encountered\n";
        // an unknown concept was found, do some intelligent guessing about its meaning
        $result = $this->analyzeUnknownConcept($input);          // will return "unknown_income" or unknown_cost"
        return result;         
    }     
  
     /** 
     * Reads the transaction type of the transaction operation
     * 
     * @param string   $input
     * @return array   $parameter2  List of all known concepts of the platform
     *    NO LONGER USED  
     */ /*  
    private function getTransactionType($input, $config) { 
        echo "Function getTransactionType, $input\n";
        foreach ($config as $configKey => $configItem) {
            $position = stripos($input, $configKey);
            echo "position = $position\n";
            if ($position !== false) { 
                
                echo "Found, configKey = $configKey, configItem =\n";
                print_r($configItem);
                return $configItem;
                foreach ($this->transactionDetails as $key => $detail) {
                    echo "\n842   $key = $key\n";
                    if ($detail['detail'] == $configItem) {
                        echo "RETURNING " . $detail['transactionType'] ."\n";
                        return $detail['transactionType'];
                    }
                }
            }
        }         
    }    */

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
