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
 * 2017-08-11		version 0.1
 * Basic version
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
    
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data received from Client\n";
        }        
        Configure::load('p2pGestor.php', 'default');
        $winvestifyBaseDirectoryClasses = Configure::read('winvestifyVendor') . "Classes";          // Load Winvestify class(es)
        require_once($winvestifyBaseDirectoryClasses . DS . 'fileparser.php');    
        
        $platformData = json_decode($job->workload(), true);

        foreach ($platformData as $linkedAccountKey => $data) {
            if ($data['pfp'] <> "mintos") { // TO BE REMOVED           TO BE REMOVED
                continue;
            }
            $platform = $data['pfp'];
            $companyHandle = $this->companyClass($data['pfp']);

            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Current platform = " . $data['pfp'] . "\n";
            }
            // Deal first with the transaction file(s)
            print_r($data);
            $files = $data['files'];
            // First analyze the transaction file(s)
            $myParser = new Fileparser();       // We are dealing with an XLS file so no special care needs to be taken

// do this first for the transaction file and then for investmentfile(s)
            $fileTypesToCheck = array (0 => TRANSACTION_FILE,
                                       1 => INVESTMENT_FILE,
                                   //    2 => EXTENDED_TRANSACTION_FILE
                );                     // So we cover Finanzarel

            foreach ($fileTypesToCheck as $actualFileType) {
                $approvedFiles = $this->readFilteredFiles($files,  $actualFileType);
                switch ($actualFileType) {
                    case INVESTMENT_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Investment File\n";
                        }
                        $parserConfig = $companyHandle->getParserConfigInvestmentFile(); 
                        break;
                    case TRANSACTION_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Transaction File\n";
                        }
                        $parserConfig = $companyHandle->getParserConfigTransactionFile();
                        break;                     
                    case EXTENDED_TRANSACTION_FILE:
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Analyzing Extended Transaction File\n";
                        } 
                        $parserConfig = $companyHandle->getParserConfigExtendedTransactionFile();
                        break; 
                }
                
                $tempResult = array();
                foreach ($approvedFiles as $approvedFile) {
                    unset($errorInfo);

                    $myParser->setConfig(array('sortParameter' => "investment_loanId"));
echo __FILE__ . " " . __LINE__ . "\n";
                    $tempResult = $myParser->analyzeFile($approvedFile, $parserConfig);     // if successfull analysis, result is an array with loanId's as index
echo __FILE__ . " " . __LINE__ . "\n";
//print_r($tempResult);
                    echo "Dealing with file $approvedFile\n";
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
                    }
                }
            }
//print_r($totalParsingresultInvestments);
            foreach ($totalParsingresultTransactions as $loanIdKey => $transaction) {
                $totalParsingresultInvestmentsTemp[$loanIdKey] = $totalParsingresultInvestments[$loanIdKey][0];
if ($loanIdKey == "1242052-01") {
    echo "LOANID = 1242052-01 FOUND \n";
    exit;
}
                if ( !array_key_exists ($loanIdKey , $totalParsingresultInvestments ))  {
                    echo "NO found match for loanId = $loanIdKey  \n";                      // THIS IS NEVER POSSIBLE
                }
            }
 echo __FILE__ . " " . __LINE__ . "   \n";

            $returnData[$linkedAccountKey]['parsingResultTransactions'] = $totalParsingresultTransactions;
            $returnData[$linkedAccountKey]['parsingResultInvestments'] = $totalParsingresultInvestmentsTemp;
            $returnData[$linkedAccountKey]['userReference'] = $data['userReference'];
            $returnData[$linkedAccountKey]['queue_id'] = $data['queue_id'];
            $returnData[$linkedAccountKey]['pfp'] = $platform;

            foreach ($totalParsingresultTransactions as $loanIdKey => $transaction) {
                if (array_search($loanIdKey, $listOfCurrentActiveLoans) !== false) {         // Check if new investments have appeared
                    $newLoans[] = $loanIdKey;
                }
            }
            $returnData[$linkedAccountKey]['newLoans'] = $newLoans;
            unset( $newLoans);
        }
        print_r($returnData);
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Data collected and being returned to Client\n";
        }        
        return json_encode($returnData);
    }
}
