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
 * @version
 * @date
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
 *
 */


App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

class ParseDataClientShell extends AppShell {
    public $uses = array('Queue', 'Payment', 'Investment');  
    
    protected $GearmanClient;
    
    protected $variablesConfig = [
        // FLOWDATA_VARIABLE_DONE: The system has copied the variable to the internal queue
        // FLOWDATA_VARIABLE_NOT_DONE: The system has not (yet) copied the variable to the internal queue
        // FLOWDATA_VARIABLE_ACCUMULATIVE: The value is accumulative, read original value, add new value and write
        //                                  for this loan and this readout period
        // FLOWDATA_VARIABLE_NOT_ACCUMULATIVE: not an accumulative value, simply (over)write the value  
        
        2 => [
                "databaseName" => "investment.investment_loanId", 
                "internalName" => "investment_loanId",
                "state" => FLOWDATA_VARIABLE_NOT_DONE,                 
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,
                "function" => ""                      // Only needed in case "complex calculations are required
            ],
        3 => [
                "databaseName" => "investment.investment_debtor", 
                "internalName" => "investment_debtor",
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE          
            ],       
        4 => [
                "databaseName" => "investment.investment_country", 
                "internalName" => "investment_country",  
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""
            ],
        5 => [
                "databaseName" => "investment.investment_loanType", 
                "internalName" => "investment_loanType",            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        6 => [
                "databaseName" => "investment.investment_amortizationMethod", 
                "internalName" => "investment_amortizationMethod",            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        7 => [
                "databaseName" => "investment.investment_market", 
                "internalName" => "investment_market",           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],       
        8 => [
                "databaseName" => "investment.investment_loanOriginator", 
                "internalName" => "investment_loanOriginator",           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""
            ],
        9 => [
                "databaseName" => "investment.investment_buyBackGuarantee", 
                "internalName" => "investment_buyBackGuarantee",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        10 => [
                "databaseName" => "investment.investment_currency", 
                "internalName" => "investment_currency",            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "AACCVV"  
            ],
        11 => [
                "databaseName" => "investment.investment_typeOfInvestment",        
                "internalName" => "investment_typeOfInvestment", 
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],   
        12 => [
                "databaseName" => "investment.investment_myInvestment",        
                "internalName" => "investment_myInvestment", 
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],   
                
        13 => [
                "databaseName" => "investment.investment_investmentDate", 
                "internalName" => "investment_investmentDate",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ], 
        14 => [
                "databaseName" => "investment.issueDate", 
                "internalName" => "investment_issueDate",            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],       
        15 => [
                "databaseName" => "investment.investment_dueDate", 
                "internalName" => "investment_dueDate",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        16 => [
                "databaseName" => "investment.investment_originalDuration", 
                "internalName" => "investment_originalDuration",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        17 => [
                "databaseName" => "investment.investment_remainingDuration", 
                "internalName" => "investment_originalDuration",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],  
        18 => [
                "databaseName" => "investment.investment_paymentFrequency", 
                "internalName" => "investment_paymentFrequency",    
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],        
        19 => [
                "databaseName" => "investment.investment_fullLoanAmount", 
                "internalName" => "investment_fullLoanAmount",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],
        20 => [
                "databaseName" => "investment.investment_nominalInterestRate", 
                "internalName" => "investment_nominalInterestRate",     
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""  
            ], 
        21 => [
                "databaseName" => "investment.investment_riskRating", 
                "internalName" => "investment_riskRating",    
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        22 => [
                "databaseName" => "investment.investment_expectedAnualYield", 
                "internalName" => "investment_expectedAnualYield",       
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],  
        23 => [
                "databaseName" => "investment.investment_LTV",
                "internalName" => "investment_LTV",      
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""    
            ],         
        24 => [
                "databaseName" => "investment.investment_originalState", 
                "internalName" => "investment_originalState",     
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        25 => [
                "databaseName" => "investment.investment_dateOfPurchase", 
                "internalName" => "investment_dateOfPurchase",     
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        26 => [
                "databaseName" => "investment.investment_secondaryMarketInvestment", 
                "internalName" => "investment_secondaryMarketInvestment",    
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        27 => [
                "databaseName" => "investment.investment_priceInSecondaryMarket", 
                "internalName" => "investment_priceInSecondaryMarket",      
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],  
        
                        28 => [ // CHECK
                                "databaseName" => "investment.investment_forSale",
                                "internalName" => "investment_forSale",       
                                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                                "function" => ""        
                            ],


        30 => [
                "databaseName" => "investment.investment_principalAndInterestPayment",  
                "internalName" => "investment_principalAndInterestPayment",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        31 => [
                "databaseName" => "investment.investment_instalmentsProgress",
                "internalName" => "investment_installmentsProgress", 
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        32 => [
                "databaseName" => "investment.investment_instalmentsPaid",  
                "internalName" => "investment_instalmentsPaid",            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],       

        33 => [
                "databaseName" => "investment.investment_totalInstalments", 
                "internalName" => "investment_totalInstalments",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],               
        34 => [
                "databaseName" => "investment.investment_capitalRepayment",  
                "internalName" => "investment_capitalRepayment",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  

        35 => [
                "databaseName" => "investment.investment_partialPrincipalPayment", 
                "internalName" => "investment_partialPrincipalPayment",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ],       
        36 => [
                "databaseName" => "investment.investment_principalBuyBack",
                "internalName" => "investment_principalBuyBack",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        37 => [
                "databaseName" => "investment.investment_outstandingPrincipal",
                "internalName" => "investment_outstandingPrincipal",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        38 => [
                "databaseName" => "investment.investment_receivedRepayment", 
                "internalName" => "investment_receivedRepayment",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""  
            ],
        39 => [
                "databaseName" => "investment.investment_nextPaymentDate", 
                "internalName" => "investment_nextPaymentDate",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        40 => [
                "databaseName" => "investment.investment_nextPayment",
                "internalName" => "investment_nextPayment",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        41 => [
                "databaseName" => "investment.investment_interestGrossExpected",  
                "internalName" => "investment_interestGrossExpected",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],       
        42 => [
                "databaseName" => "investment.investment_totalGrossIncome", 
                "internalName" => "investment_totalGrossIncome",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        43 => [
                "databaseName" => "investment.investment_interestGrossIncome", 
                "internalName" => "investment_interestGrossIncome",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        44 => [
                "databaseName" => "investment.investment_interestIncomeBuyBack",
                "internalName" => "investment_interestIncomeBuyBack",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        45 => [
                "databaseName" => "investment.investment_delayedInterestIncome",
                "internalName" => "investment_delayedInterestIncome",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ],       
        46 => [
                "databaseName" => "investment.investment_delayedInterestIncomeBuyBack",
                "internalName" => "investment_delayedInterestIncomeBuyback",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""        
            ],
        47 => [
                "databaseName" => "payment.payment_latePaymentFeeIncome",
                "internalName" => "payment_latePaymentFeeIncome",          
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateLatePaymentFeefunction"           
            ],
        48 => [
                "databaseName" => "investment.investment_recoveries", 
                "internalName" => "investment_recoveries",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        49 => [ // TO CHECK WITH LATEST INVESTMENT LIST
                "databaseName" => "investment.investment_loanIdAA",  
                "internalName" => "investment_originalDuration",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        50 => [
                "databaseName" => "investment.investment_compensation",  
                "internalName" => "investment_compensation",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ],       
        51 => [
                "databaseName" => "investment.investment_premiumSecondaryMarket",
                "internalName" => "investment_premiumSecondaryMarket",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],  
        
        
        53 => [
                "databaseName" => "investment.investment_totalCost",  
                "internalName" => "investment_totalCost",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        54 => [
                "databaseName" => "investment.investment_commissionPaid",
                "internalName" => "investment_commissionPaid",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""         
            ],
        55 => [
                "databaseName" => "investment.investment_bankCharges", 
                "internalName" => "investment_bankCharges",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""
            ],       
        56 => [
                "databaseName" => "investmentdata.investmentdata_taxVAT",  
                "internalName" => "investmentdata_taxVAT",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        57 => [
                "databaseName" => "investment.investment_incomeWithholdingTax", 
                "internalName" => "investment_incomeWithholdingTax",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        58 => [
                "databaseName" => "investment.investment_interestPaymentSecondaryMarketPurchase", 
                "internalName" => "investment_interestPaymentSecondaryMarketPurchase",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""  
            ],
        59 => [
                "databaseName" => "investment.investment_currencyExchangRateFee", 
                "internalName" => "investment_originalDuration",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ], 
        60 => [
                "databaseName" => "investment.investment_costSecondaryMarket", 
                "internalName" => "investment_costSecondaryMarket",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""   
            ],
      
        62 => [
                "databaseName" => "investment.investment_totalNetIncome",
                "internalName" => "investment_totalNetIncome",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        63 => [
                "databaseName" => "investment.investment_paymentStatus", 
                "internalName" => "investment_paymentStatus",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        64 => [
                "databaseName" => "investment.investment_statusOfLoan", 
                "internalName" => "investment_statusOfLoan",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        65 => [
                "databaseName" => "investment.investment_writtenOff",
                "internalName" => "investment_writtenOff",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],       
        66 => [
                "databaseName" => "investment.investment_deposits", 
                "internalName" => "investment_deposits",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""        
            ],
        67 => [
                "databaseName" => "userinvestmentdata.userinvestmentdata_withdrawal",  
                "internalName" => "userinvestmentdata_withdrawals",             
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE, 
                "function" => "" 
            ],
        ];
         
    
    public function startup() {
        $this->GearmanClient = new GearmanClient();
    }

    public function help() {
        $this->out('Gearman Client as a CakePHP Shell');
    }

    public function main() {

        echo "Nothing\n";
    }




    public function initDataAnalysisClient() {
        $inActivityCounter = 0;
        $this->GearmanClient->addServers();
        echo __FUNCTION__ . " " . __LINE__ .": " . "\n";       
        if (Configure::read('debug')) {
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting Gearman Flow 2 Client\n";
        }

        $this->GearmanClient->setFailCallback(array($this, 'verifyFailTask'));
        $this->GearmanClient->setExceptionCallback(array($this, 'verifyExceptionTask'));
        $this->GearmanClient->setCompleteCallback(array($this, 'verifyCompleteTask'));

        $resultQueue = $this->Queue->getUsersByStatus(FIFO, GLOBAL_DATA_DOWNLOADED);

        $inActivityCounter++;                                           // Gearman client

        Configure::load('p2pGestor.php', 'default');
        $jobsInParallel = Configure::read('dashboard2JobsInParallel');

        $response = [];

        while (true){
            $pendingJobs = $this->checkJobs(GLOBAL_DATA_DOWNLOADED, $jobsInParallel);
            if (Configure::read('debug')) {
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Checking if jobs are available for this Client\n";
            }
            if (!empty($pendingJobs)) {
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "There is work to be done\n";
                }
                foreach ($pendingJobs as $keyjobs => $job) {
                    $userReference = $job['Queue']['queue_userReference'];
                    $directory = Configure::read('dashboard2Files') . $userReference . "/" . date("Ymd",time()) . DS ;
                    $dir = new Folder($directory);
                    $subDir = $dir->read(true, true, $fullPath = true);     // get all sub directories
print_r($subDir);
                    foreach ($subDir[0] as $subDirectory) {
                        $tempName = explode("/", $subDirectory);
                        $linkedAccountId = $tempName[count($tempName) - 1];
                        $dirs = new Folder($subDirectory);
                        $allFiles = $dirs->findRecursive();
 
                        $tempPfpName = explode("/", $allFiles[0]);
                        $pfp = $tempPfpName[count($tempPfpName) - 2];
                        echo "pfp = " . $pfp . "\n";
                        $files = $this->readFilteredFiles($allFiles,  TRANSACTION_FILE + INVESTMENT_FILE);
                        $listOfActiveLoans = $this->getListActiveLoans($linkedAccountId);
                        $params[$linkedAccountId] = array('queue_id' => $job['Queue']['id'],
                                                        'pfp' => $pfp,
                                                        'listOfCurrentActiveLoans' => $listOfActiveLoans,
                                                        'userReference' => $job['Queue']['queue_userReference'],
                                                        'files' => $files);
                    }
                    debug($params);
                    
                    $response[] = $this->GearmanClient->addTask("parseFileFlow", json_encode($params));
                }
                
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Sending the previous information to Worker\n";
                }
                $this->GearmanClient->runTasks();


                

                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": " . "Result received from Worker\n";
                }
                $result = json_decode($this->workerResult, true);
                foreach ($result as $platformKey => $platformResult) {
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": " . "platformkey = $platformKey\n";
                    }
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
                    $queueId = $platformResult['queue_id'];
                    $baseDirectory = Configure::read('dashboard2Files') . $userReference . "/" . date("Ymd",time()) . DS ;
                    $baseDirectory = $baseDirectory . $platformKey . DS . $platformResult['pfp'] . DS;

                    $mapResult = $this->mapData($platformResult);

                    if (!empty($platformResult['newLoans'])) {
                        $fileHandle = new File($baseDirectory .'loanIds.json', true, 0644);
                        if ($fileHandle) {
                            if ($fileHandle->append(json_encode($platformResult['newLoans']), true)) {
                                $fileHandle->close();
                                echo "File " .  $baseDirectory . "loanIds.json written\n";
                            }
                        }
                        $newState = DATA_EXTRACTED;
                    }
                    else {
                        $newState = AMORTIZATION_TABLES_DOWNLOADED;
                    }
                    $this->Queue->id = $queueId;
                    $this->Queue->save(array('queue_status' => $newState,
                                             'queue_info' => json_encode($platformResult['newLoans']),
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
                sleep (4);                                          // Just wait a short time and check again
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
     * Get the list of all active investments for a PFP as identified by the
     * linkedaccount identifier.
     *
     * @param int $linkedaccount_id    linkedaccount reference
     * @return array
     *
     */
    public function getListActiveLoans($linkedaccount_id) {

        $this->Investment = ClassRegistry::init('Investment');

// CHECK THE FILTERCONDITION for status
        $filterConditions = array(
            //'linkedaccount_id' => $linkedaccount_id,
                                    "investment_status" => -1,
                                );
/*
	$investmentListResult = $this->Investment->find("all", array( "recursive" => -1,
							"conditions" => $filterConditions,
                                                        "fields" => array("id", "investment_loanReference"),
									));
 * */
 
 //       $list = Hash::extract($investmentListResult, '{n}.Investment.investment_loanReference');
       $list =  array('C0008363',
'C0007868',
'C0007794',
'C0007735',
'C0007686',
'C0007554',
'C0007365',
'C0006261',
'C0004412',
'C0004185',
'C0004051',
'C0003780',
'C0003027',
'C0002601',
'C0002686',
    'C0002701');

        $list[] = "20729-01";       // ONLY FOR TESTING PURPOSES, TO BE DELETED.
        return $list;
    }










    public function verifyFailTask(GearmanTask $task) {
        $data = $task->data();
        $this->workerResult = $task->data();
        echo __METHOD__ . " " . __LINE__ . "\n";
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Fail: {$m}" . GEARMAN_WORK_FAIL . "\n";
    }

    public function verifyExceptionTask (GearmanTask $task) {
        $data = $task->data();
        $this->workerResult = $task->data();
        echo __METHOD__ . " " . __LINE__ .  "\n";
        echo "ID Unique: " . $task->unique() . "\n";
        echo "Exception: {$m} " . GEARMAN_WORK_EXCEPTION . "\n";
        //return GEARMAN_WORK_EXCEPTION;
    }

    public function verifyCompleteTask (GearmanTask $task) {
        echo __METHOD__ . " " . __LINE__ . "\n";
        $data = explode(".-;", $task->unique());
        $this->workerResult = $task->data();
        echo "ID Unique: " . $task->unique() . "\n";
        echo "JOB COMPLETE: ";
  //              $task->jobHandle() . ", " . $task->data() . "\n";
        echo GEARMAN_SUCCESS;

    }




    /**
     * maps the data to its corresponding database table + variables and writes them to the database
     *  @param  $array          Array with transaction data received from Worker     
     *  @param  $array          Array with investment data received from Worker
     * v
     *  @return boolean true
     *                  false
     *
     * the data is available in two or three sub-arrays which are to be written (before checking if it is a duplicate) to the corresponding
     * database table.g
     *     platform - (1-n)loanId - (1-n) concepts
     */
    public function mapData (&$platformData) {
        $variables = array();
        $dbInvestmentTable = array('loanId' => "",
                                    'country' => "",
                                    'loanType'  => "",
                                    'amortizationMethod' => "",
                            );
 
        print_r($platformData['newLoans']);
// copy ALL static investmentTable fields, EVEN if they don't exist. Only for the first time, if we don't have a amortization table
// (i.e. is in list of NEW loans
// copy ALL "dynamic" investmentTable fields, EVEN if they don't exist.        
        
// check which once to calculate at this moment (can we use a bitmap approach?)       
 /*
        if (in_array($loanID, $platformData['newLoans'])) {
            echo "loanId already exists, so no extra storage is required\n";
        }
        $dbUserInvestmentData = array (

                            );*/
// individual methods for each and every field. 
 // copy ALL UservestmentTable fields, EVEN if they don't exist.       
        
        
        $dbAmortizationTable = array(

                             );

// create a default AmortizationTable for loan if it is a new loan
        /*
        [2] => [
                "databaseName" => "investment.investment_country", 
                "internalName" => "investment_country",  
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""
            ],*/
    echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting with mapping process\n";       
        foreach ($platformData['newLoans'] as $loanIdKey => $newLoan) {
            $newLoan = "20729-01";
            $loanIdKey = 20;
            echo "New loanIdKey = $loanIdKey and value = $newLoan\n";
            print_r($platformData['parsingResultInvestments'][$newLoan]);
            // check if we have information in the investment list about this loan.
            if (array_key_exists( $newLoan, $platformData['parsingResultInvestments'])) {  // this is a new loan and we have some info
                echo "loanIdKey = $loanIdKey, so start to copy the data\n";
                // check all the data in analyzed investment table

                foreach ($platformData['parsingResultInvestments'][$newLoan] as $investmentDataKey => $investmentData) {
                    $tempResult = $this->in_multiarray($investmentDataKey, $this->variablesConfig);

                    if (!empty($tempResult))  {    
                        $dataInformation = explode (".",$tempResult['databaseName'] );
                        $dbTable = $dataInformation[0];
                        $database[$dbTable][$investmentDataKey] = $investmentData;
                    }
                }  
            }
 // also check if they belong to the same date, if not flush it          
            if (array_key_exists( $newLoan, $platformData['parsingResultTransactions'])) {  // this is a new loan and we have some info
                 echo "loanIdKey= $loanIdKey, so start to copy the data\n";
                 print_r($platformData['parsingResultTransactions'][$newLoan]);
                // check all the data in analyzed transaction table
    
                foreach ($platformData['parsingResultTransactions'][$newLoan] as $transactionData) { 
                    foreach ($transactionData as $transactionDataKey => $transaction) {  // 0,1,2

                        echo "key = $transactionDataKey and transaction = $transaction\n";
                        if ($transactionDataKey == "internalName") {        // dirty trick to make it simple
                           $transactionDataKey = $transaction; 
                        }
                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);
print_r($tempResult);
                        if (!empty($tempResult))  { 
                            unset($result);
                            $functionToCall = $tempResult['function'];
                            echo "functionToCall = $functionToCall";
                            $dataInformation = explode (".", $tempResult['databaseName'] );
                            $dbTable = $dataInformation[0];
echo "dbTable = $dbTable\n";
                            if (!empty($functionToCall)) {
                                $result = $this->$functionToCall($transactionData, $database);

                                if ($tempResult['charAcc'] == FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                    $database[$dbTable][$transactionDataKey] = $database[$dbTable][$transactionDataKey] + $result; 
                                }
                                else {
                                    $database[$dbTable][$transactionDataKey] = $result;  
                                }
                            }
                            else {
                                $database[$dbTable][$investmentDataKey] = $transaction;
                            }
                        } 
                    }
                }   
            } 
 
  
print_r($database);           
            echo __FUNCTION__ . " " . __LINE__ . ": " . "Write the new Investment Data\n"; 
            $this->Investment->create();
            if ($this->Investment->save($database['investment'], $validate = true)) {
            }
            else {
                if (Configure::read('debug')) {
                   echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['investment']['investment_loanId']  . "\n";
                }
            } 
            
            $this->Payment->create();            
            if ($this->Payment->save($database['payment'], $validate = true)) {
            }
            else {
                if (Configure::read('debug')) {
                   echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['payment']['payment_loanId']  . "\n";
                }
            }  
            break;
        }
     echo __FUNCTION__ . " " . __LINE__ . ": " . "Finishing mapping process for new investment\n";        
  
    return;   

    }
   
 
    
    /* 
     *  Get the amount which corresponds to the "late payment fee" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */              
    public function calculateLatePaymentFeefunction(&$transactionData, &$resultData) {
        echo "CHARO";
       return $transactionData['amount']; 
    }
 
    
    /* 
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return
    */
    public function getLoanId(&$dbTableReference, $value) {
       return $newValue; 
    }

    /** 
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return 
    */
    public function calculateOriginalDuration(&$dbTableReference, $value) {

    }

    /**
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return 
    */
    public function calculateRemainingDuration(&$dbTableReference, $value) {

    }

    /**
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return 
    */
    public function getLoanType(&$dbTableReference, $value) {

    }

    /**
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return
    */
    public function getLoanOriginator(&$dbTableReference, $value) {

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
     * 
     * get the name of a variable??
     * 
     * @param type $var
     * @return type
     */
    function var_name(&$var) {
       foreach ($GLOBALS as $k => $v) {
           $global_vars[$k] = $v;
       }

       // save the variable's original value
       $saved_var = $var;

       // modify the variable whose name we want to find
       $var = !$var;

       // compare the defined variables before and after the modification
       $diff = array_keys(array_diff_assoc($global_vars, $GLOBALS));

       // restore the variable's original value
       $var = $saved_var;

       // return the name of the modified variable
       return $diff[0];
    }
    /*
     * Thanks for posting your solution. It works for variables with "global" scope. To use var_name() 
     * with variables defined within a function you would have to scan the array returned by get_defined_vars() 
     * instead of $GLOBALS. Would be great to have a class with objects that know their own name. E.g. $a1 = new varNameClass(); with 
     * a method: $a1->getName() returning the string 'a1'.
     */




}