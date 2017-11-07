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
 *
 */
App::import('Shell','GearmanWorker');
 
class ParseDataWorkerShell extends GearmanWorkerShell {

 //   var $uses = array();      // No models used


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
     *      $data['linkedAccountId']['requestOrigin']               => as a result of an account linking or a regular readout
     *      $data['linkedAccountId']['files'][filename1']           => array of filenames, FQDN's
     *      $data['linkedAccountId']['files'][filename2']
     *                                        ... ... ...
     *      $data['linkedAccountId']['listOfCurrentActiveLoans']    => list of all active loans BEFORE this analysis
     *

     * @return array queue_id, userReference, linkedaccount_id, exception error
     *  The worker provides all error information to the Client
     *
     *           array     analyse    convert internal array to external format using definitions of configuration file
     *                      true  analysis done with success
     *                      array with all errorData related to occurred error
     *
     *      $data['linkedAccountId']['userReference']
     *      $data['linkedAccountId']['queue_id']
     *      $data['linkedAccountId']['pfp']
     *      $data['linkedAccountId']['newLoans']
     *      $data['linkedAccountId']['error’]    => optional
     *      $data['linkedAccountId']['parsingResultTransactions'] 
     *      $data['linkedAccountId'][‘parsingResultInvestments'] 
     *
     */
     
    public function parseFileFlow($job) {
        
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data received from Client\n";
        }        
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');    
        
        $platformData = json_decode($job->workload(), true);

        foreach ($platformData as $linkedAccountKey => $data) {
            if ($data['pfp'] <> "mintos") { // TO BE REMOVED           TO BE REMOVED
                echo __FUNCTION__ . " " . __LINE__ . " Memory = " . memory_get_usage (false)  . "\n"; 
                continue;
            }
            $platform = $data['pfp'];
            $companyHandle = $this->companyClass($data['pfp']);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Current platform = " . $data['pfp'] . "\n";
            }

            print_r($data);
            $files = $data['files'];
            // First analyze the transaction file(s)
            $myParser = new Fileparser();       // We are dealing with an XLS file so no special care needs to be taken
            
// do this first for the transaction file and then for investmentfile(s)
            $fileTypesToCheck = array (0 => WIN_FLOW_TRANSACTION_FILE,
                                       1 => WIN_FLOW_INVESTMENT_FILE,
                                       2 => WIN_FLOW_EXTENDED_TRANSACTION_FILE,     // So we cover Finanzarel
                                       3 => WIN_FLOW_EXPIRED_LOAN_FILE              // If this file exists, we avoid collecting "old amortization tables"
                                        );                     

            foreach ($fileTypesToCheck as $actualFileType) {
                $approvedFiles = $this->readFilteredFiles($files, $actualFileType);
                switch ($actualFileType) {
                    case WIN_FLOW_TRANSACTION_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Transaction File\n";
                        }
                        $parserConfigFile = $companyHandle->getParserConfigTransactionFile();
                        $configParameters = $companyHandle->getParserTransactionConfigParms();
                        break;  
                        
                    case WIN_FLOW_INVESTMENT_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Investment File\n";
                        }
                        $parserConfigFile = $companyHandle->getParserConfigInvestmentFile(); 
                        $configParameters = $companyHandle->getParserInvestmentConfigParms();
                        break;                        
                        
                    case WIN_FLOW_EXTENDED_TRANSACTION_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Extended Transaction File\n";
                        } 
                        $parserConfigFile = $companyHandle->getParserConfigExtendedTransactionFile();
                        $configParameters = $companyHandle->getParserExtendedTransactionConfigParms();
                        break; 
                    case WIN_FLOW_EXPIRED_LOAN_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing File with expired Loans\n";
                        } 
                        $parserConfigFile = $companyHandle->getParserConfigExpiredLoanFile();
                        $configParameters = $companyHandle->getParserExpiredLoanConfigParms();
                        break;                        
                }
      
                $tempResult = array();
                foreach ($approvedFiles as $approvedFile) {
                    unset($errorInfo);
                             
                    $myParser->setConfig($configParameters);
                    $tempResult = $myParser->analyzeFile($approvedFile, $parserConfigFile);     // if successfull analysis, result is an array with loanId's as index

                    echo "Dealing with file $approvedFile\n";
                    if (empty($tempResult)) {                // error occurred while analyzing a file. Report it back to Client
                        $errorInfo = array( "typeOfError"   => "parsingError",
                                            "errorDetails"  => $myParser->getLastError(),
                                            );
                        $returnData[$linkedAccountKey]['error'][] = $errorInfo;
                         echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
                    }
                    else {
                        switch ($actualFileType) {
                            case WIN_FLOW_INVESTMENT_FILE:
                                $this->callbackInit($tempResult, $parserConfigFile, $companyHandle);
                                $totalParsingresultInvestments = $tempResult;                                
                                break;
                            case WIN_FLOW_TRANSACTION_FILE:
                                $totalParsingresultTransactions = $tempResult;
                                break;                            
                            case WIN_FLOW_EXTENDED_TRANSACTION_FILE:
                                $totalParsingresultTransactions = $tempResult;
                                break;
                            case WIN_FLOW_EXPIRED_LOAN_FILE:
                                unset($listOfExpiredLoans);
                                foreach ($tempResult as $expiredloankey => $item) {
                                    $listOfExpiredLoans[] = $expiredloankey;
                                }
    //                            print_r($listOfExpiredLoans);
                                break;                            
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
                    }
                }
            }

            $returnData[$linkedAccountKey]['parsingResultTransactions'] = $totalParsingresultTransactions;
            $returnData[$linkedAccountKey]['parsingResultInvestments'] = $totalParsingresultInvestments;
            $returnData[$linkedAccountKey]['userReference'] = $data['userReference'];
            $returnData[$linkedAccountKey]['pfp'] = $platform;
            $returnData[$linkedAccountKey]['linkedaccountId'] = $linkedAccountKey;
            
// check if we have new loans for this claculation period. Only collect the amortization tables of loans that have not already finished         
            $arrayiter = new RecursiveArrayIterator($returnData[$linkedAccountKey]['parsingResultTransactions']);
            $iteriter = new RecursiveIteratorIterator($arrayiter);
            foreach ($iteriter as $key => $value) {
                if ($key == "investment_loanId"){
     //               echo "value of loanId = $value\n";
                    if (array_search($loanIdKey, $listOfCurrentActiveLoans) !== true) {         // Check if new investments have appeared
                        if (array_search($loanIdKey, $listOfExpiredLoans) === true){
                            echo "Add new loan ";
                            $newLoans[] = $value;
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
//        print_r($data['tempArray'][885]['parsingResultInvestments']);
        print_r($data['tempArray'][885]['parsingResultTransactions']);
        return json_encode($data);
    }
    
    /**
     * Function to change values depending on callback functions for each company
     * @param array $tempResult It contains the value to change
     * @param array $valuesFile It contains the callback functions
     * @param object $companyHandle It is the company instance
     * @return It nothing if the callback array is empty
     */
    public function callbackInit(&$tempResult, $valuesFile, $companyHandle) {
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": Dealing with callbacks \n";
        } 
        $callbacks = $this->getCallbackFunction($valuesFile);
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ ;
            print_r($callbacks);
        }
        if (empty($callbacks)) {
            return;
        }
        $newArray = [];
        $arrayiter = new RecursiveArrayIterator($tempResult);
        $iteritor = new RecursiveIteratorIterator($arrayiter);
        
        foreach ($iteritor as $key => $value) {
            if (!empty($callbacks[$key])) {
                $function = $callbacks[$key];
                $valueConverted =  $companyHandle->$function($value);
                $currentDepth = $iteritor->getDepth();
                for ($subDepth = $currentDepth; $subDepth > 0; $subDepth--) {
                    // Get the current level iterator
                    $subIterator = $iteritor->getSubIterator($subDepth); 
                    // If we are on the level we want to change, use the replacements ($value) other wise set the key to the parent iterators value
                    $subIterator->offsetSet($subIterator->key(), ($subDepth === $currentDepth ? $valueConverted : $iteritor>getSubIterator(($subDepth+1))->getArrayCopy())); 
                }
                array_push($newArray, $iteritor->getArrayCopy());
            }
        }
        $tempResult = $newArray;
    }
    
    /**
     * Function to get callbacks from values of a company
     * @param array $values The are the transaction or investment values
     * @return array The callback functions
     */
    public function getCallbackFunction($values) {
        $callbacks = [];
        foreach ($values as $key => $valueCallback) {
            if (array_key_exists("callback", $valueCallback)) {   
                foreach ($valueCallback["callback"] as $value) {
                    $callbacks[$value["type"]] = $value["functionName"];
                }
            }
        }
        return $callbacks;
    }
}



