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
 * The callbacks are ONLY for new loans, NOT for ALL loans
 *
 */
App::import('Shell','GearmanWorker');
 
class ParseDataWorkerShell extends GearmanWorkerShell {

 //   var $uses = array();      // No models used
    protected $callbacks = [];
    protected $companyHandle;
    protected $myParser;


    public function main() {
        $this->GearmanWorker->addServers('127.0.0.1');

        $this->GearmanWorker->addFunction('parseFileFlow', array($this, 'parseFileFlow'));
        echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting to listen to data from its Client\n";
        
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
     *      $data['linkedAccountId']['listOfCurrentActiveLoans']    => list of all active loans BEFORE this analysis     
     *      $data['linkedAccountId']['files'][filename1']           => Array of filenames, FQDN's
     *      $data['linkedAccountId']['files'][filename2']

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
     *      $data['linkedAccountId']['controlVariableFile']
     *      * 

     *
     */
     
    public function parseFileFlow($job) {
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
            $controlVariableFile = $data['controlVariableFile'];
            $companyHandle = $this->companyClass($data['pfp']);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Current platform = " . $data['pfp'] . "\n";
            }

            $files = $data['files'];

            // First analyze the transaction file(s)
            $this->myParser = new Fileparser();       // We are dealing with an XLS file so no special care needs to be taken
            $callbacks = $companyHandle->getCallbacks();
// do this first for the transaction file and then for investmentfile(s)
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
                    case WIN_FLOW_EXPIRED_LOAN_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Files with expired Loans\n";
                        } 
                        $parserConfigFile = $companyHandle->getParserConfigExpiredLoanFile(); 
                                $configParameters = $companyHandle->getParserExpiredLoanConfigParms();  
                        break;                        
                }
                
                if (count($filesByType) === 1) {
                    $tempResult = $this->getSimpleFileData($filesByType[0], $parserConfigFile, $configParameters);
                } 
                else if (count($filesByType) > 1) {
                    $tempResult = $this->getMultipleFilesData($filesByType, $parserConfigFile, $configParameters);
                }
                if (empty($tempResult['error'])) {
                    switch ($fileTypeKey) {
                        case WIN_FLOW_INVESTMENT_FILE:
                            $this->callbacks = $callbacks["investment"];
                            $this->callbackInit($tempResult, $companyHandle);
                            $totalParsingresultInvestments = $tempResult;                                
                            break;
                        case WIN_FLOW_TRANSACTION_FILE:
                            $totalParsingresultTransactions = $tempResult;
                            break;                            
                        case WIN_FLOW_EXTENDED_TRANSACTION_FILE:
                        //    $totalParsingresultTransactions = $tempResult;
                            break;
                        case WIN_FLOW_EXPIRED_LOAN_FILE:
                            unset($listOfExpiredLoans);
                            $totalParsingresultExpiredInvestments = $tempResult;
                            $i = 0;
                            foreach ($tempResult as $expiredLoankey => $item) {
                                $listOfExpiredLoans[] = $expiredLoankey;
                                $i++;
                            }
                            break;                            
                    }
                    
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

            $returnData[$linkedAccountKey]['parsingResultTransactions'] = $totalParsingresultTransactions;
            $returnData[$linkedAccountKey]['parsingResultInvestments'] = $totalParsingresultInvestments;
            $returnData[$linkedAccountKey]['parsingResultExpiredInvestments'] = $totalParsingresultExpiredInvestments;
            $returnData[$linkedAccountKey]['userReference'] = $data['userReference'];
            $returnData[$linkedAccountKey]['actionOrigin'] = $data['actionOrigin'];
            $returnData[$linkedAccountKey]['pfp'] = $platform;  
            $returnData[$linkedAccountKey]['activeInvestments'] = $data['activeInvestments'];
            $returnData[$linkedAccountKey]['linkedaccountId'] = $linkedAccountKey;
            $returnData[$linkedAccountKey]['controlVariableFile'] = $controlVariableFile;         
            
 // THIS DEPENDS ON THE WORK DONE BY ANTONIO (SUPPORT OF VARIOUS SHEETS OF XLS FILE           &$investmentList,  
            $returnData[$linkedAccountKey]['listOfTerminatedInvestments'] = $this->getListofFinishedInvestmentsA($platform, $totalParsingresultExpiredLoans);       
           
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
                        if (in_array($value, $data['listOfCurrentActiveLoans']) == false) {         // Check if new investments have appeared
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
        }
        $data['tempArray'] = $returnData;
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
        } 
//print_r($data['tempArray'][$linkedAccountKey]['parsingResultExpiredInvestments']);
 //     print_r($data['tempArray'][$linkedAccountKey]['parsingResultInvestments']);
 //      print_r($data['tempArray'][$linkedAccountKey]['parsingResultTransactions']['2015-10-29']);
 //       print_r($data['tempArray'][$linkedAccountKey]['activeInvestments']);
 //echo "new loans = ";
 //       print_r($data['tempArray'][$linkedAccountKey]['newLoans']);
        echo "Number of new loans = " . count($data['tempArray'][$linkedAccountKey]['newLoans']) . "\n";
        echo "Number of expired loans = " . count($data['tempArray'][$linkedAccountKey]['parsingResultExpiredInvestments']) . "\n";
        echo "Number of NEW loans = " . count($data['tempArray'][$linkedAccountKey]['parsingResultInvestments']) . "\n";
 /*$i = 0;
 foreach  ($data['tempArray'][$linkedAccountKey]['parsingResultExpiredInvestments'] as $key => $dataXX){
     echo $key . "@@";
     $i++;
     if ($i == 150) break;
 }
 $i = 0;
 foreach ($data['tempArray'][$linkedAccountKey]['parsingResultInvestments'] as $key => $dataXX){
      $i++;
     if ($i == 150) break;
     echo $key . "@@";
 } */
 echo "Done\n";
        return json_encode($data);
    }       
        
      
    /**
     * Function to change values depending on callback functions for each company
     * @param array $tempResult It contains the value to change
     * @param object $companyHandle It is the company instance
     * @return It nothing if the callback array is empty
     */
    public function callbackInit(&$tempResult, $companyHandle) {
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": Dealing with callbacks \n";
        }
        //$this->getCallbackFunction($valuesFile);
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ ;
            print_r($this->callbacks);
        }

        if (empty($this->callbacks)) {
            return;
        }
        $this->companyHandle = $companyHandle;
        array_walk_recursive($tempResult,array($this, 'changeValueIteratingCallback'));
    }
    
    /**
     * Function to iterate in an array when callback is called and change the value if needed
     * @param type $item
     * @param type $key
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
        
/*
        [1691352-01] => Array
                (
                    [0] => Array
                        (
                            [transaction_transactionId] => 197424741
                            [date] => 2017-10-17
                            [investment_loanId] => 1691352-01
                            [original_concept] => Investment principal increase 
                            [internalName] => investment_myInvestment
                            [amount] => 36.01
                            [transaction_balance] => 42.999158907555
                            [currency] => 1
                        )

                )

*/  
    
        foreach ($activeTransactions as $activeTransaction) {
            if ($investmentList[$activeTransaction][0]['investment_stateOfLoan'] == WIN_LOANSTATUS_FINISHED) { // NOT CORRECT AS WE DON'T HAVE ACCESS TO investment_stateOfLoan
                $finishedInvestments[] = $activeTransaction;            // Add to list of finished investments
            }
        }
        return $finishedInvestments;
    }    
    
    
    /**
     * 
     * get the loanIds obtained during the parsing of the transactions
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
    
    public function getSimpleFileData($file, $parserConfigFile, $configParameters) {
        echo __LINE__ . "Dealing with file $file\n";     
        if (!empty($configParameters['offsetStart'])) {
            $tempResult = $this->getSimpleSheetData($file, $parserConfigFile, $configParameters);
        }
        else {
            $tempResult = $this->getMultipleSheetData($file, $parserConfigFile, $configParameters);
        }
        return $tempResult;
    }
    
    public function getMultipleFilesData($filesByType, $parserConfigFile, $configParameters) {
        $tempResult = [];
        $i = 0;
        $orderParam = array_shift($configParameters);
        foreach ($filesByType as $file) {
            if (!empty($configParameters[$i]['offsetStart'])) {
                $tempResult[] = $this->getSimpleSheetData($file, $parserConfigFile[$i], $configParameters[$i]);
            }
            else {
                //if multiple files, we need an offset and offsetEnd individual
                //An array is necessary 
                $tempResult[] = $this->getMultipleSheetData($file, $parserConfigFile[$i], $configParameters[$i]);
            }
            $i++;
        }
        $tempResultOrdered = $this->resultOrdering($tempResult, $orderParam);
        return $tempResultOrdered;
    }
    
    public function getSimpleSheetData($file, $parserConfigFile, $configParameters) {
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
    
    public function getMultipleSheetData($file, $parserConfigFile, $configParameters) {
        $orderParam = array_shift($configParameters);
        foreach ($configParameters as $key => $individualConfigParameters) {       
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
    
    public function resultOrdering($tempArray, $orderParam) {
        $countSortParameters = count($this->config['sortParameter']);
        switch ($countSortParameters) {
            case 1:
                $sortParam1 = $tempArray[$i][$this->config['sortParameter'][0]];      
                $tempArray[$sortParam1][] = $tempArray[$i];
                unset($tempArray[$i]); 
            break; 

            case 2:
                $sortParam1 = $tempArray[$i][$this->config['sortParameter'][0]];
                $sortParam2 = $tempArray[$i][$this->config['sortParameter'][1]];        
                $tempArray[$sortParam1][$sortParam2][] = $tempArray[$i];
                unset($tempArray[$i]);
            break;               
        }
    }
    
}



