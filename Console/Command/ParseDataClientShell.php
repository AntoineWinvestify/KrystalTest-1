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
    public $uses = array('Queue');
    
    protected $GearmanClient;
    
    protected $variablesConfig = [
        // FLOWDATA_VARIABLE_DONE: The system has copied/calculates the variable to the internal queue
        // FLOWDATA_VARIABLE_NOT_DONE: The system has not (yet) copied the variable to the internal queue
        // FLOWDATA_VARIABLE_ACCUMULATIVE: The value is accumulative, read original value, add new value and write
        //                                  for this loan and this readout period
        // FLOWDATA_VARIABLE_NOT_ACCUMULATIVE: not an accumulative value, simply (over)write the value  
        
        2 => [
                "databaseName" => "investment.investment_loanId", 
                "internalName" => "investment_loanId",
                "internalIndex" => 2,
                "state" => FLOWDATA_VARIABLE_NOT_DONE,                 
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,
                "function" => ""                      // Only needed in case "complex calculations are required
            ],
        3 => [
                "databaseName" => "investment.investment_debtor", 
                "internalName" => "investment_debtor",
                "internalIndex" => 3,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,
                "function" => ""                                        // Not applicable            
            ],       
        4 => [
                "databaseName" => "investment.investment_country", 
                "internalName" => "investment_country",  
                "internalIndex" => 4,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable
            ],
        5 => [
                "databaseName" => "investment.investment_loanType", 
                "internalName" => "investment_loanType",    
                "internalIndex" => 5,           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],  
        6 => [
                "databaseName" => "investment.investment_amortizationMethod", 
                "internalName" => "investment_amortizationMethod", 
                "internalIndex" => 6,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable          
            ],
        7 => [
                "databaseName" => "investment.investment_market", 
                "internalName" => "investment_market",        
                "internalIndex" => 7,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ],       
        8 => [
                "databaseName" => "investment.investment_loanOriginator", 
                "internalName" => "investment_loanOriginator",         
                "internalIndex" => 8,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable 
            ],
        9 => [
                "databaseName" => "investment.investment_buyBackGuarantee", 
                "internalName" => "investment_buyBackGuarantee", 
                "internalIndex" => 9,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],
        10 => [
                "databaseName" => "investment.investment_currency", 
                "internalName" => "investment_currency",         
                "internalIndex" => 10,           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable   
            ],
        11 => [
                "databaseName" => "investment.investment_typeOfInvestment",        
                "internalName" => "investment_typeOfInvestment", 
                "internalIndex" => 11,           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ],   
        12 => [ //OK-OK
                "databaseName" => "investment.investment_myInvestment",        
                "internalName" => "investment_myInvestment", 
                "internalIndex" => 12,           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateCapitalRepayment" 
            ],   
                
        13 => [
                "databaseName" => "investment.investment_myInvestmentDate", 
                "internalName" => "investment_myInvestmentDate",  
                "internalIndex" => 13,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateCapitalRepayment"          
            ], 
        14 => [
                "databaseName" => "investment.issueDate", 
                "internalName" => "investment_issueDate",   
                "internalIndex" => 14,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],       
        15 => [
                "databaseName" => "investment.investment_dueDate", 
                "internalName" => "investment_dueDate",         
                "internalIndex" => 15,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        16 => [
                "databaseName" => "investment.investment_originalDuration", 
                "internalName" => "investment_originalDuration",    
                "internalIndex" => 16,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        17 => [
                "databaseName" => "investment.investment_remainingDuration", 
                "internalName" => "investment_remainingDuration",   
                "internalIndex" => 17,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],  
        18 => [
                "databaseName" => "investment.investment_paymentFrequency", 
                "internalName" => "investment_paymentFrequency",    
                "internalIndex" => 18,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],        
        19 => [
                "databaseName" => "investment.investment_fullLoanAmount", 
                "internalName" => "investment_fullLoanAmount",        
                "internalIndex" => 19,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],
        20 => [
                "databaseName" => "investment.investment_nominalInterestRate", 
                "internalName" => "investment_nominalInterestRate",     
                "internalIndex" => 20,           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ], 
        21 => [
                "databaseName" => "investment.investment_riskRating", 
                "internalName" => "investment_riskRating",    
                "internalIndex" => 21,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ], 
        22 => [
                "databaseName" => "investment.investment_expectedAnualYield", 
                "internalName" => "investment_expectedAnualYield",    
                "internalIndex" => 22,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],  
        23 => [
                "databaseName" => "investment.investment_LTV",
                "internalName" => "investment_LTV",      
                "internalIndex" => 23,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable
            ],         
        24 => [
                "databaseName" => "investment.investment_originalState", 
                "internalName" => "investment_originalState",     
                "internalIndex" => 24,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        25 => [
                "databaseName" => "investment.investment_dateOfPurchase", 
                "internalName" => "investment_dateOfPurchase",     
                "internalIndex" => 25,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        26 => [    //OK-ok
                "databaseName" => "investment.investment_secondaryMarketInvestment", 
                "internalName" => "investment_secondaryMarketInvestment",  
                "internalIndex" => 26,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        27 => [
                "databaseName" => "investment.investment_priceInSecondaryMarket", 
                "internalName" => "investment_priceInSecondaryMarket",     
                "internalIndex" => 27,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],  
        
                        28 => [ // CHECK
                                "databaseName" => "investment.investment_forSale",
                                "internalName" => "investment_forSale",    
                "internalIndex" => 28,                            
                                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                                "function" => ""        
                            ],


        30 => [
                "databaseName" => "investment.investment_principalAndInterestPayment",  
                "internalName" => "investment_principalAndInterestPayment", 
                "internalIndex" => 30,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        31 => [
                "databaseName" => "investment.investment_instalmentsProgress",
                "internalName" => "investment_installmentsProgress", 
                "internalIndex" => 31,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        32 => [
                "databaseName" => "investment.investment_instalmentsPaid",  
                "internalName" => "investment_instalmentsPaid",    
                "internalIndex" => 32,               
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],       

        33 => [
                "databaseName" => "investment.investment_totalInstalments", 
                "internalName" => "investment_totalInstalments",     
                "internalIndex" => 33,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],               
        34 => [    //OK ok 
                "databaseName" => "payment.payment_capitalRepayment",  
                "internalName" => "payment_capitalRepayment",         
                "internalIndex" => 34,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateCapitalRepayment"           
            ],  

        35 => [
                "databaseName" => "investment.investment_partialPrincipalPayment", 
                "internalName" => "investment_partialPrincipalPayment",  
                "internalIndex" => 35,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculatePartialPrincipalBuyback" 
            ],       
        36 => [    //OK ok
                "databaseName" => "payment.payment_principalBuyback",
                "internalName" => "payment_principalBuyback",    
                "internalIndex" => 36,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculatePrincipalBuyback"        
            ],
        37 => [
                "databaseName" => "investment.investment_outstandingPrincipal",
                "internalName" => "investment_outstandingPrincipal", 
                "internalIndex" => 37,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        38 => [
                "databaseName" => "investment.investment_receivedRepayment", 
                "internalName" => "investment_receivedRepayment",
                "internalIndex" => 38,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""  
            ],
        39 => [
                "databaseName" => "investment.investment_nextPaymentDate", 
                "internalName" => "investment_nextPaymentDate",
                "internalIndex" => 39,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        40 => [
                "databaseName" => "investment.investment_nextPayment",
                "internalName" => "investment_nextPayment",    
                "internalIndex" => 40,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        41 => [
                "databaseName" => "investment.investment_interestGrossExpected",  
                "internalName" => "investment_interestGrossExpected",
                "internalIndex" => 41,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],       
        42 => [   
                "databaseName" => "investment.investment_totalGrossIncome", 
                "internalName" => "investment_totalGrossIncome", 
                "internalIndex" => 42,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        43 => [  //OK ok
                "databaseName" => "payment.payment_regularGrossInterestIncome", 
                "internalName" => "payment_regularGrossInterestIncome",             
                "internalIndex" => 43,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateRegularGrossInterestIncome"           
            ],  
        44 => [    //OK ok
                "databaseName" => "payment.payment_interestIncomeBuyback",
                "internalName" => "payment_interestIncomeBuyback",      
                "internalIndex" => 44,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateInterestIncomeBuyback"         
            ],
        45 => [    //OK ok
                "databaseName" => "payment.payment_delayedInterestIncome",
                "internalName" => "payment_delayedInterestPayment",         
                "internalIndex" => 45,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateDelayedInterestIncome" 
            ],       
        46 => [    //OK ok
                "databaseName" => "payment.payment_delayedInterestIncomeBuyback",
                "internalName" => "payment_delayedInterestIncomeBuyback",   
                "internalIndex" => 46,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateDelayedInterestIncomeBuyback"        
            ],
        47 => [    //OK ok
                "databaseName" => "payment.payment_latePaymentFeeIncome",
                "internalName" => "payment_latePaymentFeeIncome",        
                "internalIndex" => 47,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateLatePaymentFeeIncome"           
            ],
        48 => [
                "databaseName" => "investment.investment_loanRecoveries", 
                "internalName" => "investment_recoveries",             
                "internalIndex" => 48,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        49 => [ // TO CHECK WITH LATEST INVESTMENT LIST
                "databaseName" => "investment.investment_loanIncentivesAndBonus",  
                "internalName" => "investment_originalDuration",     
                "internalIndex" => 49,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        50 => [
                "databaseName" => "investment.investment_loanCompensation",  
                "internalName" => "investment_compensation",        
                "internalIndex" => 50,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ],       
        51 => [    //OK
                "databaseName" => "investment.investment_premiumSecondaryMarket",
                "internalName" => "investment_premiumSecondaryMarket",  
                "internalIndex" => 51,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],                 
        53 => [
                "databaseName" => "investment.investment_loanTotalCost",  
                "internalName" => "investment_totalCost",             
                "internalIndex" => 53,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        54 => [
                "databaseName" => "investment.investment_commissionPaid",
                "internalName" => "investment_commissionPaid",        
                "internalIndex" => 54,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""         
            ],
        55 => [
                "databaseName" => "investment.investment_bankCharges", 
                "internalName" => "investment_bankCharges",             
                "internalIndex" => 55,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""
            ],       
        56 => [
                "databaseName" => "investmentdata.investmentdata_taxVAT",  
                "internalName" => "investmentdata_taxVAT",             
                "internalIndex" => 56,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        57 => [
                "databaseName" => "investment.investment_incomeWithholdingTax", 
                "internalName" => "investment_incomeWithholdingTax",             
                "internalIndex" => 57,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],
        58 => [
                "databaseName" => "investment.investment_interestPaymentSecondaryMarketPurchase", 
                "internalName" => "investment_interestPaymentSecondaryMarketPurchase",        
                "internalIndex" => 58,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""  
            ],
        59 => [
                "databaseName" => "investment.investment_currencyExchangRateFee", 
                "internalName" => "investment_originalDuration",     
                "internalIndex" => 59,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ], 
        60 => [    //OK
                "databaseName" => "investment.investment_costSecondaryMarket", 
                "internalName" => "investment_costSecondaryMarket",  
                "internalIndex" => 60,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""   
            ],
      
        62 => [
                "databaseName" => "investment.investment_totalNetIncome",
                "internalName" => "investment_totalNetIncome",    
                "internalIndex" => 62,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        63 => [
                "databaseName" => "investment.investment_paymentStatus", 
                "internalName" => "investment_paymentStatus",      
                "internalIndex" => 63,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        64 => [
                "databaseName" => "investment.investment_statusOfLoan", 
                "internalName" => "investment_statusOfLoan",       
                "internalIndex" => 64,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        65 => [
                "databaseName" => "investment.investment_writtenOff",
                "internalName" => "investment_writtenOff",        
                "internalIndex" => 65,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],       
        66 => [    //OK--
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformDeposit", 
                "internalName" => "globalcashflowdata_platformDeposits",          
                "internalIndex" => 66,           
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculatePlatformDeposit"        
            ],
        67 => [   //OK--
                "databaseName" => "globalcashflowdata.globalcashflowdata__platformWithdrawal",  
                "internalName" => "globalcashflowdata_platformWithdrawals",        
                "internalIndex" => 67,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE, 
                "function" => "calculatePlatformWithdrawal" 
            ], 
        68 => [
                "databaseName" => "payment.payment_currencyFluctuationPositive", 
                "internalName" => "payment_currencyFluctuationPositive",             
                "internalIndex" => 68,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        69 => [ 
                "databaseName" => "payment.payment_currencyFluctuationNegative",  
                "internalName" => "payment_currencyFluctuationNegative",     
                "internalIndex" => 69,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        70 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformRecoveries",  
                "internalName" => "globalcashflowdata_platformRecoveries",        
                "internalIndex" => 70,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ],                 
        71 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformIncentivesAndBonus", 
                "internalName" => "globalcashflowdata_platformIncentivesAndBonus",             
                "internalIndex" => 71,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        72 => [ 
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformCompensation",  
                "internalName" => "globalcashflowdata_platformCompensation",     
                "internalIndex" => 72,            
                "state" => FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        73 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformTotalCost",  
                "internalName" => "globalcashflowdata_platformTotalCost",        
                "internalIndex" => 73,            
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
                                'linkedaccount_id' => $linkedaccount_id,
                               //     "investment_status" => -1,
                                );

	$investmentListResult = $this->Investment->find("all", array( "recursive" => -1,
							"conditions" => $filterConditions,
                                                        "fields" => array("id", "investment_loanId"),
									));
 
        $list = Hash::extract($investmentListResult, '{n}.Investment.investment_loanId');
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
     * Maps the data to its corresponding database table + variables, calculates the "Missing values" 
     * and writes all values to the database.
     *  @param  $array          Array which holds the data (perp PFP) as received from the Worker
     * 
     *  @return boolean true
     *                  false
     *
     * the principal data is available in two or three sub-arrays which are to be written 
     * (before checking if it is a duplicate) to the corresponding database table.
     *     platform - (1-n)loanId - (1-n) concepts
     */
    public function mapData (&$platformData) {
        $variables = array();
        $linkedaccountId = $platformData['linkedaccountId'];

// create a default AmortizationTable for the loan if it is a new loan?

    echo __FUNCTION__ . " " . __LINE__ . ": " . "Starting with mapping process\n";       
        foreach ($platformData['newLoans'] as $loanIdKey => $newLoan) {
if ($newLoan <> "1604819-01" ) {
    echo "flushing loanId $newLoan\n";
    continue;
}
            
            
            
//            echo "New loanIdKey = $loanIdKey and value = $newLoan\n";
 //           print_r($platformData['parsingResultInvestments'][$newLoan]);
            // check if we have information in the investment list about this loan.
            if (array_key_exists( $newLoan, $platformData['parsingResultInvestments'])) {  // this is a new loan and we have some info
                // check all the data in analyzed investment table

                foreach ($platformData['parsingResultInvestments'][$newLoan] as $investmentDataKey => $investmentData) {
                    $tempResult = $this->in_multiarray($investmentDataKey, $this->variablesConfig);
                    if (!empty($tempResult))  {    
                        $dataInformation = explode (".",$tempResult['databaseName'] );
                        $dbTable = $dataInformation[0];
                        $database[$dbTable][$investmentDataKey] = $investmentData;
                        $this->variablesConfig[$tempResult['internalIndex']]['state'] = FLOWDATA_VARIABLE_DONE;   // Mark done
                    }
                }  
            }

 // also check if they belong to the same date, if not flush it     
echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";   
            if (array_key_exists( $newLoan, $platformData['parsingResultTransactions'])) {  // this is a new loan and we have some info
                print_r($platformData['parsingResultTransactions'][$newLoan]);
                 
                // check all the data in analyzed transaction table
                foreach ($platformData['parsingResultTransactions'][$newLoan] as $transactionData) { 
                    foreach ($transactionData as $transactionDataKey => $transaction) {  // 0,1,2
                        if ($transactionDataKey == "internalName") {        // dirty trick to keep it simple
                            $transactionDataKey = $transaction; 
                        }

                        $tempResult = $this->in_multiarray($transactionDataKey, $this->variablesConfig);
                        
                        if (!empty($tempResult))  { 
                            unset($result);
                            $functionToCall = $tempResult['function'];

                            $dataInformation = explode (".", $tempResult['databaseName'] );
                            $dbTable = $dataInformation[0];
                            if (!empty($functionToCall)) {
                                $result = $this->$functionToCall($transactionData, $database);

                                if ($tempResult['charAcc'] == FLOWDATA_VARIABLE_ACCUMULATIVE) {
                                   $database[$dbTable][$transactionDataKey] = $database[$dbTable][$transactionDataKey] + $result; 
                                    $database[$dbTable][$transactionDataKey] = sprintf("%017d", $database[$dbTable][$transactionDataKey]);
                                }
                                else {
                                    $database[$dbTable][$transactionDataKey] = $result;  
                                }
                            }
                            else {
                                $database[$dbTable][$transactionDataKey] = $transaction;
                            }
                    //??        $this->variablesConfig[$tempResult['internalIndex']]['state'] = FLOWDATA_VARIABLE_DONE;  // Mark done
                        } 
                    }
                }   
            } 
 
//$database['payment']['investment_id'] = 98;
echo __FUNCTION__ . " " . __LINE__ . ": " . "\n";             
print_r($database);      
 // write all relevant tables, WE DON'T HAVE TO UPDATE AMORTIZATION TABLES???
// if it is a new loan, then save the data,
// else read the investment_id from the database and use it while saving data to payment, paymenttotals,...
            if (!empty($database['investment'])) {
                $this->Investment = ClassRegistry::init('Investment');
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Investment Data... ";                 
                $database['investment']['linkedaccount_id'] = $linkedaccountId;
                $resultCreate = $this->Investment->createNewInvestment($database['investment']);
                if ($resultCreate[0]) {
                    $investmentId = $resultCreate[1];
                    echo " investmentId = $investmentId, Done\n";
                }
                else {
                    if (Configure::read('debug')) {
                       echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['investment']['investment_loanId']  . "\n";
                    }
                }
            }
            
            if (!empty($database['payment'])) {
                $this->Payment = ClassRegistry::init('Payment');
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Payment Data for investment with id = $investmentId... ";            
                $database['payment']['investment_id'] = $investmentId;
                $this->Payment->create();            
                if ($this->Payment->save($database['payment'], $validate = true)) {
                    echo "Done\n";
                }
                else {
                    if (Configure::read('debug')) {
                       echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['payment']['payment_loanId']  . "\n";
                    }
                }
            }

            if (!empty($database['userinvestmentdata'])) {            
                $this->Userinvestmentdata = ClassRegistry::init('Userinvestmentdata');
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Userinvestmentdata Data... ";            
                $this->Userinvestmentdata->create();            
                if ($this->Userinvestmentdata->save($database['userinvestmentdata'], $validate = true)) {
                    echo "Done\n";
                }
                else {
                    if (Configure::read('debug')) {
                       echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['userinvestmentdata']['payment_loanId']  . "\n";
                    }
                }  
            }
            
// We don't write the amortization tables during Flow 2. New tables are collected and written to DB in Flow 3B 
            if (!empty($database['globalcashflowdata'])) {               
                $this->Globalcashflowdata = ClassRegistry::init('Globalcashflowdata');
                echo __FUNCTION__ . " " . __LINE__ . ": " . "Trying to write the new Globalcashflowdata Data... ";            
                $this->Globalcashflowdata->create();            
                if ($this->Globalcashflowdata->save($database['globalcashflowdata'], $validate = true)) {
                    echo "Done\n";
                }
                else {
                    if (Configure::read('debug')) {
                       echo __FUNCTION__ . " " . __LINE__ . ": " . "Error while writing to Database, " . $database['globalcashflowdata']['payment_loanId']  . "\n";
                    }
                }
            }
            
       //     break;
        unset($database);

        }
     echo __FUNCTION__ . " " . __LINE__ . ": " . "Finishing mapping process Flow 2 for an investment\n";        
 //print_r ($this->variablesConfig);
    return;   
    }
   
 
    
    /* 
     *  Get the amount which corresponds to the "late payment fee" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */              
    public function calculateLatePaymentFeeIncome(&$transactionData, &$resultData) {
       return $transactionData['amount']; 
    }

    
    /* 
     *  Get the amount which corresponds to the "capitalRepayment" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */ 
    public function calculateCapitalRepayment(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }
 
    
    /* 
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function calculateDelayedInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }

    
    /* 
     *  Get the amount which corresponds to the "InterestIncomeBuyback" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function calculateInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }


    /* 
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */             
    public function calculatePrincipalBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }   
    
    
    /* 
     *  Get the amount which corresponds to the "DelayedInterestIncomeBuyback" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function calculateDelayedInterestIncomeBuyback(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }
 
    
    /* 
     *  Get the amount which corresponds to the "PlatformDeposit" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function calculatePlatformDeposit(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }   
    
    
    /* 
     *  Get the amount which corresponds to the "Platformwithdrawal" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function calculatePlatformWithdrawal(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    } 
    
   
    
    
    /* 
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return
    */
    public function calculateRegularGrossInterestIncome(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }   
        
   
    /* 
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function getLoanOriginatork5(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }   
    
    
    /* 
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return
    */
    public function getLoanIdk(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }
 
    
     /* 
     *  Get the amount which corresponds to the "delayedInterestIncome" concept
     *  @param  array       array with the current transaction data
     *  @param  array       array with all data so far calculated and to be wirtten to DB
     *  @return string      the string representation of a large integer
    */
    public function getLoanOriginato0(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
    }   
    
    
    /* 
     * 
     *  @param  FILE            FQDN of the file to analyze
     *  @param  array           $configuration  Array that contains the configuration data of a specific "document"
     *  @return
    */
    public function getLoanId0(&$transactionData, &$resultData) {
        return $transactionData['amount']; 
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