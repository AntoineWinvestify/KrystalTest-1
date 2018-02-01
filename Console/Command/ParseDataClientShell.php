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
 * 
 * 
 */
App::import('Shell', 'GearmanClient');
App::import('Shell', 'UserData');
class ParseDataClientShell extends GearmanClientShell {

    public $uses = array('Queue', 'Paymenttotal', 'Investment', 'Investmentslice', 'Globaltotalsdata', 'Amortizationtable');
    protected $variablesConfig;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
        return;
        echo "Deleting Investment\n";
        $this->Investment->deleteAll(array('Investment.id >' => 0), false);

        echo "Deleting Paymenttotal\n";
        $this->Paymenttotal->deleteAll(array('Paymenttotal.id >' => 0), false);

        echo "Deleting Payment\n";
        $this->Payment = ClassRegistry::init('Payment');
        $this->Payment->deleteAll(array('Payment.id >' => 0), false);
       
        echo "Deleting Userinvestmentdata\n";
        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
        $this->Userinvestmentdata->deleteAll(array('Userinvestmentdata.id >' => 0), false);
        
        echo "Deleting Globalcashflowdata\n";
        $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
        $this->Globalcashflowdata->deleteAll(array('Globalcashflowdata.id >' => 0), false);

        echo "Deleting Globaltotalsdata\n";
        $this->Globaltotalsdata = ClassRegistry::init('Globaltotalsdata');
        $this->Globaltotalsdata->deleteAll(array('Globaltotalsdata.id >' => 0), false);
 
        echo "Deleting Investmentslice\n";
        $this->Investmentslice = ClassRegistry::init('Investmentslice');
        $this->Investmentslice->deleteAll(array('Investmentslice.id >' => 0), false);
        
        echo "Deleting AmortizationTable\n";
        $this->AmortizationTable = ClassRegistry::init('AmortizationTable');
        $this->AmortizationTable->deleteAll(array('AmortizationTable.id >' => 0), false);        
        
        return;
    }

    public function initClient() {

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
            $pendingJobs = $this->checkJobs(array(WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, WIN_QUEUE_STATUS_EXTRACTING_DATA_FROM_FILE),
                                                  WIN_QUEUE_STATUS_EXTRACTING_DATA_FROM_FILE,
                                                $jobsInParallel);
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
                   
                    $this->date = $this->queueInfo[$job['Queue']['id']]['date'];                // End date of collection period
                    $this->startDate = $this->queueInfo[$job['Queue']['id']]['startDate'];      // Start date of collection period

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
                        $files[WIN_FLOW_CONTROL_FILE] = $dirs->findRecursive(WIN_FLOW_CONTROL_FILE . ".*", true);
                        $listOfActiveInvestments = $this->getLoanIdListOfInvestments($linkedAccountId, WIN_LOANSTATUS_ACTIVE);
                        $listOfReservedInvestments = $this->getLoanIdListOfInvestments($linkedAccountId, WIN_LOANSTATUS_WAITINGTOBEFORMALIZED);
                        
                        $controlVariableFile = $dirs->findRecursive(WIN_FLOW_CONTROL_FILE. ".*", true);

                        
                        $params[$linkedAccountId] = array(
                            'pfp' => $pfp,
                            'activeInvestments' => count($listOfActiveInvestments),
                            'listOfCurrentActiveInvestments' => $listOfActiveInvestments,
                            'listOfReservedInvestments' => $listOfReservedInvestments,
                            'userReference' => $job['Queue']['queue_userReference'],
                            'files' => $files,
                            'finishDate' => $this->queueInfo[$queueId]['date'],
                            'startDate' => $this->queueInfo[$queueId]['startDate'][$linkedAccountId],
                            'actionOrigin' => $this->queueInfo[$job['Queue']['id']]['originExecution'],
                        );
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
                        if ($mapResult == true) { 
                            $this->userResult[$queueIdKey][$platformKey] = WIN_STATUS_COLLECT_CORRECT;
                            $newLoans = $platformResult['amortizationTablesOfNewLoans'];
                            if (!empty($newLoans)) {
                                echo "WRITING LOANIDS\n";
                      //          $controlVariableFile =  $platformData['controlVariableFile'];
                                file_put_contents($baseDirectory . "loanIds.json", json_encode(($newLoans)));
                                $newFlowState = WIN_QUEUE_STATUS_DATA_EXTRACTED;
                            } 
                            else {
                                $newFlowState = WIN_QUEUE_STATUS_STARTING_CALCULATION_CONSOLIDATION;
                            }
                        }
                        else {
                            $this->userResult[$queueIdKey][$platformKey] = WIN_STATUS_COLLECT_ERROR;
                            echo "ERROR ENCOUNTERED\n"; 
                        }
                    }                                                       //verifyStatus($status, $message, $restartStatus, $errorStatus)
                    $this->verifyStatus($newFlowState, 
                            "Data successfully downloaded", 
                            WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, 
                            WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
/*               
                    $this->Queue->id = $queueIdKey;
                    $this->Queue->save(array('queue_status' => $newFlowState,
                        'queue_info' => json_encode($this->queueInfo[$queueIdKey]),
                            ), $validate = true
                    );
*/
                }
                break;
            } 
            else {
                $inActivityCounter++;
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Nothing in queue, so go to sleep for a short time\n";
                }
                sleep(4);                                          // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {              // system has dealt with ALL request for tonight, so exit "forever"
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Maximum Waiting time expired, so EXIT\n";
                   
                }
                exit;
            }
        }
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
        
        
ini_set('memory_limit','2048M');      
$timeStart = time();
        $calculationClassHandle = new UserDataShell();
        $investmentId = null;
        $linkedaccountId = $platformData['linkedaccountId'];
        $userReference = $platformData['userReference'];
        $startDate = $platformData['startDate'];
        $finishDate = $platformData['finishDate'];
        
 //       $returnData[$linkedAccountKey]['parsingResultControlVariables'];
        
        
        $controlVariableFile =  $platformData['controlVariableFile'];                   // Control variables as supplied by P2P
        $controlVariableActiveInvestments = $platformData['activeInvestments'];         // Our control variable
