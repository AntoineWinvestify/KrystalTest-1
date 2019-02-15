<?php
/**
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                         |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 * @version 0.1
 * @date 2017-10-27
 * @package
 * 
 * Definition of 'internal variables'
 * 
 * 2017-10-27
 * 
 * 
 * 
 * This file is only for variables which are directly related to a loan, not to platform total values. 
 * Absolute daily variables are defined here, but absolute total platform values are calculates directly 
 * in the parseDataClient
 * 
 */

$config['internalVariables'] = array( 
 
    /*
     * FLOWDATA_VARIABLE_DONE: The system has copied/calculates the variable to the internal queue
     * WIN_FLOWDATA_VARIABLE_NOT_DONE: The system has not (yet) copied the variable to the internal queue
     * FLOWDATA_VARIABLE_ACCUMULATIVE: The value is accumulative, read original value, add new value and write
     *                                  for this loan and this CALCULATION period
     * WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE: not an accumulative value, simply (over)write the value  
     * 
     * internal indices > 10000 are for very special cases, i.e. when the same data needs to be written twice,
     * once in its principal field, and a copy in a "secondary" field
     * 
     * 
     * 
     * 
     */
    
    
    
        
        2 => [
                "databaseName" => "investment.investment_loanId", 
                "internalName" => "investment_loanId",
                "internalIndex" => 2,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,                 
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,
                "function" => ""                      // Only needed in case "complex calculations are required
            ],
        3 => [
                "databaseName" => "investment.investment_debtor", 
                "internalName" => "investment_debtor",
                "internalIndex" => 3,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,
                "function" => ""                                        // Not applicable            
            ],       
        4 => [
                "databaseName" => "investment.investment_country", 
                "internalName" => "investment_country",  
                "internalIndex" => 4,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable
            ],
        5 => [
                "databaseName" => "investment.investment_loanType", 
                "internalName" => "investment_loanType",    
                "internalIndex" => 5,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],  
        6 => [
                "databaseName" => "investment.investment_amortizationMethod", 
                "internalName" => "investment_amortizationMethod", 
                "internalIndex" => 6,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable          
            ],
        7 => [
                "databaseName" => "investment.investment_market", 
                "internalName" => "investment_market",        
                "internalIndex" => 7,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ],       
        8 => [
                "databaseName" => "investment.investment_loanOriginator", 
                "internalName" => "investment_loanOriginator",         
                "internalIndex" => 8,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable 
            ],
        9 => [
                "databaseName" => "investment.investment_buyBackGuarantee", 
                "internalName" => "investment_buyBackGuarantee", 
                "internalIndex" => 9,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],
        10 => [
                "databaseName" => "investment.investment_currency", 
                "internalName" => "investment_currency",         
                "internalIndex" => 10,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable   
            ],
        11 => [
                "databaseName" => "investment.investment_typeOfInvestment",        
                "internalName" => "investment_typeOfInvestment", 
                "internalIndex" => 11,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ],   
        12 => [ 
                "databaseName" => "payment.payment_myInvestment",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_investmentWithoutLoanReference",        
                "internalName" => "investment_myInvestment", 
                "internalIndex" => 12,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateMyInvestment",
                "cashflowOperation" => "bcsub",               
                "linkedIndex" => 20012
            ],   
  
        20012 => [              // this is a copy of index 12. We need to write the same data both to payment and investment
                "databaseName" => "investment.investment_myInvestment",        
        //        "internalName" => "investment_myInvestment", 
                "internalIndex" => 20012,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,  
                "function" => "" 
            ],     
    
    
        13 => [
                "databaseName" => "investment.investment_myInvestmentDate", 
                "internalName" => "investment_myInvestmentDate",  
                "internalIndex" => 13,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""          
            ], 
        14 => [
                "databaseName" => "investment.investment_issueDate", 
                "internalName" => "investment_issueDate",   
                "internalIndex" => 14,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],       
        15 => [
                "databaseName" => "investment.investment_dueDate", 
                "internalName" => "investment_dueDate",         
                "internalIndex" => 15,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        16 => [
                "databaseName" => "investment.investment_originalDuration", 
                "internalName" => "investment_originalDuration",    
                "internalIndex" => 16,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        17 => [
                "databaseName" => "investment.investment_remainingDuration", 
                "internalName" => "investment_remainingDuration",   
                "internalIndex" => 17,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateRemainingterm"        
            ],  
        18 => [
                "databaseName" => "investment.investment_paymentFrequency", 
                "internalName" => "investment_paymentFrequency",    
                "internalIndex" => 18,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable            
            ],        
        19 => [
                "databaseName" => "investment.investment_fullLoanAmount", 
                "internalName" => "investment_fullLoanAmount",        
                "internalIndex" => 19,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],
        20 => [                           
                "databaseName" => "investment.investment_nominalInterestRate", 
                "internalName" => "investment_nominalInterestRate",     
                "internalIndex" => 20,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ], 
        21 => [
                "databaseName" => "investment.investment_riskRating", 
                "internalName" => "investment_riskRating",    
                "internalIndex" => 21,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable  
            ], 
        22 => [
                "databaseName" => "investment.investment_expectedAnualYield", 
                "internalName" => "investment_expectedAnualYield",    
                "internalIndex" => 22,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""      
            ],  
        23 => [
                "databaseName" => "investment.investment_LTV",
                "internalName" => "investment_LTV",      
                "internalIndex" => 23,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""                                        // Not applicable
            ],         
        24 => [
                "databaseName" => "investment.investment_originalState", 
                "internalName" => "investment_originalState",     
                "internalIndex" => 24,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],
        25 => [
                "databaseName" => "investment.investment_dateOfPurchase", 
                "internalName" => "investment_dateOfPurchase",     
                "internalIndex" => 25,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""           
            ],  
        26 => [    //OK-ok
                "databaseName" => "payment.payment_secondaryMarketInvestment", 
                "internalName" => "payment_secondaryMarketInvestment",  
                "internalIndex" => 26,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateSecondaryMarketInvestment",         
                "cashflowOperation" => "bcsub",
                "linkedIndex" => 20026    
            ],
    
        20026 => [              // this is a copy of index 26. We need to write the same data both to payment and investment
                "databaseName" => "investment.investment_secondaryMarketInvestment",        
                "internalName" => "investment_secondaryMarketInvestment", 
                "internalIndex" => 20026,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,  
                "function" => "calculateGenericAmountReturn" 
            ],    
    
        27 => [
                "databaseName" => "investment.investment_priceInSecondaryMarket", 
                "internalName" => "investment_priceInSecondaryMarket",     
                "internalIndex" => 27,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            ],  
        
28 => [ // CHECK
        "databaseName" => "investment.investment_forSale",
        "internalName" => "investment_forSale",    
        "internalIndex" => 28,                            
        "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
        "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
        "function" => ""        
    ],


        30 => [  // This item is ONLY used to calculate the values of the base elements: PrincipalPayment and InterestPayment
                "databaseName" => "investment.investment_principalAndInterestPayment",  
                "internalName" => "investment_principalAndInterestPayment", 
                "internalIndex" => 30,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "",  
                "cashflowOperation" => "bcadd" 
            ],
        31 => [
                "databaseName" => "investment.investment_instalmentsProgress",
                "internalName" => "investment_installmentsProgress", 
                "internalIndex" => 31,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
/* See after #34
        32 => [
                "databaseName" => "investment.investment_paidInstalments",  
                "internalName" => "investment_paidInstalments",   
                "internalIndex" => 32,               
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],       
*/
        33 => [  
                "databaseName" => "investment.investment_numberOfInstalments", 
                "internalName" => "investment_numberOfInstalments",     
                "internalIndex" => 33,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""        
            ],               
        34 => [    //OK ok 
                "databaseName" => "payment.payment_capitalRepayment",  
                "internalName" => "payment_capitalRepayment",         
                "internalIndex" => 34,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateCapitalRepayment",           
                "cashflowOperation" => "bcadd",
                "linkedIndex" => 32
            ], 
        32 => [
            "databaseName" => "investment.investment_paidInstalments",
            "internalName" => "investment_paidInstalments",
            "internalIndex" => 32,
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,
            "function" => ""
        ],
    
    35 => [
                "databaseName" => "payment.payment_partialPrincipalRepayment", 
                "internalName" => "payment_partialPrincipalRepayment",  
                "internalIndex" => 35,             
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculatePartialPrincipalBuyback", 
                "cashflowOperation" => "bcadd"  
            ],       
        36 => [    //OK ok
                "databaseName" => "payment.payment_principalBuyback",
                "internalName" => "payment_principalBuyback",    
                "internalIndex" => 36,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",        
                "cashflowOperation" => "bcadd"  
            ],
        37 => [
                "databaseName" => "investment.investment_outstandingPrincipal",
                "internalName" => "investment_outstandingPrincipal", 
                "internalIndex" => 37,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateOutstandingPrincipal",          
                "cashflowOperation" => "bcadd",
                "linkedIndex" => 20037
            ],

    
        20037 => [      // Make a copy / this is to calculate the temporary outstanding principal AFTER all transactions
                        // for one investment
                "databaseName" => "investment.investment_outstandingPrincipalOriginal",  
                "internalName" => "investment_outstandingPrincipal",     // Should be commented? TO TEST   
                "internalIndex" => 20037,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateOutstandingPrincipal"
            ],    
    
    
        38 => [
                "databaseName" => "investment.investment_receivedRepayment", 
                "internalName" => "investment_receivedRepayment",
                "internalIndex" => 38,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],
        39 => [
                "databaseName" => "investment.investment_nextPaymentDate", 
                "internalName" => "investment_nextPaymentDate",
                "internalIndex" => 39,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ], 
        40 => [
                "databaseName" => "investment.investment_estimatedNextPayment",
                "internalName" => "investment_estimatedNextPayment",    
                "internalIndex" => 40,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""           
            ],
        41 => [
                "databaseName" => "investment.investment_interestGrossExpected",  
                "internalName" => "investment_interestGrossExpected",
                "internalIndex" => 41,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""      
            ],       
        42 => [   
                "databaseName" => "investment.investment_totalGrossIncome", 
                "internalName" => "investment_totalGrossIncome", 
                "internalIndex" => 42,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""        
            ],
        43 => [  //OK ok
                "databaseName" => "payment.payment_regularGrossInterestIncome", 
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_regularGrossInterestIncome",
                "internalName" => "payment_regularGrossInterestIncome",             
                "internalIndex" => 43,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",           
                "cashflowOperation" => "bcadd" 
            ],  
        44 => [    //OK ok
                "databaseName" => "payment.payment_interestIncomeBuyback",
                "internalName" => "payment_interestIncomeBuyback",      
                "internalIndex" => 44,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateInterestIncomeBuyback",       
                "cashflowOperation" => "bcadd" 
            ],
        45 => [    //OK ok
                "databaseName" => "payment.payment_delayedInterestIncome",
                "internalName" => "payment_delayedInterestIncome",         
                "internalIndex" => 45,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateDelayedInterestIncome", 
                "cashflowOperation" => "bcadd"
            ],       
        46 => [    //OK ok
                "databaseName" => "payment.payment_delayedInterestIncomeBuyback",
                "internalName" => "payment_delayedInterestIncomeBuyback",   
                "internalIndex" => 46,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateDelayedInterestIncomeBuyback",        
                "cashflowOperation" => "bcadd" 
            ],
        47 => [    //OK ok
                "databaseName" => "payment.payment_latePaymentFeeIncome",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_latePaymentFeeIncome",  
                "internalName" => "payment_latePaymentFeeIncome",        
                "internalIndex" => 47,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateLatePaymentFeeIncome",
                "cashflowOperation" => "bcadd"        
            ],
        48 => [
                "databaseName" => "payment.payment_loanRecoveries", 
                "internalName" => "payment_loanRecoveries",             
                "internalIndex" => 48,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",
                "cashflowOperation" => "bcadd"    
            ],
        49 => [ // TO CHECK WITH LATEST INVESTMENT LIST
                "databaseName" => "payment.payment_loanIncentivesAndBonus",  
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_platformIncentivesAndBonus",
                "internalName" => "payment_loanIncentivesAndBonus",
                "internalIndex" => 49,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateIncentivesAndBonus",
                "cashflowOperation" => "bcadd"    
            ], 
        50 => [
                "databaseName" => "payment.payment_loanCompensation",  
                "internalName" => "payment_compensation",        
                "internalIndex" => 50,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateLoanCompensation",
                "cashflowOperation" => "bcadd"    
            ],       
        51 => [    //OK
                "databaseName" => "payment.payment_incomeSecondaryMarket",
                "internalName" => "payment_incomeSecondaryMarket",  
                "internalIndex" => 51,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateIncomeSecondaryMarket",
                "cashflowOperation" => "bcadd"    
            ],                 
        53 => [
                "databaseName" => "investment.investment_totalLoanCost",  
                "internalName" => "investment_totalCost",             
                "internalIndex" => 53,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""        
            ],
        54 => [
                "databaseName" => "payment.payment_commissionPaid",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_commissionPaid",                                                                      // without a related loanId
                "internalName" => "payment_commissionPaid",        
                "internalIndex" => 54,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateCommissionPaid",
                "cashflowOperation" => "bcsub"    
            ],
        55 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_bankCharges", 
                "internalName" => "globalcashflowdata_bankCharges",             
                "internalIndex" => 55,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculatePlatformBankCharges",
                "cashflowOperation" => "bcsub"              
            ],       
        56 => [
                "databaseName" => "payment.payment_taxVAT",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_taxVat",
                "internalName" => "payment_taxVAT",             
                "internalIndex" => 56,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateTaxVAT",        
                "cashflowOperation" => "bcsub"    
            ],
        57 => [
                "databaseName" => "payment.payment_incomeWithholdingTax", 
                "internalName" => "payment_incomeWithholdingTax",             
                "internalIndex" => 57,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateIncomeWithholdingTax",           
                "cashflowOperation" => "bcsub"    
            ],
        58 => [
                "databaseName" => "payment.payment_interestPaymentSecondaryMarketPurchase", 
                "internalName" => "payment_interestPaymentSecondaryMarketPurchase",        
                "internalIndex" => 58,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "",  
                "cashflowOperation" => "bcsub"    
            ],
        59 => [
                "databaseName" => "payment.payment_currencyExchangeFee",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_currencyExchangeFee",
                "internalName" => "payment_currencyExchangeFee",     
                "internalIndex" => 59,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn" ,
                "cashflowOperation" => "bcsub"
            ], 
        60 => [    //OK
                "databaseName" => "payment.payment_costSecondaryMarket", 
                "internalName" => "payment_costSecondaryMarket",  
                "internalIndex" => 60,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateCostSecondaryMarket",   
                "cashflowOperation" => "bcsub"    
            ],
      
        62 => [
                "databaseName" => "investment.investment_totalNetIncome",
                "internalName" => "investment_totalNetIncome",    
                "internalIndex" => 62,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateTotalNetIncome"        
            ],
        63 => [
                "databaseName" => "investment.investment_paymentStatus", 
                "internalName" => "investment_paymentStatus",      
                "internalIndex" => 63,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""           
            ],  
        64 => [
                "databaseName" => "investment.investment_statusOfLoan", 
                "internalName" => "investment_statusOfLoan",       
                "internalIndex" => 64,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""         
            ],
        65 => [
                "databaseName" => "payment.payment_writtenOff",
                "internalName" => "payment_writtenOff",        
                "internalIndex" => 65,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateBadDebt",
            ],  
    
        66 => [    //OK--
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformDeposits", 
                "internalName" => "globalcashflowdata_platformDeposits",          
                "internalIndex" => 66,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculatePlatformDeposit",        
                "cashflowOperation" => "bcadd" 
            ],
        67 => [   //OK--
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformWithdrawals",  
                "internalName" => "globalcashflowdata_platformWithdrawals",        
                "internalIndex" => 67,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE, 
                "function" => "calculatePlatformWithdrawal", 
                "cashflowOperation" => "bcsub"                     
            ], 

        68 => [
                "databaseName" => "payment.payment_currencyFluctuation", 
                "internalName" => "payment_currencyFluctuationPositive",             
                "internalIndex" => 68,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",  
                "cashflowOperation" => "bcadd"    
            ],
        69 => [ 
                "databaseName" => "payment.payment_currencyFluctuation",  
                "internalName" => "payment_currencyFluctuationNegative",     
                "internalIndex" => 69,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericNegativeAmountReturn",
                "cashflowOperation" => "bcadd"    
            ], 
        70 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformRecoveries",  
                "internalName" => "globalcashflowdata_platformRecoveries",        
                "internalIndex" => 70,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",
                "cashflowOperation" => "bcadd"    
            ],                 
        /*71 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_platformIncentivesAndBonus", 
                "internalName" => "globalcashflowdata_platformIncentivesAndBonus",             
                "internalIndex" => 71,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => ""  
            ],*/
        72 => [ 
                "databaseName" => "payment.payment_platformCompensationPositive", 
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_platformCompensationPositive", 
                "internalName" => "payment_platformCompensationPositive",     
                "internalIndex" => 72,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculatePlatformCompensationPositive" ,
                "cashflowOperation" => "bcadd" 
            ], 
        73 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_totalPlatformCost",  
                "internalName" => "globalcashflowdata_totalPlatformCost",        
                "internalIndex" => 73,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ], 
        74 => [
                "databaseName" => "investment.investment_capitalRepaymentFromP2P",  
                "internalName" => "investment_capitalRepaymentFromP2P",        
                "internalIndex" => 74,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],   
        75 => [
                "databaseName" => "investment.investment_outstandingPrincipalFromP2P",  
                "internalName" => "investment_outstandingPrincipalFromP2P",        
                "internalIndex" => 75,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],   
        76 => [
                "databaseName" => "payment.payment_defaultInterestIncome",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_defaultInterestIncome",        
                "internalName" => "defaultInterestIncome",        
                "internalIndex" => 76,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",
                "cashflowOperation" => "bcadd" 
            ],  
        /*77 => [
                "databaseName" => "payment.payment_currencyExchangeFee",  
                "internalName" => "payment_currencyExchangeFee",        
                "internalIndex" => 77,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "" 
            ],  */ 
        78 => [
                "databaseName" => "investment.investment_sliceIdentifier",  
                "internalName" => "investment_sliceIdentifier",        
                "internalIndex" => 78,            
                "state" => "",
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "" 
            ],
        79 => [
            "databaseName" => "payment.payment_principalAndInterestPayment",
            "internalName" => "payment_principalAndInterestPayment", 
            "internalIndex" => 79,            
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            "function" => "calculatePrincipalAndInterestPayment",
        ],    
        80 => [
            "databaseName" => "payment.payment_partialPrincipalAndInterestPayment",
            "internalName" => "payment_partialPrincipalAndInterestPayment", 
            "internalIndex" => 80,            
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            "function" => "calculatePartialPrincipalAndInterestPayment",
        ],
    
        81 => [ 
                "databaseName" => "payment.payment_platformCompensationNegative",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_platformCompensationNegative",  
                "internalName" => "payment_platformCompensationNegative",     
                "internalIndex" => 81,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn" ,
                "cashflowOperation" => "bcsub" 
            ],
        82 => [ 
                "databaseName" => "investment.investment_stateOfLoan",  
                "internalName" => "investment_activeStateChange",     
                "internalIndex" => 82,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateActiveStateChange" ,
        //        "cashflowOperation" => "bcadd" 
            ],    
    
        /*83 => [
                "databaseName" => "investment.investment_reservedFunds",
                "internalName" => "investment_reservedFunds", 
                "internalIndex" => 83,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateReservedFunds",          
                "cashflowOperation" => "bcadd",
            ],
        20083 => [      // Make a copy / this is to calculate the temporary outstanding principal AFTER all transactions
                        // for one investment
                "databaseName" => "investment.investment_reservedFunds",  
                "internalName" => "investment_reservedFunds",     // Should be commented? TO TEST   
                "internalIndex" => 20083,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "verifyStatusWaitingToBeFormalized"
            ],*/
        83 => [ 
                "databaseName" => "payment.payment_myInvestment",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_investmentWithoutLoanReference",        
                "internalName" => "investment_myInvestmentActiveVerification", 
                "internalIndex" => 83,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateMyInvestment",
                "cashflowOperation" => "bcsub",               
                //"linkedIndex" => 20012
            ],
        84 => [ 
                "databaseName" => "payment.payment_reservedFunds",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_investmentWithoutLoanReference",        
                "internalName" => "investment_myInvestmentPreactive", 
                "internalIndex" => 84,           
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateReservedFunds",
                "cashflowOperation" => "bcsub",               
                //"linkedIndex" => 20012
            ],
        85 => [  //OK ok
                "databaseName" => "payment.payment_regularGrossInterestIncome", 
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_regularGrossInterestIncome",
                "internalName" => "payment_regularGrossInterestCost",             
                "internalIndex" => 85,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericNegativeAmountReturn",           
                "cashflowOperation" => "bcadd" 
            ], 
        86 => [    //OK ok 
                "databaseName" => "payment.payment_capitalRepayment",  
                "internalName" => "payment_capitalRepaymentCost",         
                "internalIndex" => 86,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericNegativeAmountReturn",           
                "cashflowOperation" => "bcadd",
            ],
        87 => [
                "databaseName" => "payment.payment_secondaryMarketSell",  
                "internalName" => "payment_secondaryMarketSell",         
                "internalIndex" => 87,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateSecondaryMarketSell",           
                "cashflowOperation" => "bcadd",
        ],
        88 => [
                "databaseName" => "payment.payment_defaultInterestIncomeRebuy", 
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_defaultInterestIncomeRebuy",
                "internalName" => "DefaultInterestIncomeRebuy",         
                "internalIndex" => 88,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",           
                "cashflowOperation" => "bcadd",
        ],
        89 => [ 
                "databaseName" => "globalcashflowdata.globalcashflowdata_incomingCurrencyExchangeTransaction",  
                "internalName" => "incomingCurrencyExchangeTransaction",     
                "internalIndex" => 89,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn" ,
                "cashflowOperation" => "bcadd" 
            ],    
        90 => [ 
                "databaseName" => "globalcashflowdata.globalcashflowdata_outgoingCurrencyExchangeTransaction",  
                "internalName" => "outgoingCurrencyExchangeTransaction",     
                "internalIndex" => 90,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn" ,
                "cashflowOperation" => "bcsub" 
            ],
        /*20087 => [      // Make a copy / this is to calculate the temporary outstanding principal AFTER all transactions
                        // for one investment
                "databaseName" => "investment.investment_reservedFunds",  
                "internalName" => "investment_myInvestmentPreactiveSumVerification",     // Should be commented? TO TEST   
                "internalIndex" => 20087,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateReservedFunds"
            ], */
    
    
        91 => [ 
                "databaseName" => "payment.payment_latePaymentFeeIncome",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_latePaymentFeeIncome",  
                "internalName" => "payment_latePaymentFeeCost",     
                "internalIndex" => 91,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericNegativeAmountReturn" ,
                "cashflowOperation" => "bcadd" 
        ],  
        92 => [ 
                "databaseName" => "payment.payment_commissionPaid",
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_commissionPaid",  
                "internalName" => "payment_commissionIncome",     
                "internalIndex" => 92,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericNegativeAmountReturn",
                "cashflowOperation" => "bcsub" 
        ], 
        93 => [ 
                "databaseName" => "payment.payment_principalRepaymentGuarantee",
                "internalName" => "payment_principal_repayment_guarantee",     
                "internalIndex" => 93,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",
                "cashflowOperation" => "bcadd" 
        ], 
        94 => [ 
                "databaseName" => "payment.payment_interestIncomeGuarantee",
                "internalName" => "payment_interest_income_guarantee",     
                "internalIndex" => 94,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericAmountReturn",
                "cashflowOperation" => "bcadd" 
        ],     
        95 => [ 
                "databaseName" => "payment.payment_writtenOff",
                "internalName" => "payment_writeoff_income",     
                "internalIndex" => 95,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGenericNegativeAmountReturn",
        ],   
        96 => [ 
            "databaseName" => "payment.payment_myInvestment",
            "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_investmentWithoutLoanReference",        
            "internalName" => "investment_myInvestmentPreactiveActived", 
            "internalIndex" => 96,           
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            "function" => "calculateMyActivedInvestment",
            //"linkedIndex" => 20012
        ],
        97 => [
            "databaseName" => "payment.payment_principalAndInterestPayment",
            "internalName" => "payment_principalAndInterestPayment2", 
            "internalIndex" => 97,            
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            "function" => "calculatePrincipalAndInterestPayment2",
            "cashflowOperation" => "bcadd",
        ],   
        98 => [
            "databaseName" => "payment.payment_partialPrincipalAndInterestPayment",
            "internalName" => "payment_partialPrincipalAndInterestPayment2", 
            "internalIndex" => 98,            
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            "function" => "calculateGenericAmountReturn",
            "cashflowOperation" => "bcadd",
            "linkedIndex" => 99
        ], 
        99 => [
            "databaseName" => "payment.payment_partialPrincipalRepayment",
            "internalName" => "payment_partialPrincipalAndInterestPayment2Extra", 
            "internalIndex" => 99,            
            "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
            "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
            "function" => "calculatePartialPrincipalAndInterestPayment2",
        ], 
    
        1000 => [
                "databaseName" => "payment.payment_disinvestment",  // Disinvestment
                "globalDatabaseName" => "globalcashflowdata.globalcashflowdata_disinvestmentWithoutLoanReference",  // Disinvestment
                "internalName" => "disinvestmentPrimaryMarket",        
                "internalIndex" => 1000,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,  
                "function" => "calculateDisinvestmentPrimaryMarket",        
                "cashflowOperation" => "bcadd",
                "linkedIndex" => 1001       // link to next index to execute
            ],
        1004 => [
                "databaseName" => "globalcashflowdata.globalcashflowdata_disinvestmentWithoutLoanReference",  // Disinvestment
                "internalName" => "disinvestmentWithoutLoanReference",        
                "internalIndex" => 1004,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,  
                "function" => "calculateDisinvestmentPrimaryMarketWinouthLoanReference",        
                "cashflowOperation" => "bcadd",
            ],
        1001 => [      // Deal with the state change of a disinvestment
                "databaseName" => "investment.investment_statusOfLoan", 
          //      "internalName" => "investment_statusOfLoanDisinvestment",        
                "internalIndex" => 1001,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateCancellationState"
            ],
    
        1002 => [
                "databaseName" => "Userinvestmentdata.userinvestmentdata_reservedAssets",  
                "internalName" => "createReservedFunds",        
                "internalIndex" => 1002,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateReservedComplex",
                "cashflowOperation" => "bcsub"
            ],   
 /*   
        1003 => [
                "databaseName" => "Userinvestment.userinvestmentdata_reservedAssets",  
                "internalName" => "createReservedFundsNoImpactCashInPlatform",        
                "internalIndex" => 1003,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateReservedSimple" 
            ],   
 */  
 
// Below are internal variables which are needed for "technical reasons" or "efficiency reasons"   
        10001 => [
                "databaseName" => "Userinvestmentdata.userinvestmentdata_outstandingPrincipal",  
                "internalName" => "userinvestmentdata_outstandingPrincipal",        
                "internalIndex" => 10001,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateTotalOutstandingPrincipal"
            
            ],   
        10002 => [
                "databaseName" => "Userinvestmentdata.userinvestmentdata_numberActiveInvestments",  
                "internalName" => "userinvestmentdata_numberActiveInvestments",        
                "internalIndex" => 10002,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateNumberOfActiveInvestments"
            ],     
/*
        10003 => [
                "databaseName" => "Userinvestmentdata.userinvestmentdata_cashInPlatform",  
                "internalName" => "userinvestmentdata_cashInPlatforms",        
                "internalIndex" => 10003,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => ""
            ],  
 */   
        10004 => [
                "databaseName" => "investment.investment_technicalStateTemp",  
                "internalName" => "investment_technicalStateTemp",        
                "internalIndex" => 10004,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateTechnicalState"
            ], 
    
        // The function of this variable is calculate the sum of each payment concept per day
        // This is done in the method calculateGlobalTotalsPerDay which write the results DIRECTLY
        // in the shadow database. Due to this the return value is of no importance
        // This function is called everytime when all transactions of a loan are analyzed.
        10005 => [ 
                "databaseName" => "globaltotals.globalTotalsPerDay",
                "internalName" => "globaltotals_globalTotalsPerDay", 
                "internalIndex" => 10005,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalsPerDay",          
            ],
    
 
    
// The following functions make up all the data (as existing in Mintos) required so we can 
// calculate the yield of a P2P
        10006 => [                                                              //Required for yield calculations 
                "databaseName" => "globaltotalsdata.globaltotalsdata_latePaymentFeeIncomePerDay",
                "internalName" => "globaltotals_latePaymentFeeIncomePerDay", 
                "internalIndex" => 10006,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalLatePaymentFeeIncomePerDay",          
            ],   
    
        10007 => [                                                              //Required for yield calculations 
                "databaseName" => "globaltotalsdata.globaltotalsdata_capitalRepaymentPerDay",
                "internalName" => "globaltotalsdata_capitalRepaymentPerDay", 
                "internalIndex" => 10007,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalCapitalRepaymentPerDay",          
            ],     
    
        10008 => [                                                              //Required for yield calculations 
                "databaseName" => "globaltotalsdata.globaltotalsdata_principalBuybackPerDay",
                "internalName" => "globaltotalsdata_principalBuybackPerDay", 
                "internalIndex" => 10008,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
               "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalPrincipalBuybackPerDay",          
            ],    
        10009 => [                                                              //Required for yield calculations 
                "databaseName" => "globaltotalsdata.globaltotalsdata_interestIncomeBuybackPerDay",
                "internalName" => "globaltotalsdata_interestIncomeBuybackPerDay", 
                "internalIndex" => 10009,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalInterestIncomeBuybackPerDay",          
            ],    
    
        10010 => [                                                              //Required for yield calculations 
                "databaseName" => "globaltotalsdata.globaltotalsdata_regularGrossInterestIncomePerDay",
                "internalName" => "globaltotalsdata_regularGrossInterestIncomePerDay", 
                "internalIndex" => 10010,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalRegularGrossInterestIncomePerDay",          
            ], 
    
        10011 => [                                                              //Required for yield calculations 
                "databaseName" => "globaltotalsdata.globaltotalsdata_myInvestmentPerDay",
                "internalName" => "globaltotalsdata_myInvestmentPerDay", 
                "internalIndex" => 10011,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalMyInvestmentPerDay",          
            ],    
    
        10012 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_secondaryMarketInvestmentPerDay",
                "internalName" => "globaltotalsdata_secondaryMarketInvestmentPerDay", 
                "internalIndex" => 10012,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalSecondaryMarketInvestmentPerDay",          
            ],   

        10013 => [                                                               //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_costSecondaryMarketPerDay",
                "internalName" => "globaltotalsdata_costSecondaryMarketPerDay", 
                "internalIndex" => 10013,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalCostSecondaryMarketPerDay",          
            ],
        10014 => [                                                              //Required for yield calculations
                "databaseName" => "payment.payment_principalAndInterestPayment",
                "internalName" => "payment_principalAndInterestPayment", 
                "internalIndex" => 10014,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateOfCapitalRepaymentOrRegularGrossInterest",    
        ],
        10015 => [                                                              //Required for yield calculations
                "databaseName" => "payment.payment_partialPrincipalAndInterestPayment",
                "internalName" => "payment_partialPrincipalAndInterestPayment", 
                "internalIndex" => 10015,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateOfPartialCapitalRepaymentOrRegularGrossInterest",    
        ],
 
        10016 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_partialPrincipalRepaymentPerDay",
                "internalName" => "globaltotalsdata_partialPrincipalRepaymentPerDay",
                "internalIndex" => 10016,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotalPartialPrincipalRepaymentPerDay",
        ],
        10017 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_delayedInterestIncomePerDay",
                "internalName" => "globaltotalsdata_delayedInterestIncomePerDay",
                "internalIndex" => 10017,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotalDelayedInterestIncomePerDay",
        ],
        10018 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_commissionPaidPerDay",
                "internalName" => "globaltotalsdata_commissionPaidPerDay",
                "internalIndex" => 10018,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotalCommissionPaidPerDay",
        ],
        10019 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_taxVATPerDay",
                "internalName" => "globaltotalsdata_taxVATPerDay",
                "internalIndex" => 10019,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotaltaxVATPerDay",
        ],
        10020 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_currencyExchangeTransactionPerDay",
                "internalName" => "globaltotalsdata_currencyExchangeTransactionPerDay",
                "internalIndex" => 10020,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotalCurrencyExchangeTransactionPerDay",
        ],
        10021 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_currencyExchangeFeePerDay",
                "internalName" => "globaltotalsdata_currencyExchangeFeePerDay",
                "internalIndex" => 10021,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotalCurrencyExchangeFeePerDay",
        ],
        10022 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_incomeWithholdingTaxPerDay",
                "internalName" => "globaltotalsdata_incomeWithholdingTaxPerDay",
                "internalIndex" => 10022,
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,
                "function" => "calculateGlobalTotalIncomeWithholdingTaxPerDay",
        ],
    
        10023 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_writtenOff",
          //      "internalName" => "investment_writtenOff",        
                "internalIndex" => 10023,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalWrittenOffPerDay" 
            ],    
        10024 => [
                "databaseName" => "Userinvestmentdata.userinvestmentdata_reservedAssets",  
                "internalName" => "userinvestmentdata_reservedAssets",        
                "internalIndex" => 10024,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_NOT_ACCUMULATIVE,   
                "function" => "calculateTotalReservedAssets"
    
            ],
        10025 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_loanIncentivesAndBonusPerDay", 
                "internalName" => "globaltotalsdata_loanIncentivesAndBonusPerDay",        
                "internalIndex" => 10025,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalLoanIncentivesAndBonusPerDay" 
            ],
        10026 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_defaultInterestIncome",
                "internalName" => "globaltotalsdata_defaultInterestIncome",        
                "internalIndex" => 10026,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalDefaultInterestIncome" 
            ],
        10027 => [                                                              //Required for yield calculations
                "databaseName" => "globaltotalsdata.globaltotalsdata_defaultInterestIncomeRebuy",
                "internalName" => "globaltotalsdata_defaultInterestIncomeRebuy",        
                "internalIndex" => 10027,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalDefaultInterestIncomeRebuy" 
            ],
        10028 => [
                "databaseName" => "globaltotalsdata.globaltotalsdata_delayedInterestIncomeBuybackPerDay",
                "internalName" => "globaltotalsdata_delayedInterestIncomeBuybackPerDay",        
                "internalIndex" => 10028,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalDelayedInterestIncomeBuybackPerDay" 
        ],
        10029 => [
                "databaseName" => "globaltotalsdata.globaltotalsdata_incomeSecondaryMarket",
                "internalName" => "globaltotalsdata_incomeSecondaryMarket",        
                "internalIndex" => 10029,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalIncomeSecondaryMarket" 
        ],
        /*10030 => [
                "databaseName" => "globaltotalsdata.globaltotalsdata_reversedLatePayment",
                "internalName" => "globaltotalsdata_reversedLatePayment",        
                "internalIndex" => 10030,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalReversedLatePayment" 
        ],*/
        10031 => [
                "databaseName" => "globaltotalsdata.globaltotalsdata_secondaryMarketSell",
                "internalName" => "globaltotalsdata_secondaryMarketSell",        
                "internalIndex" => 10031,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalSecondaryMarketSell" 
        ],
        10032 => [
                "databaseName" => "globaltotalsdata.globaltotalsdata_recoveriesPerDay",
                "internalName" => "globaltotalsdata_recoveriesPerDay",        
                "internalIndex" => 10032,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalRecoveries" 
        ],
        10033 => [ 
                "databaseName" => "globaltotalsdata.globaltotalsdata_principalRepaymentGuarantee",
                "internalName" => "globaltotalsdata_principal_repayment_guarantee",     
                "internalIndex" => 10033,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalReapymentGuarantee",
        ], 
        10034 => [ 
                "databaseName" => "globaltotalsdata.globaltotalsdata_interestIncomeGuarantee",
                "internalName" => "globaltotalsdata_interest_income_guarantee",     
                "internalIndex" => 10034,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateGlobalTotalInterestIncomeGuarantee",
        ],    
    
    
    
        // This is an accumulative value, and for the time being will never decrease. 
        // as we  don't know which mechanism(s) is/are used by the different P2P Lending Platforms
        // to remove the amount
        20065 => [ 
                "databaseName" => "Userinvestmentdata.userinvestmentdata_writtenOff",
          //      "internalName" => "investment_writtenOff",        
                "internalIndex" => 20065,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "calculateDashboard2GlobalWrittenOff" 
            ], 
    
        200037 => [
                "databaseName" => "investment.investment_outstandingPrincipal",
          //      "internalName" => "investment_writtenOff",        
                "internalIndex" => 200037,            
                "state" => WIN_FLOWDATA_VARIABLE_NOT_DONE,
                "charAcc" => WIN_FLOWDATA_VARIABLE_ACCUMULATIVE,   
                "function" => "recalculateRoundingErrors",
                "linkedIndex" => 20037
        ],   
            
    
    );
    
