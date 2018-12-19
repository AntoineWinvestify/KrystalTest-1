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
 * @author
 * @version 0.7
 * @date 2017-08-16
 * @package
 */

/**
 * 2017-08-23 version_0.2
 * link account
 *
 * 2017-09-04 version_0.3
 * Added logout
 *
 * 2017-09-14 version_0.4
 * Added function collectUserGlobalFilesParallel
 *
 * 2017-09-26 version_0.5
 * Finished to download files from Mintos and integration with Gearman
 *
 * 2017-09-28 version_0.6
 * Added configuration files so we can analyze "investment_X.xls", transactions_X.xls"
 * Added function to download Amortization Tables
 *
 * 2017-09-29 version_0.7
 * Integration of downloading amortization tables with Gearman
 * 
 * 2017-10-24 version_0.8
 * Integration of parsing amortization tables with Gearman and fileparser
 *
 * 2017-03-11 Version_0.9
 * Download all investment in function getPfpFiles
 */

/**
 * Contains the code required for accessing the website of "Mintos".
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[Not OK]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [OK, tested]
 * Parser AmortizationTables                                            [OK, tested]
 * parallelization                                                      [OK, tested]
 * 
 * 
 * 
 * 2017-10-24 version_0.8.1
 * Introduction of call back functions for translation of company specific concepts to
 * Winvestify standardized concepts 
 * 
 * 2017-03-06   version 0.8.2
 * New definition for callback of amortizationtable
 * 
 * 
 */