$tempMeasurements = array(
        'winTechActiveStateCounting' => 0,
        'winTechNewLoanCounting' => 0
);  

        if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) {
            $platformData['workingNewLoans'] = array_values($platformData['newLoans']);
            $expiredLoanValues = array_values(array_keys($platformData['parsingResultExpiredInvestments']));
 
            $precision = $platformData['dashboard2ConfigurationParameters']['outstandingPrincipalRoundingParm'];
            if (empty($precision)) {
                $precision = '0.00001';     // Default precision
            }

            // merge the two arrays
            $countArray1 = count($platformData['workingNewLoans']);
            
            foreach ($expiredLoanValues as $key => $value) {
                $platformData['workingNewLoans'][$countArray1 + $key] = $value;   
            }
        }
        else {
 //           echo "regular update\n";
            $platformData['workingNewLoans'] = $platformData['newLoans'];
        } 

        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');          // A new table exists for EACH new calculation interval
        $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
        $this->Payment = ClassRegistry::init('Payment');

        foreach ($platformData['parsingResultTransactions'] as $dateKey => $dates) {    // these are all the transactions, PER day
echo "\ndateKey = $dateKey \n";

if ($dateKey == "2014-01- 21"){ 
    echo "Exiting when date = " . $dateKey . "\n";
    $timeStop = time();
    echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) . "\n"; 
    exit;
}


