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

    public $uses = array('Queue', 'Paymenttotal', 'Investment', 'Investmentslice', 'Globaltotalsdata');
    protected $variablesConfig;

// Only used for defining a stable testbed definition
    public function resetTestEnvironment() {
 //       return;
        echo "Deleting Investment\n";
        $this->Investment->deleteAll(array('Investment.id >' => 10121), false);

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

        echo "Deleting Investmentslice\n";
 //       $this->Investmentslice = ClassRegistry::init('Investmentslice');
        $this->Investmentslice->deleteAll(array('Investmentslice.id >' => 0), false);

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

                        $controlVariableFile = $dirs->findRecursive(WIN_FLOW_CONTROL_FILE. ".*", true);
                        $params[$linkedAccountId] = array(
                            'pfp' => $pfp,
                            'activeInvestments' => count($listOfActiveInvestments),
                            'listOfCurrentActiveLoans' => $listOfActiveInvestments,
                            'userReference' => $job['Queue']['queue_userReference'],
                            'controlVariableFile' => $controlVariableFile[0],
                            'files' => $files,
                            'actionOrigin' => WIN_ACTION_ORIGIN_ACCOUNT_LINKING);
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
                            $newLoans = $platformResult['newLoans'];
                            if (!empty($newLoans)) {
                      //          $controlVariableFile =  $platformData['controlVariableFile'];
                                file_put_contents($baseDirectory . "loanIds.json", json_encode(($newLoans)));
                                $newFlowState = WIN_QUEUE_STATUS_DATA_EXTRACTED;
                            } 
                            else {
                                $newFlowState = WIN_QUEUE_STATUS_AMORTIZATION_TABLES_DOWNLOADED;
                            }
                        }
                        else {
                            echo "ERROR ENCOUNTERED\n"; 
                        }
                    }

                    $this->Queue->id = $queueIdKey;
                    $this->Queue->save(array('queue_status' => $newFlowState,
                        'queue_info' => json_encode($this->queueInfo[$queueIdKey]),
                            ), $validate = true
                    );
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
      
$timeStart = time();
        $calculationClassHandle = new UserDataShell();
        $investmentId = NULL;
        $linkedaccountId = $platformData['linkedaccountId'];
        $userReference = $platformData['userReference']; 
        $controlVariableFile =  $platformData['controlVariableFile'];                   // Control variables as supplied by P2P
        $controlVariableActiveInvestments = $platformData['activeInvestments'];         // Our control variable
$tempMeasurements = array(
        'winTechActiveStateCounting' => 0,
        'winTechNewLoanCounting' => 0
);  

        if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) {
            $platformData['workingNewLoans'] = array_values($platformData['newLoans']);
            $expiredLoanValues = array_values(array_keys($platformData['parsingResultExpiredInvestments']));
         
            // merge the two arrays
            $countArray1 = count($platformData['workingNewLoans']);
//echo "Totalcount1 = " . count($platformData['workingNewLoans']);            
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
echo "dateKey = $dateKey \n";

if ($dateKey == "2  015-11-05"){ 
    echo "Exiting when date = " . $dateKey . "\n";
    $timeStop = time();
    echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) . "\n"; 

    echo "FINISHED_ACCOUNT = $FINISHED_ACCOUNT   \n";
    echo "STARTED_NEW_ACCOUNTS = $STARTED_NEW_ACCOUNTS \n"; 
    
$myArray = array ('finished' => $FINISHED_ACCOUNT,
            'finished_list' => $FINISHED_ACCOUNT_LIST,
            'countFinishedList' => count($FINISHED_ACCOUNT_LIST),
            'started_new_accounts'  => $STARTED_NEW_ACCOUNTS,
            'started_new_accounts_list' => $STARTED_NEW_ACCOUNTS_LIST,
            'countNewAccountList' => count($STARTED_NEW_ACCOUNTS_LIST),
            'finished_duplicates_list' => $FINISHED_DUPLICATES_LIST,
            'countFinishedDuplicatesList' => count($FINISHED_DUPLICATES_LIST),
            'measurements' => $tempMeasurements,
            'workingNewLoans' => $platformData['workingNewLoans'], 
            'countWorkingNewLoans' => count($platformData['workingNewLoans']),
            'errorDeletingWorkingNewloans' => $errorDeletingWorkingNewloans,
        );
    file_put_contents("/home/antoine/controlData6.json", json_encode(($myArray)));
    exit;
}