class mintos extends p2pCompany {
    protected $dashboard2ConfigurationParameters = [
        'outstandingPrincipalRoundingParm' => '0.01',                            // This *optional* parameter is used to determine what we 
                                                                                // consider 0 € in order to "close" an active investment
    ];
    protected $valuesTransaction = [     // All types/names will be defined as associative index in array
        [
            "A" =>  [
                    "name" => "transaction_transactionId"                       // Winvestify standardized name  NOT NEEDED, ONLY USEFULL FOR TESTING
             ],
            "B" => [ 
                [
                    "type" => "date",                                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "Y-M-D",
                                ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "C" => [
                [
                    "type" => "investment_loanId",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // trick to get the complete cell data as purpose
                                "input2" => "Loan ID: ",                        // May contain trailing spaces
                                "input3" => "",
                                "input4" => 1                                   // 'input3' is mandatory. If not found then return "global_xxxxxx"
                            ],
                    "functionName" => "extractDataFromString",
                ],
                [
                    "type" => "original_concept",                               // 
                    "inputData" => [                                            // Get the "original" Mintos concept, which is used later on
                                "input2" => "",                                 // 
                                "input3" => "Loan ID: ",
                                "input4" => 0                                   // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "extractDataFromString",
                ],
                /*[
                    "type" => "transactionDetail",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // List of all concepts that the platform can generate
                                                                                // format ["concept string platform", "concept string Winvestify"]
                                "input2" => [0 => ["Incoming client payment" => "Cash_deposit"],                // OK
                                            1 => ["Investment principal increase" => "Primary_market_investment"],
                                            2 => ["Investment share buyer pays to a seller" => "Secondary_market_investment"],
                                            3 => ["Investment principal repayment" => "Capital_repayment"],    //OK
                                            4 => ["Investment principal rebuy" => "Principal_buyback"],        // OK                               
                                            5 => ["Interest income on rebuy" => "Interest_income_buyback"],    // OK
                                            6 => ["Interest income" => "Regular_gross_interest_income"],       //
                                            7 => ["Delayed interest income on rebuy" => "Delayed_interest_income_buyback"],     // OK
                                            8 => ["Late payment fee income" =>"Late_payment_fee_income"],      // OK                                       
                                            9 => ["Delayed interest income" => "Delayed_interest_income"],  // OK
                                            10 => ["Discount/premium for secondary market transaction" => "Income_secondary_market"],   // For seller
                                            11 => ["Discount/premium for secondary market transaction" => "Cost_secondary_market"],     // for buyer
                                            12 => ["Default interest income Loan ID:" => "Late_payment_fee_income"], 
                                            13 => ["Default interest income" => "Late_payment_fee_income"],         
                                            14 => ["Client withdrawal" => "Cash_withdrawal"],
                                  //        15 => ["Outgoing currency exchange transaction" => "Currency_exchange_transaction"],
                                  //        16 => ["Incoming currency exchange transaction" => "Currency_exchange_transaction"],
                                            17 => ["Cashback bonus" => "Incentives_and_bonus"],                                    
                                            18 => ["Incoming currency exchange transaction" => "Incoming_currency_exchange_transaction"],
                                            19 => ["Outgoing currency exchange transaction" => "Outgoing_currency_exchange_transaction"],                           
                                            20 => ["Reversed late payment fee income" => "Compensation_negative"], 
                                            21 => ["FX commission with Exchange Rate" => "Currency_exchange_fee"],
                                            22 => ["Cashback bonus" => "Incentives_and_bonus"],
                                            23 => ["Affiliate bonus" => "Incentives_and_bonus"],
                                            24 => ["Investment share buyer pays to a seller. "  => "Sell_secondary_market"],
                                        ],
                                ],
                    "functionName" => "getTransactionDetail",
                ]*/
            ],
            "D" => [
                [
                    "type" => "amount",                                         // This is *mandatory* field which is required for the 
                    "inputData" => [                                            // "transactionDetail"
                                "input2" => "",                                 // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ".",                                // and its content is the result of the "getAmount" method
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ],
                
                [
                    "type" => "transactionDetail",                              // The "original field" transactionDetail in [C] will be overwritten
                    "inputData" => [                                            // but keeping in mind if the amount of current row is an income or a cost
                                "input2" => "#current.original_concept",  
                                                                                // input3 is a two dimensional array as a key, which is the 
                                                                                // original concept may be mapped to different Winvestify concept
                                                                                // depending if the amount is positive or negative
                                "input3" => [0 => ["Incoming client payment" => "Cash_deposit"],                // OK
                                            1 => ["Investment principal increase" => "Primary_market_investment"],
                                            2 => ["Investment share buyer pays to a seller." => "Secondary_market_investment"],
                                            3 => ["Investment principal repayment" => "Capital_repayment"],    //OK
                                            4 => ["Investment principal rebuy" => "Principal_buyback"],        // OK                               
                                            5 => ["Interest income on rebuy" => "Interest_income_buyback"],    // OK
                                            6 => ["Interest income" => "Regular_gross_interest_income"],       //
                                            7 => ["Delayed interest income on rebuy" => "Delayed_interest_income_buyback"],     // OK
                                            9 => ["Delayed interest income" => "Delayed_interest_income"],  // OK
                                            10 => ["Default interest income on rebuy Rebuy" => "Default_interest_income_rebuy"],
                                            11 => ["Default interest income" => "Default_interest_income"],                 
                                            12 => ["Late payment fee income" =>"Late_payment_fee_income"],      // OK                                             
                                            13 => ["Discount/premium for secondary market transaction" => "Income_secondary_market"],   // For seller
                                            14 => ["Discount/premium for secondary market transaction" => "Cost_secondary_market"],     // for buyer                                    
                                            15 => ["Client withdrawal" => "Cash_withdrawal"],
 //                                           15 => ["Outgoing currency exchange transaction" => "Currency_exchange_transaction"],
 //                                           16 => ["Incoming currency exchange transaction" => "Currency_exchange_transaction"],
                                            18 => ["Incoming currency exchange transaction" => "Incoming_currency_exchange_transaction"],
                                            19 => ["Outgoing currency exchange transaction" => "Outgoing_currency_exchange_transaction"],                           
                                            20 => ["Reversed late payment fee income" => "Compensation_negative"], 
                                            21 => ["FX commission with Exchange Rate" => "Currency_exchange_fee"],
                                            22 => ["Cashback bonus" => "Incentives_and_bonus"],
                                            23 => ["Affiliate bonus" => "Incentives_and_bonus"],
                                            24 => ["Investment share buyer pays to a seller. "  => "Sell_secondary_market"],
                                            

                                        ]                    
                                ],
                    "functionName" => "getComplexTransactionDetail",
                ],                
            ],      
            "E" => [
                [
                    "type" => "transaction_balance",                            // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
            ],      
            "F" => [
                [
                    "type" => "currency",                                       // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                ],
                [
                    "type" => "conceptChars",                                   // Winvestify standardized name
                    "inputData" => [
				"input2" => "#current.internalName",            // get Winvestify concept
                                ],
                    "functionName" => "getConceptChars",
                ]                
             ],
        ]
    ];

    protected $valuesInvestment = [
        [
            "A" =>  [
                [
                "type" => "investment_country",                                 // Winvestify standardized name  OK              
                "functionName" => "getCountry",
                ],
             ],
            "B" =>  [
                "name" => "investment_loanId"                                   // Winvestify standardized name  OK
             ],
            "C" =>  [
                [
                    "type" => "investment_issueDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D.M.Y",

                                ],
                    "functionName" => "normalizeDate",
                ]
             ],
            "D" =>  [
                "name" => "investment_loanType"                                 // Winvestify standardized name   OK
             ],
            "E" =>  [
                "name" => "investment_amortizationMethod"                       // Winvestify standardized name  OK
             ],
            "F" =>  [
                "name" => "investment_loanOriginator"                           // Winvestify standardized name  OK
             ],
            "G" =>  [
                [
                    "type" => "investment_fullLoanAmount",                      // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "H" =>  [
                [
                    "type" => "investment_remainingPrincipalTotalLoan",         // THIS FIELD IS NOT NEEDED?
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "I" =>  [
                [
                    "type" => "investment_nextPaymentDate",                     // Winvestify standardized name
                    "inputData" => [
				"input2" => "D.M.Y",
                                ],
                    "functionName" => "normalizeDate",
                ]
             ],
            "J" =>  [
                [
                    "type" => "investment_estimatedNextPayment",                // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "K" =>  [
                "name" => "investment_LTV"                                      // Winvestify standardized name   OK
             ],
            "L" => [
                [
                    "type" => "investment_nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "100",
                        "input3" => 2,
                        "input4" => "."
                    ],
                    "functionName" => "handleNumber",
                ]
            ],
            /*"M" =>  [
                "name" => "investment_numberOfInstalments"                      // Winvestify standardized name. This is, 
                                                                                // at time of investing, the number of
                                                                                // instalments.
             ],*/
            "N" =>  [
                "name" => "investment_paidInstalments"                          // Winvestify standardized name OK
                ],
            "O" =>  [
                "name" => "investment_originalState"                            // Winvestify standardized name
             ],
            "P" =>  [
                "name" => "investment_buyBackGuarantee"                         // Winvestify standardized name  OK
             ],
            "Q" =>  [
                [
                    "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment_paidInstalmentsProgress",             // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "#current.investment_paidInstalments",
                                "input3" => "#current.investment_numberOfInstalments",
                                ],
                    "functionName" => "getProgressString",
                ],
             ],
            "R" =>  [
                [
                    "type" => "investment_myInvestmentDate",                    // Winvestify standardized name OK
                    "inputData" => [
				"input2" => "D.M.Y",
                                ],
                    "functionName" => "normalizeDate",
                ],
                [
                    "type" => "investment_dateOfPurchase",                      // Winvestify standardized name OK
                    "inputData" => [
				"input2" => "D.M.Y",
                                ],
                    "functionName" => "normalizeDate",
                ]
             ],
            "S" =>  [
                [
                    "type" => "investment_capitalRepaymentFromP2P",             // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "T" =>  [
                [
                    "type" => "investment_outstandingPrincipalFromP2P",         // Winvestify standardized name OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "U" =>  [
                [
                    "type" => "investment_secondaryMarketInvestment",           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "V" =>  [
                [
                    "type" => "investment_priceInSecondaryMarket",              // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "W" =>  [
                [
                    "type" => "investment_discount_premium",                    // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
             ],
            "X" =>  [
                [
                    "type" => "investment_currency",                            // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                ],
                /*[
                    "type" => "investment_statusOfLoan",                        // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "#current.investment_originalState",  // set to "ACTIVE"
                                ],
                    "functionName" => "getDefaultValue",
                ],*/
                [
                    "type" => "investment_typeOfInvestment",                    // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => 99,                                 // set to "Not provided by P2P
                                ],
                    "functionName" => "getDefaultValue",
                
                ],
            ]
        ]
    ];

    protected $valuesAmortizationTable = [
        0 => [
            [
                "type" => "amortizationtable_scheduledDate",                    // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "D.M.Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        1 => [
            [
                "type" => "amortizationtable_capitalRepayment",                 // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        2 => [
            [
                "type" => "amortizationtable_interest",                         // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        4 => [
            [
                "type" => "amortizationtable_capitalAndInterestPayment",        // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        5 => [
            [
                "type" => "amortizationtable_paymentDate",                      // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "D.M.Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        6 => [
                [
                    "type" => "amortizationtable_paymentStatus",                          
                    "inputData" => [                                            
                                "input2" => "",                                  
                                "input3" => "",
                                "input4" => 0                                   
                            ],
                    "functionName" => "extractDataFromString",
                ],        
                [
                    "type" => "amortizationtable_paymentStatusOriginal",    
                    "inputData" => [                                       
                                "input2" => "",                        
                                "input3" => "",
                                "input4" => 0                             
                            ],
                    "functionName" => "extractDataFromString",
                ],
        ]
    ];
    

    protected $valuesExpiredLoan = [                                            // We are only interested in the investment_loanId
        [
            "A" =>  [
                [
                    "type" => "investment_country",                             // Winvestify standardized name  OK              
                    "functionName" => "getCountry",
                    ],
                 ],
            "B" => [
                    "name" => "investment_loanId"                               // Winvestify standardized name  OK
                 ],
            "D" =>  [
                "name" => "investment_loanType"                                 // Winvestify standardized name   OK
             ],

            "E" =>  [
                "name" => "investment_amortizationMethod"                       // Winvestify standardized name  OK
             ],

            "F" =>  [
                    "name" => "investment_loanOriginator"                       // Winvestify standardized name  OK
                 ],
            "G" =>  [
                    [
                        "type" => "investment_fullLoanAmount",                  // Winvestify standardized name   OK
                        "inputData" => [
                                    "input2" => "",
                                    "input3" => ".",
                                    "input4" => 16
                                    ],
                        "functionName" => "getAmount",
                    ]
                 ],

            /*        
            "H" =>  [
                [
                    "type" => "investment_remainingPrincipal",                  // Winvestify standardized name [remainder of TOTAL loan?
                    "inputData" => [
                                "input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]           
             ],
    */
            "J" => [
                [
                    "type" => "investment_nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "100",
                        "input3" => 2,
                        "input4" => "."
                    ],
                    "functionName" => "handleNumber",
                ]
            ],
            "M" =>  [
                    "name" => "investment_originalState"                        // Winvestify standardized name  OK
                 ], 
            "N" =>  [
                    "name" => "investment_buyBackGuarantee"                     // Winvestify standardized name  OK
                 ],
            "R" =>  [
                [
                    "type" => "investment_outstandingPrincipalFromP2P",                // Winvestify standardized name OK 
                    "inputData" => [
                                "input2" => "",
                                "input3" => ".",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ],
                /*[
                    "type" => "investment_statusOfLoan",                        // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "#current.investment_originalState",                            
                                ],
                    "functionName" => "getDefaultValue",
                ],*/
            ],
            "V" =>  [
                [
                    "type" => "investment_currency",                            // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                    ],
                ]
        ]
    ];
    
    
    protected $transactionConfigParms = [
        [
            'offsetStart'   => 1,
            'offsetEnd'     => 0,
            'sortParameter' => array("date","investment_loanId")                // used to "sort" the array and use $sortParameter(s) as prime index.
                                
        ]
    ];
 
    protected $investmentConfigParms = [
        [
            'offsetStart'   => 1,
            'offsetEnd'     => 0,
            'sortParameter' => array("investment_loanId")                       // used to "sort" the array and use $sortParameter as prime index.
        ]
    ]; 
    
    protected $amortizationConfigParms = [
        [
            'offsetStart'   => 1,
            'offsetEnd'     => 0,
            'sortParameter' => array("investment_loanId")                       // used to "sort" the array and use $sortParameter as prime index.
        ]
    ];
  
    protected $expiredLoanConfigParms = [
        [
            'offsetStart'   => 1,
            'offsetEnd'     => 0,
            'sortParameter' => array("investment_loanId")                       // used to "sort" the array and use $sortParameter as prime index.
        ]
    ]; 
    
    
     protected $valuesControlVariables = [
        [
        "myWallet" => [
            [
                "type" => "myWallet",                                           // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        "activeInvestments" => [
            [
                "type" => "activeInvestments",                                  // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "1",
                    "input3" => "0",
                    "input4" => ".",
                ],
                "functionName" => "handleNumber",
            ]
        ],  
        "outstandingPrincipal" => [
            [
                "type" => "outstandingPrincipal",                               // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        "reservedFunds" => [
            [
                "type" => "reservedFunds",                                      // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ],
        ],
        ]
    ];      
    
    protected $controlVariablesConfigParms = [
        [
            'offsetStart' => 0,
            'offsetEnd' => 0,
        ]
    ];
    
    protected $callbacks = [
        "investment" => [
            "parserDataCallback" => [
                "investment_buyBackGuarantee" => "translateInvestmentBuyBackGuarantee",
                "investment_loanType" => "translateLoanType",
                "investment_amortizationMethod" => "translateAmortizationMethod",  
                //"investment_statusOfLoan" => "translateOriginalLoanState"
            ]
        ],
        "expiredLoan" => [
            "parserDataCallback" => [
                "investment_buyBackGuarantee" => "translateInvestmentBuyBackGuarantee",
                "investment_loanType" => "translateLoanType",
                "investment_amortizationMethod" => "translateAmortizationMethod",  
                //"investment_statusOfLoan" => "translateOriginalLoanState"
            ]
        ]
    ];
    
    protected $callbackAmortizationTable = [
        "parserDataCallback" => [
            "amortizationtable_paymentStatus" => "translateAmortizationPaymentStatus",
        ]
    ];    
    
    
     protected $investmentHeader = array('A' => 'Country', 'B' => 'ID', 'C' => 'Issue Date', 'D' => 'Loan Type',
            'E' => 'Amortization Method', 'F' => 'Loan Originator', 'G' => 'Loan Amount', 'H' => 'Remaining Principal', 'I' => 'Next Payment',
            'J' => 'Estimated Next Payment', 'K' => 'LTV', 'L' => 'Interest Rate', 'M' => 'Remaining Term', 'N' => 'Payments Received', 'O' => 'Status', 
            'P' => 'Buyback Guarantee', 'Q' => 'My Investments', 'R' => 'Date of Investment' , 'S' => 'Received Payments', 
            'T' => 'Outstanding Principal', 'U' => 'Amount in Secondary Market', 'V' => 'Price', 'W' => 'Discount/Premium', 'X' => 'Currency'
            );
     
      protected $expiredLoansHeader = array('A' => 'Country', 'B' => 'ID', 'C' => 'Issue Date', 'D' => 'Loan Type',
            'E' => 'Amortization Method', 'F' => 'Loan Originator', 'G' => 'Loan Amount', 'H' => 'Remaining Principal', 
            'I' => 'LTV', 'J' => 'Interest Rate', 'K' => 'Initial Term', 'L' => 'Payments Received', 'M' => 'Status', 
            'N' => 'Buyback Guarantee', 'O' => 'My Investments', 'P' => 'Date of Investment' , 'Q' => 'Received Payments', 
            'R' => 'Outstanding Principal', 'S' => 'Amount in Secondary Market', 'T' => 'Price', 'U' => 'Discount/Premium', 
            'V' => 'Currency', 'W' => 'Finished', 'X' => 'Rebuy reasons');
      
    protected $transactionHeader = array(
        'A' => 'Transaction ID', 
        'B' => 'Date',
        'C' => 'Details',
        'D' => 'Turnover',
        'E' => 'Balance', 
        'F' => 'Currency'
            );
    
    
       
    function __construct() {
        parent::__construct();
        $this->i = 0;
        $this->typeFileTransaction = "xlsx";
        $this->typeFileInvestment = "xlsx";
        $this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";
        //$this->minEmptySize = 3104;
        //$this->maxEmptySize = 3400;
        
        //$this->loanIdArray = array("15058-01","12657-02 ","14932-01 ");
        //$this->maxLoans = count($this->loanIdArray);
        // Do whatever is needed for this subsclass
    }


    
    /**
     *
     * 	Collects the marketplace data. We must login first in order to obtain the marketplace data
     * 	@return array	Each investment option as an element of an array
     *
     */
    function collectCompanyMarketplaceData() {

    }

    /**
     *
     * 	Checks if the user can login to its portal. Typically used for linking a company account
     * 	to our account
     *
     * 	@param string	$user		username
     * 	@param string	$password	password
     * 	@return	boolean	true: 		user has succesfully logged in. $this->mainPortalPage contains the entry page of the user portal
     * 					false: 		user could not log in
     *
     */
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY YYYYYYYYY  DURING LOGIN PROCESS
          $credentials['_csrf_token'] = "XXXXX";
         */

        //First we need get the $csrf token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
        $csrf = $input[0]->getAttribute('value'); //this is the csrf token

        $credentials['_username'] = $user;
        $credentials['_password'] = $password;
        $credentials['_csrf_token'] = $csrf;
        $credentials['_submit'] = '';


        if (!empty($options)) {
            foreach ($options as $key => $option) {
                $credentials[$key] = $option[$key];
            }
        }

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login


        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        // echo $str;

        $confirm = false;

        $as = $dom->getElementsByTagName('a');
        $this->verifyNodeHasElements($as);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
        }
        foreach ($as as $a) {
            // echo 'Entrando ' . 'href value; ' . $a->getAttribute('herf') . ' node value' . $a->nodeValue . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Overview') {
                //echo 'a encontrado' . HTML_ENDOFLINE;
                $confirm = true;
            }

            //Get logout url
            if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                $url = $a->getAttribute('href');
            }
        }


        if ($confirm) {
            return true;
        }
        return false;
    }

/**
     *
     * 	Checks if the user can login to its portal. Typically used for linking a company account
     * 	to our account
     *
     * 	@param string	$user		username
     * 	@param string	$password	password
     * 	@return	boolean	true: 		user has succesfully logged in. $this->mainPortalPage contains the entry page of the user portal
     * 					false: 		user could not log in
     *
     */
    function companyUserLoginMultiAccount($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY YYYYYYYYY  DURING LOGIN PROCESS
          $credentials['_csrf_token'] = "XXXXX";
         */

        //First we need get the $csrf token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
        $csrf = $input[0]->getAttribute('value'); //this is the csrf token

        $credentials['_username'] = $user;
        $credentials['_password'] = $password;
        $credentials['_csrf_token'] = $csrf;
        $credentials['_submit'] = '';


        if (!empty($options)) {
            foreach ($options as $key => $option) {
                $credentials[$key] = $option[$key];
            }
        }

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login


        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        // echo $str;

        $confirm = false;

        $as = $dom->getElementsByTagName('a');
        $this->verifyNodeHasElements($as);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
        }
        foreach ($as as $a) {
            // echo 'Entrando ' . 'href value; ' . $a->getAttribute('herf') . ' node value' . $a->nodeValue . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Overview') {
                //echo 'a encontrado' . HTML_ENDOFLINE;
                $confirm = true;
            }

            //Get logout url
            if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                $url = $a->getAttribute('href');
            }
        }

        

        if ($confirm) {
            return true;
        }
        return false;
    }
    
    /**
     * Download investments and cash flow files and collect control variables
     * 
     * @param string $str It is the web converted to string of the company.
     * @return array Control variables.
     */
    function collectUserGlobalFilesParallel($str = null) {

        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 1:
                //Login fixed
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $input = $this->getElements($dom, 'input', 'name', '_csrf_token');
                $csrf = $input[0]->getAttribute('value'); //this is the csrf token

                $this->credentials['_username'] = $this->user;
                $this->credentials['_password'] = $this->password;
                $this->credentials['_csrf_token'] = $csrf;
                $this->credentials['_submit'] = '';

                $this->print_r2($this->credentials);

                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                unset($this->credentials);
                break;
            case 2:
                $this->idForSwitch++;
                //echo $str;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;  //Check if works
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;
                $resultLogin = false;
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": Check login \n";
                }
                $as = $dom->getElementsByTagName('a');
                $this->verifyNodeHasElements($as);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($as as $a) {
                    //echo $a->nodeValue . SHELL_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Overview') {
                        if (Configure::read('debug')) {
                            echo __FUNCTION__ . " " . __LINE__ . ": Login found";
                        }
                        $resultLogin = true;
                        break;
                    }
                }

                if (!$resultLogin) {   // Error while logging in
                    if (Configure::read('debug')) {
                        echo __FUNCTION__ . " " . __LINE__ . ": Error login \n";
                    }
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Mintos login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    echo "Error login";   
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            ////////DOWNLOAD FILE
            case 4:
                //$credentialsFile = 'purchased_from=&purchased_till=&statuses%5B%5D=256&statuses%5B%5D=512&statuses%5B%5D=1024&statuses%5B%5D=2048&statuses%5B%5D=8192&statuses%5B%5D=16384&+=256&+=512&+=1024&+=2048&+=8192&+=16384&listed_for_sale_status=&min_interest=&max_interest=&min_term=&max_term=&with_buyback=&min_ltv=&max_ltv=&loan_id=&sort_field=&sort_order=DESC&max_results=20&page=1&include_manual_investments=';
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->investmentHeader;
                $url = array_shift($this->urlSequence);
                $referer = array_shift($this->urlSequence);
                $credentials = array_shift($this->urlSequence);
                $headersJson = array_shift($this->urlSequence);
                $headers = strtr($headersJson, array('{$baseUrl}' => $this->baseUrl));
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": headers are : " . $headers . "\n";
                }
                $headers = json_decode($headers, true);
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": headers decode are : " . $headers . "\n";
                }
                //$referer = 'https://www.mintos.com/en/my-investments/?currency=978&statuses[]=256&statuses[]=512&statuses[]=1024&statuses[]=2048&statuses[]=8192&statuses[]=16384&sort_order=DESC&max_results=20&page=1';
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, $referer, $credentials, $headers, $this->fileName);
                //echo 'Downloaded';
                break;
            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }               
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){  //Compare mine type for mintos files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }
                //$size = filesize($this->getFolderPFPFile() . DS . $this->fileName);
                if (Configure::read('debug')) {
                    echo 'File size:     ' . $size;
                }
               
                //if ($size < $this->minEmptySize || $size > $this->maxEmptySize) {
                    $headerError = $this->compareHeader();
                    if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                        return $this->getError(__LINE__, __FILE__, $headerError);
                    } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                        return $this->getError(__LINE__, __FILE__, $headerError);
                    }
                    else if( $headerError === WIN_ERROR_FLOW_EMPTY_FILE ) {
                         unlink($this->getFolderPFPFile() . DS . $this->fileName);
                    }
                /*} else {
                    unlink($this->getFolderPFPFile() . DS . $this->fileName);
                } */
                
                
                if(empty($this->tempUrl['transactionPage'])){                 
                    $this->tempUrl['transactionPage'] = array_shift($this->urlSequence);
                    //Url preparation for download multiple tramsaction files
                    $this->numberOfFiles = 0;
                    $this->tempUrl['downloadTransactionUrl'] = array_shift($this->urlSequence);
                    $this->tempUrl['transactionReferer'] = array_shift($this->urlSequence);         
                    $this->tempUrl['transactionsCredentials'] = array_shift($this->urlSequence);
                    $this->tempUrl['headersJson'] = array_shift($this->urlSequence);
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['transactionPage']);
                break;
            case 6:
                $continue = $this->downloadTimePeriod($this->dateInit, $this->period);

                $dateInit = date("d.m.Y",  strtotime($this->dateInitPeriod));      //date("d.m.Y", strtotime($this->dateInit));
                $dateFinish = date("d.m.Y",  strtotime($this->dateFinishPeriod));  //date('d.m.Y',strtotime($this->dateFinish));
                
                $referer = strtr($this->tempUrl['transactionReferer'], array('{$date1}' => $dateInit));
                $referer = strtr($referer, array('{$date2}' => $dateFinish));
                echo "referer " . $referer;
                
                $credentials = strtr($this->tempUrl['transactionsCredentials'], array('{$date1}' => $dateInit));
                $credentials = strtr($credentials, array('{$date2}' => $dateFinish));
                echo "credentials " . $credentials;
                
                $headers = strtr( $this->tempUrl['headersJson'], array('{$baseUrl}' => $this->baseUrl));
                $headers = json_decode($headers, true);
                echo "headers " . $headers;
                
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "_" . $this->numPartFileTransaction . "." . $this->typeFileTransaction;
                $this->numPartFileTransaction++;
                $this->headerComparation = $this->transactionHeader;
                if(!$continue){
                    if ($this->originExecution == WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) {
                        $this->idForSwitch++;
                    }
                    else {
                        array_shift($this->urlSequence);
                        array_shift($this->urlSequence);
                        array_shift($this->urlSequence);
                        array_shift($this->urlSequence);
                        $this->idForSwitch = 9;
                    }
                }
                else {
                     $this->idForSwitch = 5;
                }
                $this->getPFPFileMulticurl($this->tempUrl['downloadTransactionUrl'], $referer, $credentials, $headers, $this->fileName);
                break;
            case 7:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){  //Compare mine type for mintos files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }
                //$size = filesize($this->getFolderPFPFile() . DS . $this->fileName);
                if (Configure::read('debug')) {
                    echo 'File size:     ' . $size;
                }
      
                //if ($size < $this->minEmptySize || $size > $this->maxEmptySize) {
                    $headerError = $this->compareHeader();
                    if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                        return $this->getError(__LINE__, __FILE__, $headerError);
                    } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                        return $this->getError(__LINE__, __FILE__, $headerError);
                    }
                    else if( $headerError === WIN_ERROR_FLOW_EMPTY_FILE ) {
                         unlink($this->getFolderPFPFile() . DS . $this->fileName);
                    }
                /*} else {
                    unlink($this->getFolderPFPFile() . DS . $this->fileName);
                }*/
                   
                $this->fileName = $this->nameFileExpiredLoan . $this->numFileExpiredLoan . "." . $this->typeFileExpiredLoan;
                $this->headerComparation = $this->expiredLoansHeader;
                $url = array_shift($this->urlSequence);
                $referer = array_shift($this->urlSequence);
                $credentials = array_shift($this->urlSequence);
                $headersJson = array_shift($this->urlSequence);
                $headers = strtr($headersJson, array('{$baseUrl}' => $this->baseUrl));
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": headers are : " . $headers . "\n";
                }
                $headers = json_decode($headers, true);
                if (Configure::read('debug')) {
                    echo __FUNCTION__ . " " . __LINE__ . ": headers decode are : " . $headers . "\n";
                }
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, $referer, $credentials, $headers, $this->fileName);
                break;
            case 8:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){  //Compare mine type for mintos files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                else if( $headerError === WIN_ERROR_FLOW_EMPTY_FILE ) {
                     unlink($this->getFolderPFPFile() . DS . $this->fileName);
                }

                
                $this->idForSwitch++;     
            case 9:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"){  //Compare mine type for mintos files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                else if( $headerError === WIN_ERROR_FLOW_EMPTY_FILE ) {
                    unlink($this->getFolderPFPFile() . DS . $this->fileName);
                }
                $this->idForSwitch++;          
                $this->getCompanyWebpageMultiCurl();
                break; 
            //////LOGOUT
            case 10:
                echo "Read Globals";
                //echo $str;
                $dom = new DOMDocument;  //Check if works
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $boxes = $this->getElements($dom, 'ul', 'id', 'mintos-boxes');
                $eurDashboardFound = false;
                foreach($boxes as $keyBox => $box){
                    $boxValue = $box->nodeValue;
                    echo $boxValue;
                    if(strpos($boxValue, '€') !== false){
                        echo 'Dashboard with € found';
                        $eurDashboardFound = true;
                    }
                    else{
                        continue;
                    }

                    //echo $box->nodeValue;
                    //echo "BOX NUMBER: =>" . $keyBox;
                    $tds = $box->getElementsByTagName('td');
                    $this->verifyNodeHasElements($tds);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    foreach($tds as $key => $td){
                        echo $key . " => " . $td->nodeValue . SHELL_ENDOFLINE;
                        $tempArray["global"]["myWallet"] = $tds[1]->nodeValue;
                        $tempArray["global"]["outstandingPrincipal"] = $tds[3]->nodeValue;   
                       //$tempArray["global"]["totalEarnedInterest"] = $this->getMonetaryValue($tds[21]->nodeValue);


                    }
                    $divs = $box->getElementsByTagName('div');
                    $this->verifyNodeHasElements($divs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    /*foreach($divs as $key => $div){
                        //echo $key . " => " . $div->nodeValue . SHELL_ENDOFLINE;
                        $tempArray["global"]["profitibility"] = $this->getPercentage($divs[6]->nodeValue);
                    }*/
                    break;

                }
                
                if(!$eurDashboardFound){
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_CURRENCY);
                }
                
                $lis = $boxes[0]->getElementsByTagName('li');
                $this->verifyNodeHasElements($lis);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $divs = $lis[2]->getElementsByTagName('div');
                $this->verifyNodeHasElements($divs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $tempArray["global"]["activeInvestment"] = trim($divs[2]->nodeValue);
                print_r($tempArray["global"]);
                return $tempArray["global"];
        }
    }
    
