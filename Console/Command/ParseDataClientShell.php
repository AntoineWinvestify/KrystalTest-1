<?php
/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2018, http://www.winvestify.com                   	  	|
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
 * @version 0.4
 * @date 2018-02-15
 * @package
 *
 * This client deals with parsing of the files that have been downloaded
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
 * 2018-01-05		version 0.3
 * deal with the flows for reserved funds
 * introduction of state management
 * 
 * 2018-02-15		version 0.4
 * Code for copying userinvestmentdata records in case the transactions don't cover all the natural days (after
 * an account linking)
 * new method, repaymentReceived added. This method updates the amortization tables of a loan
 * 
 * 
 * 
 * 
 * 
 *
 * PENDING:
 * -
 * 
 */
App::import('Shell', 'GearmanClient');
App::import('Shell', 'UserData');
class ParseDataClientShell extends GearmanClientShell {

    public $uses = array('Queue2', 'Paymenttotal', 'Investment', 'Investmentslice', 'Globaltotalsdata', 'Userinvestmentdata', 'Amortizationtable');
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
        
        echo "Deleting Dashboardoverview table\n";
        $this->AmortizationTable = ClassRegistry::init('Dashboardoverviewdata');
        $this->AmortizationTable->deleteAll(array('Dashboardoverviewdata.id >' => 0), false);        
        
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
                    $userReference = $job['Queue2']['queue2_userReference'];
                    $queueId = $job['Queue2']['id'];
                    $this->queueInfo[$job['Queue2']['id']] = json_decode($job['Queue2']['queue2_info'], true);
                    //print_r($this->queueInfo);
                   
                    $this->date = $this->queueInfo[$job['Queue2']['id']]['date'];                // End date of collection period
                    $this->startDate = $this->queueInfo[$job['Queue2']['id']]['startDate'];      // Start date of collection period

                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
                    $i = 0;

                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        $dirs = new Folder($subDirectory);
                        $allFiles = $dirs->findRecursive();
                        if (!in_array($linkedAccountId, $this->queueInfo[$job['Queue2']['id']]['companiesInFlow'])) {
                            continue;
                        }
                        $tempPfpName = explode("/", $allFiles[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        $this->userLinkaccountIds[$job['Queue2']['id']][$i] = $linkedAccountId;
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
                            'controlVariableFile' => $controlVariableFile[0],
                            'activeInvestments' => count($listOfActiveInvestments),
                            'listOfCurrentActiveInvestments' => $listOfActiveInvestments,
                            'listOfReservedInvestments' => $listOfReservedInvestments,
                            'userReference' => $job['Queue2']['queue2_userReference'],
                            'files' => $files,
                            'controlVariablefile' => $controlVariableFile,
                            'finishDate' => $this->queueInfo[$queueId]['date'],
                            'startDate' => $this->queueInfo[$queueId]['startDate'][$linkedAccountId],
                            'actionOrigin' => $this->queueInfo[$job['Queue2']['id']]['originExecution'],
                        );
                    }
                    debug($params);

                    $this->GearmanClient->addTask($workerFunction, json_encode($params), null, $job['Queue2']['id'] . ".-;" .
                            $workerFunction . ".-;" . $job['Queue2']['queue2_userReference']);
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
                        $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . $this->queueInfo[$job['Queue2']['id']]['date'] . DS;
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
                    }           
                    $this->verifyStatus($newFlowState, 
                            "Data successfully downloaded", 
                            WIN_QUEUE_STATUS_GLOBAL_DATA_DOWNLOADED, 
                            WIN_QUEUE_STATUS_UNRECOVERED_ERROR_ENCOUNTERED);