// Lets allocate a userinvestmentdata for this calculation period (normally daily)
            // reset the relevant variables before going to next date
            unset ($database);              // Start with a clean shadow database
            unset ($investmentListToCheck);
            unset ($loanStatus);
            $filterConditions = array("linkedaccount_id" => $linkedaccountId);
            $database = $calculationClassHandle->getLatestTotals("Userinvestmentdata", $filterConditions);

            $this->Userinvestmentdata->create();
            $database['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
            $database['Userinvestmentdata']['userinvestmentdata_investorIdentity'] = $userReference;
            $database['Userinvestmentdata']['date'] = $dateKey; 
            $database['configParms']['outstandingPrincipalRoundingParm'] = $precision;      // configuration parameter 
    
            foreach ($dates as $keyDateTransaction => $dateTransaction) {           // read all *individual* transactions of a loanId per day

/*
 * if ($keyDateTransaction == "29016-01") {   
        echo "Reached LoanId $keyDateTransaction, so quitting\n ";
        continue;
        echo "Exiting\n";
        $timeStop = time();
        echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart); 
        exit;
} 
*/ 
              
// Do some pre-processing in order to see if a *global* loanId really is a global loanId, i.e. 
// convert the global loanId to a real loanId, this works for new investments only  
                $keyDateTransactionNames = explode("_", $keyDateTransaction);
                if ($keyDateTransactionNames[0] == "global") {                      
                    if ($dateTransaction[0]['conceptChars'] === "AM_TABLE") {        // new investment
     // This could be a Ghost loan (from Zank). Let's check the investments and expired_investments to see if 
     // a reference exists to the loan and, if succesfull, assign the loanId.
                        $ghostInvestment = $this->searchInvestmentArrays($dateTransaction[0], 
                                                                    $platformData['parsingResultInvestments'], 
                                                                    $platformData['parsingResultExpiredInvestments']);
                        if (!empty($ghostInvestment)) {
                            switch ($ghostInvestment[0]['investment_statusOfLoan']) {
                                case WIN_LOANSTATUS_FINISHED:
                                case WIN_LOANSTATUS_CANCELLED:
                                case WIN_LOANSTATUS_WRITTEN_OFF:
                                    $investmentListToCheck = $platformData['parsingResultExpiredInvestments'][$dateTransaction[0]['investment_loanId']][0];
                                    break;
                                case WIN_LOANSTATUS_WAITINGTOBEFORMALIZED:
                                case WIN_LOANSTATUS_ACTIVE: 
                                    $investmentListToCheck = $platformData['parsingResultInvestments'][$dateTransaction[0]['investment_loanId']][0];
                                    break;
                            }
         
                            $dateTransaction[0]['investment_loanId'] = $ghostInvestment[0]['investment_loanId'];  // Now everything continues in a normal way
                        }
                    }
                }
                
                // special procedure for platform related transactions, i.e. when we don't have a real loanId
                $dateTransactionNames = explode("_", $dateTransaction[0]['investment_loanId']);

                if ($dateTransactionNames[0] == "global") {                // --------> ANALYZING GLOBAL, PLATFORM SPECIFIC DATA
                    // cycle through all individual fields of the transaction record
                    foreach ($dateTransaction[0] as $transactionDataKey => $transaction) {  // cycle through all individual fields of the transaction record
                        if ($transactionDataKey == "internalName") {        // 'dirty trick' to keep it simple
                            $transactionDataKey = $transaction;
                        }  
                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);
                        if (!empty($tempResult)) {                            
                            unset($result);
                            $functionToCall = $tempResult['function'];
                            if (isset($tempResult['globalDatabaseName']))   {  
                                $dataInformation = explode(".", $tempResult['globalDatabaseName']);
                            }
                            else {
                                $dataInformation = explode(".", $tempResult['databaseName']);
                            }
                            $dbTable = $dataInformation[0];
                            $dbTableField = $dataInformation[1];

                            if (!empty($functionToCall)) {
                                $result = $calculationClassHandle->$functionToCall($dateTransaction[0], $database);

                                // update the field userinvestmentdata_cashInPlatform   
                                $cashflowOperation = $tempResult['cashflowOperation'];
                                if (!empty($cashflowOperation)) {
                                    $database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = 
                                        $cashflowOperation($database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $result, 16);                          
                                }                                

                                if ($tempResult['charAcc'] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                    $database[$dbTable][$dbTableField] = bcadd($database[$dbTable][$dbTableField], $result, 16);
                                } 
                                else {
                                    $database[$dbTable][$dbTableField] = $result;
                                }
                            } 
                            else {
                                echo "=====> dbTable = $dbTable, transaction = $transaction and dbTableField = $dbTableField\n",
                                $database[$dbTable][$dbTableField] = $result;
                            }
                        }
                    }
                    continue;
                }
 
                echo "---------> ANALYZING NEXT LOAN ------- with LoanId = " .  $dateTransaction[0]['investment_loanId'] . "\n";

                if (isset($platformData['parsingResultInvestments'][$dateTransaction[0]['investment_loanId']])) {
                    echo "THIS IS AN ACTIVE LOAN\n";
                    $investmentListToCheck = $platformData['parsingResultInvestments'][$dateTransaction[0]['investment_loanId']][0];
                    $loanStatus = WIN_LOANSTATUS_ACTIVE;
                }

                if (isset($platformData['parsingResultExpiredInvestments'][$dateTransaction[0]['investment_loanId']])) {
                    echo "THIS IS AN ALREADY EXPIRED LOAN\n";
                    $investmentListToCheck = $platformData['parsingResultExpiredInvestments'][$dateTransaction[0]['investment_loanId']][0];
                    $loanStatus = WIN_LOANSTATUS_FINISHED;
                }
                if (in_array($dateTransaction[0]['investment_loanId'], $platformData['workingNewLoans'])) {          // check if loanId is new
                    $arrayIndex = array_search($dateTransaction[0]['investment_loanId'], $platformData['workingNewLoans']);
                    echo "FOUND in Newloans\n";
                    if ($arrayIndex !== false) {        // Deleting the array from new loans list
                        unset($platformData['workingNewLoans'][$arrayIndex]);
                    }

                    echo "Storing the data of a 'NEW LOAN' in the shadow DB table\n";
                    $database['investment']['investment_myInvestment'] = 0;
                    $database['investment']['investment_secondaryMarketInvestment'] = 0;  

//$database['investment']['technicalState'] = WIN_TECH_STATE_ACTIVE;


                    $controlVariableActiveInvestments = $controlVariableActiveInvestments + 1;
 
             //       $platformData['newLoans'][]= $transactionData['investment_loanId'];
                   
             //       if ($transactionData['conceptChars'] == "AM_TABLE") {       // Add loanId so new amortizationtable shall be collected
             //           if ($loanStatus == WIN_LOANSTATUS_ACTIVE) {         // used for currently active loans and for Zombie loans
             //               $database['investment']['markCollectNewAmortizationTable'] = "AM_TABLE";
             //           }
             //       }
            //        $database['investment']['investment_sliceIdentifier'] = "ZZXXXX";  //TO BE DECIDED WHERE THIS ID COMES FROM    
                    
                    // Load all the data of the investment  
                    foreach ($investmentListToCheck as $investmentDataKey => $investmentData) {
                        $tempResult = $this->in_multiarray($investmentDataKey, $this->variablesConfig);

                        if (!empty($tempResult)) {
                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            $database[$dbTable][$investmentDataKey] = $investmentData;
           //               $this->variablesConfig[$investmentDataKey]['state'] = WIN_FLOWDATA_VARIABLE_DONE;   // Mark done
                        }
                    }
                    
                    
                    switch($database['investment']['investment_statusOfLoan']) {
                        case WIN_LOANSTATUS_WAITINGTOBEFORMALIZED:
                        case WIN_LOANSTATUS_ACTIVE:
                        case WIN_LOANSTATUS_FINISHED:    
//                            $database['investment']['investment_new'] = YES;        // Serves for writing it to the DB as a NEW loan  
                            $database['investment']['investment_amortizationTableAvailable'] = WIN_AMORTIZATIONTABLES_NOT_AVAILABLE;
                            $database['investment']['investment_technicalStateTemp'] = "INITIAL";                            
                        break;
                    }   
                } 
                else {  // Already a loan which exists in our database, can be in any state
                    $filterConditions = array("investment_loanId" => $dateTransaction[0]['investment_loanId'] ,
                                                "linkedaccount_id" => $linkedaccountId);
                    $tempInvestmentData = $this->Investment->getData($filterConditions, array("id", 
                        "investment_priceInSecondaryMarket" , "investment_outstandingPrincipal", "investment_totalGrossIncome",
                        "investment_totalLoancost", "investment_totalPlatformCost", "investment_myInvestment", "investment_technicalStateTemp",
                        "investment_secondaryMarketInvestment", "investment_paidInstalments", "investment_statusOfLoan", "investment_sliceIdentifier"));
 
                    $investmentId = $tempInvestmentData[0]['Investment']['id'];
                    if (empty($investmentId)) {     // This is a so-called Zombie Loan. It exists in transaction records, but not in the investment list
                                                    // We mark to collect amortization table and hope that the PFP will return amortizationtable data.       

echo "THE LOAN WITH ID " . $dateTransaction[0]['investment_loanId']  . " IS A ZOMBIE LOAN\n";
echo "Storing the data of a 'NEW ZOMBIE LOAN' in the shadow DB table and putting its state to WIN_LOANSTATUS_ACTIVE\n";
                        $loanStatus = WIN_LOANSTATUS_ACTIVE;        // So amortization data is collected
 //                       $database['investment']['investment_new'] = YES;        // SO we store it as new loan in the database
                        $database['investment']['investment_myInvestment'] = 0;
                        $database['investment']['investment_secondaryMarketInvestment'] = 0;  
                        $database['investment']['investment_sliceIdentifier'] = "ZZAAXXX";  //TO BE DECIDED WHERE THIS ID COMES FROM  
             //           $database['investment']['markCollectNewAmortizationTable'] = "AM_TABLE";        // Is this needed???? ALREADY DONE IN LINE 501
                        $database['investment']['investment_technicalData'] = WIN_TECH_DATA_ZOMBIE_LOAN;  
                        $database['investment']['investment_technicalStateTemp'] = "INITIAL";
                        $database['investment']['investment_amortizationTableAvailable'] = WIN_AMORTIZATIONTABLES_NOT_AVAILABLE;
                    }
                    else {  // A normal regular loan, which is already defined in our database
                    // Copy the information to the shadow database, for processing later on
echo __FUNCTION__ . " " . __LINE__ . " : Reading the set of initial data of an existing loan with investmentId = $investmentId\n";
                        $database['investment']['investment_statusOfLoan'] = $tempInvestmentData[0]['Investment']['investment_statusOfLoan'];
                        $database['investment']['investment_myInvestment'] = $tempInvestmentData[0]['Investment']['investment_myInvestment'];
                        $database['investment']['investment_secondaryMarketInvestment'] = $tempInvestmentData[0]['Investment']['investment_secondaryMarketInvestment'];
                        $database['investment']['investment_outstandingPrincipal'] = $tempInvestmentData[0]['Investment']['investment_outstandingPrincipal'];
                        $database['investment']['investment_outstandingPrincipalOriginal'] = $tempInvestmentData[0]['Investment']['investment_outstandingPrincipal'];
                        $database['investment']['investment_totalGrossIncome'] = $tempInvestmentData[0]['Investment']['investment_totalGrossIncome'];   
                        $database['investment']['investment_totalLoanCost'] = $tempInvestmentData[0]['Investment']['investment_totalLoanCost'];   
                        $database['investment']['investment_technicalStateTemp'] = $tempInvestmentData[0]['Investment']['investment_technicalStateTemp'];
                        $database['investment']['investment_sliceIdentifier'] = $tempInvestmentData[0]['Investment']['investment_sliceIdentifier'];
                        $database['investment']['id'] = $investmentId;
//$database['investment']['technicalState'] = WIN_TECH_STATE_ACTIVE;
                    }
                }

                // load all the transaction data
                foreach ($dateTransaction as $transactionKey => $transactionData) {       // read one by one all transactions of this loanId
echo "====> ANALYZING NEW TRANSACTION transactionKey = $transactionKey transactionData = \n";
                    
                    if (isset($transactionData['conceptChars'])) {
                        $conceptChars = explode(" ", $transactionData['conceptChars']);
                        if (in_array("AM_TABLE", $conceptChars)) {          // New, or extra investment, so new amortizationtable shall be collected
                            if ($loanStatus == WIN_LOANSTATUS_ACTIVE) {
                                unset ($sliceIdentifier);
                                if (isset($transactionData['sliceIdentifier'])) {
                                        $sliceIdentifier = $transactionData['sliceIdentifier'];
                                    }
                                if (isset($database['investment']['investment_sliceIdentifier'])) {
                                        $sliceIdentifier = $database['investment']['investment_sliceIdentifier'];
                                    }                                    
                                if (empty($sliceIdentifier)) {                       // Take the default one
                                    $sliceIdentifier = $transactionData['investment_loanId'];
echo "@@@@ sliceIdentifier has been obtained from Investment array\n";                                  
                                }
                                                               
                                $sliceIdExists = array_search ($sliceIdentifier, $platformData['newLoans']);
                                if ($sliceIdExists !== false) {     // loanSliceId does not exist in newLoans array, so add it
                                    $slicesAmortizationTablesToCollect[] = $sliceIdentifier;    // For later processing
                                }
                            }
                        }
                    }
                    
                    echo __FILE__ . " " . __LINE__ . "\n";
//print_r($transactionData);
                    foreach ($transactionData as $transactionDataKey => $transaction) {  // read all transaction concepts
                        if ($transactionDataKey == "internalName") {        // 'dirty trick' to keep it simple
                            $transactionDataKey = $transaction;
                        }
                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);
                        print_r($tempResult);
                        echo '-----------------------------';
                        print_r($transactionDataKey);
                        echo __FILE__ . " " . __LINE__ . "\n";
                        if (!empty($tempResult)) {
                            unset($result);
                            
                            $functionToCall = $tempResult['function'];

                            echo __FILE__ . " " . __LINE__ . " Function to call = $functionToCall, transactionDataKey = $transactionDataKey\n";
                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            $dbVariableName = $dataInformation[1];
                            
echo "index = " . $tempResult['internalIndex'] . " \n";
                            echo "Execute calculationfunction: $functionToCall\n";
                            if (!empty($functionToCall)) { 
                                $result = $calculationClassHandle->$functionToCall($transactionData, $database);

echo "Result = $result and index = " . $tempResult['internalIndex'] ."\n";

                                if (isset($tempResult['linkedIndex'])) {
                                    echo ">>>>>>>>>>>>>>>> LINKED INDEX\n";
   // print_r($this->variablesConfig[$tempResult['linkedIndex']]);
                                    $dataInformationInternalIndex = explode(".", $this->variablesConfig[$tempResult['linkedIndex']]['databaseName']);
                                    $dbTableInternalIndex = $dataInformationInternalIndex[0];
                                 
                                    if ($tempResult['charAcc'] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {   
                                        echo "ADDING $result to existing result " . $database[$dataInformationInternalIndex[0]][$dataInformationInternalIndex[1]] . "\n";
                                        $database[$dataInformationInternalIndex[0]][$dataInformationInternalIndex[1]] = 
                                                bcadd($database[$dataInformationInternalIndex[0]][$dataInformationInternalIndex[1]], $result, 16);
                                    } 
                                    else {
                                        echo "POSSIBLY overwriting existing result\n";
                                        $database[$dataInformationInternalIndex[0]][$dataInformationInternalIndex[1]] = $result;
                                    }                                        
                                }

                                // update the field userinvestmentdata_cashInPlatform   
                                $cashflowOperation = $tempResult['cashflowOperation'];
                                if (!empty($cashflowOperation)) {
echo "[dbTable] = " . $dbTable . " and [transactionDataKey] = " . $transactionDataKey . " and dbTableInternalIndex = $dbTableInternalIndex\n";
//echo "================>>  " . $cashflowOperation . " ADDING THE AMOUNT OF " . $result ."\n";
                                    $database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = 
                                                $cashflowOperation($database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], 
                                                $result, 16); 
//echo "#########========> database_cashInPlatform = " .    $database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] ."\n";                            
                                }
   
                                if ($database['investment']['amortizationTableAvailable'] == WIN_AMORTIZATIONTABLES_AVAILABLE) {
                                    if (in_array("REPAYMENT", $conceptChars)) {    
                                        $this->repaymentReceived($transactionData, $database); 
                                    }                                
                                }
                                else {
                                    // Store the information so it can be processed in flow 3B
                                }
                                            
                                if ($tempResult['charAcc'] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                    echo "Adding $result to existing result " . $database[$dbTable][$dbVariableName] . "\n";
                                    $database[$dbTable][$dbVariableName] = bcadd($database[$dbTable][$dbVariableName], $result, 16);
                                } 
                                else {
                                    echo "possibly overwriting existing result\n";
                                    $database[$dbTable][$dbVariableName] = $result;
                                } 
                                echo $database[$dbTable][$dbVariableName] . "\n";
                            } 
                            else {
                                $database[$dbTable][$dbVariableName] = $transaction;
                                if (isset($tempResult['linkedIndex'])) {   // THIS IS UNTESTED AND PROBABLY NOT NEEDED ANYWAY
                                    echo "LINKED-INDEX";
 //      print_r($this->variablesConfig[$tempResult['linkedIndex']]);
                                    $dataInformationInternIndex = explode(".", $tempResult['databaseName']);
                                    $dbTableInternalIndex = $dataInformationInternalIndex[0];
                                    $database[[$dbTableInternalIndex][0]][[$dbTableInternalIndex][1]] = $transaction;
                                }
                            }        
                        }
                    }       
                    

                    
                }   
                    
