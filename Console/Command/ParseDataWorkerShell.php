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
 * (per P2P).
 * If possible, the worker will deal with *all* the P2P's as instructed by the Client
 * even if an error is found in one of the P2P's.
 *
 *
 * @author
 * @version v 0.2
 * @date    2017-10-26
 * @package
 *
 *
 * 2017-08-11           version 0.1
 * Basic version
 *
 * 
 * 2017-10-26           version 0.2
 * 
 * 2017-10-31           version 0.3
 * Deal with the new file "expiredLoans-x" for obtaining the loanId's of "expired" files
 * 
 * 
 * 
 * 
 * TO BE DONE:
 * CHECK THE STRUCTURE OF A XLS/XLSX/CSV FILE BY CHECKING THE NAMES OF THE HEADERS.
 * detecting "unknown concept"
 * Rename the index loanId of a disinvestment record
 *
 */
App::import('Shell','GearmanWorker');
 
class ParseDataWorkerShell extends GearmanWorkerShell {

    protected $callbacks = [];
    protected $companyHandle;
    protected $myParser;
    protected $cleanValueControlStop = false;
    protected $cleanDepthControl = 0;

    protected $filteredArray;
    protected $tempKey = array();
    protected $tempDepth = 0;      // Required to see if the $depth is decreasing    
    protected $startDate;
    protected $finishDate;
    
    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');

        $this->GearmanWorker->addFunction('parseFileFlow', array($this, 'parseFileFlow'));
        echo __FUNCTION__ . " " . __LINE__ . ": " . "ParseDataWorker starting to listen to data from its Client\n";
        
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
     *      $data['linkedAccountId']['activeInvestments']
     *      $data['linkedAccountId']['actionOrigin']                => Account linking or regular update
     *      $data['linkedAccountId']['listOfReservedInvestments']   => Array of loanIds (Many not always be present)
     *      $data['linkedAccountId']['listOfCurrentActiveInvestments']    => list of all active loans BEFORE this analysis     
     *      $data['linkedAccountId']['files'][filename1']           => Array of filenames, FQDN's
     *      $data['linkedAccountId']['files'][filename2']
     *      $data[$linkedAccountKey]['startDate'] = $data['startDate'];  => startDate of the reading period 
     *      $data[$linkedAccountKey]['finishDate'] = $data['finishDate']; => end date of the reading period

     * @return array 
     *  The worker provides all error information to the Client according to the following format:  
     *      $data['linkedAccountId']['userReference']
     *      $data['linkedAccountId']['queue_id']
     *      $data['linkedAccountId']['pfp']
     *      $data['linkedAccountId']['newLoans']
     *      $data['linkedAccountId']['error’]    => optional
     *      $data['linkedAccountId']['parsingResultTransactions'] 
     *      $data['linkedAccountId'][‘parsingResultInvestments'] 
     *      $data['linkedAccountId']['activeInvestments']
     *      $data['linkedAccountId']['linkedaccountId']
     *      $data['linkedAccountId']['parsingResultControlVariables']
     *      * 

     *
     */
     
    public function parseFileFlow($job) {
 $timeStart = time();       
        //for debugging error purpose
        $this->job = $job;
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data received from Client\n";
        }        
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');    
        
        $platformData = json_decode($job->workload(), true);
        foreach ($platformData as $linkedAccountKey => $data) {
            $platform = $data['pfp'];
            $companyHandle = $this->companyClass($data['pfp']);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Current platform = " . $data['pfp'] . "\n";
            }

            $files = $data['files'];
            $this->startDate = $data['startDate'];
            $this->finishDate = $data['finishDate'];
            // First analyze the transaction file(s)
            $this->myParser = new Fileparser();       // We are dealing with an XLS file so no special care needs to be taken
            $callbacks = $companyHandle->getCallbacks();
            $this->myParser->setDefaultFinishDate($this->finishDate);
            $dashboard2ConfigurationParameters = $companyHandle->getDashboard2ConfigurationParameters();

            foreach ($files as $fileTypeKey => $filesByType) {
                switch ($fileTypeKey) {
                    case WIN_FLOW_TRANSACTION_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Transaction Files \n";
                        }
                        $parserConfigFile = $companyHandle->getParserConfigTransactionFile();
                        $configParameters = $companyHandle->getParserTransactionConfigParms();
                        break;  
                        
                    case WIN_FLOW_INVESTMENT_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Investment Files \n";
                        }
                        $parserConfigFile = $companyHandle->getParserConfigInvestmentFile(); 
                        $configParameters = $companyHandle->getParserInvestmentConfigParms();
                        break;                        
                        
                    case WIN_FLOW_EXTENDED_TRANSACTION_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Extended Transaction Files \n";
                        } 
                        $parserConfigFile = $companyHandle->getParserConfigExtendedTransactionFile();
                        $configParameters = $companyHandle->getParserExtendedTransactionConfigParms();
                        break; 
                        
                    case WIN_FLOW_CONTROL_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing ControlVariables file \n";
                        } 
                        $parserConfigFile = $companyHandle->getParserConfigControlVariablesFile();
                        $configParameters = $companyHandle->getParserControlVariablesConfigParms();
                        break;                         
                    case WIN_FLOW_EXPIRED_LOAN_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Files with expired Loans\n";
                        } 
                        $parserConfigFile = $companyHandle->getParserConfigExpiredLoanFile(); 
                        $configParameters = $companyHandle->getParserExpiredLoanConfigParms();  
                        break;                        
                }