// Lets allocate a userinvestmentdata for this calculation period (normally daily)
            // reset the relevant variables before going to next date
            unset($database);              // Start with a clean shadow database

            $filterConditions = array("linkedaccount_id" => $linkedaccountId);
            $database = $calculationClassHandle->getLatestTotals("Userinvestmentdata", $filterConditions);

            $this->Userinvestmentdata->create();
            $database['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
            $database['Userinvestmentdata']['userinvestmentdata_investorIdentity'] = $userReference;
            $database['Userinvestmentdata']['date'] = $dateKey;
            
            $database['configParms']['outstandingPrincipalRoundingParm'] = '0.00001';      // configuration parameter 
            $database['measurements'] = $tempMeasurements;
            
            foreach ($dates as $keyDateTransaction => $dateTransaction) {           // read all *individual* transactions of a loanId per day

//if ($keyDateTransaction == "29016-01") {   
 //   echo "Reached LoanId $keyDateTransaction, so quitting\n ";
 //   continue;
 //   echo "Exiting\n";
  //  $timeStop = time();
 //   echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart); 
//   exit;
//} 
/*
                if (isset($dateTransaction[0]['conceptChars'])) {                       // To get rid of PHP warning
                    if ($dateTransaction[0]['conceptChars'] == "+* ") {
                    // If the loanId does not correspond to a brand new loan, then it is a extra participation in an 
                    // exiting loan, so new amortizationtable must be collected
                        $platformData['newLoans'][] =  $dateTransaction[0]['investment_loanId'];  // or the id for the loan slice....
                        $database['investment']['investment_sliceIdentifier'] = "XXXY55";  //TO BE DECIDED WHERE THIS ID COMES FROM   
                    }
                } 
 */            
                $database['investment']['investment_new'] = NO;
                echo "\nkeyDateTransaction = $keyDateTransaction \n";
                
                // special procedure for platform related transactions, i.e. when we don't have a real loanId
                $keyDateTransactionNames = explode("_", $keyDateTransaction);
                if ($keyDateTransactionNames[0] == "global") {               // --------> ANALYZING GLOBAL, PLATFORM SPECIFIC DATA
                    // cycle through all individual fields of the transaction record
                    foreach ($dateTransaction[0] as $transactionDataKey => $transaction) {  // cycle through all individual fields of the transaction record
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
                                $result = $calculationClassHandle->$functionToCall($dateTransaction[0], $database);

                                // update the field userinvestmentdata_cashInPlatform   
                                $cashflowOperation = $tempResult['cashflowOperation'];
                                if (!empty($cashflowOperation)) {
                                    $database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'] = 
                                        $cashflowOperation($database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'], $result, 16);                          
                                }                                

                                if ($tempResult['charAcc'] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                    $database[$dbTable][$transactionDataKey] = bcadd($database[$dbTable][$transactionDataKey], $result, 16);
                                } 
                                else {
                                    $database[$dbTable][$transactionDataKey] = $result;
                                }
                            } 
                            else {
                                $database[$dbTable][$transactionDataKey] = $transaction;
                            }
                        }
                    }
                    continue;
                }
 
                echo "---------> ANALYZING NEXT LOAN ------- with data = $keyDateTransaction\n";

                if (isset($platformData['parsingResultInvestments'][$keyDateTransaction])) {
                    echo "THIS IS AN ACTIVE LOAN\n";
                    $investmentListToCheck = $platformData['parsingResultInvestments'][$keyDateTransaction][0];
                    $loanStatus = WIN_LOANSTATUS_ACTIVE;
                }

                if (isset($platformData['parsingResultExpiredInvestments'][$keyDateTransaction])) {
                    echo "THIS IS AN ALREADY EXPIRED LOAN\n";
                    $investmentListToCheck = $platformData['parsingResultExpiredInvestments'][$keyDateTransaction][0];
                    $loanStatus = WIN_LOANSTATUS_FINISHED;
                }
  
                if (in_array($keyDateTransaction, $platformData['workingNewLoans'])) {          // check if loanId is new
                    $arrayIndex = array_search($keyDateTransaction, $platformData['workingNewLoans']);
                    if ($arrayIndex !== false) {        // Deleting the array from new loans list
                        unset($platformData['workingNewLoans'][$arrayIndex]);
                    }
                    else {          // ONLY FOR TESTING
                        $errorDeletingWorkingNewloans++;
                    }

                    echo "Storing the data of a 'NEW LOAN' in the shadow DB table\n";
                    $database['investment']['investment_myInvestment'] = 0;
                    $database['investment']['investment_secondaryMarketInvestment'] = 0;  
                    $database['investment']['investment_new'] = YES;
//$database['investment']['technicalState'] = WIN_TECH_STATE_ACTIVE;
$database['measurements'][$keyDateTransaction]['decrements'] = 0;
$database['measurements'][$keyDateTransaction]['increments'] = 0; 

$STARTED_NEW_ACCOUNTS = $STARTED_NEW_ACCOUNTS + 1;
$STARTED_NEW_ACCOUNTS_LIST[] = $keyDateTransaction;
 
                    
                    
                    $controlVariableActiveInvestments = $controlVariableActiveInvestments + 1;
 
             //       $platformData['newLoans'][]= $transactionData['investment_loanId'];
                   
             //       if ($transactionData['conceptChars'] == "AM_TABLE") {       // Add loanId so new amortizationtable shall be collected
             //           if ($loanStatus == WIN_LOANSTATUS_ACTIVE) {         // used for currently active loans and for Zombie loans
             //               $database['investment']['markCollectNewAmortizationTable'] = "AM_TABLE";
             //           }
             //       }
                    $database['investment']['investment_sliceIdentifier'] = "ZZXXXX";  //TO BE DECIDED WHERE THIS ID COMES FROM    
                    
                        
                    foreach ($investmentListToCheck as $investmentDataKey => $investmentData) {
                        $tempResult = $this->in_multiarray($investmentDataKey, $this->variablesConfig);

                        if (!empty($tempResult)) {
                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            $database[$dbTable][$investmentDataKey] = $investmentData;
           //               $this->variablesConfig[$investmentDataKey]['state'] = WIN_FLOWDATA_VARIABLE_DONE;   // Mark done
                        }
                    }                  
                } 
                else {  // Already an existing loan
                    $filterConditions = array("investment_loanId" => $keyDateTransaction,
                                                "linkedaccount_id" => $linkedaccountId);
                    $tempInvestmentData = $this->Investment->getData($filterConditions, array("id", 
                        "investment_priceInSecondaryMarket" , "investment_outstandingPrincipal", "investment_totalGrossIncome",
                        "investment_totalLoancost", "investment_totalPlatformCost", "investment_myInvestment", 
                        "investment_secondaryMarketInvestment", "investment_technicalStateTemp" ));
 
                    $investmentId = $tempInvestmentData[0]['Investment']['id'];
                    if (empty($investmentId)) {         // This is a so-called Zombie Loan. It exists in transaction records, but not in the investment list
                                                        // We mark to collect amortization table and hope that the PFP will return amortizationtable data.
                        

echo "THE LOAN WITH ID $keyDateTransaction IS A ZOMBIE LOAN\n";
                            echo "Storing the data of a 'NEW ZOMBIE LOAN' in the shadow DB table\n";
                        $loanStatus = WIN_LOANSTATUS_ACTIVE;        // So amortization data is collected
                        $database['investment']['investment_new'] = YES;        // SO we store it as new loan in the database
                        $database['investment']['investment_myInvestment'] = 0;
                        $database['investment']['investment_secondaryMarketInvestment'] = 0;  
                        $database['investment']['investment_sliceIdentifier'] = "ZZAAXXX";  //TO BE DECIDED WHERE THIS ID COMES FROM  
             //           $database['investment']['markCollectNewAmortizationTable'] = "AM_TABLE";        // Is this needed???? ALREADY DONE IN LINE 510
                        $database['investment']['investment_technicalData'] = WIN_TECH_DATA_ZOMBIE_LOAN;                        
                    }
                    else {  // A normal regular loan, which is already defined in our database
                    // Copy the information to the shadow database, for processing later on
echo __FUNCTION__ . " " . __LINE__ . " : Reading the set of initial data of an existing loan with investmentId = $investmentId\n";
                        $database['investment']['investment_myInvestment'] = $tempInvestmentData[0]['Investment']['investment_myInvestment'];
                        $database['investment']['investment_secondaryMarketInvestment'] = $tempInvestmentData[0]['Investment']['investment_secondaryMarketInvestment'];
                        $database['investment']['investment_outstandingPrincipal'] = $tempInvestmentData[0]['Investment']['investment_outstandingPrincipal'];
                        $database['investment']['investment_outstandingPrincipalOriginal'] = $tempInvestmentData[0]['Investment']['investment_outstandingPrincipal'];
                        $database['investment']['investment_totalGrossIncome'] = $tempInvestmentData[0]['Investment']['investment_totalGrossIncome'];   
                        $database['investment']['investment_totalLoanCost'] = $tempInvestmentData[0]['Investment']['investment_totalLoanCost'];                        
                        $database['investment']['id'] = $investmentId;
//$database['investment']['technicalState'] = WIN_TECH_STATE_ACTIVE;
                    }
                }

                echo __FILE__ . " " . __LINE__ . "\n"; 
                // load all the transaction data
                foreach ($dateTransaction as $transactionKey => $transactionData) {       // read one by one all transactions of this loanId
echo "====> ANALYZING NEW TRANSACTION transactionKey = $transactionKey transactionData = \n";
                    if (isset($transactionData['conceptChars'])) {
                        if ($transactionData['conceptChars'] == "AM_TABLE") {       // Add loanId so new amortizationtable shall be collected
                            if ($loanStatus == WIN_LOANSTATUS_ACTIVE) {
                                $sliceId = $this->linkNewSlice($investmentId, $database['investment']['investment_sliceIdentifier']);
                                if ($sliceId) {             //NOTE $investmentId does not exist for new loans and zombies so this won't always work
                                    $platformData['newLoans'][$sliceId] = $database['investment']['investment_sliceIdentifier'];
     //                             $database['investment']['markCollectNewAmortizationTable'] = "AM_TABLE";
                                }
                            }
                        }
                    }
print_r($transactionData);
                    foreach ($transactionData as $transactionDataKey => $transaction) {  // read all transaction concepts
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
                            $dbVariableName = $dataInformation[1];
echo "index = " . $tempResult['internalIndex'] . " \n";
                            echo "Execute calculationfunction: $functionToCall\n";
                            if (!empty($functionToCall)) { 
                                $result = $calculationClassHandle->$functionToCall($transactionData, $database);

echo "Result = $result and index = " . $tempResult['internalIndex'] ."\n";

                                if (isset($tempResult['linkedIndex'])) {
                                    echo ">>>>>>>>>>>>>>>> LINKED INDEX\n";
                                    print_r($this->variablesConfig[$tempResult['linkedIndex']]);
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
                                    echo "LINKED-INDEX_787G";
                                    print_r($this->variablesConfig[$tempResult['linkedIndex']]);
                                    $dataInformationInternIndex = explode(".", $tempResult['databaseName']);
                                    $dbTableInternalIndex = $dataInformationInternalIndex[0];
                                    $database[[$dbTableInternalIndex][0]][[$dbTableInternalIndex][1]] = $transaction;
                                }
                            }        
                        }
                    }                           
                }
                