    /**
     * Get amortization tables of user investments
     * @param string $str It is the web converted to string of the company.
     * @return array html of the tables
     */
    function collectAmortizationTablesParallel($str = null){
        switch ($this->idForSwitch) {
            case 0:
                $this->loanTotalIds = $this->loanIds;
                $this->loanKeys = array_keys($this->loanIds);
                $this->loanIds = array_values($this->loanIds);
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                echo 'Next: ' . $next . SHELL_ENDOFLINE;
                break;
            case 1:
                //Login fixed
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $input = $this->getElements($dom, 'input', 'name', '_csrf_token');

                $csrf = $input[0]->getAttribute('value'); //this is the csrf token

                $this->credentials['_username'] = $this->user;
                $this->credentials['_password'] = $this->password;
                $this->credentials['_csrf_token'] = $csrf;
                $this->credentials['_submit'] = '';

                $this->print_r2($this->credentials);

                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                unset($this->credentials);
                break;
            case 2:
                $this->idForSwitch++;
                //echo $str;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;  //Check if works
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;
                $resultLogin = false;
                echo 'Check login' . SHELL_ENDOFLINE;
                $as = $dom->getElementsByTagName('a');
                $this->verifyNodeHasElements($as);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($as as $a) {
                    echo $a->nodeValue . SHELL_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Overview') {
                        echo 'FindLOGGGGGGIN\n';
                        $resultLogin = true;
                        break;
                    }
                }

                if (!$resultLogin) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Mintos login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 4:
                if(empty($this->tempUrl['investmentUrl'])){
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);
                }
                echo "Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url =  $this->tempUrl['investmentUrl'] . $this->loanIds[$this->i];
                echo "the table url is: " . $url;
                $this->i = $this->i + 1;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url);                        // Read individual investment
                break;
            case 5:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                echo "Read table: ";
                $tables = $dom->getElementsByTagName('table');
                $this->verifyNodeHasElements($tables);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach($tables as $table) {
                    if ($table->getAttribute('class') == 'loan-table') {
                        $AmortizationTable = new DOMDocument();
                        $clone = $table->cloneNode(TRUE);                       // Clean the table
                        $AmortizationTable->appendChild($AmortizationTable->importNode($clone,TRUE));
                        $AmortizationTableString = $AmortizationTable->saveHTML();
                        echo $AmortizationTableString;
                        $revision = $this->structureRevisionAmortizationTable($AmortizationTableString,$this->tableStructure);
                        if($revision){
                            echo ' ok';
                            $this->tempArray['tables'][$this->loanIds[$this->i - 1]] = $AmortizationTableString; //Save the html string in temp array
                            $this->tempArray['correctTables'][$this->loanKeys[$this->i - 1]] = $this->loanIds[$this->i - 1];
                        } else{
                            $this->tempArray['errorTables'][$this->loanKeys[$this->i - 1]] = $this->loanIds[$this->i - 1];
                        }                     
                        break;
                    }
                }

                echo "Is " . $this->i . " and limit is " . $this->maxLoans;
                if ($this->i < $this->maxLoans) {
                    echo "Read again";
                    $this->idForSwitch = 4;
                    $next = $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentUrl'] . $this->loanId[$this->i]);
                    break;
                }
                else {
                    return $this->tempArray;
                }
        }

    }

    /**
     *
     * 	Logout of user from the company portal.
     * 	@param type $url
     * 	@return boolean	true: user has logged out
     *
     */
    function companyUserLogout($url) {

        $str =  $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str); //Load page with the url
        $dom->preserveWhiteSpace = false;
        $as = $dom->getElementsByTagName('a');
        $this->verifyNodeHasElements($as);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
        }
        foreach ($as as $a) { //get logout url
            if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                $logoutUrl = $a->getAttribute('href');
                break;
            }
        }

        $this->doCompanyLogout($logoutUrl); //logout
        return true;
    }

    function companyUserLogoutMultiCurl($str) {
        echo $this->idForSwitch . SHELL_ENDOFLINE;
        //Get logout url
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $as = $dom->getElementsByTagName('a');
        $this->verifyNodeHasElements($as);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
        }
        foreach ($as as $a) {
            echo $a->getAttribute('class') . HTML_ENDOFLINE;
            if ($a->getAttribute('class') == 'logout main-nav-logout u-c-gray') {
                $logoutUrl = $a->getAttribute('href');
                break;
            }
        }
        echo 'Logout:' . $logoutUrl . HTML_ENDOFLINE;
        $this->doCompanyLogoutMultiCurl(null,$logoutUrl); //Logout

    }
    
    
    
    
    /**
     * Function to translate the company specific loan type to the Winvestify standardized
     * loan type
     * @param string $inputData     company specific loan type
     * @return int                  Winvestify standardized loan type
     */
    public function translateLoanType($inputData) {
        $type = WIN_TYPEOFLOAN_UNKNOWN;
        $inputData = mb_strtoupper(trim($inputData));
        switch ($inputData){
            case "MORTGAGE LOAN":
                $type = WIN_TYPEOFLOAN_MORTGAGE;
                break;
            case "BUSINESS LOAN":
                $type = WIN_TYPEOFLOAN_BUSINESSLOAN;
                break;
            case "CAR LOAN":
                $type = WIN_TYPEOFLOAN_CARLOAN;
                break;
            case "PERSONAL LOAN":
                $type = WIN_TYPEOFLOAN_PERSONAL;
                break;
            case "SHORT-TERM LOAN":
                $type = WIN_TYPEOFLOAN_SHORTTERM;
                break;
            case "AGRICULTURAL LOAN":
                $type = WIN_TYPEOFLOAN_AGRICULTURAL;
                break;
            case "INVOICE FINANCING":
                $type = WIN_TYPEOFLOAN_INVOICETRADING;
                break;
            case "PAWNBROKING LOAN":
                $type = WIN_TYPEOFLOAN_PAWNBROKING;
                break;
        }
        return $type;
    }
    
    /**
     * Function to translate the company specific amortization method to the Winvestify standardized
     * amortization type
     * @param string $inputData     company specific amortization method
     * @return int                  Winvestify standardized amortization method
     */
    public function translateAmortizationMethod($inputData) {
        $type = WIN_AMORTIZATIONMETHOD_UNKNOWN;
        $inputData = mb_strtoupper(trim($inputData));
        switch ($inputData){
            case "FULL":
                $type = WIN_AMORTIZATIONMETHOD_FULL;
                break;
            case "PARTIAL":    
                $type = WIN_AMORTIZATIONMETHOD_PARTIAL;
                break;
            case "INTEREST-ONLY":
                $type = WIN_AMORTIZATIONMETHOD_INTEREST_ONLY;
                break;
            case "BULLET":
                $type = WIN_AMORTIZATIONMETHOD_BULLET;
                break;
        }
        return $type;
    }   
    
    /**
     * Function to translate the company specific type of investment to the Winvestify standardized
     * type of investment
     * @param string $inputData     company specific type of investment
     * @return int                  Winvestify standardized type of investment
     */
    public function translateTypeOfInvestment($inputData) { //We don't have this in mintos
 
    }
    
    /**
     * Function to translate the company specific payment frequency to the Winvestify standardized
     * payment frequency
     * @param string $inputData     company specific payment frequency
     * @return int                  Winvestify standardized payment frequency
     */
    public function translatePaymentFrequency($inputData) {  //We don't have this in mintos
        
    }
        
    /**
     * Function to translate the type of investment market to an to the Winvestify standardized
     * investment market concept
     * @param string $inputData     company specific investment market concept
     * @return int                  Winvestify standardized investment marke concept
     */
    public function translateInvestmentMarket($inputData) {  //We don't have this in mintos
        
    }
    
    /**
     * Function to translate the company specific investmentBuyBackGuarantee to the Winvestify standardized
     * investmentBuyBackGuarantee
     * @param string $inputData     company specific investmentBuyBackGuarantee
     * @return int                  Winvestify standardized investmentBuyBackGuarantee
     */
    public function translateInvestmentBuyBackGuarantee($inputData) {
        $data = WIN_BUYBACKGUARANTEE_NOT_PROVIDED;
        $inputData = mb_strtoupper(trim($inputData));
        switch ($inputData) {
            case "YES":
                $data = WIN_BUYBACKGUARANTEE_PROVIDED;
                break;
        }
        return $data;        
    }
        
     /**
     * Function to translate the company specific 'originalLoanState' to the Winvestify standardized
     * 'StatusOfLoan'
     * @param string $inputData     company specific originalLoanState
     * @return int                  Winvestify standardized investmentBuyBackGuarantee
     */
    /*public function translateOriginalLoanState($inputData) {

        switch ($inputData) {
            case "Current":
                $result = WIN_LOANSTATUS_ACTIVE;
                break;
            case "1-15 Days Late":
                $result = WIN_LOANSTATUS_ACTIVE;
                break;
            case "16-30 Days Late":
                $result = WIN_LOANSTATUS_ACTIVE;
                break;
            case "31-60 Days Late":
                $result = WIN_LOANSTATUS_ACTIVE;
                break;
            case "60+ Days Late": 
                $result = WIN_LOANSTATUS_ACTIVE;
                break; 
            case "Default": 
                $result = WIN_LOANSTATUS_ACTIVE;
                break;
            case "Grace Period": 
                $result = WIN_LOANSTATUS_ACTIVE;
                break;
            case "Finished": 
                $result = WIN_LOANSTATUS_FINISHED;
                break; 
            case "Finished prematurely": 
                $result = WIN_LOANSTATUS_FINISHED;
                break;   
        }   
        return $result; 
    }*/       
  
 
    function structureRevisionAmortizationTable($node1, $node2){
        
        $dom1 = new DOMDocument();
        $dom1->loadHTML($node1);
        
        $dom2 = new DOMDocument();
        $dom2->loadHTML($node2);
        
        $dom1 = $this->cleanDomTag($dom1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'tbody'),
            array('typeSearch' => 'tagElement', 'tag' => 'i'),
        ));
         
        $dom2 = $this->cleanDomTag($dom2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'tbody'),
            array('typeSearch' => 'tagElement', 'tag' => 'i'),
        ));
        
        echo 'compare structure';
        $structureRevision = $this->verifyDomStructure($dom1, $dom2);
        return $structureRevision;
    }
    

    /**
     * Function to translate the company specific AmortizationPaymentStatus to the Winvestify standardized
     * concept
     * 
     * @param string $inputData     company specific AmortizationPaymentStatus
     * @return int                  Winvestify standardized AmortizationPaymentStatus
     */
    public function translateAmortizationPaymentStatus($inputData) {
        $data = WIN_AMORTIZATIONTABLE_PAYMENT_UNKNOWN;
        $inputData = mb_strtoupper(trim($inputData));

        switch ($inputData) {
            case "PAID AFTER THE DUE DATE":
                $data = WIN_AMORTIZATIONTABLE_PAYMENT_PAID_AFTER_DUE_DATE;
                break;
            case "PAID":
                $data = WIN_AMORTIZATIONTABLE_PAYMENT_PAID;
                break;
            case "SCHEDULED":
                $data = WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED;
                break;
            case "LATE":
                $data = WIN_AMORTIZATIONTABLE_PAYMENT_LATE;
                break;                     
        }
        return $data;        
    }
/*

20.12.2016	 € 282.03	 € 545.26	 € 827.29	  20.12.2016	 Paid
20.01.2017	 € 267.01	 € 560.28	 € 827.29	  31.01.2017	 Paid after the due date
Received late payment fee: € 36.36
20.12.2017	 € 319.97	 € 507.32	 € 827.29                        Late
20.01.2018	 € 306.64	 € 520.65	 € 827.29			 Scheduled 


WIN_AMORTIZATIONTABLE_PAYMENT_SCHEDULED    
WIN_AMORTIZATIONTABLE_PAYMENT_PARTIALLY_PAID
WIN_AMORTIZATIONTABLE_PAYMENT_PAID_AFTER_DUE_DATE
WIN_AMORTIZATIONTABLE_PAYMENT_PAID
WIN_AMORTIZATIONTABLE_PAYMENT_PENDING                     
WIN_AMORTIZATIONTABLE_PAYMENT_UNKNOWN

 
 


 */    
    
}