                if (count($filesByType) === 1) {
echo "\n" . __FILE__. " " . __LINE__ . "\n";                    
                    $tempResult = $this->getSimpleFileData($filesByType[0], $parserConfigFile, $configParameters);
                } 
                else if (count($filesByType) > 1) {
                    $tempResult = $this->getMultipleFilesData($filesByType, $parserConfigFile, $configParameters);
                }

                if (empty($tempResult['error'])) {
                    switch ($fileTypeKey) {
                        case WIN_FLOW_INVESTMENT_FILE:
                            $this->callbackInit($tempResult, $companyHandle, $callbacks["investment"]);
                            $totalParsingresultInvestments = $tempResult;
                            break;
                        case WIN_FLOW_TRANSACTION_FILE:
                            $this->callbackInit($tempResult, $companyHandle, $callbacks["transactionFile"]);
                            $totalParsingresultTransactions = $tempResult;
                            break;                            
                        case WIN_FLOW_EXTENDED_TRANSACTION_FILE:
                        //    $totalParsingresultTransactions = $tempResult;
                            break;
                        case WIN_FLOW_CONTROL_FILE:
                            $totalParsingresultControlVariables = $tempResult;               
                            break;
                        case WIN_FLOW_EXPIRED_LOAN_FILE:
                            unset($listOfExpiredLoans);
                            $this->callbackInit($tempResult, $companyHandle, $callbacks["expiredLoan"]);
                            $totalParsingresultExpiredInvestments = $tempResult;
                            $i = 0;
                            foreach ($tempResult as $expiredLoankey => $item) {
                                $listOfExpiredLoans[] = $expiredLoankey;
                                $i++;
                            }
                            break;                            
                    }
                    unset($tempResult);
                    
/*
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
 */ 
                }
                else {               // error occurred while analyzing a file. Report it back to Client
                    $returnData[$linkedAccountKey]['error'][] = $tempResult['error'];
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
                }
            } 
            
print_r($totalParsingresultInvestments);   
print_r($totalParsingresultExpiredInvestments); 
print_r($totalParsingresultTransactions);
print_r($totalParsingresultControlVariables);

            
            $returnData[$linkedAccountKey]['parsingResultTransactions'] = $totalParsingresultTransactions;
            $returnData[$linkedAccountKey]['parsingResultInvestments'] = $totalParsingresultInvestments;
            $returnData[$linkedAccountKey]['parsingResultControlVariables'] = $totalParsingresultControlVariables[""][0];
            $returnData[$linkedAccountKey]['parsingResultExpiredInvestments'] = $totalParsingresultExpiredInvestments;
            $returnData[$linkedAccountKey]['userReference'] = $data['userReference'];
            $returnData[$linkedAccountKey]['actionOrigin'] = $data['actionOrigin'];
            $returnData[$linkedAccountKey]['pfp'] = $platform;  
            $returnData[$linkedAccountKey]['activeInvestments'] = $data['activeInvestments'];
            $returnData[$linkedAccountKey]['linkedaccountId'] = $linkedAccountKey;
            $returnData[$linkedAccountKey]['startDate'] = $data['startDate'];  
            $returnData[$linkedAccountKey]['finishDate'] = $data['finishDate'];
            $returnData[$linkedAccountKey]['dashboard2ConfigurationParameters'] = $dashboard2ConfigurationParameters;
            $returnData[$linkedAccountKey]['controlVariables'] = $data['totalParsingresultControlVariables'];         
            