/*               
                    $this->Queue->id = $queueIdKey;
                    $this->Queue->save(array('queue2_status' => $newFlowState,
                        'queue2_info' => json_encode($this->queueInfo[$queueIdKey]),
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
                sleep(WIN_SLEEP_DURATION);                                      // Just wait a short time and check again
            }
            if ($inActivityCounter > MAX_INACTIVITY) {                          // system has dealt with ALL request for tonight, so exit "forever"
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
     * An 'userinvestmentdatas' table is generated for each day, i.e. even if no activity exists. This is only
     * done for the regular backups, not for the initial account linking procedure
     * 
     *  @param  $array          Array which holds the data (per PFP) as received from the Worker
     *  @return boolean true
     *                  false
     *
     * the principal data is available in various sub-arrays which are to be written
     * (before checking if it is a duplicate) to the corresponding database table.
     *     platform - (1-n)loanId - (1-n) concepts
     */
    public function mapData(&$platformData) {
        
        $timeStart = time();
        $calculationClassHandle = new UserDataShell();
        $investmentId = null;
        $linkedaccountId = $platformData['linkedaccountId'];
        $userReference = $platformData['userReference'];
        $startDate = $platformData['startDate'];
        $finishDate = $platformData['finishDate'];
        $amortizationTablesNotNeeded = array(); 
        $collectTablesIndex = 0;
 //       $returnData[$linkedAccountKey]['parsingResultControlVariables'];
            

        $controlVariableActiveInvestments = $platformData['activeInvestments']; // Our control variable

        if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) {
            $platformData['workingNewLoans'] = array_values($platformData['newLoans']);
            $expiredLoanValues = array_values(array_keys($platformData['parsingResultExpiredInvestments']));
 
            $precision = $platformData['dashboard2ConfigurationParameters']['outstandingPrincipalRoundingParm'];
            if (empty($precision)) {
                $precision = '0.00001';                                         // Default precision
            }
            $countArray1 = count($platformData['workingNewLoans']);
            
            foreach ($expiredLoanValues as $key => $value) {
                $platformData['workingNewLoans'][$countArray1 + $key] = $value;   
            }
        }
        else {
            $platformData['workingNewLoans'] = $platformData['newLoans'];
        } 
      
        $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');  // A new table exists for EACH new calculation interval
        $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
        $this->Payment = ClassRegistry::init('Payment');

 // Lets allocate a userinvestmentdata for this calculation period (normally daily)
            // reset the relevant variables before going to next date
        $database = array();                                                    // Start with a clean shadow database
        $database['investment']['investment_totalLoanCost'] = "";
        $database['investment']['investment_paidInstalments'] = "";
        $database['payment']['payment_latePaymentFeeIncome'] = "";
        $database['payment']['payment_capitalRepayment'] = "";
        $database['payment']['payment_principalAndInterestPayment'] = "";
        $database['payment']['payment_partialPrincipalRepayment'] = "";
        $database['payment']['payment_principalBuyback'] = ""; 
        $database['payment']['payment_outstandingPrincipal'] = ""; 
        $database['payment']['payment_receivedRepayment'] = "";
        $database['payment']['payment_totalGrossIncome'] = "";
        $database['payment']['payment_interestGrossIncome'] = "";
        $database['payment']['payment_interestIncomeBuyback'] = "";
        $database['payment']['payment_loanRecoveries'] = "";                                               
        $database['payment']['payment_loanIncentivesAndBonus'] = "";                
        $database['payment']['payment_loanCompensation'] = "";                    
        $database['payment']['payment_regularGrossInterestIncome'] = "";                    
        $database['payment']['payment_delayedInterestIncome'] = "";                
        $database['payment']['payment_delayedInterestIncomeBuyback'] = "";                    
        $database['payment']['payment_currencyFluctuationPositive'] = ""; 
        $database['payment']['payment_currencyFluctuationNegative'] = "";     
        $database['payment']['payment_myInvestment'] = "";
        $database['payment']['payment_secondaryMarketInvestment'] = "";
        $database['payment']['payment_incomeSecondaryMarket'] = "";
        $database['payment']['payment_costSecondaryMarket'] = "";
        $database['payment']['payment_commissionPaid'] = "";                
        $database['payment']['payment_currencyExchangeFee'] = "";        
        $database['payment']['payment_currencyExchangeTransaction'] = "";    
        $database['payment']['payment_incomeWithholdingTax'] = ""; 
        $database['globaltotalsdata']['globaltotalsdata_secondaryMarketInvestmentPerDay'] = "";
        $database['globaltotalsdata']['globaltotalsdata_myInvestmentPerDay'] = "";
        $database['globaltotalsdata']['globaltotalsdata_regularGrossInterestIncomePerDay'] = "";           
        $database['globaltotalsdata']['globaltotalsdata_interestIncomeBuybackPerDay '] = "";           
        $database['globaltotalsdata']['globaltotalsdata_principalBuybackPerDay'] = "";
        $database['globaltotalsdata']['globaltotalsdata_capitalRepaymentPerDay'] = "";              
        $database['globaltotalsdata']['globaltotalsdata_costSecondaryMarketPerDay'] = "";       


       
        

        // Copy the last userinvestmentdata for the "missing" dates before we start analyzing transaction records 
        if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_REGULAR_UPDATE) { 
            $dateKeys = array_keys($platformData['parsingResultTransactions']);
            $firstnewTransactionDate = $dateKeys[0];

            $filterConditions = array("linkedaccount_id" => $linkedaccountId);
            $tempDatabase = $this->Userinvestmentdata->find('first', array(
                                             'conditions'   => $filterConditions,
                                             'recursive'    => -1,
                                             'order'        => 'Userinvestmentdata.date'
                                            )); 
            unset($tempDatabase['Userinvestmentdata']['id']);
            $lastSavedTransaction =  $tempDatabase['Userinvestmentdata']['date'];         
            $date2 = new DateTime($lastSavedTransaction);  
            $date2->modify('+1 day');
            $nextTransactiondateToSave = $date2->format('Y-m-d');       

        // Copy the userinvestmentdata for all missing days BEFORE these new transactions
            if ($nextTransactiondateToSave < $firstnewTransactionDate) {         
                while ($nextTransactiondateToSave <> $firstnewTransactionDate) {
                    $this->Userinvestmentdata->create();
                    $tempDatabase['Userinvestmentdata']['date'] = $nextTransactiondateToSave; 
                    $this->Userinvestmentdata->save($tempDatabase, $validate = true); 
                    $date = new DateTime($nextTransactiondateToSave);  
                    $date->modify('+1 day');
                    $nextTransactiondateToSave = $date->format('Y-m-d');                    
                }  
            }  
        }

        unset($tempDatabase);
       
        foreach ($platformData['parsingResultTransactions'] as $dateKey => $dates) {            // these are all the transactions, PER day
echo __FUNCTION__ .  " " . __LINE__ . "\ndateKey = $dateKey \n";

            // Copy the last userinvestmentdata for any missing dates in the transaction records sequence
            if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_REGULAR_UPDATE && !empty($oldDateKey)) {
                echo __FUNCTION__ . " " . __LINE__ . " actualDate = $actualDate \n";

                $date1 = new DateTime($oldDateKey);  
                $date1->modify('+1 day');
                $actualDate = $date1->format('Y-m-d'); 

                if ($actualDate <> $dateKey) {
                    while ($actualDate <> $dateKey) {
                        $filterConditions = array("linkedaccount_id" => $linkedaccountId);
                        if (empty($tempDatabase)) {
                            $tempDatabase = $this->getLatestTotals("Userinvestmentdata", $filterConditions); 
                            $tempDatabase['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
                        }

                        $this->Userinvestmentdata->create();
                        $tempDatabase['Userinvestmentdata']['date'] = $actualDate; 
                        $this->Userinvestmentdata->save($tempDatabase, $validate = true); 
                        $tempActualDate = $actualDate;
                        $date = new DateTime($tempActualDate);  
                        $date->modify('+1 day');
                        $actualDate = $date->format('Y-m-d');                    
                    }   
                }  
            }
            
            $oldDateKey = $dateKey;               
        
            unset ($investmentListToCheck);
            unset ($loanStatus);
            $filterConditions = array("linkedaccount_id" => $linkedaccountId);
            $database = $calculationClassHandle->getLatestTotals("Userinvestmentdata", $filterConditions);

            $this->Userinvestmentdata->create();
            $database['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
            $database['Userinvestmentdata']['userinvestmentdata_investorIdentity'] = $userReference;
            $database['Userinvestmentdata']['date'] = $dateKey; 
            $database['configParms']['outstandingPrincipalRoundingParm'] = $precision;          // configuration parameter 
    
            foreach ($dates as $keyDateTransaction => $dateTransaction) {                       // read all *individual* transactions of a loanId per day           
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
                        if ($transactionDataKey == "internalName") {                        // 'dirty trick' to keep it simple
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
                    $loanStatus = WIN_LOANSTATUS_ACTIVE;            // status could also be WIN_LOANSTATUS_WAITINGTOBEFORMALIZED
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
                        }
                    }
                    
                    
                    switch($database['investment']['investment_statusOfLoan']) {
                        case WIN_LOANSTATUS_WAITINGTOBEFORMALIZED:
                        case WIN_LOANSTATUS_ACTIVE:
                        case WIN_LOANSTATUS_FINISHED:    
                            $database['investment']['investment_amortizationTableAvailable'] = WIN_AMORTIZATIONTABLES_NOT_AVAILABLE;
                            $database['investment']['investment_technicalStateTemp'] = "INITIAL";                            
                        break;
                    }  
                } 
                else {  // Not a new loan, so a loan which (should) exist(s) in our database, but can be in any state
                    $filterConditions = array("investment_loanId" => $dateTransaction[0]['investment_loanId'] ,
                                                "linkedaccount_id" => $linkedaccountId);
                    $tempInvestmentData = $this->Investment->getData($filterConditions, array("id", 
                        "investment_priceInSecondaryMarket" , "investment_outstandingPrincipal", "investment_totalGrossIncome",
                        "investment_totalLoancost", "investment_totalPlatformCost", "investment_myInvestment", "investment_technicalStateTemp",
                        "investment_secondaryMarketInvestment", "investment_paidInstalments", "investment_statusOfLoan", 
                        "investment_sliceIdentifier", "investment_amortizationTableAvailable"));
 
                    $investmentId = $tempInvestmentData[0]['Investment']['id'];
                    if (empty($investmentId)) {     // This is a so-called Zombie Loan. It exists in transaction records, but not in the investment list
                                                    // We mark to collect amortization table and hope that the PFP will return amortizationtable data.       

echo "THE LOAN WITH ID " . $dateTransaction[0]['investment_loanId']  . " IS A ZOMBIE LOAN\n";
echo "Storing the data of a 'NEW ZOMBIE LOAN' in the shadow DB table and setting its state to WIN_LOANSTATUS_ACTIVE\n";
                        $loanStatus = WIN_LOANSTATUS_ACTIVE;                                                                    // So amortization data is collected
                        $database['investment']['investment_myInvestment'] = 0;
                        $database['investment']['investment_secondaryMarketInvestment'] = 0;  
                        $database['investment']['investment_sliceIdentifier'] = $dateTransaction[0]['investment_loanId'];       // TO BE DECIDED WHERE THIS ID COMES FROM  
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
                        $database['investment']['investment_amortizationTableAvailable'] = $tempInvestmentData[0]['Investment']['investment_amortizationTableAvailable'];
                        $database['investment']['id'] = $investmentId;
                    }
                }

                // load all the transaction data
                foreach ($dateTransaction as $transactionKey => $transactionData) {                 // read one by one all transaction data of this loanId
echo "====> ANALYZING NEW TRANSACTION transactionKey = $transactionKey transactionData = \n";
print_r($database);
                    if (isset($transactionData['conceptChars'])) {
                        $conceptChars = explode(",", $transactionData['conceptChars']);
                        
                        foreach ($conceptChars as $itemKey => $item) {
                            $conceptChars[$itemKey] = trim($item);
                        }
                        
                        if (in_array("PRE-ACTIVE", $conceptChars)) {                         
                            $database['investment']['investment_statusOfLoan'] = WIN_LOANSTATUS_WAITINGTOBEFORMALIZED;
                        }
                        
                        if (in_array("AM_TABLE", $conceptChars)) {                                  // New, or extra investment, so new amortizationtable shall be collected
                            if ($loanStatus == WIN_LOANSTATUS_ACTIVE) {
//                                unset ($sliceIdentifier);

                                $sliceIdentifier = $this->getSliceIdentifier($transactionData, $database);
                                // Check if sliceIdentifier has already been defined in $slicesAmortizationTablesToCollect,
                                // if not then reate a new array with the data available so far, sliceIdentifier and loanId
                                $isNewTable = YES;
                                foreach ($slicesAmortizationTablesToCollect as $tableCollectKey => $tableToCollect) {
                                    if ($tableToCollect['sliceIdentifier'] == $sliceIdentifier) {
                                        $isNewTable = NO;
                                        break;
                                    }                                    
                                }
                                if ($isNewTable == YES) {     
                                    $collectTablesIndex++;
                                    $slicesAmortizationTablesToCollect[$collectTablesIndex]['loanId'] = $transactionData['investment_loanId'];    // For later processing
                                    $slicesAmortizationTablesToCollect[$collectTablesIndex]['sliceIdentifier'] = $sliceIdentifier;
                                }
                            }
                        }
                        
                        if ((in_array("REMOVE_AM_TABLE", $conceptChars))) {
                            $sliceIdentifier = $this->getSliceIdentifier($transactionData, $database);
                            foreach ($slicesAmortizationTablesToCollect as $tableCollectKey => $tableToCollect) {
                                if ($tableToCollect['sliceIdentifier'] == $sliceIdentifier) {
                                    if ($tableToCollect['loanId'] == $transactionData['investment_loanId']) {
                                        unset ($slicesAmortizationTablesToCollect[$tableCollectKey]);  
                                    }
                                }
                            }                        
                        }  
 
                        if ((in_array("READ_INVESTMENT_DATA", $conceptChars))) {    
/*                            
echo __FILE__ . " " . __LINE__ . " READ_INVESTMENT_DATA label found\n"; 
print_r($transactionData['investment_loanId']);
print_r($platformData['parsingResultInvestments'][$transactionData['investment_loanId']]);
echo __FILE__ . " " . __LINE__ . "\n"; 
// Define clearly WHICH fields to reread
*/
      //                      foreach ($platformData['parsingResultInvestments'][$transactionData['investment_loanId'][0]] as $investmentDatumKey => $investmentDatum) {
        //                        $database['investment'][$investmentDatumKey] = $investmentDatum;   
          //                  }
//print_r($database['investment']);
//echo __FILE__ . " " . __LINE__ . " new version of Investment data printed\n";
                        }
                        
                    }
                    
                    foreach ($transactionData as $transactionDataKey => $transaction) {     // read all transaction concepts
                        if ($transactionDataKey == "internalName") {                        // 'dirty trick' to keep it simple
                            $transactionDataKey = $transaction;
                        }
                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);

                        echo __FILE__ . " " . __LINE__ . "\n";
                        if (!empty($tempResult)) {
                            unset($result);
                            
                            $functionToCall = $tempResult['function'];

                            echo __FILE__ . " " . __LINE__ . " Function to call = $functionToCall, transactionDataKey = $transactionDataKey\n";
                            $dataInformation = explode(".", $tempResult['databaseName']);
                            $dbTable = $dataInformation[0];
                            $dbVariableName = $dataInformation[1];

                            echo "Execute calculationfunction: $functionToCall\n";
                            if (!empty($functionToCall)) { 
                                $result = $calculationClassHandle->$functionToCall($transactionData, $database);

echo "Result = $result and index = " . $tempResult['internalIndex'] ."\n";
                                if (isset($tempResult['linkedIndex'])) {
                                    echo ">>>>>>>>>>>>>>>> LINKED INDEX\n";
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
                                    echo "LINKED-INDEX";
                                    $dataInformationInternIndex = explode(".", $tempResult['databaseName']);
                                    $dbTableInternalIndex = $dataInformationInternalIndex[0];
                                    $database[[$dbTableInternalIndex][0]][[$dbTableInternalIndex][1]] = $transaction;
                                }
                            }        
                        }
                    }       
                } 


                