// Now start consolidating of the results on investment level and per day                
                $internalVariableToHandle = array(10014, 10015, 37, 10004);
                foreach ($internalVariableToHandle as $keyItem => $item) {
                    $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                    $functionToCall = $this->variablesConfig[$item]['function'];
                    echo "Calling the function: $functionToCall and dbtable = " . $varName[0] . " and varname =  " . $varName[1].  "\n";
                    $result = $calculationClassHandle->$functionToCall($transactionData, $database);   
                    if ($this->variablesConfig[$item]["charAcc"] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                        if (!isset($database[$varName[0]][$varName[1]])) {
                            $database[$varName[0]][$varName[1]] = 0;
                        }
                        $database[$varName[0]][$varName[1]] = bcadd($database[$varName[0]][$varName[1]], $result, 16);
                    } 
                    else {
                        $database[$varName[0]][$varName[1]] = $result;
                    }                     
                }                  
                echo "printing relevant part of database\n";
              
                $database['investment']['linkedaccount_id'] = $linkedaccountId;
                
                    
//               if ($database['investment']['investment_new'] == YES) {
                if (empty($investmentId)) {     // The investment data is not yet stored in the database, so store it
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Investment Data... ";
                    $resultCreate = $this->Investment->createInvestment($database['investment']);

                    if (!empty($resultCreate)) {
                        $investmentId = $resultCreate;
                        echo "Saving 'NEW' loan with investmentId = $investmentId, Done\n";
                        $database['investment']['id'] = $resultCreate;
                    } else {
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['investment']['investment_loanId'] . "\n";
                        }
                    }
                } 
                else {
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
                
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Payment Data for investment with id = $investmentId... ";
                $database['payment']['investment_id'] = $investmentId;
                $database['payment']['date'] = $dateKey;
                $this->Payment->create();
                if ($this->Payment->save($database['payment'], $validate = true)) {
                    echo "Done\n";
                } 
                else {
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['payment']['payment_loanId'] . "\n";
                    }
                }
     
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Execute functions for consolidating the data of Flow for loanId = " . $database['investment']['investment_loanId'] . "\n";
  