// check if we have new loans for this calculation period. Only collect the amortization tables of loans that have not already finished         
            if ($data['actionOrigin'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) {
                echo "action = account linking\n";
                $newLoans = array_keys($returnData[$linkedAccountKey]['parsingResultInvestments']);
            }
            else {      // WIN_ACTION_ORIGIN_REGULAR UPDATE
                $arrayiter = new RecursiveArrayIterator($returnData[$linkedAccountKey]['parsingResultTransactions']);
                $iteriter = new RecursiveIteratorIterator($arrayiter);
                foreach ($iteriter as $key => $value) {
                    if ($key == "investment_loanId"){
                        if (in_array($value, $data['listOfCurrentActiveInvestments']) == false) {         // Check if new investments have appeared
                            $loanIdstructure = explode("_", $value);
                            if ($loanIdstructure[0] == "global") {
                                continue;
                            }
                            if (in_array($value, $listOfExpiredLoans) == false){
                                $newLoans[] = $value;
                            }
                       //     $newLoans[] = $value;         temp fix for Zank
                        }
                    }
                }
            }
            $newLoans = array_unique($newLoans);
            $returnData[$linkedAccountKey]['newLoans'] = $newLoans;
            unset( $newLoans);          
            
            
            if ($data['actionOrigin'] == WIN_ACTION_ORIGIN_REGULAR_UPDATE) {       
// Detect if a loan has been deleted (i.e. NOT matured) or if it has changed state from "Reserved" to "Active
                if (isset($data['listOfReservedInvestments']))  { 
                    foreach ($data['listOfReservedInvestments'] as $loanKey => $loanId) {
                        $existsInActive = array_key_exists($loanId, $totalParsingresultInvestments);
                        if ($existsInActive) {
                            if ($totalParsingresultInvestments[$loanId]['investment_statusOfLoan'] == WIN_LOANSTATUS_ACTIVE) {
                        //      generate a statechange record, state is changed to "active"
                                $dateKeys = array_keys($totalParsingresultTransactions);
                                $key = $dateKeys[count($dateKeys) - 1];                           // Take the last date 
                                $totalParsingresultTransactions[$key][$loanId][100]['date'] = $key;
                                $totalParsingresultTransactions[$key][$loanId][100]['investment_loanId'] = $loanId;
                                $totalParsingresultTransactions[$key][$loanId][100]['internalName'] = "activeStateChange";
echo __FUNCTION__ . " " . __LINE__ . " activeStateChange transaction record generated for loanId = $loanId\n";
                                unset($data['listOfReservedInvestments'][$loanKey]);
                                continue;
                            }
                            if ($totalParsingresultInvestment[$loanId]['investment_statusOfLoan'] == WIN_LOANSTATUS_WAITINGTOBEFORMALIZED) {
echo __FUNCTION__ . " " . __LINE__ . " Loan $loanid detected with state WAITINGTOBEFORMALIZED\n";                                
                                unset($data['listOfReservedInvestments'][$loanKey]);
                                continue;
                            } 
                        }
                    }
                    // $data['listOfReservedInvestments'] now contains only loanIDs of Ghosts.

                 
                    $temp = new BaseClass();
                    $temp->array_keys_recursive($data['listOfReservedInvestments'], 4, "internalName", "disinvestment");
                    $foundArrays = $temp->getlevel();
                    echo __FUNCTION__ . " " . __LINE__ . " \n";
                    print_r($foundArrays);
                    
                    
                    
                    if (count($foundArrays) <> count($data['listOfReservedInvestments'])) {
                        echo "some error occurred in PFP, but we will mark all Ghosts";
                    }

                    foreach ($foundArrays as $key => $levels) {
                        $loan = array_pop($data['listOfReservedInvestments']);
                        $myArray[$levels[0]][$loan][$levels[2]] = $myArray[$levels[0]][$levels[1]][$levels[2]];
                        $myArray[$levels[0]][$loan][0]['investment_loanId'] = $loan;
                        unset($myArray[$levels[0]][$loan][0]['amount']);
                        unset($myArray[$levels[0]][$levels[1]]);                       
                    } 
                }     
            }     
        }
        
        $data['tempArray'] = $returnData;
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
        } 
       
        echo "\nNumber of new loans = " . count($data['tempArray'][$linkedAccountKey]['newLoans']) . "\n";
        echo "Number of expired loans = " . count($data['tempArray'][$linkedAccountKey]['parsingResultExpiredInvestments']) . "\n";
        echo "Number of NEW loans = " . count($data['tempArray'][$linkedAccountKey]['parsingResultInvestments']) . "\n";

        echo "The size of data to be sent to the Client = " . strlen(json_encode($data)) . " Bytes\n";
