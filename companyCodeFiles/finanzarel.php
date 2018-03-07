<?php

/**
 * +--------------------------------------------------------------------------------------------+
 * | Copyright (C) 2016, http://www.winvestify.com                   	  	|
 * +--------------------------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or 	|
 * | (at your option) any later version.                                      		|
 * | This file is distributed in the hope that it will be useful   		    	|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the      	|
 * | GNU General Public License for more details.        			              	|
 * +---------------------------------------------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.5
 * @date 2017-08-23
 * @package
 *
 * 
 * 2017-08-23 version_0.1
 * Created
 * 
 * 2017-08-24 version_0.2
 * Added login
 * 
 * 2017-09-21 version_0.3
 * Added download file and integration with Gearman
 * 
 * 2017-09-26 version_0.4
 * Download all files correctly with Gearman
 * Added logout
 * 
 * 2017-09-28 version_0.5
 * Added new file to download
 * 
 * 2017-10-27
 * Control variables
 * 
 */

/**
 * Contains the code required for accessing the website of "Finanzarel".
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[Not OK]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Not OK]
 * parallelization                                                      [OK, tested]
 */
class finanzarel extends p2pCompany {

    protected $pInstanceGlobal = '';
    protected $credentialsGlobal = array();
    protected $requestFiles = array();
    protected $tempRequest = [];
    
    protected $dashboard2ConfigurationParameters = [
        'outstandingPrincipalRoundingParm' => '0.05',                            // This *optional* parameter is used to determine what we 
                                                                                // consider 0 in order to "close" an active investment
        'recalculateRoundingErrors' => [
            "function" => "recalculationOfRoundingErrors",
            "values" => [                                                       //Values needed to modify due to rounding errors of the platform
                0 => [
                    "from" => ["investment_outstandingPrincipal"],              //From are all the values we take the values
                    "to" => ["payment_regularGrossInterestIncome"],              //To are all the values we modify the values
                    "sign" => "negative"                                         //Sign could be positive or negative
                                                                                //If it is positive, the + will be + and the - will be -
                                                                                //If it is negative, the + will be - and the - will be +
                ],
                1 => [
                    "from" => ["investment_outstandingPrincipal"],              //From are all the values we take the values
                    "to" => ["payment_capitalRepayment"],                       //To are all the values we modify the values
                    "sign" => "positive"                                         //Sign could be positive or negative
                                                                                //If it is positive, the + will be + and the - will be -
                                                                                //If it is negative, the + will be - and the - will be +
                ],
                2 => [
                    "from" => ["investment_outstandingPrincipal"],              //From are all the values we take the values
                    "to" => ["investment_outstandingPrincipal"],              //To are all the values we modify the values
                    "sign" => "negative"                                         //Sign could be positive or negative
                                                                                //If it is positive, the + will be + and the - will be -
                                                                                //If it is negative, the + will be - and the - will be +
                ]
            ],
                                                                               
        ]
    ];
    