// Now start consolidation of the results on investment level and per day                
                $internalVariableToHandle = array(10014, 10015, 37, 10004, 20065);

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
                echo  __FUNCTION__ . " " . __LINE__ . "printing relevant part of database\n";
              
                $database['investment']['linkedaccount_id'] = $linkedaccountId;

                if (isset($database['investment']['investment_amortizationTableAvailable'])) {     // Write payment data in amortization table
                    if ($database['investment']['investment_amortizationTableAvailable'] == WIN_AMORTIZATIONTABLES_AVAILABLE) {
                        if (in_array("REPAYMENT", $conceptChars)) {        
                            $this->repaymentReceived($transactionData, $database); 
                        }                                
                    }
                    else {
                        // Store the information so it can be processed in flow 3B
                    }
                } 
    
                if ($database['investment']['investment_statusOfLoan'] == WIN_LOANSTATUS_FINISHED) {
                    $amortizationTablesNotNeeded[] = $database['investment']['investment_loanId'];
                }
                
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
  

                foreach ($slicesAmortizationTablesToCollect as $tableCollectKey => $tableToCollect) {           // Add: investmentId
                    if (empty($tableToCollect['investmentId'])) {
                        $slicesAmortizationTablesToCollect[$tableCollectKey]['investmentId'] = $investmentId; 
                    }
                }                
                
                
    
                $internalVariablesToHandle = array(10001,
                                                    10006, 10007, 10008,
                                                    10009, 10010, 10011, 
                                                    10012, 10013, 10016,
                                                    10017, 10018, 10019,
                                                    10020, 10021, 10022);      
                foreach ($internalVariablesToHandle as $keyItem => $item) {
                    $varName = explode(".", $this->variablesConfig[$item]['databaseName']);
                    $functionToCall = $this->variablesConfig[$item]['function'];                      
                    $result = $calculationClassHandle->$functionToCall($transactionData, $database);                
echo __FUNCTION__ . " " . __LINE__ . " Var = $item, Function to Call = $functionToCall and Executing Calc. specific variables=>: orig. amount = " . $database[$varName[0]][$varName[1]] ." and new result = $result". "\n";

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
//               unset($slicesAmortizationTablesToCollect);

                $database['investment']['investment_totalLoanCost'] = "";
                $database['investment']['investment_paidInstalments'] = "";
                
                $database['payment']['payment_latePaymentFeeIncome'] = "";
                $database['payment']['payment_capitalRepayment'] = "";
                $database['payment']['payment_principalAndInterestPayment'] = "";
                $database['payment']['payment_partialPrincipalRepayment'] = "";
                $database['payment']['payment_principalBuyback'] = ""; 
                $database['payment']['payment_outstandingPrincipal'] = ""; 
                $database['payment']['payment_receivedRepayment'] = "";
                $database['payment']['payment_totalGrossIncome'] = "";
                $database['payment']['payment_interestGrossIncome'] = "";
                $database['payment']['payment_interestIncomeBuyback'] = "";
                $database['payment']['payment_loanRecoveries'] = "";                                               
                $database['payment']['payment_loanIncentivesAndBonus'] = "";                
                $database['payment']['payment_loanCompensation'] = "";                    
                $database['payment']['payment_regularGrossInterestIncome'] = "";                    
                $database['payment']['payment_delayedInterestIncome'] = "";                
                $database['payment']['payment_delayedInterestIncomeBuyback'] = "";                    
                $database['payment']['payment_currencyFluctuationPositive'] = ""; 
                $database['payment']['payment_currencyFluctuationNegative'] = "";     
                $database['payment']['payment_myInvestment'] = "";
                $database['payment']['payment_secondaryMarketInvestment'] = "";
                $database['payment']['payment_incomeSecondaryMarket'] = "";
                $database['payment']['payment_costSecondaryMarket'] = "";
                $database['payment']['payment_commissionPaid'] = "";                
                $database['payment']['payment_currencyExchangeFee'] = "";        
                $database['payment']['payment_currencyExchangeTransaction'] = "";    
                $database['payment']['payment_incomeWithholdingTax'] = ""; 
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
            echo "Starting to consolidate the platform data, using the control variables, calculating each variable\n";
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
                $database['globalcashflowdata']['linkedaccount_id'] = $linkedaccountId;
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
            
            $controlVariables['outstandingPrincipal'] = $database['Userinvestmentdata']['userinvestmentdata_outstandingPrincipal'];  // Holds the *last* calculated value so far
            $controlVariables['myWallet'] = $database['Userinvestmentdata']['userinvestmentdata_cashInPlatform'];      // Holds the *last* calculated valueso far
            $controlVariables['activeInvestments'] = $activeInvestments; // Holds the last calculated valueso far

            print_r($database['Userinvestmentdata']);
            
            unset($database['Userinvestmentdata']);
            unset($database['globalcashflowdata']);
            unset($database['globaltotalsdata']);
            $database['globaltotalsdata']['globaltotalsdata_secondaryMarketInvestmentPerDay'] = "";
            $database['globaltotalsdata']['globaltotalsdata_myInvestmentPerDay'] = "";
            $database['globaltotalsdata']['globaltotalsdata_regularGrossInterestIncomePerDay'] = "";           
            $database['globaltotalsdata']['globaltotalsdata_interestIncomeBuybackPerDay '] = "";           
            $database['globaltotalsdata']['globaltotalsdata_principalBuybackPerDay'] = "";
            $database['globaltotalsdata']['globaltotalsdata_capitalRepaymentPerDay'] = "";              
            $database['globaltotalsdata']['globaltotalsdata_costSecondaryMarketPerDay'] = "";  
            
           


            $tempUserInvestmentDataItem = array('id' => $userInvestmentDataId,
                                                'userinvestmentdata_numberActiveInvestments' => $activeInvestments);
            $this->Userinvestmentdata->save($tempUserInvestmentDataItem, $validate = true);
        }
       

        