//Define which amortization tables shall be collected 
                foreach ($slicesAmortizationTablesToCollect as $tableSliceIdentifier) {
                    $loanSliceId = $this->linkNewSlice($investmentId, $tableSliceIdentifier);
                    
                    if (!in_array($loanSliceId, $platformData['amortizationTablesOfNewLoans'])) {       // avoid duplicates
                        $platformData['amortizationTablesOfNewLoans'][$loanSliceId] = $tableSliceIdentifier;
                    }
                    
                }
                unset($slicesAmortizationTablesToCollect);
//  print_r($platformData['amortizationTablesOfNewLoans']);

    
                $internalVariablesToHandle = array(10001,  // removed 10004 
                                                    10006, 10007, 10008,
                                                    10009, 10010, 10011, 
                                                    10012, 10013, 10016,
                                                    10017, 10018, 10019);      
                foreach ($internalVariablesToHandle as $keyItem => $item) {
                    $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                    $functionToCall = $this->variablesConfig[$item]['function'];                      
                    $result = $calculationClassHandle->$functionToCall($transactionData, $database);                
echo "&&&&&=>: original amount = " . $database[$varName[0]][$varName[1]] ." and new result = $result". "\n";
/*
if ($this->variablesConfig[$item]['internalIndex'] == 10002 ){
    if ($database[$varName[0]][$varName[1]] < $result){  // we close an investment
        $FINISHED_ACCOUNT = $FINISHED_ACCOUNT + 1;
        $FINISHED_ACCOUNT_LIST[] = $database['investment']['investment_loanId'];   
        if (in_array($database['investment']['investment_loanId'], $FINISHED_ACCOUNT_LIST)) {
            $FINISHED_DUPLICATES_LIST[] = $database['investment']['investment_loanId'];
        }
    }
}
*/
                    if ($this->variablesConfig[$item]["charAcc"] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                        if (!isset($database[$varName[0]][$varName[1]])) {
                            $database[$dbTable][$transactionDataKey] = 0;
                        }
                        $database[$varName[0]][$varName[1]] = bcadd($database[$varName[0]][$varName[1]], $result, 16);
                    } else {
                        $database[$varName[0]][$varName[1]] = $result;
                    }
                }
                
                echo "DELETING INVESTMENT RELATED PART OF SHADOW DATABASE\n";
                unset($investmentId);
                unset($database['investment']);
                unset($database['payment']);
            }

            echo "printing global data for the date = $dateKey\n";
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Finishing mapping process Flow 2\n";
            // The following is done only once per readout period independent if period covers one day, 1 week or if
            // it is a "link account" action
            // We also have to reduce the total values with the amounts of the investments that we finished TODAY, as (normally)
            // all loan related amounts are for active investments only
            // 
            // determine which loans have terminated
            // loop through all of them and subtracts amounts from total values
            echo "Starting to consolidate the platform data, using the control variables, calculating each variable\n\n\n\n";
            $internalVariableToHandle = array();  // Can be expanded according to new requirements
            // = outstanding principal,. totalnumberofinvestments and cashinplatform
            foreach ($internalVariableToHandle as $keyItem => $item) {
                echo "VariableIndex being handled = " . $item . "\n";

                $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                $functionToCall = $this->variablesConfig[$item]['function'];
                echo "Calling the function: $functionToCall and index = $keyItem\n";
                $database[$varName[0]][$varName[1]] = $calculationClassHandle->$functionToCall($transactionData, $database);                
                echo "inputs are " . $varName[0] . " and " . $varName[1] . "\n";
                echo $database[$varName[0]][$varName[1]];
            }            
            
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Userinvestmentdata Data... ";
            if ($this->Userinvestmentdata->save($database['Userinvestmentdata'], $validate = true)) {
                $userInvestmentDataId = $this->Userinvestmentdata->id;
                echo "Done, id = $userInvestmentDataId\n";
                $database['Userinvestmentdata']['id'] = $userInvestmentDataId;
            } 
            else {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['Userinvestmentdata']['payment_loanId'] . "\n";
                }
            }

            if (!empty($database['globalcashflowdata'])) {
                $database['globalcashflowdata']['userinvestmentdata_id'] = $userInvestmentDataId;
                $database['globalcashflowdata']['date'] = $dateKey;
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Globalcashflowdata Data... ";                
                $this->Globalcashflowdata->create();
                if ($this->Globalcashflowdata->save($database['globalcashflowdata'], $validate = true)) {
                    echo "Done\n";
                } 
                else {
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['globalcashflowdata']['payment_loanId'] . "\n";
                    }
                }
            }  

            if (!empty($database['globaltotalsdata'])) {
                $database['globaltotalsdata']['userinvestmentdata_id'] = $userInvestmentDataId;
                $database['globaltotalsdata']['linkedaccount_id'] = $linkedaccountId;
                $database['globaltotalsdata']['date'] = $dateKey;
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Globaltotalsdata Data... ";               
                $this->Globaltotalsdata->create();
                if ($this->Globaltotalsdata->save($database['globaltotalsdata'], $validate = true)) {
                    echo "Done\n";
                } 
                else {
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['globalcashflowdata']['payment_loanId'] . "\n";
                    }
                }
            }   
            
            // Determine the number of active investments. NOTE THAT THIS IS A SIMPLE PATCH. THE REAL SOLUTION 
            // ENTAILS THE INCREMENTING/DECREMENTING OF A COUNTER WHEN A NEW LOAN ENTERS OR WHEN A LOAN FINISHES
            //      READ FROM investments 

            $filterConditions = array("AND" => array(array('linkedaccount_id' => $linkedaccountId 
                                             )),
                                        "OR" =>  array(array('investment_technicalStateTemp' => 'INITIAL'), 
                                                      array('investment_technicalStateTemp' => 'ACTIVE')
                                              ));
            
            $activeInvestments = $this->Investment->find('count', array(
                                        'conditions' => $filterConditions));

            $tempUserInvestmentDataItem = array('id' => $userInvestmentDataId,
                                                'userinvestmentdata_numberActiveInvestments' => $activeInvestments);
            $this->Userinvestmentdata->save($tempUserInvestmentDataItem, $validate = true);
        }

        $controlVariables['outstandingPrincipal'] = $database['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'];  // Holds the *last* calculated value
        $controlVariables['myWallet'] = $database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'];      // Holds the *last* calculated value
        $controlVariables['activeInvestments'] = $database['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']; // Holds the last calculated value

        $fileString = file_get_contents($controlVariableFile);         // must be a json file
        $externalControlVariables = json_decode($fileString, true);     // Read control variables as supplied by p2p 
        echo "Consolidation Phase 2, checking control variables\n";        
 //       print_r($externalControlVariables);
        $controlVariablesCheck = $calculationClassHandle->consolidatePlatformControlVariables($controlVariables, $externalControlVariables);
        if ($controlVariablesCheck > 0) { // mismatch detected
            echo "CONTROL VARIABLES CHECK DOES NOT PASS\n";
            // STILL TO FINISH
            $errorData['line'] = __LINE__; 
            $errorData['file'] = __FILE__;
            $errorData['urlsequenceUrl'] = "";
            $errorData['subtypeErrorId'];                   //It is the subtype of the error
            $errorData['typeOfError'];              //It is the type of error or the summary of the detailed information of the error
            $errorData['detailedErrorInformation'];         //It is the detailed information of the error
            $errorData['typeErrorId'];                      //It is the principal id of the error           
            $this->saveGearmanError($errorData);
        }
       
        $calculationClassHandle->consolidatePlatformData($database);
        // remove duplicates from the 'newLoans'AND remove all loans whose loanId/slice are in expiredLoans
