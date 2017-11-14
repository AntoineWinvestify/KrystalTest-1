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
 * @author
 * @version 0.2
 * @date 2017-10-27
 * @package
 *
 * This client deals with performing the parsing of the files that have been downloaded
 * from the PFP's. Once the data has been parsed by the Worker, the Client starts analyzing
 * the data and writes the data-elements to the corresponding database tables.
 * Encountered errors are stored in the database table "applicationerrors".
 *
 *
 * 2017-08-11		version 0.1
 * Basic version
 *
 * 2017-10-27		version 0.2
 * client adapted to global Gearman framework
 *
 *
 * PENDING:
 * Adapt to     public function verifiedStatus($status, $message, $restartStatus, $errorStatus)
 * use of  createInvestment can be avoided if the investment model has a aftersave and check for create. In that case also create
 * the paymenttotal table. now here we can use the simple save (with or without the id
 * add list of finished loans to be sent to the
 */
App::import('Shell', 'GearmanClient');

class ParseDataClientShell extends GearmanClientShell {

    public $uses = array('Queue', 'Paymenttotal', 'Investment');
    protected $variablesConfig;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
        echo "Deleting Investment\n";
        $this->Investment->deleteAll(array('Investment.id >' => 10121), false);

        echo "Deleting Paymenttotal\n";
        $this->Paymenttotal->deleteAll(array('Paymenttotal.id >' => 0), false);

        echo "Deleting Payment\n";
        $this->Payment = ClassRegistry::init('Payment');
        $this->Payment->deleteAll(array('Payment.id >' => 0), false);

//    echo "Deleting Userinvestmentdata\n";
//    $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
//    $this->Userinvestmentdata->deleteAll(array('Userinvestmentdata.id >' => 0), false);
//    echo "Deleting Globalcashflowdata\n";
//    $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
//    $this->Globalcashflowdata->deleteAll(array('Globalcashflowdata.id >' => 0), false);


        return;
    }

    public function initDataAnalysisClient() {
        $this->resetTestEnvironment();      // Temporary function
        $this->GearmanClient->addServers();
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $this->flowName = "GEARMAN_FLOW2";
        $inActivityCounter = 0;
        $workerFunction = "parseFileFlow";

        echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 2 Client\n";
        }

        //$resultQueue = $this->Queue->getUsersByStatus(FIFO, GLOBAL_DATA_DOWNLOADED);
        $inActivityCounter++;

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');
        Configure::load('internalVariablesConfiguration.php', 'default');
        $this->variablesConfig = Configure::read('internalVariables');


        while (true) {
            $pendingJobs = $this->checkJobs(WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, $jobsInParallel);
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
            }
            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $userReference = $job['Queue']['queue_userReference'];
                    $queueId = $job['Queue']['id'];
                    $this->queueInfo[$job['Queue']['id']] = json_decode($job['Queue']['queue_info'], true);
                    print_r($this->queueInfo);
                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue']['id']]['date'] . DS;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
                    $i = 0;
                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        $dirs = new Folder($subDirectory);
                        $allFiles = $dirs->findRecursive();
                        if (!in_array($linkedAccountId, $this->queueInfo[$job['Queue']['id']]['companiesInFlow'])) {
                            continue;
                        }
                        $tempPfpName = explode("/", $allFiles[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        $this->userLinkaccountIds[$job['Queue']['id']][$i] = $linkedAccountId;
                        $i++;
                        echo "pfp = " . $pfp . "\n";
                        $files = $this->readFilteredFiles($allFiles, WIN_FLOW_EXPIRED_LOAN_FILE + WIN_FLOW_TRANSACTION_FILE +
                                WIN_FLOW_INVESTMENT_FILE + WIN_FLOW_EXTENDED_TRANSACTION_FILE);
                        $listOfActiveInvestments = $this->getListActiveLoans($linkedAccountId);
                        print_r($listOfActiveInvestments);
                        
                        $params[$linkedAccountId] = array(
                            'pfp' => $pfp,
                            'activeInvestments' => count($listOfActiveInvestments),
                            'listOfCurrentActiveLoans' => $listOfActiveInvestments,
                            'userReference' => $job['Queue']['queue_userReference'],
                            'files' => $files);
                    }
                    debug($params);

                    $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue']['id'] . ".-;" .
                            $workerFunction . ".-;" . $job['Queue']['queue_userReference']);
                }

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the information to Worker\n";
                }

                $this->GearmanClient->runTasks();

                // ######################################################################################################

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n";
                }

                foreach ($this->tempArray as $queueIdKey => $result) {
                    foreach ($result as $platformKey => $platformResult) {
                        // First check for application level errors
                        // if an error is found then all the files related to the actions are to be
                        // deleted including the directory structure.
                        if (!empty($platformResult['error'])) {         // report error
                            $this->Applicationerror = ClassRegistry::init('applicationerror');
                            $this->Applicationerror->saveAppError("ERROR ", json_encode($platformResult['error']), 0, 0, 0);
                            // Delete all files for this user for this regular update
                            // break
                            continue;
                        }
                        $userReference = $platformResult['userReference'];
                        $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue']['id']]['date'] . DS;
                        $baseDirectory = $baseDirectory . $platformKey . DS . $platformResult['pfp'] . DS;
// Add the status per PFP, 0 or 1
                        $mapResult = $this->mapData($platformResult);

                        if (!empty($platformResult['newLoans'])) {
                            $fileHandle = new File($baseDirectory . 'loanIds.json', true, 0644);
                            if ($fileHandle) {
                                if ($fileHandle->write(json_encode($platformResult['newLoans']), "w", true)) {
                                    $fileHandle->close();
                                    echo "File " . $baseDirectory . "loanIds.json written\n";
                                }
                            }
                            $newFlowState = WIN_QUEUE_STATUS_DATA_EXTRACTED;
                        } else {
                            $newFlowState = WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED;
                        }
                    }
                    
                    $this->queueInfo[$queueIdKey]["loanIds"] = $platformResult['newLoans']; // store the list of loan Ids in DB, for FLOW3B
                    $this->Queue->id = $queueIdKey;
                    $this->Queue->save(array('queue_status' => $newFlowState,
                        'queue_info' => json_encode($this->queueInfo[$queueIdKey]),
                            ), $validate = true
                    );
                }
                break;
            } else {
                $inActivityCounter++;
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n";
                }
                sleep(4);                                          // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT\n";
                    exit;
                }
            }
        }
    }

    /**
     * Get the list of all active investments for a P2P as identified by the
     * linkedaccount identifier.
     *
     * @param int $linkedaccount_id    linkedaccount reference
     * @return array
     *
     */
    public function getListActiveLoans($linkedaccount_id) {
        $this->Investment = ClassRegistry::init('Investment');
        $filterConditions = array(
            'linkedaccount_id' => $linkedaccount_id,
            "investment_statusOfloan" => WIN_LOANSTATUS_ACTIVE,
        );

        $investmentListResult = $this->Investment->find("all", array("recursive" => -1,
            "conditions" => $filterConditions,
            "fields" => array("id", "investment_loanId"),
        ));

        $list = Hash::extract($investmentListResult, '{n}.Investment.investment_loanId');
        return $list;
    }

    /**
     * Maps the data to its corresponding database table + variables, calculates the "Missing values"
     * and writes all values to the database.
     *  @param  $array          Array which holds the data (per PFP) as received from the Worker
     *
     *  @return boolean true
     *                  false
     *
     * the principal data is available in two or three sub-arrays which are to be written
     * (before checking if it is a duplicate) to the corresponding database table.
     *     platform - (1-n)loanId - (1-n) concepts
     */
    public function mapData(&$platformData) {
        $investmentId = NULL;
        $linkedaccountId = $platformData['linkedaccountId'];
        $userReference = $platformData['userReference']; 
        $controlVariableActiveInvestments = $platformData['activeInvestments'];

        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');       // A new table exists for EACH new calculation interval
        $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');

        foreach ($platformData['parsingResultTransactions'] as $dateKey => $dates) { // these are all the transactions, PER day
            echo "dateKey = $dateKey \n";
// Lets allocate a userinvestmentdata for this calculation period (normally daily)
            // reset the relevant variables before going to next date
            unset($database);              // Start with a clean shadow database
            foreach ($this->variablesConfig as $variablesKey => $item) {
                $this->variablesConfig[$variablesKey]['state'] = WIN_FLOWDATA_VARIABLE_NOT_DONE;
            }

            $filterConditions = array("linkedaccount_id" => $linkedaccountId);
            $database = $this->getLatestTotals("Userinvestmentdata", $filterConditions);

            $this->Userinvestmentdata->create();
            $database['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
            $database['Userinvestmentdata']['investorIdentity'] = $userReference;
            $database['Userinvestmentdata']['date'] = $dateKey;

            foreach ($dates as $keyDateTransaction => $dateTransaction) {            // read all *individual* transactions
                $newLoan = NO;
                echo "\nkeyDateTransaction = $keyDateTransaction \n";
                //        print_r($dateTransaction);
// special procedure for platform related transactions, i.e. when we don't have a real loanId
                $keyDateTransactionNames = explode("_", $keyDateTransaction);
                if ($keyDateTransactionNames[0] == "global") {
                    echo "---------> ANALYZING GLOBAL, PLATFORM SPECIFIC DATA\n";
                    // cycle through all individual fields of the transaction record
                    foreach ($dateTransaction[0] as $transactionDataKey => $transaction) {  // 0,1,2
                        if ($transactionDataKey == "internalName") {        // 'dirty trick' to keep it simple
                            $transactionDataKey = $transaction;
                        }
                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);

                        if (!empty($tempResult)) {
                            unset($result);
                            $functionToCall = $tempResult['function'];

                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            if (!empty($functionToCall)) {
                                $result = $this->$functionToCall($dateTransaction, $database);

                                if ($tempResult['charAcc'] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                    $database[$dbTable][$transactionDataKey] = bcadd($database[$dbTable][$transactionDataKey], $result, 16);
                                } else {
                                    $database[$dbTable][$transactionDataKey] = $result;
                                }
                            } else {
                                $database[$dbTable][$transactionDataKey] = $transaction;
                            }
                            echo $this->variablesConfig[$tempResult['internalIndex']]['databaseName'];
                        }
                    }
                    continue;
                }

                echo "---------> ANALYZING NEXT LOAN\n";
                if (in_array($keyDateTransaction, $platformData['newLoans'])) {          // check if loanId is new
                    $arrayIndex = array_search($keyDateTransaction, $platformData['newLoans']);
                    if ($arrayIndex !== false) {        // Deleting the array from new loans list
                        unset($platformData['newLoans'][$arrayIndex]);
                    }
                    echo "Storing the data of a NEW loan in the shadow db table\n";
                    $controlVariableActiveInvestments = $controlVariableActiveInvestments + 1;
                    
//print_r($platformData['parsingResultInvestments'][$keyDateTransaction]);
                    // check all the data in the analyzed investment table
                    foreach ($platformData['parsingResultInvestments'][$keyDateTransaction] as $investmentDataKey => $investmentData) {
                        $tempResult = $this->in_multiarray($investmentDataKey, $this->variablesConfig);

                        if (!empty($tempResult)) {
                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            $database[$dbTable][$investmentDataKey] = $investmentData;
                            $this->variablesConfig[$investmentDataKey]['state'] = WIN_FLOWDATA_VARIABLE_DONE;   // Mark done
                            $newLoan = YES;
                        }
                    }
                } else { // get the investment_id of the existing loan
                    $filterConditions = array("investment_loanId" => $keyDateTransaction,
                        "linkedaccount_id" => $linkedaccountId);
                    $tempInvestmentId = $this->Investment->getData($filterConditions, array("id", "investment_myInvestment",
                         "investment_priceInSecondaryMarket" , "investment_secondaryMarketInvestment"));
                    // read some values of the existing loan, like myInvestment [12] and 
//                      investment_priceInSecondaryMarket and [27] and investment_secondaryMarketInvestment [26]
                    $investmentId = $tempInvestmentId[0]['Investment']['id'];
                    $database['investment']['id'] = $investmentId;
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "EXISTING Loan.. Id of the existing loan $investmentId\n ";
                }

                // load all the transaction data
                foreach ($dateTransaction as $transactionKey => $transactionData) {
                    //                   echo "---> ANALYZING NEW TRANSACTION transactionKey = $transactionKey transactionData = $transactionData\n";
                    foreach ($transactionData as $transactionDataKey => $transaction) {  // 0,1,2
                        if ($transactionDataKey == "internalName") {        // 'dirty trick' to keep it simple
                            $transactionDataKey = $transaction;
                        }
                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);

                        if (!empty($tempResult)) {
                            unset($result);
                            $functionToCall = $tempResult['function'];
                            echo __FILE__ . " " . __LINE__ . " Function to call = $functionToCall, transactionDataKey = $transactionDataKey\n";
                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            if (!empty($functionToCall)) {
                                $result = $this->$functionToCall($transactionData, $database);

                                if ($tempResult['charAcc'] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                    $database[$dbTable][$transactionDataKey] = bcadd($database[$dbTable][$transactionDataKey], $result, 16);
                                } else {
                                    $database[$dbTable][$transactionDataKey] = $result;
                                }
                            } else {
                                $database[$dbTable][$transactionDataKey] = $transaction;
                            }
                            echo "------>  changing state of $transactionDataKey [index = " . $tempResult['internalIndex'] . "] to DONE\n";
                            $this->variablesConfig[$tempResult['internalIndex']]['state'] = WIN_FLOWDATA_VARIABLE_DONE;  // Mark done
                            //                           print_r($this->variablesConfig[$tempResult['internalIndex']]);
                            echo $this->variablesConfig[$tempResult['internalIndex']]['databaseName'];
                        }
                    }
                }
// Now start consolidating the results, these are to be stored in the investment table (variable part)
// check if variable is already defined: loading of data in investment and payment, globalcashflowdata
                //           $internalVariableToHandle = array(17,47,34,45,44,36,46,66,67,43);
                $internalVariableToHandle = array();
                foreach ($internalVariableToHandle as $keyItem => $item) {
                    //                 print_r($this->variablesConfig[$item]);
                    if ($this->variablesConfig[$item]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // remaining term [17]
                        $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                        $functionToCall = $this->variablesConfig[$item]['function'];
                        echo "Calling the function: $functionToCall and index = $keyItem\n";
                        $database[$varName[0]][$varName[1]] = $this->$functionToCall($database);
                        $this->variablesConfig[$item]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
                    }
                }

                $database['investment']['linkedaccount_id'] = $linkedaccountId;
                if ($newLoan == YES) {
                    print_r($database['investment']);
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Investment Data... ";
                    $resultCreate = $this->Investment->createInvestment($database['investment']);

                    if ($resultCreate[0]) {
                        $investmentId = $resultCreate[1];
                        echo "Saving NEW loan with investmentId = $investmentId, Done\n";
                        $database['investment']['id'] = $resultCreate[1];
                    } else {
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['investment']['investment_loanId'] . "\n";
                        }
                    }
                } else {
                    $database['investment']['id'] = $investmentId;
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Writing NEW data to already existing investment ... ";
                    $result = $this->Investment->save($database['investment']);
                    if ($result) {
                        echo "Saving existing loan with investmentId = $investmentId, Done\n";
                    } else {
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['investment']['investment_loanId'] . "\n";
                        }
                    }
                }

                $this->Payment = ClassRegistry::init('Payment');
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Payment Data for investment with id = $investmentId... ";
                $database['payment']['investment_id'] = $investmentId;
                $database['payment']['date'] = $dateKey;
                $this->Payment->create();
                if ($this->Payment->save($database['payment'], $validate = true)) {
                    echo "Done\n";
                } else {
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['payment']['payment_loanId'] . "\n";
                    }
                }
                echo "printing relevant part of database\n";
                print_r($database['investment']);
                print_r($database['payment']);
                unset($investmentId);
                unset($database['investment']);
                unset($database['payment']);
            }

            echo __FUNCTION__ . " " . __LINE__ . ": " . "Execute functions for consolidating the data of Flow for date = $dateKey\n";
            $internalVariableToHandle = array(20000);
            foreach ($internalVariableToHandle as $keyItem => $item) {
                if ($this->variablesConfig[$item]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // remaining term [17]
                    $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                    $functionToCall = $this->variablesConfig[$item]['function'];
                    echo "Calling the function: $functionToCall and index = $keyItem\n";
                    $database[$varName[0]][$varName[1]] = $this->$functionToCall($database);
                    echo "inputs are " . $varName[0] . " and" . $varName[1] . "\n";
                    echo $database[$varName[0]][$varName[1]];
                    $this->variablesConfig[$item]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
                }
            }


            echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Userinvestmentdata Data... ";
            if ($this->Userinvestmentdata->save($database['Userinvestmentdata'], $validate = true)) {
                $userInvestmentDataId = $this->Userinvestmentdata->id;
                echo "Done, id = $userInvestmentDataId\n";
                $database['Userinvestmentdata']['id'] = $userInvestmentDataId;
            } else {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['userinvestmentdata']['payment_loanId'] . "\n";
                }
            }

            if (!empty($database['globalcashflowdata'])) {
                $database['globalcashflowdata']['userinvestmentdata_id'] = $userInvestmentDataId;
                $database['globalcashflowdata']['date'] = $dateKey;
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Globalcashflowdata Data... ";
                $this->Globalcashflowdata->create();
                if ($this->Globalcashflowdata->save($database['globalcashflowdata'], $validate = true)) {
                    echo "Done\n";
                } else {
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['globalcashflowdata']['payment_loanId'] . "\n";
                    }
                }
            }
            echo "printing global data for the date = $dateKey\n";
            print_r($database['Userinvestmentdata']);
            print_r($database['globalcashflowdata']);
            
            
        }
        echo __FUNCTION__ . " " . __LINE__ . ": " . "Finishing mapping process Flow 2\n";
        // The following is only done once per readout period independent of period covers one day, 1 week or if
        // it is a "link account" action
        // We also have to reduce the total values with the amounts of the investments that we finished TODAY, as (normally)
        // all loan related amounts are for active investments only
        // 
        // determine which loans have terminated
        // loop through all of them and subtracts amounts from total values
        echo "Start consolidating the platform data, using the control variables\n";
        $this->consolidateControlVariables($file);
        echo "Consolidation Phase 2, checking control variables\n";
        $this->consolidatePlatformData();
        return;
    }

    /*
     *
     *  Consolidates all the basic variables that are required on platformlevel.
     *
     */

    
    
    /**
     * CHECKING OF THE CONTROL VARIABLES WITH CALCULATED VARIABLES
     * 
     * 
     */
    public function consolidateControlVariables($externalControlVariables, $internalControlVariables) {
        // Calculate the values of the control variables /or read them from DB
       /*
     *  the variables are :
        * Also keep in mind WHEN the control variables are read. I should count all investment uptil that moment
        * which could be at the morning when the flow is executing. The collected data up until midnight is stored in
        * xls files.
        *  @param  array       array with the calculated control variables for today's readout
        *  @param  array       array with the control variables as provided by platform
        *  @return boolean     
        * 
        */
        $result = false;
        $error = true;
        foreach ($externalControlVariables as $variableKey => $variable) {
             switch ($variableKey) {
                case WIN_CONTROLVARIABLE_MYWALLET:
                    if ($internalControlVariables['myWallet'] == $externalControlVariables) {
                        
                    }
                    else {
                        $error = true;
                    }
                    break;
                case WIN_CONTROLVARIABLE_OUTSTANDINGPRINCIPAL:
                    if ($internalControlVariables['outstandingPrincipal'] == $externalControlVariables) {
                    
                    }
                    else {
                        $error = true;
                    }
                    break;
                case WIN_CONTROLVARIABLE_ACTIVEINVESTMENT:
                    if ($internalControlVariables['activeInvestments'] == $externalControlVariables) {
                        
                    }
                    else {
                        $error = true;
                    }
                    break;
            }  
        }  
        if ($error == true) {
            return $result;
        }
        else {
            //generate application error
        }
        // If approved, write the new values to DB
    }
    
    
    
    public function consolidatePlatformData(&$database) {
    /*
     * 
     * here I SHOULD call the functions that do the simple calculations. 
     * 
     * 
     */
        echo "FxF";
        $database['userinvestmentdata']['userinvestmentdata_capitalRepayment'] = $this->consolidateCapitalRepayment();  // 38
        echo "FtF";
        $database['userinvestmentdata']['userinvestmentdata_partialPrincipalRepayment'] = $this->consolidatePartialPrincipalRepayment();
        echo "FccFgF";
        $database['userinvestmentdata']['userinvestmentdata_outstandingPrincipal'] = $this->consolidateOutstandingPrincipal();  // 37
        echo "FFgF";
        $database['userinvestmentdata']['userinvestmentdata_receivedPrepayments'] = $this->consolidateReceivedPrepayment();
        echo "FtytyFgF";
        $database['userinvestmentdata']['userinvestmentdata_totalGrossIncome'] = $this->consolidateTotalGrossIncome();
        echo "FhF";
        $database['userinvestmentdata']['userinvestmentdata_interestgrossIncome'] = $this->consolidateInterestgrossIncome();
        echo "FFF";
        //        $database['userinvestmentdata']['userinvestmentdata_totalCost'] = $this->consolidateTotalCost();
        // substract the corresponding amounts of loans that have terminated
        // also using a loop
    }



    /* OK
     *  Get the amount which corresponds to the "PartialPrincipalPayment" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */

    public function consolidatePartialPrincipalRepayment() {
        $sum = 0;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_partialPrincipalRepayment'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }

    /* var 37
     *  Get the amount which corresponds to the "OutstandingPrincipal" concept
     *  "Outstanding principal" = total amount of investment - paymenttotal_capitalRepayment
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */

    public function calculateOutstandingPrincipal(&$transactionData, &$resultData) {
        $result = "0.0";

        if (!empty($resultData['payment']['payment_capitalRepayment'])) {
            $result = bcsub($result,$resultData['payment']['payment_capitalRepayment'], 16); 
        }
        if (!empty($resultData['payment']['payment_partialPrincipalRepayment'])) {
            $result = bcsub($result,$resultData['payment']['payment_partialPrincipalRepayment'], 16); 
        }  
        if (!empty($resultData['payment']['payment_principalBuyback'])) {
            $result = bcsub($result,$resultData['payment']['payment_principalBuyback'], 16); 
        }
        if (!empty($resultData['investment']['investment_priceInSecondaryMarket'])) { // read from db
            $result = bcsub($result,$resultData['investment']['investment_priceInSecondaryMarket'], 16); 
        }
        if (!empty($resultData['payment']['payment_currencyFluctuationNegative'])) {
            $result = bcsub($result,$resultData['payment']['payment_currencyFluctuationNegative'], 16);            
        }  
        if (!empty($resultData['payment']['payment_currencyFluctuationPositive'])) {
            $result = bcadd($result,$resultData['payment']['payment_currencyFluctuationPositive'], 16);   
        } 
        return $result;
    }

    /* var 38
     * Get the amount which corresponds to the "ReceivedPrepayments" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function calculateReceivedRepayment(&$transactionData, &$resultData) {
        $result = 0.0;
        if (!empty($resultData['payment']['payment_capitalRepayment'])) {
            $result = bcadd($result,$resultData['payment']['payment_capitalRepayment'], 16);   
        }
        if (!empty($resultData['payment']['payment_partialPrincipalRepayment'])) {
            $result = bcadd($result,$resultData['payment']['payment_partialPrincipalRepayment'], 16);   
        }
         if (!empty($resultData['payment']['payment_principalBuyback'])) {
            $result = bcadd($result,$resultData['payment']['payment_principalBuyback'], 16);   
        }
        if (!empty($resultData['investment']['investment_priceInSecondaryMarket'])) {  // read from db
            $result = bcadd($result,$resultData['investment']['investment_priceInSecondaryMarket'], 16);  
        } 
        $result1 = 0.0;
        if (!empty($resultData['investment']['investment_myInvestment'])) {  // read from db
            $result1 = bcadd($result1,$resultData['investment']['investment_myInvestment'], 16);  
        }        
        if (!empty($resultData['investment']['investment_secondaryMarketInvestment'])) {  // read from db
            $result1 = bcadd($result1,$resultData['investment']['investment_secondaryMarketInvestment'], 16);  
        }        
        $result1 = bcdiv($result,$result, 16);  
        return $result;
    }

    /*
     * Get the amount which corresponds to the "TotalGrossIncome" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function xxxconsolidateTotalGrossIncome() {
        $sum = 0;
        return;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_totalGrossIncome'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }

    /*
     * Get the amount which corresponds to the "InterestgrossIncome" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function consolidateInterestgrossIncome() {
        $sum = 0;
        return;
        $listResult = $this->Paymenttotal->find('list', array(
            'fields' => array('paymenttotal_interestgrossIncome'),
            "conditions" => array("status" => WIN_PAYMENTTOTALS_LAST),
        ));

        foreach ($listResult as $item) {
            $sum = bcadd($sum, $item, 16);
        }
        return $sum;
    }

    /* NOT YET
     * Get the amount which corresponds to the "TotalLoanCost" concept CHECK
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function calculateTotalLoanCost(&$transactionData, &$resultData) {
        $result = "0.0";
        
        if (!empty($resultData['payment']['payment_commissionPaid'])) {
            $result = bcadd($resultData['payment']['payment_regularGrossInterestIncome'],$result, 16);
        }
        if (!empty($resultData['globalcashflowdata']['globalcashflowdata_bankCharges'])) {
            $result = bcadd($resultData['globalcashflowdata']['payment_interestIncomeBuyback'],$result, 16);
        }        
        if (!empty($resultData['payment']['payment.payment_taxVAT'])) {
            $result = bcadd($resultData['payment']['payment_delayedInterestIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment.payment_incomeWithholdingTax'])) {
            $result = bcadd($resultData['payment']['payment_delayedInterestIncomeBuyback'],$result, 16);
        } 
        if (!empty($resultData['payment']['payment.payment_interestPaymentSecondaryMarketPurchase'])) {
            $result = bcadd($resultData['payment']['payment_latePaymentFeeIncome'],$result, 16);
        }
        if (!empty($resultData['investment']['investment_currencyExchangRateFee'])) {
            $result = bcadd($resultData['investment']['investment_currencyExchangRateFee'],$result, 16);
        }       
        if (!empty($resultData['payment']['payment.payment_costSecondaryMarket'])) {
            $result = bcadd($resultData['payment']['payment_costSecondaryMarket'],$result, 16);
        }       
        
        
        return $result;   
    }

    /* NOT YET
     * Get the amount which corresponds to the "NextPaymentDate" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function consolidateNextPaymentDate() {
        $sum = 0;
        return $sum;
    }

    /* NOT YET
     * Get the amount which corresponds to the "EstimatedNextPayment" concept
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function consolidateEstimatedNextPayment() {
        $sum = 0;
        return $sum;
    }


    /* NOT YET  chewck if the index is investment or payment
     * Get the result of the fields: 'Total gross income [42] - 'Loan Total cost' [53]
     * @param  array       array with the current transaction data
     * @param  array       array with all data so far calculated and to be written to DB
     * @return string      the string representation of a large integer
     */

    public function calculateTotalNetIncome(&$transactionData, &$resultData) {
        if (empty($resultData['investment']['investment_loanTotalCost'])) {
            $resultData['investment_loanTotalCost'] = 0.0;
        }
        if (empty($resultData['investment']['investment_totalGrossIncome'])) {
            $resultData['investment_totalGrossIncome'] = 0.0;
        }        
        $result = bcsub($resultData['investment_totalGrossIncome'],$resultData['investment_loanTotalCost'], 16);
        return $result;
        
    }




    /* NOT YET
     *  Get the result of the fields: 'Total gross income [42] - 'Loan Total cost' [53]
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */

    public function calculateTotalGrossIncome(&$transactionData, &$resultData) {
        $result = 0.0;
        
        if (!empty($resultData['payment']['payment_regularGrossInterestIncome'])) {
            $result = bcadd($resultData['payment_regularGrossInterestIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_interestIncomeBuyback'])) {
            $result = bcadd($resultData['payment_interestIncomeBuyback'],$result, 16);
        }        
        if (!empty($resultData['payment']['payment_delayedInterestIncome'])) {
            $result = bcadd($resultData['payment_delayedInterestIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_delayedInterestIncomeBuyback'])) {
            $result = bcadd($resultData['payment_delayedInterestIncomeBuyback'],$result, 16);
        } 
        if (!empty($resultData['payment']['payment_latePaymentFeeIncome'])) {
            $result = bcadd($resultData['payment_latePaymentFeeIncome'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_loanRecoveries'])) {
            $result = bcadd($resultData['payment_loanRecoveries'],$result, 16);
        } 
        if (!empty($resultData['payment']['payment_loanIncentivesAndBonus'])) {
            $result = bcadd($resultData['payment_loanIncentivesAndBonus'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_loanCompensation'])) {
            $result = bcadd($resultData['payment_loanCompensation'],$result, 16);
        }
        if (!empty($resultData['payment']['payment_incomeSecondaryMarket'])) {
            $result = bcadd($resultData['payment_incomeSecondaryMarket'],$result, 16);
        }  
        return $result;   
    }
    
    
    
    
    
    
    
    
    

    /* NOT YET
     *  Get the amount which corresponds to the "InstallmentPaymentProgress" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     */

    public function consolidateInstalmentPaymentProgress() {
        $sum = 0;
        return $sum;
    }

    /*
     *  Get the amount which corresponds to the "Primary_market_investment" concept, which is a new investment
     *  @param  $transactionData    array      array with the current transaction data
     *  @param  $resultData         array       array of shadow database with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     * 12
     */

    public function calculateMyInvestment(&$transactionData, &$resultData) {
        $resultData['payment']['payment_myInvestment'] = $transactionData['amount'];        // THIS IS A HARDCODED RULE
        return $transactionData['amount'];
    }

    public function calculateMyInvestmentFromPayment(&$transactionData, &$resultData) {
        echo "----------------->  BBBBBBBBB\n";
        print_r($transactionData);
        return $transactionData['investment']['investment_myInvestment'];
    }

    /*
     *  Get the amount which corresponds to the "late payment fee" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string      the string representation of a large integer
     * 17
     */

    public function calculateRemainingTerm(&$transactionData, &$resultData) {
        return 44332211;
        return $transactionData['amount'];
        //investment.investment_remainingDuration
    }

    /*
     *  Get the amount which corresponds to the "late payment fee" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 47
     */

    public function calculateLatePaymentFeeIncome(&$transactionData, &$resultData) {

        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "capitalRepayment Winvestify Format" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 34
     */

    public function calculateCapitalRepayment(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 45
     */

    public function calculateDelayedInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "InterestIncomeBuyback" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 44
     */

    public function calculateInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 36
     */

    public function calculatePrincipalBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     *  Get the amount which corresponds to the "DelayedInterestIncomeBuyback" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 46
     */

    public function calculateDelayedInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /* DONE
     *  Get the amount which corresponds to the "PlatformDeposit" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 66
     */

    public function calculatePlatformDeposit(&$transactionData, &$resultData) {
        return $transactionData[0]['amount'];
    }

    /* DONE
     *  Get the amount which corresponds to the "Platformwithdrawal" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 67
     */

    public function calculatePlatformWithdrawal(&$transactionData, &$resultData) {
        return $transactionData[0]['amount'];
    }

    /*
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return string
     * 43
     */

    public function calculateRegularGrossInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount'];
    }

    /*
     * Calculates the number of active investments. This function SHOULD be the very last function to execute
     * and will execute only once per DAY
     *
     *  @param  array       array with the current transaction data [NOT REALLY NEEDED]
     *  @param  array       array with all data so far calculated and to be written to DB [NOT REALLY NEEDED]
     *  @return string
     * 20000
     */

    public function calculateNumberOfActiveInvestments(&$transactionData, &$resultData) {
        $filterConditions = array('Investment.investment_statusOfLoan' => WIN_LOANSTATUS_ACTIVE);
        $activeInvestments = $this->Investment->find('count', array(
            'conditions' => $filterConditions));
        return $activeInvestments;
    }

    /*
     *  Get the amount which corresponds to the "PlatformbankCharges" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be written to DB
     *  @return string
     * 55
     */

    public function calculatePlatformBankCharges(&$transactionData, &$resultData) {
        return $transactionData[0]['amount'];
    }

    /**
     * checks if an element with value $element exists in a two dimensional array
     * @param type $element
     * @param type $array
     *
     * @return array with data
     *          or false of $elements does not exist in two dimensional array
     */
    public function in_multiarray($element, $array) {
        while (current($array) !== false) {
            if (current($array) == $element) {
                return true;
            } elseif (is_array(current($array))) {
                if ($this->in_multiarray($element, current($array))) {
                    return(current($array));
                }
            }
            next($array);
        }
        return false;
    }

    /**
     * Gets the latest (=last entry in DB) data of a model table
     * @param string    $model
     * @param array     $filterConditions
     *
     * @return array with data
     *          or false if $elements do not exist in two dimensional array
     */
    public function getLatestTotals($model, $filterConditions) {

        $temp = $this->$model->find("first", array('conditions' => $filterConditions,
            'order' => array($model . '.id' => 'desc'),
            'recursive' => -1
        ));

        if (empty($temp)) {
            return false;
        }

        foreach ($temp[$model] as $key => $item) {
            $keyName = explode("_", $key);
            if (strtoupper($model) <> strtoupper($keyName[0])) {
                unset($temp[$model][$key]);
            }
        }
        return $temp;
    }

}

/*
 // these are the total values per PFP

            if ($this->variablesConfig[30]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // principal and interest payment [30]
                $varName = $this->variablesConfig[30]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidatePrincipalAndInterestPayment($database);
                $this->variablesConfig[30]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[31]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // installmentPaymentProgress [31]
                $varName = $this->variablesConfig[31]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateInstallmentPaymentProgress($database);
                $this->variablesConfig[31]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[34]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // capital repayment (34)
                $varName = $this->variablesConfig[34]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateCapitalRepayment($database);
                $this->variablesConfig[34]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[35]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // partial principal payment(35
                $varName = $this->variablesConfig[35]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidatePartialPrincipalPayment($database);
                $this->variablesConfig[35]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[37]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // outstanding principal (37)
                $varName = $this->variablesConfig[37]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateOutstandingPrincipal($database);
                $this->variablesConfig[37]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[38]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // received repayments( 38)
                $varName = $this->variablesConfig[38]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateReceivedPrepayments($database);
                $this->variablesConfig[38]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[42]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // total gross income (42
                $varName = $this->variablesConfig[42]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateTotalGrossIncome($database);
                $this->variablesConfig[42]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[43]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // interest gross income (43)
                $varName = $this->variablesConfig[43]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateInterestgrossIncome($database);
                $this->variablesConfig[43]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[53]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // total cost (53)
                $varName = $this->variablesConfig[53]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateTotalCost($database);
                $this->variablesConfig[53]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[53]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // next payment date (39)
                $varName = $this->variablesConfig[53]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateNextPaymentDate($database);
                $this->variablesConfig[53]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

            if ($this->variablesConfig[53]['state'] == WIN_FLOWDATA_VARIABLE_NOT_DONE) {   // estimated next payment (40)
                $varName = $this->variablesConfig[53]['databaseName'];
                $database[$varName[0]][$varName[1]] =  $this->consolidateEstimatedNextPayment($database);
                $this->variablesConfig[53]['state'] = WIN_FLOWDATA_VARIABLE_DONE;
            }

 */