// Now start consolidating of the results, these are to be stored in the investment table? (variable part)          
                $internalVariableToHandle = array(37, 10004);
                foreach ($internalVariableToHandle as $keyItem => $item) {
                    $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                    $functionToCall = $this->variablesConfig[$item]['function'];
                    echo "Calling the function: $functionToCall and dbtable = " . $varName[0] . " and varname =  " . $varName[1].  "\n";
                    print_r($this->variablesConfig[$item]); 
                    $result = $calculationClassHandle->$functionToCall($transactionData, $database);   
                    if ($this->variablesConfig[$item]["charAcc"] == WIN_FLOWDATA_VARIABLE_ACCUMULATIVE) {
                        if (!isset($database[$varName[0]][$varName[1]])) {
                            $database[$dbTable][$transactionDataKey] = 0;
                        }
                        $database[$varName[0]][$varName[1]] = bcadd($database[$varName[0]][$varName[1]], $result, 16);
                    } else {
                        $database[$varName[0]][$varName[1]] = $result;
                    }                  
                }                 
  
                echo "printing relevant part of database\n";
                print_r($database['investment']);
                print_r($database['payment']);
                
                $database['investment']['linkedaccount_id'] = $linkedaccountId;
                if ($database['investment']['investment_new'] == YES) {   
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

                $internalVariablesToHandle = array(10001, 10002, 
                                                    10006, 10007, 10008,
                                                    10009, 10010, 10011, 
                                                    10012, 10013);      
                foreach ($internalVariablesToHandle as $keyItem => $item) {
                    $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                    $functionToCall = $this->variablesConfig[$item]['function'];
 //                   print_r($this->variablesConfig[$item]);                       
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
            
            
            
            print_r($database['Userinvestmentdata']);
            print_r($database['globalcashflowdata']);  
            print_r($database['globaltotalsdata']);  

            $tempMeasurements = $database['measurements'];
        }


        $controlVariables['myWallet'] = $database['Userinvestmentdata']['cashInPlatform'];      // Holds the *last* calculated value
        $controlVariables['activeInvestments'] = $database['Userinvestmentdata']['userinvestmentdata_numberActiveInvestments']; // Holds the last calculated value

        $fileString = file_get_contents($controlVariableFile);         // must be a json file

        $externalControlVariables = json_decode($fileString, true);     // Read control variables as supplied by p2p 
        echo "Consolidation Phase 2, checking control variables\n";        
 //       print_r($externalControlVariables);
        $controlVariablesCheck = $calculationClassHandle->consolidatePlatformControlVariables($controlVariables, $externalControlVariables);
        if ($controlVariablesCheck == false) {
            
            
        }
        
echo "FINISHED_ACCOUNT = $FINISHED_ACCOUNT   \n";
echo "STARTED_NEW_ACCOUNTS = $STARTED_NEW_ACCOUNTS \n"; 
$myArray = array ('finished' => $FINISHED_ACCOUNT,
            'finished_list' => $FINISHED_ACCOUNT_LIST,
            'countFinishedList' => count($FINISHED_ACCOUNT_LIST),
            'started_new_accounts'  => $STARTED_NEW_ACCOUNTS,
            'started_new_accounts_list' => $STARTED_NEW_ACCOUNTS_LIST,
            'countNewAccountList' => count($STARTED_NEW_ACCOUNTS_LIST),
            'finished_duplicates_list' => $FINISHED_DUPLICATES_LIST,
            'countFinishedDuplicatesList' => count($FINISHED_DUPLICATES_LIST),
            'measurements' => $tempMeasurements,
            'workingNewLoans' => $platformData['workingNewLoans'], 
            'countWorkingNewLoans' => count($platformData['workingNewLoans']),
            'errorDeletingWorkingNewloans' => $errorDeletingWorkingNewloans,
        );
file_put_contents("/home/antoine/controlData6.json", json_encode(($myArray)));
       

        $calculationClassHandle->consolidatePlatformData($database);
        // remove duplicates from the 'newLoans'AND remove all loans whose loanId/slice are in expiredLoans
$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) ."\n";

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
    
    
    
    
}