echo "Done\n";
$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) . "\n"; 

        return json_encode($data);
    }       
        
      
    /**
     * Function to change values depending on callback functions for each company
     * 
     * @param array $tempResult It contains the value to change
     * @param object $companyHandle It is the company instance
     * @return It nothing if the callback array is empty
     */
    public function callbackInit(&$tempResult, $companyHandle, $callbackFunctions) {
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": Dealing with callbacks \n";
        }
        //$this->getCallbackFunction($valuesFile);
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ ;
            print_r($callbackFunctions);
        }
        if (empty($callbackFunctions)) {
            return;
        }
        
        foreach ($callbackFunctions as $functionNameKey => $callback) {
            $this->$functionNameKey($tempResult, $companyHandle, $callback);
        }
    }
    
    /**
     * Function to change values depending on callback functions for each company
     * 
     * @param array $tempResult It contains the value to change
     * @param object $companyHandle It is the company instance
     * @return It nothing if the callback array is empty
     */
    public function parserDataCallback(&$tempResult, $companyHandle, $callbackData) {
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": Dealing with callbacks \n";
        }
        $this->callbacks = $callbackData;
        //$this->getCallbackFunction($valuesFile);
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ ;
            print_r($this->callbacks);
        }
        
        if (empty($this->callbacks)) {
            return;
        }
        
        //$this->cleanData($tempResult, $callbacks["investment"]["cleanTempArray"]);
        
        
        $this->companyHandle = $companyHandle;
        array_walk_recursive($tempResult,array($this, 'changeValueIteratingCallback'));
    }
    
    /**
     * Function to iterate through an array when callback is called and change the value if needed
     * 
     * @param arrayValue $item It is the value of an array key
     * @param arrayKey $key It is the key of the array value
     */
    public function changeValueIteratingCallback(&$item,$key){
        foreach ($this->callbacks as $callbackKey => $callback) {
            if($key == $callbackKey){
                $valueConverted =  $this->companyHandle->$callback(trim($item));
                $item = $valueConverted; // Do This!
           }
        }
    }
    
    /* NOT YET
     * Determine the list of investments that have finished TODAY, i.e. that have
     * outstandingPrincipal = 0.
     * The simple fact that a loanId exists in the $expiredLoans is enough to decide that the loan
     * actually has finished.
     *  
     * @param  array        $investmentList => not used
     * @param  array        $expiredLoans   list of Id,s that have terminated
     * @param  array        $parsingResultTransactions
     * @return array        list of investment ids that finished since the last readout period 
     */   
    public function getListofFinishedInvestmentsA(&$investmentList, &$expiredLoans, &$parsingResultTransactions) {
        $finishedInvestments = array();
        
        $activeTransactions = $this->getLoanIdsActiveTransactions($parsingResultTransactions);
        print_r($activeTransactions);

        foreach ($activeTransactions as $activeTransaction) {
            if (array_key_exists ($activeTransaction, $expiredLoans)) {
                $finishedInvestments[] = $activeTransaction;
            }
        }
        return $finishedInvestments;
    }    
    
    /* NOT YET
     * Determine the list of investments that have finished TODAY, i.e. that have
     * outstandingPrincipal = 0.
     * Check the list of investments for the loanID's and check if the outstandingPrincipal = 0.
     *      
     * @param  array        $investmentList
     * @param  array        $expiredLoans   list of Id,s that have terminated => not used due to unavailability 
     * @param  array        $parsingResultTransactions
     * @return array        list of investment ids that finished since the last readout period
     * 
     * This function is not tested yet, waiting for Antonio para terminar lo de las hojasde un xls and checking 
     * 
     */   
    public function getListofFinishedInvestmentsB(&$investmentList, &$expiredLoans, &$parsingResultTransactions) {
        $finishedInvestments = array();
        
        $activeTransactions = $this->getLoanIdsActiveTransactions($parsingResultTransactions);
        print_r($activeTransactions);  
    
        foreach ($activeTransactions as $activeTransaction) {
            if ($investmentList[$activeTransaction][0]['investment_stateOfLoan'] == WIN_LOANSTATUS_FINISHED) { // NOT CORRECT AS WE DON'T HAVE ACCESS TO investment_stateOfLoan
                $finishedInvestments[] = $activeTransaction;            // Add to list of finished investments
            }
        }
        return $finishedInvestments;
    }    
    
    
    /**
     * get the loanIds obtained during the parsing of the transactions
     * 
     * @param array $tempResult It contains the value to change
     * @param object $companyHandle It is the company instance
     * @return It nothing if the callback array is empty
     * 
     */        
    public function getLoanIdsActiveTransactions(&$parsingResultTransactions) {
        $listExpiredInvestments = array();
        foreach ($parsingResultTransactions as $dateTransactions) {
            $loans = array_keys($dateTransactions);
            foreach ($loans as $loanKey => $loan){
                $position = stripos($loan, "global_");
                if ($position !== false ) {
                    unset ($loans[$loanKey]);
                }
            }
            $listExpiredInvestments = array_merge($listExpiredInvestments, $loans);  
        } 
        return($listExpiredInvestments);
    }
    
    /**
     * Get the data from a single file but it could have a single sheet file or 
     * a file with multiple sheet
     * 
     * @param string $file FQDN of the files
     * @param array $parserConfigFile Array that contains the configuration data of a specific "document"
     * @param array $configParameters Configuration parameters
     * @return array
     */
    public function getSimpleFileData($file, $parserConfigFile, $configParameters) { 
        //We need to pass the 0 value of the array because every company has a two-dimensional array starting with the value 0
        if (isset($configParameters[0]['offsetStart'])) {
            $tempResult = $this->getSimpleSheetData($file, $parserConfigFile[0], $configParameters[0]);
        }
        else {
            $tempResult = $this->getMultipleSheetData($file, $parserConfigFile[0], $configParameters[0]);
        }
        return $tempResult;
    }
    
    /**
     * Get data from multiples files, it could be the same file with the same structure cut in files
     * or different files with different structure
     * 
     * @param string $filesByType FQDN of the files
     * @param array $parserConfigFile Array that contains the configuration data of a specific "document"
     * @param array $configParameters Configuration parameters
     * @return array
     */
    public function getMultipleFilesData($filesByType, $parserConfigFile, $configParameters) {
        $filesJoinedByParts = $this->joinFilesByParts($filesByType);
        //If exit this key in the array, it is a multi variable files data an it has that in the first key of the array
        if (array_key_exists("fileConfigParam", $configParameters)) {
            $orderParam = array_slice($configParameters, 0,1);
        }
        $i = 0;
        $arrayByType = [];
        foreach ($filesJoinedByParts as $filesByType) {
            $tempResult = null;
            $arrayByType[$i] = [];
            foreach ($filesByType as $file) {
                if (!empty($configParameters[$i]['offsetStart'])) {
                    $tempResult = $this->getSimpleSheetData($file, $parserConfigFile[$i], $configParameters[$i]);
                }
                else {
                    //if multiple files, we need an offset and offsetEnd individual
                    //An array is necessary 
                    $tempResult = $this->getMultipleSheetData($file, $parserConfigFile[$i], $configParameters[$i]);
                }
                $arrayByType[$i] = $arrayByType[$i] + $tempResult;
            }
            $this->callbackInit($arrayByType[$i], $companyHandle, $configParameters[$i]["callback"]);
            $i++;
        }
        if (!empty($orderParam)) {
            if ($orderParam['fileConfigParam']['type'] == "joinTogether") {
                //This function comes from the config parameter in the company
                $function = $orderParam['fileConfigParam']['function'];
                $tempResultOrdered = $this->$function($arrayByType, $orderParam);
            }
            else {
                $tempResultOrdered = $this->resultOrdering($arrayByType, $orderParam);
            }
        }
        else {
            $tempResultOrdered = $arrayByType[0];
        }
        return $tempResultOrdered;
    }
    
    /**
     * Get data from one sheet data
     * 
     * @param string $file FQDN of the files
     * @param array $parserConfigFile Array that contains the configuration data of a specific "document"
     * @param array $configParameters Configuration parameters
     * @return array
     */
    public function getSimpleSheetData($file, $parserConfigFile, $configParameters) {
        $config = array (
            'offsetStart' => 0,
            'offsetEnd'     => 0,
            'separatorChar' => ";",
            'sortParameter' => "",
            'changeCronologicalOrder' => 0
        );
        $this->myParser->cleanConfig($config);
        $this->myParser->setConfig($configParameters);
        $extension = $this->getExtensionFile($file);
        $tempResult = $this->myParser->analyzeFile($file, $parserConfigFile, $extension);     // if successfull analysis, result is an array with loanId's as index
        if (empty($tempResult)) {
            $tempResult['error'] = array( 
                "typeOfError"   => "parsingError",
                "errorDetails"  => $this->myParser->getLastError(),
                "errorDetails1" => "approved file " . $file,
            );
        }
        return $tempResult;
    }
    
    /**
     * Get data from multiple sheet data with their individual config parameters
     * 
     * @param string $file FQDN of the files
     * @param array $parserConfigFile Array that contains the configuration data of a specific "document"
     * @param array $configParameters Configuration parameters
     * @return array
     */
    public function getMultipleSheetData($file, $parserConfigFile, $configParameters) {
        $orderParam = array_shift($configParameters);
        foreach ($configParameters as $key => $individualConfigParameters) {     
            $config = array (
                'offsetStart' => 0,
                'offsetEnd'     => 0,
                'separatorChar' => ";",
                'sortParameter' => "",
                'changeCronologicalOrder' => 0
            );
            $this->myParser->cleanConfig($config);
            $this->myParser->setConfig($individualConfigParameters);
            $tempResult[] = $this->myParser->analyzeFileBySheetName($file, $parserConfigFile[$key]);     // if successfull analysis, result is an array with loanId's as index
            if (empty($tempResult)) {
                $tempResult['error'] = array( 
                    "typeOfError"   => "parsingError",
                    "errorDetails"  => $this->myParser->getLastError(),
                    "errorDetails1" => "approved file " . $file,
                );
                break;
            }
        }
        $tempResultOrdered = $this->resultOrdering($tempResult, $orderParam);
        return $tempResultOrdered;
    }
    
    /**
     * Order an array based on ordering parameters
     * 
     * @param array $tempArray Contain all the data
     * @param array $orderParam Contain the order parameters
     * @return array
     */
    public function resultOrdering($tempArray, $orderParam) {
        $countSortParameters = count($orderParam);
        switch ($countSortParameters) {
            case 1:
                $sortParam1 = $tempArray[$i][$orderParam[0]];      
                $tempArray[$sortParam1][] = $tempArray[$i];
                unset($tempArray[$i]); 
            break; 

            case 2:
                $sortParam1 = $tempArray[$i][$orderParam[0]];
                $sortParam2 = $tempArray[$i][$orderParam[1]];        
                $tempArray[$sortParam1][$sortParam2][] = $tempArray[$i];
                unset($tempArray[$i]);
            break;               
        }
        return $tempArray;
    }
    
    /**
     * Join together two or more arrays with the same keys with two levels
     * 
     * @param array $array It is an array of arrays
     * @param array $orderParam With orderParams if needed
     */
    public function joinTwoDimensionArrayTogether($arrays, $orderParam) {
        $numberArrays = count($array);
        $dates = [];
        foreach ($arrays as $array) {
            foreach ($array as $keyDate => $value) {
                if (!in_array($keyDate, $dates)) {
                    $dates[] = $keyDate;
                }
            }
        }
        sort($dates);
        $fullArray = [];
        //$fullArray = array_shift($arrays);
        $i = 0;
        foreach ($dates as $date) {
            $value = null;
            foreach ($arrays as $arrayKey => $array) {
                foreach ($array[$date] as $loanIdKey => $loanData) {
                    foreach ($loanData as $key => $data) {
                        $fullArray[$date][$loanIdKey][] = $data;
                    }
                }
            }
        }
        return $fullArray;
    }
    
    /**
     * Function to join two or more arrays together keeping the keys
     * 
     * @param array $arrays Arrays to join together
     * @param array $orderParam Order parameters of the array
     * @return array The array fully merged
     */
    public function joinOneDimensionArrayTogether($arrays, $orderParam) {
        
        $loanIds = [];
        foreach ($arrays as $array) {
            foreach ($array as $keyLoanId => $value) {
                if (!in_array($keyLoanId, $loanIds)) {
                    $loanIds[] = $keyLoanId;
                }
            }
        }
        $fullArray = [];
        $i = 0;
        foreach ($loanIds as $loanId) {
            $value = null;
            foreach ($arrays as $arrayKey => $array) {
                foreach ($array[$loanId] as $Key => $loanData) {
                    $fullArray[$loanId][] = $loanData;
                }
            }
        }
        
        return $fullArray;
    }
    
    /**
     * Group the FQDN of the files by number, for example, the transaction_1 could have
     * transaction_1_1, transaction_1_2, transaction_1_3 and transaction_2_1
     * This function groups the FQDN of the file in an array bidimensional like
     * $tempArray[1][1], $tempArray[1][2]...
     * 
     * @param string $filesByType The FQDN of the files
     * @return array
     */
    public function joinFilesByParts ($filesByType) {
        $tempArrayFiles = [];
        foreach ($filesByType as $filePath) {
            $file = new File($filePath);
            $nameFile = $file->name();
            $tempNumber = explode("_", $nameFile);
            $tempArrayFiles[$tempNumber[1]][$tempNumber[2]] = $filePath;
        }
        return $tempArrayFiles;
    }
    
    /**
     * Clean the array of unnecessary values using array_walk_recursive_delete
     * @param array $tempArray the array to walk recursively
     * @param object $companyHandle It is the company instance
     * @param array $config Configuration array with functions from which we will clean the array
     * @return null if config not exist
     */
    public function cleanTempArray(&$tempArray, $companyHandle, $config) {
        if (empty($config)) {
            return;
        }
        foreach ($config as $functionNameKey => $values) {
            $this->array_walk_recursive_delete($tempArray, array($this, $functionNameKey), $values);
        }
    }
    
    /**
     * Remove any elements where the callback returns true
     * Code from https://akrabat.com/recursively-deleting-elements-from-an-array/
     * 
     * @param  array    $array    the array to walk
     * @param  callable $callback callback takes ($value, $key, $userdata)
     * @param  mixed    $userdata additional data passed to the callback.
     * @return array
     */
    function array_walk_recursive_delete(&$array, callable $callback, $valuesToDelete, $userdata = null) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                $value = $this->array_walk_recursive_delete($value, $callback, $valuesToDelete, $userdata);
            }
            if ($this->cleanValueControlStop && $this->cleanDepthControl < $valuesToDelete['valueDepth']) {
                unset($array[$key]);
                $this->cleanDepthControl++;
                if ($this->cleanDepthControl == $valuesToDelete['valueDepth']) {
                    $this->cleanDepthControl = 0;
                    $this->cleanValueControlStop = false;
                }
            }
            else if ($callback($value, $key, $valuesToDelete, $userdata)) {
                unset($array[$key]);
            }
        }
        return $array;
    }
   
    /**
     * Function to find a value in an array
     * @param string/integer $value It is the actual value
     * @param string/integer $key It is the key of the array 
     * @param array $valuesToDelete They are the values to find and delete
     * @param mixed $userdata additional data passed to the callback
     * @return boolean
     */
    function findValueInArray($value, $key, $valuesToDelete, $userdata = null) {
        $result = false;
        if (is_array($value)) {
            return empty($value);
        }
        if ($key == $valuesToDelete['key']) {
            foreach ($valuesToDelete['values'] as $valueToDelete) {
                $functionToCall = $valuesToDelete['function'];
                if ($this->$functionToCall($value, $valueToDelete)) {
                    $result = true;
                    $this->cleanValueControlStop = true;
                    break;
                }
            }
        }
        return $result;
    }
    
    /**
     * Function to verify if two data are equal
     * @param string/integer $value Value from array
     * @param string/integer $valueToVerify Value to find
     * @return boolean
     */
    public function verifyEqual($value, $valueToVerify) {
        $result = false;
        if ($value === $valueToVerify) {
            $result  = true;
        }
        return $result;
    }
    
    /**
     * Function to verify if two data are not equal
     * @param string/integer $value Value from array
     * @param string/integer $valueToVerify Value to find
     * @return boolean
     */
    public function verifyNotEqual($value, $valueToVerify) {
        $result = false;
        if ($value !== $valueToVerify) {
            $result  = true;
        }
        return $result;
    }
   
    
    /**
     * Clean the array of unnecessary dates
     * @param array $tempArray the array to clean
     * @param object $companyHandle It is the company instance
     * @param array $config Configuration array with values to use to delete
     * @return null if $config not exist or $startDate is empty
     */
    public function cleanDatesTempArray(&$tempArray, $companyHandle, $config) {
        if (empty($config)) {
            return;
        }
        if (empty($this->startDate)) {
            return;
        }
        $rangeDates = $this->createDateRange($this->startDate, $this->finishDate);
        array_shift($rangeDates);
        array_push($rangeDates, $this->finishDate);
        foreach ($tempArray as $keyDate => $data) {
            $date = date("Ymd", strtotime($keyDate));
            if (!in_array($date, $rangeDates)) {
                unset($tempArray[$keyDate]);
            }
        }
    }
    
}

