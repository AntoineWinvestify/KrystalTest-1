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
App::import('Shell', 'UserData');
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
        $handle = new UserDataShell();

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
            print_r($pendingJobs);
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
                        $files[WIN_FLOW_TRANSACTION_FILE] = $dirs->findRecursive(WIN_FLOW_TRANSACTION_FILE . ".*", true);
                        $files[WIN_FLOW_INVESTMENT_FILE] = $dirs->findRecursive(WIN_FLOW_INVESTMENT_FILE . ".*", true);
                        $files[WIN_FLOW_EXPIRED_LOAN_FILE] = $dirs->findRecursive(WIN_FLOW_EXPIRED_LOAN_FILE . ".*", true);
                        $listOfActiveInvestments = $this->getListActiveInvestments($linkedAccountId);

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
    public function getListActiveInvestments($linkedaccount_id) {
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
        $calculationClassHandle = new UserDataShell();
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
            $database = $calculationClassHandle->getLatestTotals("Userinvestmentdata", $filterConditions);

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
                                $result = $calculationClassHandle->$functionToCall($transactionData, $database);
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
                                
                                $result = $calculationClassHandle->$functionToCall($transactionData, $database);

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
                        $database[$varName[0]][$varName[1]] = $calculationClassHandle->$functionToCall($transactionData, $database);
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
                    $database[$varName[0]][$varName[1]] = $calculationClassHandle->$functionToCall($transactionData, $database);                

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
        // The following is done only once per readout period independent if period covers one day, 1 week or if
        // it is a "link account" action
        // We also have to reduce the total values with the amounts of the investments that we finished TODAY, as (normally)
        // all loan related amounts are for active investments only
        // 
        // determine which loans have terminated
        // loop through all of them and subtracts amounts from total values
        echo "Start consolidating the platform data, using the control variables\n";
        $calculationClassHandle->consolidateControlVariables($file);
        echo "Consolidation Phase 2, checking control variables\n";
        $calculationClassHandle->consolidatePlatformData();
        return;
    }

}