    protected $valuesTransaction = [                                            // All types/names will be defined as associative index in array
        [
            "D" =>  [
                [
                    "type" => "date",                                           // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "D/M/y",
                                ],
                    "functionName" => "normalizeDate",
                ] 
            ],
            "E" => [
                [
                    "type" => "investment_loanId",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // trick to get the complete cell data as purpose
                                "input2" => "-",                                // May contain trailing spaces
                                "input3" => "",
                                "input4" => 2                                   // 'input3' is mandatory. With mandatory 2 If found then return "global_xxxxxx"
                            ],
                    "functionName" => "extractDataFromString",
                ]
            ], 
            "F" => [// NOT FINISHED YET
                [
                    "type" => "transactionDetail",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // List of all concepts that the platform can generate
                                                                                // format ["concept string platform", "concept string Winvestify"]
                                    "input3" => [
                                        0 => ["Provisión de fondos" => "Cash_deposit"],
                                        1 => ["Retirada de fondos" => "Cash_withdrawal"],
                                        2 => ["Cargo por inversión en efecto" => "Primary_market_investment"],
                                        3 => ["Provisi?n de fondos" => "Cash_deposit"],
                                        4 => ["Cargo por inversi?n en efecto" => "Primary_market_investment"],
                                        5 => ["Abono por cobro parcial de efecto" => "Partial_principal_and_interest_payment"],
                                        7 => ["Abono por cobro efecto" => "Principal_and_interest_payment"],
                                        9 => ["Intereses de demora" => "Delayed_interest_income"],
                                        14 => ["Retrocesión de comisiones" => "Compensation_positive"],
                                        15 => ["Retrocesi?n de comisiones" => "Compensation_positive"],
                                        16 => ["IVA sobre Comisiones" => "Tax_VAT"],
                                        18 => ["Comisiones" => "Commission"],
                                        29 => ["Retiro de fondos" => "Cash_withdrawal"]
                                    ]
                            ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
            "G" => [
                [
                    "type" => "amount",                                         // Winvestify standardized name  OK
                    "inputData" => [                               
                                "input2" => ".",
                                "input3" => ",",        
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "H" => [
                [
                    "type" => "balance",                                        // Winvestify standardized name  OK
                    "inputData" => [                             
                                "input2" => ".",                   
                                "input3" => ",",             
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "conceptChars",                                   // Winvestify standardized name
                    "inputData" => [
				"input2" => "#current.internalName",            // get Winvestify concept
                                ],
                    "functionName" => "getConceptChars",
                ]
            ]
        ],
        [
            "D" =>  [
                [
                    "type" => "date",                                           // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "D/M/y",
                                ],
                    "functionName" => "normalizeDate",
                ] 
            ],
            "E" => [
                [
                    "type" => "investment_loanId",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // trick to get the complete cell data as purpose
                                "input2" => "-",                                // May contain trailing spaces
                                "input3" => "",
                                "input4" => 2                                   // 'input3' is mandatory. With mandatory 2 If found then return "global_xxxxxx"
                            ],
                    "functionName" => "extractDataFromString",
                ]
            ], 
            "F" => [
                [
                    "type" => "transactionDetail",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // List of all concepts that the platform can generate
                                                                                // format ["concept string platform", "concept string Winvestify"]
                                    "input3" => [
                                        0 => ["Intereses" => "Regular_gross_interest_income"],
                                        1 => ["Efecto fallido" => "Write-off"],
                                        2 => ["Inversi?n en efecto" => "dummy_concept"],
                                        3 => ["Amortizaci?n de efecto" => "dummy_concept"],
                                        4 => ["Efecto retrasado" => "dummy_concept"],
                                        5 => ["Amortizaci?n parcial de efecto" => "dummy_concept"],
                                        6 => ["Efecto Impagado" => "dummy_concept"]
                                    ]
                            ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
            "G" => [
                [
                    "type" => "amount",                                         // Winvestify standardized name  OK
                    "inputData" => [                                       
                                "input2" => ".",     
                                "input3" => ",",   
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "conceptChars",                                   // Winvestify standardized name
                    "inputData" => [
				"input2" => "#current.internalName",            // get Winvestify concept
                                ],
                    "functionName" => "getConceptChars",
                ]
            ],
        ],
        [
            "A" => [
                [
                    "type" => "investment_loanId",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // trick to get the complete cell data as purpose
                                "input2" => "-",                                // May contain trailing spaces
                                "input3" => "",
                                "input4" => 2                                   // 'input3' is mandatory. With mandatory 2 If found then return "global_xxxxxx"
                            ],
                    "functionName" => "extractDataFromString",
                ]
            ],
            "B" => [
                [
                    "type" => "date",                                           // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "D/M/y",
                                ],
                    "functionName" => "getDefaultDate",
                ]
            ],
            "C" => [
                [
                    "type" => "internalName",                        
                    "inputData" => [                                            // Get the "original" Mintos concept, which is used later on
                                "input2" => "createReservedFunds",                                // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ]
            ],
            "K" => [
                [
                    "type" => "amount",                                         // Winvestify standardized name  OK
                    "inputData" => [                                            
                                "input2" => ".",    
                                "input3" => ",",       
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ]
        ]
    ];
    
    protected $valuesInvestment = [                                             // All types/names will be defined as associative index in array
        [
            "A" =>  [
                "name" => "investment_loanId"                                   // Winvestify standardized name
            ],
            "B" => [
                "name" => "investment_debtor",                                  // Winvestify standardized name  OK
            ],
            "C" => [
                "name" => "investment_riskRating",
            ], 
            "D" =>  [
                "name" => "investment_typeOfInvestment"
            ],
            "E" => [  
                [
                    "type" => "investment_fullLoanAmount",                      // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ], 
            "F" => [
                "name" => "investment_originalDuration"
            ],
            "G" => [
                [
                    "type" => "investment_issueDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D/M/y",

                                ],
                    "functionName" => "normalizeDate",
                ],
                [
                    "type" => "investment_myInvestmentDate",                    // Winvestify standardized name OK
                    "inputData" => [
				"input2" => "D/M/y",
                                ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "I" => [
                [
                    "type" => "investment_expectAnnualYield",                   // Winvestify standardized name   OK
                    "inputData" => [
                                "input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]       
            ],
            "J" =>  [
                /*[
                    "type" => "investment_nominalInterestRate",                 // Winvestify standardized name
                    "inputData" => [
				"input2" => ".",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]*/
                [
                    "type" => "investment_nominalInterestRate",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // trick to get the complete cell data as purpose
                                "input2" => "",                                // May contain trailing spaces
                                "input3" => "&",
                            ],
                    "functionName" => "extractDataFromString",
                ]
            ],
            "L" => [
                [
                    "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "M" => [
                [
                    "type" => "investment_nextPaymentDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D/M/y",

                                ],
                    "functionName" => "normalizeDate",
                ],
                [
                    "type" => "investment_dueDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D/M/y",

                                ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "N" => [
                [
                    "type" => "investment_statusOfLoan",                          
                    "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                                "input2" => "",                               
                                "input3" => "",
                                "input4" => 0                                   // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "extractDataFromString",
                ],
                [
                    "type" => "investment_originalLoanState",                    
                    "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                                "input2" => "#current.investment_statusOfLoan", // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ]
            ],
            "O" => [
                [
                    "type" => "investment_estimatedNextPayment",                // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ]
        ],
        [
            "A" =>  [
                "name" => "investment_loanId"                                   // Winvestify standardized name
            ],
            "B" => [
                "name" => "investment_debtor",                                  // Winvestify standardized name  OK
            ],
            "C" => [
                "name" => "investment_typeOfInvestment"
            ], 
            "D" =>  [
                "name" => "investment_riskRating",
            ],
            
            "E" => [  
                [
                    "type" => "investment_nextPaymentDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D/M/y",

                                ],
                    "functionName" => "normalizeDate",
                ],
                [
                    "type" => "investment_issueDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D/M/Y",

                                ],
                    "functionName" => "normalizeDate",
                ]
            ], 
            "G" => [
                [
                    "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "H" => [
                [
                    "type" => "investment_statusOfLoan",                          
                    "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                                "input2" => "",                               
                                "input3" => "",
                                "input4" => 0                                   // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "extractDataFromString",
                ],
                [
                    "type" => "investment_originalLoanState",                    
                    "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                                "input2" => "#current.investment_statusOfLoan", // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ]
            ],
            "I" => [
                [
                    "type" => "investment_estimatedNextPayment",                // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ]
        ],
        [
            "A" =>  [
                "name" => "investment_loanId"                                          // Winvestify standardized name
            ],
            "B" => [
                "name" => "investment_debtor",                           // Winvestify standardized name  OK
            ],
            "C" => [
                "name" => "investment_riskRating",
            ], 
            "D" =>  [
                "name" => "investment_typeOfInvestment"
            ],
            "E" => [  
                [
                    "type" => "investment_fullLoanAmount",                // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ], 
            "F" => [
                "name" => "investment_originalDuration"
            ],
            "I" =>  [
                [
                    "type" => "investment_nominalInterestRate",                           // Winvestify standardized name
                    "inputData" => [
				"input2" => ".",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            //FAKE CELL, IT IS A DEFAULT VALUE
            "J" => [
                [
                    "type" => "investment_statusOfLoan",                        
                    "inputData" => [                                            
                                "input2" => "PREACTIVE",                                 
                            ],
                    "functionName" => "getDefaultValue",
                ],
            ],
            "K" => [
                [
                    "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
        ]
    ];
    
    protected $parserValuesAmortizationTable = [
            "A" =>  [
                "name" => "investment_loanId"                                          // Winvestify standardized name
            ],
            "L" => [
                [
                    "type" => "capitalRepayment",                        // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "M" => [
                [
                    "type" => "scheduledDate",                           // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "D/M/y",

                                ],
                    "functionName" => "normalizeDate",
                ]
            ],
            "N" => [
                [
                    "type" => "statusOfLoan",                          
                    "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                                "input2" => "",                               
                                "input3" => "",
                                "input4" => 0                                   // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "extractDataFromString",
                ]
            ],
            "O" => [
                [
                    "type" => "capitalAndInterestPayment",                // Winvestify standardized name
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ]
    ];

    protected $valuesAmortizationTable = [
        0 => [
            [
                "type" => "investment_id",                          
                "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                            "input2" => "",                               
                            "input3" => "",
                            "input4" => 0                                   // 'input3' is NOT mandatory. 
                        ],
                "functionName" => "extractDataFromString",
            ]
        ],
        1 => [
            [
                "type" => "amortizationtable_capitalRepayment",                 // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 2
                ],
                "functionName" => "getAmount",
            ]
        ],
        2 => [
            [
                "type" => "amortizationtable_scheduledDate",                    // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "Y-M-D",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        3 => [
            [
                "type" => "amortizationtable_paymentStatus",                          
                "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                            "input2" => "",                               
                            "input3" => "",
                            "input4" => 0                                   // 'input3' is NOT mandatory. 
                        ],
                "functionName" => "extractDataFromString",
            ]
        ],
        4 => [
            [
                "type" => "amortizationtable_capitalAndInterestPayment",        // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 2
                ],
                "functionName" => "getAmount",
            ]
        ]
    ];
    
    protected $transactionConfigParms = [
        "fileConfigParam" => [
            "type" => "joinTogether",
            "function" => "joinTwoDimensionArrayTogether",
            "sortParameter" => array("date","investment_loanId")
        ],
        0 => [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
            'separatorChar' => ";",
            'sortParameter' => array("date","investment_loanId"),               // used to "sort" the array and use $sortParameter(s) as prime index.
            'changeCronologicalOrder' => 1,                                     // 1 = inverse the order of the elements in the transactions array
        ],
        1 => [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
            'separatorChar' => ";",
            'sortParameter' => array("date","investment_loanId"),               // used to "sort" the array and use $sortParameter(s) as prime index.
            'changeCronologicalOrder' => 1,                                     // 1 = inverse the order of the elements in the transactions array
            'callback' => [
                "cleanTempArray" => [
                    "findValueInArray" => [
                        "key" => "internalName",
                        "function" => "verifyEqual",
                        "values" => ["dummy"],
                        "valueDepth" => 2
                    ]
                ]
            ]
        ],
        2 => [
            'offsetStart' => 1,
            'offsetEnd'     => 1,
            'separatorChar' => ";",
                            'sortParameter' => array("date","investment_loanId"),   // used to "sort" the array and use $sortParameter(s) as prime index.
            //'changeCronologicalOrder' => 1,                 // 1 = inverse the order of the elements in the transactions array
        ]
    ];
    
    protected $investmentConfigParms = [
        "fileConfigParam" => [
            "type" => "joinTogether",
            "function" => "joinOneDimensionArrayTogether",
            "sortParameter" => array("investment_loanId")
        ],
        0 => [
            'offsetStart' => 1,
            'offsetEnd'     => 1,
            'separatorChar' => ";",
            'debugEnd' => true,
            'sortParameter' => array("investment_loanId")                       // Used to "sort" the array and use $sortParameter as prime index.
        ],
        1 => [
            'offsetStart' => 1,
            'offsetEnd'     => 1,
            'separatorChar' => ";",
            'sortParameter' => array("investment_loanId"),                      // Used to "sort" the array and use $sortParameter as prime index.
            'callback' => [
                "cleanTempArray" => [
                    "findValueInArray" => [
                        "key" => "investment_originalLoanState",
                        "function" => "verifyNotEqual",
                        "values" => ["Fallida"],
                        "valueDepth" => 2
                    ]
                ]
            ]
        ],
        2 => [
            'offsetStart' => 1,
            'offsetEnd'     => 1,
            'separatorChar' => ";",
            'sortParameter' => array("investment_loanId"),   // used to "sort" the array and use $sortParameter(s) as prime index.
        ]
    ];
    
    protected $amortizationConfigParms = array(
        [
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'sortParameter' => "investment_loanId"          // used to "sort" the array and use $sortParameter as prime index.
        ]
    );
    
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
                    "input4" => ",",
                ],
                "functionName" => "handleNumber",
            ]
        ],
   
        "outstandingPrincipal" => [
            [
                "type" => "outstandingPrincipal",                               // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
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
                "investment_typeOfInvestment" => "translateLoanType",
                "investment_nominalInterestRate" => "translateNominalInterestRate",
                "investment_statusOfLoan" => "translateLoanStatus"
            ]
        ],
        "transactionFile" => [
            "cleanDatesTempArray" => [
                "values" => [
                    "startDate",
                    "finishDate"
                ]
            ]
        ]
    ];

            /************************************/
            /**HEADERS FOR SRUCTURE COMPARATION**/
            /************************************/
    protected  $investmentHeader = array (
                                    "A" => "Subasta",
                                    "B" => "Deudor/Emisor",
                                    "C" => "Rating",
                                    "D" => "T?tulo",
                                    "E" => "Importenominal",
                                    "F" => "Vto.(d)",
                                    "G" => "Fecha finsubasta",
                                    "H" => "Tasa dto.solicitada",
                                    "I" => "Tasafinal",
                                    "J" => "Mi oferta",
                                    "K" => "Importeasignado",
                                    "L" => "Mi oferta(precio)",
                                    "M" => "Fecha de vencimiento",
                                    "N" => "Estado",
                                    "O" => "Amortizaci?nPendiente",
                                    "P" => " ");

    protected $investment2Header = array(
                                    "A" => "Subasta",
                                    "B" => "Deudor",
                                    "C" => "Tipo",
                                    "D" => "Rating",
                                    "E" => "Vencimiento",
                                    "F" => "Mi oferta",
                                    "G" => "Precio de compra",
                                    "H" => "Estado",
                                    "I" => "Pendiente",
                                    "J" => " ");
    
    protected $expiredLoansHeader = array(
                                    "A" => "Subasta",
                                    "B" => "Resultado",
                                    "C" => "Deudor/Emisor",
                                    "D" => "Rating",
                                    "E" => "T?tulo",
                                    "F" => "Importe",
                                    "G" => "Vto.(d)",
                                    "H" => "Fecha finsubasta",
                                    "I" => "Tasasolicicitada",
                                    "J" => "Tasafinal",
                                    "K" => "Mi oferta",
                                    "L" => "ImporteOferta",
                                    "M" => "Mi oferta(precio)",
                                    "N" => "Plusval?a",
                                    "O" => "Fecha de vencimiento");
    
    protected $transactionHeader = array(
                                 "A" => "Id",
                                 "B" => "A?o",
                                 "C" => "Trimestre",
                                 "D" => "Fecha",
                                 "E" => "Subasta",
                                 "F" => "Descripci?n",
                                 "G" => "Importe",
                                 "H" => "Saldo");
    
    protected $transaction2Header = array(
                                 "A" => "Id",
                                 "B" => "A?o",
                                 "C" => "Trimestre",
                                 "D" => "Fecha",
                                 "E" => "Subasta",
                                 "F" => "Descripci?n",
                                 "G" => "Importe",
                                 "H" => "Saldo");
    
    protected $transaction3Header = array(
                                "A" => "Id",
                                "B" => "Deudor/Emisor",
                                "C" => "Rating  ?",
                                "D" => "T?tulo",
                                "E" => "Importe",
                                "F" => "Vto.(d)",
                                "G" => "Mejor Ofertaponderada",
                                "H" => "Cobertura acumulada",
                                "I" => "Mi oferta",
                                "J" => "Importe Asignado",
                                "K" => "Mi oferta(precio)",
                                "L" => "Plusval?aEsperada",
                                "M" => "TiempoRestante"
                                );
    
    protected  $compareHeaderConfigParam = array( 'separatorChar' => ";",
                                                  'chunkInit' => 1,
                                                  'chunkSize' => 1 );

    
    

    /*protected $callbacks = [
        "investment" => [
            "investment_loanType" => "translateLoanType",
            "investment_LoanState" => "translateLoanStatus"
        ]
    ];*/

    
    function __construct() {
        parent::__construct();
        $this->typeFileTransaction = "csv";
        $this->typeFileInvestment = "csv";
        //$this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";
// Do whatever is needed for this subsclass
    }   

    
    
    /**
     *
     * 	Checks if the user can login to its portal. Typically used for linking a company account
     * 	to our account
     * 	
     * 	@param string	$user		username
     * 	@param string	$password	password
     * 	@return	boolean	true: 		user has successfully logged in.
     * 			false: 		user could not log in
     * 	
     */
    function companyUserLogin($user = "", $password = "") {
        /*
          FIELDS USED BY finanzarel DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */



        //Get credentials from form in pfp login page
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $input) {
            //echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
            $name = $input->getAttribute('name');
            switch ($name) {
                case 'p_flow_id':
                    $pFlowId = $input->getAttribute('value');
                    break;
                case 'p_flow_step_id':
                    $pFlowStepId = $input->getAttribute('value');
                    break;
                case 'p_instance':
                    $pInstance = $input->getAttribute('value');
                    break;
                case 'p_page_submission_id':
                    $pPageSubmissionId = $input->getAttribute('value');
                    break;
                case 'p_request':
                    $pRequest = $input->getAttribute('value');
                    break;
                case 'p_reload_on_submit':
                    $pReloadOnSubmit = $input->getAttribute('value');
                    break;
            }
            if ($input->getAttribute('id') == 'pSalt') {
                $pSalt = $input->getAttribute('value');
            }
            if ($input->getAttribute('id') == 'pPageItemsProtected') {
                $pPageItemsProtected = $input->getAttribute('value');
            }
        }


        $credentials['p_json'] = '{"salt":"' . $pSalt . '","pageItems":{"itemsToSubmit":[{"n":"P101_USERNAME","v":"' . $user . '"},{"n":"P101_PASSWORD","v":"' . $password . '"}],"protected":"' . $pPageItemsProtected . '","rowVersion":""}}';
        $credentials['p_flow_id'] = $pFlowId;
        $credentials['p_flow_step_id'] = $pFlowStepId;
        $credentials['p_instance'] = $pInstance;
        $this->pInstanceGlobal = $pInstance;
        $credentials['p_page_submission_id'] = $pPageSubmissionId;
        $credentials['p_request'] = 'Login';
        $credentials['p_reload_on_submit'] = $pReloadOnSubmit;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login

        $url = array_shift($this->urlSequence);
        //echo $url . HTML_ENDOFLINE;
        $str = $this->getCompanyWebpage($url . $pInstance);
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;
        $h2s = $dom->getElementsByTagName('h2');
        foreach ($h2s as $h2) {
            //echo $h2->nodeValue . HTML_ENDOFLINE;
            if (trim($h2->nodeValue) == 'Dashboard') {
                //echo 'ok' . HTML_ENDOFLINE;
                return true;
            }
        }
        return false;
    }
    
    /**
     * Download investment and cash flow files and collect control variables
     * 
     * @param string $str It is the web converted to string of the company.
     * @return array Control variables.
     */
    function collectUserGlobalFilesParallel($str = null) {
        
        switch ($this->idForSwitch) { 
            case 0:
                $this->baseUrl = array_shift($this->urlSequence);
                echo $this->idForSwitch . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;

                $inputs = $dom->getElementsByTagName('input');
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $input) {
                    echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
                    $name = $input->getAttribute('name');
                    switch ($name) {
                        case 'p_flow_id':
                            $pFlowId = $input->getAttribute('value');
                            break;
                        case 'p_flow_step_id':
                            $pFlowStepId = $input->getAttribute('value');
                            break;
                        case 'p_instance':
                            $pInstance = $input->getAttribute('value');
                            break;
                        case 'p_page_submission_id':
                            $pPageSubmissionId = $input->getAttribute('value');
                            break;
                        case 'p_request':
                            $pRequest = $input->getAttribute('value');
                            break;
                        case 'p_reload_on_submit':
                            $pReloadOnSubmit = $input->getAttribute('value');
                            break;
                    }
                    if ($input->getAttribute('id') == 'pSalt') {
                        $pSalt = $input->getAttribute('value');
                    }
                    if ($input->getAttribute('id') == 'pPageItemsProtected') {
                        $pPageItemsProtected = $input->getAttribute('value');
                    }
                }
                
                $this->credentialsGlobal['p_json'] = '{"salt":"' . $pSalt . '","pageItems":{"itemsToSubmit":[{"n":"P101_USERNAME","v":"' . $this->user . '"},{"n":"P101_PASSWORD","v":"' . $this->password . '"}],"protected":"' . $pPageItemsProtected . '","rowVersion":""}}';
                $this->credentialsGlobal['p_flow_id'] = $pFlowId;
                $this->credentialsGlobal['p_flow_step_id'] = $pFlowStepId;
                $this->credentialsGlobal['p_instance'] = $pInstance;
                $this->credentialsGlobal['p_page_submission_id'] = $pPageSubmissionId;
                $this->credentialsGlobal['p_request'] = 'Login';
                $this->credentialsGlobal['p_reload_on_submit'] = $pReloadOnSubmit;

                //print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentialsGlobal); //do login
                break;
            case 2:
                $url = array_shift($this->urlSequence);
                //echo $url . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url . $this->credentialsGlobal['p_instance']);
                break;
            case 3:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                //echo $str;
                $h2s = $dom->getElementsByTagName('h2');
                $this->verifyNodeHasElements($h2s);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $resultLogin = false;
                foreach ($h2s as $h2) {
                    echo $h2->nodeValue . HTML_ENDOFLINE;
                    if (trim($h2->nodeValue) == 'Dashboard') {
                        //echo 'ok' . HTML_ENDOFLINE;
                        $resultLogin = true;
                    }
                }
                
                if (!$resultLogin) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Finanzarel login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }
                echo 'Login ok';
                
                //Get cv
                
                $controlVariables = $this->getElements($dom, 'span', 'class', 't-MediaList-badge');
                $controlVariables = array_merge($controlVariables, $this->getElements($dom, 'p', 'class', 't-MediaList-desc'));
                $controlVariablesArray = array();
                foreach ($controlVariables as $controlVariable){
                    $controlVariablesArray[] = $controlVariable->nodeValue;
                }
                print_r($controlVariablesArray);
                
                
                $this->tempArray['global']['outstandingPrincipal'] = $controlVariablesArray[1];
                //$this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($controlVariablesArray[11]);
                //Finanzarel doenst have number of investments
                $this->tempArray['global']['reservedFunds'] = (float) filter_var(str_replace(",",".",str_replace(".","",$controlVariablesArray[6])), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ); //They call it "Inversion neta comprometida"
                $this->tempArray['global']['myWallet'] = (float) filter_var(str_replace(",",".",str_replace(".","",$controlVariablesArray[5])), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) - $this->tempArray['global']['reservedFunds'];
                
                
                print_r($this->tempArray);
                //Get the request to download the file
                $as = $dom->getElementsByTagName('a');
                $this->verifyNodeHasElements($as);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $this->verifyNodeHasElements($as);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($as as $key => $a) {
                    //echo $key . " => " . $a->getAttribute('href') . "   " . $a->nodeValue .  HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Descargar en csv') {
                        $this->request[] = explode("'", $a->getAttribute('href'))[1];
                        
                    }
                }
                $url =  array_shift($this->urlSequence);
                //echo "The url is " . $url . "\n";
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                    '{$p_flow_step_id}' => 1,
                    '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                if (count($this->request) === 3) {
                    $this->tempRequest = array_shift($this->request);
                }
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->request[0]);
                print_r($credentialsFile);
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->investmentHeader;
                $this->numFileInvestment++;
                //$fileType = 'csv';
                //$referer = 'https://marketplace.finanzarel.com/apex/f?p=MARKETPLACE:' . $this->credentialsGlobal['p_flow_step_id'] . ":" . $this->credentialsGlobal['p_instance'];
                //$referer = 'https://marketplace.finanzarel.com/apex/f?p=MARKETPLACE:{$credential_p_flow_step_id}:{$credential_p_instance}';
                //How we get fix Finanzarel
                //https://chrismckee.co.uk/curl-http-417-expectation-failed/
                //https://stackoverflow.com/questions/3755786/php-curl-post-request-and-error-417
                $headers = array('Expect:');
                //array_shift($this->urlSequence);         
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url,$referer, $credentialsFile, $headers, $this->fileName);
                break; 
            case 4:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain"){  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }              
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                
                $this->url =  array_shift($this->urlSequence);
                $referer = array_shift($this->urlSequence);
                $this->referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 1,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->request[1]);
                $this->fileName = "LoansExpired" . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->expiredLoansHeader;
                $headers = array('Expect:');
                
                if (!empty($this->tempRequest)) {
                    $this->idForSwitch++;                   
                }
                else {
                    $this->from = 4;
                    $this->idForSwitch = 6;
                }
                $this->getPFPFileMulticurl($this->url,$this->referer, $credentialsFile, $headers, $this->fileName);
                break;
            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain"){  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }                           
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->tempRequest);
                $this->numFileTransaction = 3;
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->headerComparation = $this->transaction3Header;
                $headers = array('Expect:');
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($this->url,$this->referer, $credentialsFile, $headers, $this->fileName);
                break;
            case 6:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain"){  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }                      
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                if (!empty($this->tempRequest)) {
                    $path = $this->getFolderPFPFile();
                    $file = $path . DS . $this->fileName;
                    $newFile = $path . DS . $this->nameFileInvestment . "3" . "." . $this->typeFileInvestment;
                    $this->copyFile($file, $newFile);                 
                }
                $this->idForSwitch++;
            case 7:
                $url = array_shift($this->urlSequence);
                //echo $url . HTML_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url . $this->credentialsGlobal['p_instance']);
                break;
            case 8:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $buttons = $this->getElementsByClass($dom, "a-IRR-button");
                $this->verifyNodeHasElements($buttons);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($buttons as $button) {
                    $id = $button->getAttributeNode("id")->nodeValue;
                    //echo "El id es $id \n";
                    $pos = stripos($id, "actions_button");
                    if ($pos !== false) {
                        echo "cashflow $id";
                        $credentialCashflows = explode("_", $id);
                        $this->credentialCashflow[] = $credentialCashflows[0];
                        echo "Found cashflow $this->credentialCashflow";
                    }        
                }
                
                $as = $dom->getElementsByTagName('a');
                $this->verifyNodeHasElements($as);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($as as $key => $a) {
                    //echo $key . " => " . $a->getAttribute('href') . "   " . $a->nodeValue .  HTML_ENDOFLINE;
                    if (trim($a->nodeValue) == 'Descargar') {
                        $this->requestInvestment2 = explode("'", $a->getAttribute('href'))[1];
                        
                    }
                }
                                   
                $url = array_shift($this->urlSequence);
                echo "The url of last is : ".$url;
                $url = strtr($url, array(
                            '{$p_instance}' => $this->credentialsGlobal['p_instance'],
                            '{$credentialCashflow}' => $this->credentialCashflow[0]
                        ));
                echo "now the url is " . $url;
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 11,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                $headers = array('Expect:'/* 'Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,*//*;q=0.8"', 'Accept-Language: "en-US,en;q=0.5"', 'Accept-Encoding: "gzip, deflate, br"'*/);
                $this->numFileTransaction = 1;
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->headerComparation = $this->transactionHeader;
                $this->idForSwitch++;
                echo "referer: " . $referer;
                $this->getPFPFileMulticurl($url, $referer, false, $headers, $this->fileName);
                break;
            case 9:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain"){  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }                      
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
               
                $url = array_shift($this->urlSequence);
                echo "The url of last is : ".$url;
                $url = strtr($url, array(
                            '{$p_instance}' => $this->credentialsGlobal['p_instance'],
                            '{$credentialCashflow}' => $this->credentialCashflow[1]
                        ));
                echo "now the url is " . $url;
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 11,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                $headers = array('Expect:'/* 'Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,*//*;q=0.8"', 'Accept-Language: "en-US,en;q=0.5"', 'Accept-Encoding: "gzip, deflate, br"'*/);
                $this->numFileTransaction = 2;
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->headerComparation = $this->transaction2Header;

                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, $referer, false, $headers, $this->fileName);                 
                break;
            case 10:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain"){  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }                      
                $headerError = $this->compareHeader();
                echo $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                
               
                $url =  array_shift($this->urlSequence);
                //echo "The url is " . $url . "\n";
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                    '{$p_flow_step_id}' => 1,
                    '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 11, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->requestInvestment2);
                print_r($credentialsFile);
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->investment2Header;
                $this->numFileInvestment++;

                $headers = array('Expect:');
                //array_shift($this->urlSequence);         
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url,$referer, $credentialsFile, $headers, $this->fileName);                 
                break;
 
            case 11:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if(mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain"){  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }                      
                $headerError = $this->compareHeader();
                echo $this->compareHeader();
                
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                return $this->tempArray;
        }
    }

    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserInvestmentData($user, $password) {

        $resultLogin = $this->companyUserLogin($user, $password);

        if (!$resultLogin) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Finazarel login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        echo 'Login ok';

        //echo $this->pInstanceGlobal;

        $url = array_shift($this->urlSequence); //Load the page that contains the file url
        $dom = new DOMDocument;
        $str = $this->getCompanyWebpage($url . $this->pInstanceGlobal);
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //$this->print_r2($dom);

        //Get credentials to download the file
        $inputs = $dom->getElementsByTagName('input');
        $this->verifyNodeHasElements($inputs);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
        }
        foreach ($inputs as $input) {
            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
        }


        //Get the request to download the file
        $as = $dom->getElementsByTagName('a');
        $this->verifyNodeHasElements($as);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
        }
        foreach ($as as $key => $a) {
            //echo $key . " => " . $a->getAttribute('href') . HTML_ENDOFLINE;
            if (trim($a->nodeValue) == 'Descargar en csv') {
                $request = explode("'", $a->getAttribute('href'))[1];
                echo $request . HTML_ENDOFLINE;
                break;
            }
        }

        $url = array_shift($this->urlSequence);
        $fileUrl = $url . "p_flow_id=" . $credentials['p_flow_id'] . "&p_flow_step_id=" . $credentials['p_flow_step_id'] . "&p_instance=" . $credentials['p_instance'] . "&p_debug&p_request=" . $request;
        echo $fileUrl . HTML_ENDOFLINE;
        $this->fileName = 'Finanzarel';
        $fileType = 'csv';

        $pfpBaseUrl = 'http://www.finanzarel.com';
        $path = 'prueba';

        $this->downloadPfpFile($fileUrl, $this->fileName, $fileType, $pfpBaseUrl, 'Finanzarel', 'prueba');
        echo 'Downloaded';
    }
    
    /**
     * Get amortization tables of user investments.
     * In finanzarel we don't get the tables via curl, we parser the investment file and read the info there.
     * 
     * @param string $str It is the web converted to string of the company.
     * @return array html of the tables
     */
    function collectAmortizationTablesParserFile($str = null) {
        $this->loanTotalIds = $this->loanIds;
        $this->myParser = new Fileparser();                                                                             //Call the parser
        $folder = $this->getFolderPFPFile();
        $file = $folder . DS . $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;  //Get the pfp folder and file name
        $this->myParser->setConfig($this->investmentConfigParms[0]);//Set the config 
        $info = $this->myParser->analyzeFile($file, $this->parserValuesAmortizationTable, $this->typeFileInvestment);             //Parse the file
        foreach ($info as $key => $value) {
            
            if (!in_array($key, $this->loanIds)) {
                //echo $key . " dont found, dont compare \n";
                unset($info[$key]); //Delete old investments that we don't have in loanId.json from parsed array.
                continue;
            }

            foreach ($this->loanIds as $slice => $id) { //Set the slice_id to the loans that we find
                $this->tempArray['errorTables'][$slice] = $id; //If we had a loan in loansId and that loan isnt in investment_1.csv, we cant get the invesment table.                          //                                                                   
                //echo $slice . " " . $id . " slice and id from json" . "\n";
                //echo $key . " investment file id" . "\n\n\n\n\n\n\n";

                if ($key == $id) {
                    //echo 'compare ok';
                    $this->tempArray['correctTables'][$slice] = $key; //If the investment exist in the file, we can get the table. Save the id in correcTabes.
                    continue;
                }
            }

            foreach ($this->tempArray['correctTables'] as $slice => $id) {
                unset($this->tempArray['errorTables'][$slice]); //If we can get the amortization table of the investment, delete from errorTables,.
            }


            $this->tempArray['tables'][$key] = $this->arrayToTableConversion($info[$key]); //Get the html table from the array         
        }
        //print_r($this->tempArray);
        return $this->tempArray;
    }

    public function companyUserLogout($url = null) {
        $this->doCompanyLogout(); //logout
        return true;
    }
    
    
    public function companyUserLogoutMultiCurl($str = null) {
        //Get logout url
        $this->doCompanyLogoutMultiCurl(); //Logout

    }
    
    /**
     * Function to translate the company specific loan type to the Winvestify standardized
     * loan type
     * @param string $inputData     company specific loan type
     * @return int                  Winvestify standardized loan type
     */
    public function translateLoanType($inputData) {
        $type = WIN_TYPEOFLOAN_UNKNOWN;
        $inputData = mb_strtoupper($inputData);
        switch ($inputData){
            case "FACTURA":
                $type = WIN_TYPEOFLOAN_PERSONAL;
                break;
            case "PAGAR?":
                $type = WIN_TYPEOFLOAN_PAGARE;
                break;
            case "PAGARÉ":
                $type = WIN_TYPEOFLOAN_PAGARE;
                break;
            case "PAGAR? N.O.":
                $type = WIN_TYPEOFLOAN_PAGARE;
                break; 
            case "PAGARÉ N.O.":
                $type = WIN_TYPEOFLOAN_PAGARE;
                break; 
            case "CONFIRMING":
                $type = WIN_TYPEOFLOAN_CONFIRMING;
                break;
        }
        return $type;

    }
    
     /**
     * Function to translate the company specific loan status to the Winvestify standardized
     * loan type
     * @param string $inputData     company specific loan status
     * @return int                  Winvestify standardized loan status
     */ 
    public function translateLoanStatus($inputData){
        $status = WIN_LOANSTATUS_UNKNOWN;
        $inputData = strtoupper(trim($inputData));
         switch ($inputData) {
            case "PREACTIVE":
                $data =  WIN_LOANSTATUS_WAITINGTOBEFORMALIZED;
                break;
            case "PENDIENTE":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "IMPAGADA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "RETRASADA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "FALLIDA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
          /*case "GANADA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "NO ADJUDICADA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "SUBASTA RETIRADA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "CADUCADA":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
           */
        }
        return $data;
    }
    
    /**
     * Function to translate the company specific amortization method to the Winvestify standardized
     * amortization type
     * @param string $inputData     company specific amortization method
     * @return int                  Winvestify standardized amortization method
     */
    public function translateAmortizationMethod($inputData) {

    }   
    
    /**
     * Function to translate the company specific type of investment to the Winvestify standardized
     * type of investment
     * @param string $inputData     company specific type of investment
     * @return int                  Winvestify standardized type of investment
     */
    public function translateTypeOfInvestment($inputData) {

    }
    
    /**
     * Function to translate the company specific payment frequency to the Winvestify standardized
     * payment frequency
     * @param string $inputData     company specific payment frequency
     * @return int                  Winvestify standardized payment frequency
     */
    public function translatePaymentFrequency($inputData) {
        
    }
        
    /**
     * Function to translate the type of investment market to an to the Winvestify standardized
     * investment market concept
     * @param string $inputData     company specific investment market concept
     * @return int                  Winvestify standardized investment marke concept
     */
    public function translateInvestmentMarket($inputData) {
        
    }
    
    /**
     * Function to translate the company specific investmentBuyBackGuarantee to the Winvestify standardized
     * investmentBuyBackGuarantee
     * @param string $inputData     company specific investmentBuyBackGuarantee
     * @return int                  Winvestify standardized investmentBuyBackGuarantee
     */
    public function translateInvestmentBuyBackGuarantee($inputData) {
        
    }
    
    /**
     * Function to translate the company specific investmentBuyBackGuarantee to the Winvestify standardized
     * nominalInterestRate
     * @param string $inputData     company specific nominalInterestRate
     * @return int                  Winvestify standardized nominalInterestRate
     */
    public function translateNominalInterestRate($inputData) {
        return str_replace(",",".",$inputData);
    }

}
