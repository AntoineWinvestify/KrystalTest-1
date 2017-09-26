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
 * @author 
 * @version
 * @date
 * @package
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
     *      $data['PFPname']['userReference']
     *      $data['PFPname']['queue_id']
     *      $data['PFPname']['files'][filename']       array of filenames, FQDN's
     *      
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
 echo __FILE__ . " " . __LINE__ . "\n";     
        $platformData = json_decode($job->workload(), true);

        $collectLoanIds = array();
        
        foreach ($platformData as $platformKey => $data) {
            if ($platformKey <> "mintos") { continue;}
            $companyHandle = $this->companyClass($platformKey);
            
            echo "CURRENT PLATFORM = $platformKey \n";
            // Deal first with the transaction file(s)
            print_r($data);
            $files = $this->data['files']; 
            $myParser = new Fileparser();

            $parserConfig = $companyHandle->getParserConfigTransactionFile();
            echo __FILE__ . " " . __LINE__ . " parserConfig = $parserConfig\n";

            echo $parserConfig;
        
            $params[] = "parserConfig this is a string for antoine de Poorter\n";
            //    return json_encode($params);                                        // normal end of execution of worker  

            unset($companyHandle);
        }

    //    return json_encode($params);
        
        
        
            foreach ($data['files'] as $key => $filename) {             // check all the platforms of the investor
                if ($data['files'['filetype']] == "CSV") {
                        $config = array('seperatorChar' => ";",         // Only makes sense in case input file is a CSV.
                                'sortByLoanId'  => true,                // Make the loanId the main index and all XLS/CSV entries of
                                                                        // same loan are stored under the same index.
                                'offset'    => 3,                       // Number of "lines" to be discarded at beginning of file

                                );
                        //parse cvs 
                }
                else {
                    //parse XLS
                }

                $myParser = new FileParser($config);           

                $myCompany = companyClass($data['PFPname']);        // create instance of companyCodeClass

                if (!empty($parsedFile = $myParser->analyse(data['filename'], $configFile)) ) {    // if successfull analysis, result is an array 
                                                                                                            // with loanId's as indice 

                    if ($myCompany->fileanalyzed($fileName, $typeOfFile, $fileContent)) {                   // Generate the callback function
                        // continue 
                    }
                    else {
                        // an error has occurred or been detected by companycode file. Exit gracefully
                        return false;
                    }

                    //run through the array and search for new loans.
                    $loans = array_keys($parsedFileStructure);
                    $this->Investment = ClassRegistry::init('Investment');

                    // execute callback
                    $result = $mycompany->beforeamortizationlist($parsedFile);

                    foreach ($loans as $key => $loan) {
                        if ($key == "global") {
                            continue;
                        }

                        $this->Investment->create();
                        // check if loanId already exists for the current user
                        $conditions = array('investment_loanReference' => $loan); // for the user

                        $resultInvestment = $this->Investment->find("all", $params = array('recursive' =>  -1,
                                                                                          'conditions' => $conditions,
                                                                            ));
                        if (empty($resultInvestment)) {                                                     // loanId does not yet exist for current user
                            $collectLoanIds[$data['PFPname']] = $loan;                                      // A Gearman worker has to collect the amortization Tables
                        } 


                    }
                    if ($mycompany->amortizationtablesdownloaded($XXXXXXXX) ) {

                    }
                    else {
                        // store error
                    }
                }
                else {                                                          // Error encountered
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
                }
            }
            unset($myParser); 
   return json_encode($params);
        

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



        
        
    }    
    
    
    
    
    
    
   
    /** COPIED FROM APP.CONTROLLER
     *
     * 	Creates a new instance of class with name company, like zank, or comunitae....
     *
     * 	@param 		int 	$companyCodeFile		Name of "company"
     * 	@return 	object 	instance of class "company"
     *
     */
    function companyClass($companyCodeFile) {

        $dir = Configure::read('companySpecificPhpCodeBaseDir');
        $includeFile = $dir . $companyCodeFile . ".php";
        require_once($dir . 'p2pCompany.class' . '.php');   // include the base class IMPROVE WITH spl_autoload_register
        require_once($includeFile);
        $newClass = $companyCodeFile;
        $newComp = new $newClass;
        return $newComp;
    }    
    
    
    
    
    
}







    /**
     * 
     * Class that can analyze a xls/csv/pdf file and put the information in an array
     * 
     * 
     */
    class Fileparser {

    protected $offsetStart = 0;
    protected $offsetEnd = 0;
    protected $seperatorChar = ";";
    protected $sortParameter = "";              // used to "sort" the array and use $sortParameter as prime index. 
                                                // if array does not have $sortParameter then "global2 index is used
                                                // Typically used for sorting by loanId index
    
    protected $errorData;                       // Contains the information of the last occurred error
                                                // THIS IS MOST LIKELY AN ARRAY/JSON 
    
    /**
     * Starts the process of analyzing the file
     *
     *  @return array   $analyzedData
     *          false in case an error occurred
     */
    public function analyzeFile($file, $referenceFile) {
       // determine first if it csv, if yes then run command
        $fileNameChunks = explode(DS, $file);
        if (strpos($fileNameChunks[count($fileNameChunks)-1], "CSV")) {
    //        $command = "iconv -f cp1250 -t utf-8 " . $file " > " $file ";
  //        $inputFileType = 'CSV';
            $inputFileName = '/var/www/html/compare_local/twino-investments.xlsx';
  //      $objReader = PHPExcel_IOFactory::createReader($inputFileType);  
//        $objReader->setDelimiter(";");
//        $objPHPExcel = $objReader->load($inputFileName);            
            //execute command php has a function for this which works on a string
        }
        else {      // xls/xlsx file


            $objPHPExcel = PHPExcel_IOFactory::load($file);            
   //     $objPHPExcel = PHPExcel_IOFactory::load($file);
        }  
        
        ini_set('memory_limit','1024M');
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
  
        /*$loadedSheetNames = $objPHPExcel->getSheetNames();
        foreach ($loadedSheetNames as $sheetIndex => $loadedSheetName) {
            echo '<b>Worksheet #', $sheetIndex, ' -> ', $loadedSheetName, ' (Raw)</b><br />';
            $objPHPExcel->setActiveSheetIndexByName($loadedSheetName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, false, false, true);
            //var_dump($sheetData);
            echo '<br />';
        }*/
     
 
 // All data has been loaded, let's analye it 
        
        
        $datas = $this->saveExcelArrayToTemp($sheetData, $values_twino_investment, $offset);

   

   
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
            if ($i == $this->offsetStart) {
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
    
   
   
   
    }

    
    /**
     * Returns informationn of the last occurred error
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
     * seperatorChar
     * 
     */
    public function setConfig()  {
        
        
    }
        
    
    
    
    
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

    public $currencies = array(EUR => ["EUR", "€"], 
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
    
    
    
    
    function __construct() {
        echo "starting parser\n";
 //       parent::__construct();    

//  Do whatever is needed for this subsclass
    }

    
    /**
     *
     * 	Read the normalized transaction details
     * 
     * 	@return array $transactionDetails
     *
     */
    function getTransactionDetails() {
        return $this->transactionDetails;
    }

    
    /**
     *
     * 	Read the supported currencies and their properties
     * 
     * 	@return array $currencies
     *
     */
    function getCurrencyDetails() {
        return $this->currencies;
    }


    /**
     *
     * 	Read number of decimals to be used for amounts
     * 
     * 	@return int $numberOfDecimals
     *
     */
    function getNumberofDecimals() {
        return $this->numberofDecimals;
    }   
    
       
    
    
}