$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) ."\n";
print_r($platformData['amortizationTablesOfNewLoans']);
        return true;
    }
    
    
    
    /**
     * Connects a new 'Investmentslice' model to the 'Investment' model
     * 
     *  @param bigInt   $investmentId       The database 'id' of the 'Investment' table
     *  @param string   $sliceIdentifier    The identifier of the new slice     
     *  @return bigInt                      The database reference of the 'Investmentslice' model
     *                  
     */
    public function linkNewSlice($investmentId, $sliceIdentifier) {
        $id = $this->Investmentslice->getNewSlice ($investmentId, $sliceIdentifier);
        return $id;
    }  
    
    
    
    /**is needed for partial payments???
     *  Deals with all the actions of a repayment of a loan. This is BEFORE ANYTHING has been
     *  changed in the database by the main flow
     * 
     *  @param  array   array with the current transaction data
     *  @param  array   array with all data so far calculated and to be written to DB
     *  @return 
     *                  
     */
    public function repaymentReceived(&$transactionData, &$resultData) {
        echo __FUNCTION__ . __LINE__ . "\n";
        print_r($transactionData);
        print_r($resultData);
        
        /* transaction data contains loanId and slice identifier in case of Finbee.
         * I just implement loanId (as for Mintos)
         * store repayment data in amortization table
         * get next repayment data (date and amount)
         * As this is a repayment, so an investmentId already exists.
         * 
         * get all slices where investment_id = XXXXX;
         * for those investmentslices get the amortization table(s)
         * 
         * 
         * 
         */
        $resultData['investment']['investment_paymentStatus'] = 0;
        
        $conditions = array("AND" => array('investor_id' => $resultData['investment']['investment_loanId']));        
 	$this->Investment->Behaviors->load('Containable');
	$this->Investment->contain('Investmentslice');  
        print_r($conditions);
	$resultInvestmentData = $this->Investment->find("all", $params = array('recursive' => 2,
										'conditions'	=> $conditions,
												));
       
        print_r($resultInvestmentData);
        if (!empty($resultInvestmentData)) {
            
        }
        else {      // An error occurred, I cannot find the investmentslice model
            
        }
        echo "Exiting from " . __FUNCTION__ ;
        exit;
        return;
    }    

    /** is it worth the while to do this? I basically am only interested in the
     *  next payment date and I trust the platform calculates correctly.
     * 
     *  Queues the repayment data as no amortization table(s) are stored yet.
     *  This data is read when the tables are collected from the P2P.
     *  This function is typically called for active investments at time of linking 
     *  a new account
     * 
     *  @param  array   array with the current transaction data
     *  @param  array   array with all data so far calculated and to be written to DB
     *  @return 
     *                  
     */
    public function queueRepaymentData(&$transactionData, &$resultData) {
        echo __FUNCTION__ . __LINE__ . "\n";
        print_r($transactionData);
        print_r($resultData);
        
        if (isset($resultData['investment']['investment_queuedPaymentData'])) {
            
        }
        

        echo "Exiting from " . __FUNCTION__ ;
        exit;
        return;
    }       
 
    
    /** 
     *  Reads the queued repaymentData and update the amortization tables
     *  THIS IMPLEMENTATION ONLY COVERS MINTOS, ZANK AND DOES *NOT* HANDLE
     *  FINBEE
     * 
     *  @param  array   array with the current transaction data
     *  @param  array   array with all data so far calculated and to be written to DB
     *  @return 
     *                  
     */
    public function getRepaymentDataFromQueue($loanId, $sliceIdentifier) {
        echo __FUNCTION__ . __LINE__ . "\n";
        print_r($transactionData);
        print_r($resultData);
        
        if (isset($resultData['investment']['investment_queuedPaymentData'])) {
            
        }
        

        echo "Exiting from " . __FUNCTION__ ;
        exit;
        return;
    } 

  
    
    
    
    
    
    
     function arrayToExcel($array, $excelName) {
        /*$array = array("market" => 1, "q" => 2, "a" => 3, "s" => 4, "d" => 5, "f" => 6, "e" => 7, "r" => 8, "t" => 9, "y" => 11, "u" => 12, "i" => 13, "o" => 14, "p" => 15, "l" => 16);
        $excelName = "prueba";*/
        $keyArray = array();
        App::import('Vendor', 'PHPExcel', array('file' => 'PHPExcel' . DS . 'PHPExcel.php'));
        App::import('Vendor', 'PHPExcel_IOFactory', array('file' => 'PHPExcel' . DS . 'PHPExcel' . DS . 'IOFactory.php'));

        foreach ($array as $key => $val) {
            $keyArray[] = $key;
        }

        $filter = null;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setTitle($excelName);

        $objPHPExcel->setActiveSheetIndex(0)
                ->fromArray($keyArray, NULL, 'A1')
                ->fromArray($array, NULL, 'A2');
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($excelName);
        echo "FILE $excelName has been written\n";

    }   
    
    
     /** 
     *  Searches in the investments and expired_investment arrays for an *investment* done on 
     *  the date as defined in the dateTransaction array. Also the amount is checked
     *  and investments without the mark: "InvestmentAlreadyDetected"
     *  The result can be 0 or 1 array with investment information
     * 
     *  @param  array   array with the current transaction data
     *  @param  array   array with all data so far calculated and to be written to DB
     *  @return array
     *                  
     */   
    function searchInvestmentArrays($transaction, &$investments, &$expiredInvestments) {  

        foreach ($investments as $investmentKey => $investment) {
            if ($transaction['date'] == $investment[0]['investment_myInvestmentDate']) {  
                if (($transaction['amount']) == $investment[0]['investment_myInvestment']) {
                    if ($investments[$investmentKey][0]['InvestmentAlreadyDetected'] <> YES) {
                        $investments[$investmentKey][0]['InvestmentAlreadyDetected'] = YES;
                        return $investment;
                    }
                }                
            }   
        }
        foreach ($expiredInvestments as $investmentKey => $expiredInvestment) {
            if (($transaction['date']) == $expiredInvestment[0]['investment_myInvestmentDate']) {
                if (($transaction['amount']) == $expiredInvestment[0]['investment_myInvestment']) {
                    if ($expiredInvestments[$investmentKey][0]['InvestmentAlreadyDetected'] <> YES) {
                        $expiredInvestments[$investmentKey][0]['InvestmentAlreadyDetected'] = YES;
                        return $expiredInvestment;
                    }
                }                
            }   
        }
        return null;
    }    
    
    
}