class BaseClass {
    public $filteredArray;
    public $tempKey = array();
    public $tempDepth = 0;      // Required to see if the $depth is decreasing
 
 
function getlevel() {
    return $this->filteredArray;
  }  
    
    /**
     * Recursively extracts arrays from a list of arrays according to filter conditions (name-value of the array fields)
     * 
     * @param  array    $inputArray     the array to walk
     * @param  int      $maxDepth       Maximum depth level you like to search (recursive)
     * @param  string   $searchKey      Key to search for
     * @param  string   $searchValue    Corresponding value of the key
     * @return array    array with the set of indices for each matched array
     */
    function array_keys_recursive(&$inputArray, $maxDepth, $searchKey, $searchValue, $depth = 0 ){

        if ($depth < $maxDepth) {
            $depth++;
            $keys = array_keys($inputArray);

            foreach($keys as $key){
                if ($this->tempDepth > $depth) {
                    $control = $this->tempDepth - $depth;
                    for ($i = 0; $i < $control; $i++)  {               
                        array_pop($this->tempKey);
                    }    
                }
                $this->tempKey[] = $key;
                $this->tempDepth = $depth;

                if(is_array($inputArray[$key])){
                    $arrayKeys[$key] = $this->array_keys_recursive($inputArray[$key], $maxDepth, $searchKey, $searchValue, $depth);
                }
                else {
                    if ($depth == $maxDepth) {
                        if ($searchValue == $inputArray[$key] && $searchKey == $key){
                            $this->filteredArray[] = $this->tempKey;
                            array_pop($this->tempKey);
                        }
                        else {
                            array_pop($this->tempKey);
                        }
                    }
                }
            }
        }
    } 
  
}