// All transactions have been analyzed. So consolidate the data of the total platform.
// Define which amortization tables shall be collected but remove the unnecessary ones 
        foreach ($slicesAmortizationTablesToCollect as $tableCollectKey => $tableToCollect) {
            $item = array_search($tableToCollect['loanId'], $amortizationTablesNotNeeded);
            if ($item !== false) {           
                unset ($slicesAmortizationTablesToCollect[$tableCollectKey]);  
            }
        } 

        foreach ($slicesAmortizationTablesToCollect as $tableToCollect) {
            $loanSliceId = $this->linkNewSlice($tableToCollect['investmentId'], $tableToCollect['sliceIdentifier']);
            $platformData['amortizationTablesOfNewLoans'][$loanSliceId] = $tableToCollect['sliceIdentifier']; 
        }

        
        
    
// Deal with the control variables     
        echo __FILE__ . " " . __LINE__ . " Consolidation Phase 2, checking control variables\n";        
     
        $controlVariablesCheck = $calculationClassHandle->consolidatePlatformControlVariables($controlVariables, 
                                                    $platformData['parsingResultControlVariables']);
        if ($controlVariablesCheck > 0) { // mismatch detected
            echo "DOES NOT PASS CONTROL VARIABLES CHECK \n";
            // STILL TO FINISH
            $errorData['line'] = __LINE__; 
            $errorData['file'] = __FILE__;
            $errorData['urlsequenceUrl'] = "";
            $errorData['subtypeErrorId'];                                       // It is the subtype of the error
            $errorData['typeOfError'];                                          // It is the type of error or the summary of the detailed information of the error
            $errorData['detailedErrorInformation'];                             // It is the detailed information of the error
            $errorData['typeErrorId'];                                          // It is the principal id of the error           
            $this->saveGearmanError($errorData);
        }
       
        $calculationClassHandle->consolidatePlatformData($database);
   
        
        unset ($tempDatabase);
        // Make sure that we have an entry in Userinvestmentdata for yesterday as required for yield calculation     
        if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_ACCOUNT_LINKING) { 
            $date = new DateTime(date("Y-m-d"));                                    // = today
            $date->modify('-1 day');
            $lastDateToCalculate = $date->format('Y-m-d');            
            if ($dateKey <> $lastDateToCalculate) {               
                $filterConditions = array("linkedaccount_id" => $linkedaccountId);
                $tempDatabase = $this->getLatestTotals("Userinvestmentdata", $filterConditions);   
  
                $this->Userinvestmentdata->create();
                $tempDatabase['Userinvestmentdata']['date'] = $lastDateToCalculate;    
                $tempDatabase['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
                $this->Userinvestmentdata->save($tempDatabase, $validate = true);
            }            
        }
        
        
        // Copy the userinvestmentdata for all missing days
        if ($platformData['actionOrigin'] == WIN_ACTION_ORIGIN_REGULAR_UPDATE) {
            $date = new DateTime($dateKey);  
            $date->modify('+1 day');
            $actualDate = $date->format('Y-m-d');

            while ($actualDate <= $lastDateToCalculate) {               
                if (empty($tempDatabase)) {                   
                    $filterConditions = array("linkedaccount_id" => $linkedaccountId);
                    $tempDatabase = $this->getLatestTotals("Userinvestmentdata", $filterConditions); 
                    unset($tempDatabase['Userinvestmentdata']['id']);
                }
                $this->Userinvestmentdata->create();
                $tempDatabase['Userinvestmentdata']['date'] = $actualDate;
                $tempDatabase['Userinvestmentdata']['linkedaccount_id'] = $linkedaccountId;
                $this->Userinvestmentdata->save($tempDatabase, $validate = true); 
                $tempActualDate = $actualDate;
                $date = new DateTime($tempActualDate);  
                $date->modify('+1 day');
                $actualDate = $date->format('Y-m-d');                  
            }  
        }
            
        
        
        
        
        
        
        
        
        
$timeStop = time();
echo "NUMBER OF SECONDS EXECUTED = " . ($timeStop - $timeStart) ."\n";
//print_r($platformData['amortizationTablesOfNewLoans']);
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
    
    
    
    /** PARTIAL PAYMENTS ARE NOT YET TAKEN INTO CONSIDERATION
     *  Updates the amortization table of an loan when a repayment is detected.
     *  This method is executed AFTER all the transactions for the loan have been processed by the main flow.
     *  This method is NOT used during the account linking procedure
     * 
     *  @param  array   array with the current transaction data
     *  @param  array   array with all data so far calculated and to be written to DB
     *  @return 
     *                  
     */
    public function repaymentReceived(&$transactionData, &$resultData) {

        if ($resultData['payment']['payment_principalAndInterestPayment'] <> 0) {
            $table['amortizationtable_capitalAndInterestPayment'] = $resultData['payment_principalAndInterestPayment'];
            if ($resultData['payment']['payment_capitalRepayment'] <> 0 ) {
                $table['amortizationtable_capitalRepayment'] = $resultData['payment']['payment_capitalRepayment'];
                $table['amortizationtable_interest'] = bcsub($resultData['payment']['payment_principalAndInterestPayment'], $resultData['payment']['payment_capitalRepayment'] ,16);
            }
            else {
                $table['amortizationtable_capitalRepayment'] = bcsub($resultData['payment']['payment_principalAndInterestPayment'], $resultData['payment']['payment_regularGrossInterestIncome'] ,16);
                $table['amortizationtable_interest'] = $resultData['payment']['payment_regularGrossInterestIncome'];
            } 
        } 
        else {
            $table['amortizationtable_capitalRepayment'] = $resultData['payment']['payment_capitalRepayment'];
            $table['amortizationtable_interest'] = $resultData['payment']['payment_regularGrossInterestIncome'];            
        }
        $table['amortizationtable_paymentDate'] = $transactionData['date'];     
       

        $sliceIdentifier = $this->getSliceIdentifier($transactionData, $resultData);   
        $slices = $this->Investment->getInvestmentSlices ($resultData['investment']['id']);   
     
        foreach ($slices as $slice) {   
            if ($slice['investmentslice_identifier'] == $sliceIdentifier) {                
                $sliceDbreference = $slice['id'];
                break;
            }
        }
      
        $filterConditions = array ('amortizationtable_paymentDate =' => null);
       
        $amortizationTable = $this->Investmentslice->getAmortizationTable($sliceDbreference, $filterConditions);  // for instance all entries of table which are
echo __FUNCTION__ . " " . __LINE__ . " sliceDbreference = $sliceDbreference\n";        
print_r($amortizationTable); 
        $tableDbReference = $amortizationTable[0]['Amortizationtable']['id'];                                                       // not yet paid Normally ask for first one with capitalRepayment = "" or 0;
/*
 * check if date if reception of money == date previsto
 * 
 * 
 * 
 */
        $table['id'] = $tableDbReference;
        echo __FUNCTION__ . " " . __LINE__ . " Updating the amortization table with reference = $tableDbReference\n";       
        if ($this->Investmentslice->updateAmortizationTable($table)) {
            echo __FUNCTION__ . " " . __LINE__ . " Amortization table succesfully updated\n"; 
        }
        else {
            echo __FUNCTION__ . " " . __LINE__ . " Error detected while updating the amortization table with reference $tableDbReference\n"; 
        }
        return;       
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
    
 
    /** 
     *  Determines the sliceIdentifier (.i.e. the amortization table) to be used
     * 
     *  @param  array   array with the current transaction data
     *  @param  array   array with all data so far calculated and to be written to DB
     *  @return string  sliceIndentifier
     *                  
     */ 
    public function getSliceIdentifier(&$transactionData, &$resultData) {
  
        if (isset($transactionData['sliceIdentifier'])) {                       // For P2P's that have individual slices per investment, like FinBee
            $sliceIdentifier = $transactionData['sliceIdentifier'];                                        
        } 
        if (isset($resultData['investment']['investment_sliceIdentifier'])) {     
            $sliceIdentifier = $resultData['investment']['investment_sliceIdentifier'];                                         
        }                                    
        if (empty($sliceIdentifier)) {                                          // Take the default one
            $sliceIdentifier = $transactionData['investment_loanId'];                                
        }
        return $sliceIdentifier;
    }
    
 
}
