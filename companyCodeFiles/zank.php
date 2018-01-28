<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +-----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by  	|
 * | the Free Software Foundation; either version 2 of the License, or 		|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
 * | GNU General Public License for more details.        			|
 * +-----------------------------------------------------------------------------+
 *
 *
 * @author 
 * @version 0.3
 * @date 2017-01-28
 * @package
 *
 *
 *
 *
 * function calculateLoanCost()										[OK, tested]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function companyUserLogout										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * parallelization on collectUserInvestmentData                                                            [OK, tested]
 *
 * 2016-08-04	  version 2016_0.1
 * Basic version
 *
 * 2016-11-30	  version 2016_0.2
 * Zank introduced csrf code for improved security
 *
 *
 * 2017-04-25
 * Estado amortizado
 *
 * 2017-04-26
 * Total invertido correcto, fecha
 *
 * 2017-05-16      version 2017_0.3
 *
 * Added parallelization to collectUserInvestmentData
 * Added dom verification to collectUserInvestmentData
 *
 * 2017/06/01
 * Added loop when we take json investments                                     [OK, STILL TO CHECK]
 *
 * 2017/06/22
 * Added mechanism to take more than 100 investments by Json
 *
 * 2017/08/02
 * zank code adaptation for 100%
 * 
 * 2017-08-14
 * Structure Revision added
 * Status definition added
 * 
 * 2017-08-29
 * json revision
 * 
 * 2017-10-24 version_0.9
 * Integration of parsing amortization tables with Gearman and fileparser
 * 
 * Parser AmortizationTables                                            [OK, tested]
 * 
 * Pending:
 * Fecha en duda
 *
 *
 */

/**
 * Contains the code required for accessing the website of "Zank".
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[OK, tested]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Ok, not tested]
 * parallelization                                                      [OK, tested]
 */


class zank extends p2pCompany {

    private $credentials = array();
    private $userId;
    private $resultMiZank = false;
    private $url;
    private $start = 0;
 
    
    protected $transactionConfigParms = [
        [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
    //        'separatorChar' => ";",
            'sortParameter' => array("date","investment_loanId"),   // used to "sort" the array and use $sortParameter(s) as prime index.
            'changeCronologicalOrder' => 1,                 // 1 = inverse the order of the elements in the transactions array
        ]
    ];                                                      // 0 = do not inverse order of elements (=default)
 
    protected $valuesTransaction = [
        [
            "A" =>  [
                [
                    "type" => "date",                                               // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "D/M/Y",
                                ],
                    "functionName" => "normalizeDate",
                ] 
            ],
            "B" => [
                [
                    "type" => "original_concept",                               // 
                    "inputData" => [                                            // Get the "original" Mintos concept, which is used later on
                                "input2" => "",                                 // 
                                "input3" => "",
                                "input4" => 0                                   // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "extractDataFromString",
                ],
                [
                    "type" => "transactionDetail",                                  // Winvestify standardized name   OK
                    "inputData" => [                                                // List of all concepts that the platform can generate  
                                                                                    // format ["concept string platform", "concept string Winvestify"]
                                "input2" => [
                                    0 => ["ingreso" => "Cash_deposit"],
                                    1 => ["retirado" => "Cash_withdrawal"],
                                    2 => ["inversion" => "Primary_market_investment"],
                        //            3 => ["inversion" => "Disinvestment"],  
                                    4 => ["principal" => "Capital_repayment"],
                                    5 => ["intereses" => "Regular_gross_interest_income"],
                                    6 => ["recargo" => "Delayed_interest_income"],
                                    7 => ["promocion" => "Incentives_and_bonus"],
                                    8 => ["comision" => "Commission"],
                                ]                    
                            ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
            "C" => [
                
                [
                    "type" => "amount",                                             // This is an "empty variable name". So "type" is
                    "inputData" => [                                                // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                    // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                    // and its content is the result of the "getAmount" method
                                "input4" => 4
                                ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "transactionDetail",                                  // Winvestify standardized name   OK
                    "inputData" => [                                                // List of all concepts that the platform can generate  
                                                                                    // format ["concept string platform", "concept string Winvestify"]
                                  "input2" => "#current.original_concept",                                                    
                                  "input3" => [
                                    0 => ["ingreso" => "Cash_deposit"],
                                    1 => ["retirado" => "Cash_withdrawal"],
                                    2 => ["inversion" => "Primary_market_investment"],
                                    3 => ["inversion" => "Disinvestment"],  
                                    4 => ["principal" => "Capital_repayment"],
                                    5 => ["intereses" => "Regular_gross_interest_income"],
                                    6 => ["recargo" => "Delayed_interest_income"],
                                    7 => ["promocion" => "Incentives_and_bonus"],
                                    8 => ["comision" => "Commission"],
                                ]                    
                            ],
                    "functionName" => "getComplexTransactionDetail",
                ]
            ], 
            "D" =>  [
                [
                    "type" => "investment_loanId",                                  // Typically used for generating a 'psuedo loanid' for platform related actions
                    "inputData" => [                                                // like for instance cash deposit or cash withdrawal
                                "input2" => "global_",                                    
                                "input3" => "rand",                   
                                ],
                    "functionName" => "generateId",
                ],
                [
                    "type" => "conceptChars",                                   // Winvestify standardized name
                    "inputData" => [
				"input2" => "#current.internalName",            // get Winvestify concept
                                ],
                    "functionName" => "getConceptChars",
                ]
            ]           
        ]
    ];

    
    protected $investmentConfigParms = [
        [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
     //       'separatorChar' => ";",
            'sortParameter' => array("investment_loanId")   // Used to "sort" the array and use $sortParameter as prime index.
        ]
    ];
    
    protected $expiredLoanConfigParms = [
        [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
     //       'separatorChar' => ";",
            'sortParameter' => array("investment_loanId")   // Used to "sort" the array and use $sortParameter as prime index.
        ]
    ];
    
    protected $valuesInvestment = [                                             // All types/names will be defined as associative index in array
        [
            "A" =>  [
                [
                    "type" => "investment_myInvestmentDate",                      // Winvestify standardized name
                    "inputData" => [
                                "input2" => "D.M.Y",
                                ],
                    "functionName" => "normalizeDate",
                ]                                    
            ],
            "B" => [
                "name" => "investment_loanId"                                   // Winvestify standardized name  OK
            ],
               
            "C" => [
                [
                    "type" => "investment_nominalInterestRate",                   // Winvestify standardized name   OK
                    "inputData" => [
                                "input2" => "100",
                                "input3" => 0
                                ],
                    "functionName" => "handleNumber",
                ]                                           
            ], 
            "D" =>  [
                "name" => "investment_originalDuration"
            ],
            "E" => [
                [
                    "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                    "inputData" => [
                                "input2" => "",
                                "input3" => ",",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment_typeOfInvestment",                        
                    "inputData" => [                                            // Get the "original" Mintos concept, which is used later on
                                "input2" => " ",                                // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ],
                [
                    "type" => "investment_typeOfInvestment",                                    
                    "inputData" => [                                                   
                                "input2" => "€",                                       
                                "input3" => "",
                            ],
                    "functionName" => "extractDataFromString",
                ],
                [
                    "type" => "investment_currency",                            // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                ],
            ],
            "F" => [
                "name" => "investment_capitalRepaymentFromP2P"
            ],
            /*"G" => DON'T TAKE, ASK ANTOINE*/
            /* "H" => DON'T TAKE, ASK ANTOINE*/
            "I" => [
                [
                    "type" => "investment_commissionPaid",                      // This is an "empty variable name". So "type" is
                    "inputData" => [                                            // obtained from $parser->TransactionDetails['type']
                                "input2" => "",                                 // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                // and its content is the result of the "getAmount" method
                                "input4" => 4
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            ///CHANGEEEE WITH REAL VALUE
            "J" =>  [
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
            ]
        ]
    ];
    
    protected $valuesExpiredLoan = [                                             // All types/names will be defined as associative index in array
        [
            "A" =>  [
                [
                    "type" => "investment_investmentDate",                      // Winvestify standardized name
                    "inputData" => [
                                "input2" => "D.M.Y",
                                ],
                    "functionName" => "normalizeDate",
                ]                                    
            ],
            "B" => [
                "name" => "investment_loanId"                                   // Winvestify standardized name  OK
            ],
               
            "C" => [
                [
                    "type" => "investment_nominalInterestRate",                   // Winvestify standardized name   OK
                    "inputData" => [
                                "input2" => "100",
                                "input3" => 0
                                ],
                    "functionName" => "handleNumber",
                ]                                         
            ], 
            "D" =>  [
                "name" => "investment_originalDuration"
            ],
            "E" => [
                [
                    "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                    "inputData" => [
                                "input2" => "",
                                "input3" => ",",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ],
                [
                    "type" => "investment_typeOfInvestment",   
                    "inputData" => [                                            // Get the "original" Mintos concept, which is used later on
                                "input2" => " ",                                // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ],
                [
                    "type" => "investment_typeOfInvestment",                                    
                    "inputData" => [                                                   
                                "input2" => "€",                                       
                                "input3" => "",
                            ],
                    "functionName" => "extractDataFromString",
                ],
                [
                    "type" => "investment_currency",                            // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                ],
            ],
            "F" => [
                "name" => "investment_capitalRepaymentFromP2P"
            ],
            /*"G" => DON'T TAKE, ASK ANTOINE*/
            /* "H" => DON'T TAKE, ASK ANTOINE*/
            "I" => [
                [
                    "type" => "investment_commissionPaid",                      // This is an "empty variable name". So "type" is
                    "inputData" => [                                            // obtained from $parser->TransactionDetails['type']
                                "input2" => "",                                 // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                // and its content is the result of the "getAmount" method
                                "input4" => 4
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "J" =>  [
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
                    "type" => "investment_originalState",     
                    "inputData" => [                                            // Get the "original" Zank concept, which is used later on
                                "input2" => "#current.investment_statusOfLoan", // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ]
            ]
        ]
    ];

    
    protected $amortizationConfigParms = array ('OffsetStart' => 1,
                                'offsetEnd'     => 0,
                                'separatorChar' => ";",
                                'sortParameter' => "investment_loanId"          // used to "sort" the array and use $sortParameter as prime index.
                                 ); 
    
    protected $valuesAmortizationTable = [
        1 => [
            [
                "type" => "amortizationtable_scheduledDate",                    // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "D/M/Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        2 => [
            [
                "type" => "amortizationtable_capitalRepayment",                 // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        3 => [
            [
                "type" => "amortizationtable_interest",                         // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
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
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        5 => [
            [
                "type" => "amortizationtable_comission",                        // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        6 => [
            [
                "type" => "amortizationtable_latePaymentFee",                   // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        8 => [
            "name" => "amortizationtable_paymentStatus"
        ]
    ];
    
    protected $callbacks = [
        "investment" => [
            "cleanTempArray" => [
                "findValueInArray" => [
                    "key" => "investment_statusOfLoan",
                    "function" => "verifyEqual",
                    "values" => ["Amortizado"],
                    "valueDepth" => 2
                ]
            ],
            "parserDataCallback" => [
                "investment_typeOfInvestment" => "translateTypeOfInvestment",
                "investment_statusOfLoan" => "translateLoanStatus"
            ]
        ],
        "expiredLoan" => [
            "cleanTempArray" => [
                "findValueInArray" => [
                    "key" => "investment_statusOfLoan",
                    "function" => "verifyNotEqual",
                    "values" => ["Amortizado"],
                    "valueDepth" => 2
                ]
            ],
            "parserDataCallback" => [
                "investment_typeOfInvestment" => "translateTypeOfInvestment",
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
    
        protected $investmentHeader = array(   
        'A' => 'Fecha',
        'B' => 'Préstamo',
        'C' => 'Rentabilidad',
        'D' => 'Plazo',
        'E' => 'Inversión',
        'F' => 'Capital amortizado',
        'G' => 'Intereses ordinarios',
        'H' => 'Intereses demora',
        'I' => 'Comision',
        'J' => 'Estado');
    
    protected $transactionHeader = array(
        'A' => 'Fecha',
        'B' => 'Tipo',
        'C' => 'Cantidad',
        'D' => 'Destino',
        'E' => 'Saldo');

    
    protected $tableStructure = '<table id="parte" class="table table-hover"><tr><th>Cuota</th>
                                                                <th class="info-tooltip">Fecha cobro <a href="#" data-toggle="tooltip" title="Fecha en la que se solicita el cobro al prestatario. Zank necesita 30 d&iacute;as despu&eacute;s para gestionar todos los cobros y enviarlos a tu monedero"><i class="fa fa-info-circle"></i></a></th>
                                                                <th>Principal</th>
                                                                <th>Intereses</th>
                                                                <th>Mensualidad</th>
                                                                <th>Comisi&oacute;n</th>
                                                                <th>Intereses demora</th>
                                                                <th>Estado</th>
                                                                <th>Informaci&oacute;n</th>
                                                            </tr><tr><td>INI</td>
                                                                        <td>16/12/2015</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>1</td>
                                                                        <td>01/01/2016</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td>0,37 &euro;</td>
                                                                        <td>0,37 &euro;</td>
                                                                        <td>-0,04 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>2</td>
                                                                        <td>01/02/2016</td>
                                                                        <td>1,76 &euro;</td>
                                                                        <td>0,71 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,08 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>3</td>
                                                                        <td>01/03/2016</td>
                                                                        <td>1,79 &euro;</td>
                                                                        <td>0,68 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,08 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>4</td>
                                                                        <td>01/04/2016</td>
                                                                        <td>1,81 &euro;</td>
                                                                        <td>0,66 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,08 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>5</td>
                                                                        <td>01/05/2016</td>
                                                                        <td>1,84 &euro;</td>
                                                                        <td>0,63 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,07 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>6</td>
                                                                        <td>01/06/2016</td>
                                                                        <td>1,87 &euro;</td>
                                                                        <td>0,61 &euro;</td>
                                                                        <td>2,53 &euro;</td>
                                                                        <td>-0,07 &euro;</td>
                                                                        <td>0,06 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>7</td>
                                                                        <td>01/07/2016</td>
                                                                        <td>1,89 &euro;</td>
                                                                        <td>0,58 &euro;</td>
                                                                        <td>2,50 &euro;</td>
                                                                        <td>-0,07 &euro;</td>
                                                                        <td>0,03 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>8</td>
                                                                        <td>01/08/2016</td>
                                                                        <td>1,92 &euro;</td>
                                                                        <td>0,55 &euro;</td>
                                                                        <td>2,63 &euro;</td>
                                                                        <td>-0,07 &euro;</td>
                                                                        <td>0,16 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>9</td>
                                                                        <td>01/09/2016</td>
                                                                        <td>1,95 &euro;</td>
                                                                        <td>0,53 &euro;</td>
                                                                        <td>2,60 &euro;</td>
                                                                        <td>-0,06 &euro;</td>
                                                                        <td>0,12 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>10</td>
                                                                        <td>01/10/2016</td>
                                                                        <td>1,97 &euro;</td>
                                                                        <td>0,50 &euro;</td>
                                                                        <td>2,57 &euro;</td>
                                                                        <td>-0,06 &euro;</td>
                                                                        <td>0,10 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>11</td>
                                                                        <td>01/11/2016</td>
                                                                        <td>2,00 &euro;</td>
                                                                        <td>0,47 &euro;</td>
                                                                        <td>2,53 &euro;</td>
                                                                        <td>-0,06 &euro;</td>
                                                                        <td>0,06 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>12</td>
                                                                        <td>01/12/2016</td>
                                                                        <td>2,03 &euro;</td>
                                                                        <td>0,44 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,05 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>13</td>
                                                                        <td>01/01/2017</td>
                                                                        <td>2,06 &euro;</td>
                                                                        <td>0,41 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,05 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>14</td>
                                                                        <td>01/02/2017</td>
                                                                        <td>2,09 &euro;</td>
                                                                        <td>0,38 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,05 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>15</td>
                                                                        <td>01/03/2017</td>
                                                                        <td>2,12 &euro;</td>
                                                                        <td>0,35 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,04 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>16</td>
                                                                        <td>01/04/2017</td>
                                                                        <td>2,15 &euro;</td>
                                                                        <td>0,32 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,04 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>17</td>
                                                                        <td>01/05/2017</td>
                                                                        <td>2,18 &euro;</td>
                                                                        <td>0,29 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,03 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>18</td>
                                                                        <td>01/06/2017</td>
                                                                        <td>2,21 &euro;</td>
                                                                        <td>0,26 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,03 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>19</td>
                                                                        <td>01/07/2017</td>
                                                                        <td>2,24 &euro;</td>
                                                                        <td>0,23 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,03 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>20</td>
                                                                        <td>01/08/2017</td>
                                                                        <td>2,27 &euro;</td>
                                                                        <td>0,20 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,02 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>21</td>
                                                                        <td>01/09/2017</td>
                                                                        <td>2,30 &euro;</td>
                                                                        <td>0,17 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,02 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-success estados-cuotas">CO</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota cobrada.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>22</td>
                                                                        <td>01/10/2017</td>
                                                                        <td>2,34 &euro;</td>
                                                                        <td>0,14 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,02 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-encobro estados-cuotas">EC</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota En cobro.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>23</td>
                                                                        <td>01/11/2017</td>
                                                                        <td>2,37 &euro;</td>
                                                                        <td>0,10 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,01 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label label-info estados-cuotas">DE</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota Devengandose.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>24</td>
                                                                        <td>01/12/2017</td>
                                                                        <td>2,40 &euro;</td>
                                                                        <td>0,07 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,01 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label estados-cuotas" style="background-color: #aaaaaa;">PD</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota pendiente.
                                                                                                                                                    </td>
                                                                    </tr><tr><td>25</td>
                                                                        <td>01/01/2018</td>
                                                                        <td>2,44 &euro;</td>
                                                                        <td>0,03 &euro;</td>
                                                                        <td>2,47 &euro;</td>
                                                                        <td>-0,00 &euro;</td>
                                                                        <td>0,00 &euro;</td>
                                                                        <td><span class="label estados-cuotas" style="background-color: #aaaaaa;">PD</span></td>
                                                                                                                                                <td>
                                                                                                                                                            Cuota pendiente.
                                                                                                                                                    </td>
                                                                    </tr></table>';
    
    
    
    function __construct() {
        parent::__construct();
        $this->i = 0;
        $this->typeFileTransaction = "xlsx";
        $this->typeFileInvestment = "xlsx";
        $this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";
        //$this->loanIdArray = array(8363);
        //$this->maxLoans = count($this->loanIdArray);
        //{"loanIds": "683":["8800", "8800"]}}
        //{"loanIds":{"686":["6b3649c5-9a6b-4cee-ac05-a55500ef480a","7e89377c-15fc-4de3-8b65-a55500ef6a1b"], "682":["629337", "629331", "629252"], "683":["8800", "8800"], "684":["472"]}}
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how much it will cost in total to obtain a loan for a certain amount
     * 	from a company. This includes fixed fee amortization fee(s) etc.
     * 
     * 	@param  int	$amount 	: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in months) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int			: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 2% of requested amount with a minimum of 20 €	Checked: 25-08-2016

        $minimumCommission = 12000;   // in  €cents

        $fixedCost = 2 * $amount / 100;
        if ($fixedCost < $minimumCommission) {
            $fixedCost = $minimumCommission;
        }

        $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12);
        $totalCost = $fixedCost + $interest + $amount;
        return $fixedCost + $interest + $amount;
    }

    /**
     * Collects the marketplace data.
     * 	ZANK is special as one has to login in order to see all the details of the offers in their marketplace
     * @param Array $companyBackup
     * @param Array $structure
     * @return array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList) {
        $reading = true; //Loop controller
        echo "Username: " . $this->config['company_username'] . " Password: " . $this->config['company_password'];
        $result = $this->companyUserLogin($this->config['company_username'], $this->config['company_password']);
        // echo __FUNCTION__ . __LINE__ . "<br>";
//set_time_limit(25);		// Zank is very very slow
        //echo $result; 
        $this->investmentDeletedList = $loanIdList;
        if (!$result) {   // Error while logging in
            echo __FUNCTION__ . __LINE__ . "login fail" . SHELL_ENDOFLINE;
            $tracings = "Tracing: " . SHELL_ENDOFLINE;
            $tracings .= __FILE__ . " " . __LINE__ . SHELL_ENDOFLINE;
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . SHELL_ENDOFLINE;
            $tracings .= SHELL_ENDOFLINE;
            $msg = "Error while entering user's portal. Wrong userid/password" . SHELL_ENDOFLINE;
            $msg = $msg . $tracings . SHELL_ENDOFLINE;
            $this->logToFile("Warning", $msg);
            exit;
        }


        $form = [
            "length" => 25,
            "start" => $this->start
        ];
        //echo 'aqui es ' . $form['start'];
        $url = array_shift($this->urlSequence);
        $this->url = $url;
        $totalArray = array();
        $readControl = 0; //Read control, stop the loop if it find existing and completed inversions
        while ($reading) {

            $reading = false;
            //echo __FUNCTION__ . __LINE__ . "start with first read<br>";
            $str = $this->getCompanyWebpage($url, $form);


//print_r($str);
            //echo __FUNCTION__ . __LINE__ . "<br>";

            $pos1 = stripos($str, '[');
            $pos2 = stripos($str, ']');
            $resultPreJSON = substr($str, $pos1, ($pos2 - $pos1 + 1));

            $jsonResults = json_decode($resultPreJSON, true);

            //If we have the page completed, load other page
            $numberOfInversions = count($jsonResults);
            //echo 'el numero es ' . $numberOfInversions;

            if ($numberOfInversions == $form['length']) {
                $reading = true;
                $form['start'] = $form['start'] + $form['length'];
            }
            //echo 'aqui es' . $form['start'];

            foreach ($jsonResults as $key => $jsonEntry) {

                if ($form['start'] == $form['length'] && $key == 0) { //Only compare the first entry
                    $structureRevision = $this->jsonRevision($structure, $jsonEntry);
                    if ($structureRevision[1]) { //Structural error
                        $totalArray = false; //Stop reading in error
                        $reading = false;
                        break;
                    }
                }

                $inversionReadController = 0; //Varible, unset a already existing and completed inversion in the backup

                $tempArray = array();
                $tempArray['marketplace_country'] = 'ES'; //Zank is in spain
                $tempArray['marketplace_loanReference'] = strip_tags($jsonEntry['Prestamo']);
                $tempArray['marketplace_category'] = strtoupper(strip_tags($jsonEntry['Categoria']));
                $tempArray['marketplace_rating'] = strtoupper(strip_tags($jsonEntry['Categoria']));
                $tempArray['marketplace_interestRate'] = $this->getPercentage(strip_tags($jsonEntry['Rentabilidad']));

                if (strtoupper(strip_tags($jsonEntry['Tipo'])) == 'F') {
                    $tempArray['marketplace_productType'] = 3;
                } else if (strtoupper(strip_tags($jsonEntry['Tipo'])) == 'P') {
                    $tempArray['marketplace_productType'] = 2;
                }
                $tempInformation = explode("€", strip_tags($jsonEntry['Informacion']));
                $tempArray['marketplace_amount'] = $this->getMonetaryValue($tempInformation[0]);

                list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($tempInformation[1]);

                $dom = new DOMDocument;
                $dom->loadHTML($jsonEntry['Completado']);
                $dom->preserveWhiteSpace = false;
                $divs = $dom->getElementsByTagName('div');


                /* 	
                  <div class="tabla-faltan info-tooltip clompletado">
                  <a href="#" data-toggle="tooltip" data-original-title="Inversores que han invertido.">
                  23
                  <i class="fa fa-user">
                  </i>
                  </a>
                  </div>

                  <div class="progress">
                  <div class="progress-bar progress-bar-striped active" role="progressbar" style="width:24.66%">24,66 %
                  </div>
                  </div>

                  <div class="tabla-faltan text-warning">Quedan 30 días
                  </div>

                  -------------------

                  <div class="tabla-faltan info-tooltip clompletado">
                  <a href="#" data-toggle="tooltip" data-original-title="Inversores que han invertido.">
                  16
                  <i class="fa fa-user">
                  </i><
                  /a>
                  </div>

                  <div class="progress">
                  <div class="progress-bar progress-bar-success" role="progressbar" style="width:100%">Completado
                  </div>
                  </div>

                 */



                $index = 0;
                foreach ($divs as $div) {
                    switch ($index) {
                        case 0:
                            $tempArray['marketplace_numberOfInvestors'] = str_replace(['+', '-'], '', filter_var($div->nodeValue, FILTER_SANITIZE_NUMBER_INT));

                            break;
                        case 1:
                            if (stristr(trim($div->nodeValue), "%") == true) {
                                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($div->nodeValue);
                                $tempArray['marketplace_statusLiteral'] = 'En Proceso';
                            } else if ($div->nodeValue == 'Completado') {
                                $tempArray['marketplace_subscriptionProgress'] = 10000;
                                $tempArray['marketplace_statusLiteral'] = 'Completado';
                                $tempArray['marketplace_status'] = PERCENT;
                                //Read inversions limiter controller
                                foreach ($companyBackup as $inversionBackup) {
                                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                                        $inversionReadController = 1;
                                    }
                                }
                            } else if (strpos($div->nodeValue, 'mortiza') != false || $div->nodeValue == 'Amortizado' || $div->nodeValue == 'Retrasado') {

                                $tempArray['marketplace_subscriptionProgress'] = 10000;
                                $tempArray['marketplace_statusLiteral'] = $div->nodeValue;

                                if (strpos($div->nodeValue, 'mortiza') != false) {
                                    $tempArray['marketplace_status'] = CONFIRMED;
                                } else if ($div->nodeValue == 'Amortizado' || $div->nodeValue == 'Retrasado') {
                                    $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                }

                                //Read inversions limiter controller
                                foreach ($companyBackup as $inversionBackup) {
                                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && ($inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status'])) {
                                        $inversionReadController = 1;
                                    }
                                }
                            } else if (!$div->nodeValue) {
                                $tempArray['marketplace_subscriptionProgress'] = 0;
                                $tempArray['marketplace_statusLiteral'] = 'Cancelado';
                                $tempArray['marketplace_status'] = REJECTED;
                            }
                            break;
                        case 2:
                            // Error in HTML of ZANK website source. It generates and extra "/div" tag. Do not do anything
                            break;
                        case 4:  //
                            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($div->nodeValue);
                            break;
                        case 3:  //
                            list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($div->nodeValue);
                            break;
                        default:
                    }
                    $index++;
                }

                $dom = new DOMDocument;
                $dom->loadHTML($jsonEntry['Finalidad']);
                $dom->preserveWhiteSpace = false;

                $as = $dom->getElementsByTagName('a');
                foreach ($as as $a) {
                    $tempArray['marketplace_purpose'] = $a->getAttribute('data-original-title');
                }




                if ($inversionReadController == 1) {
                    //echo __FUNCTION__ . __LINE__ . "Inversion completada ya existe" . HTML_ENDOFLINE . SHELL_ENDOFLINE;
                    $readControl++;
                    unset($tempArray);
                    echo 'Advance:' . $readControl . SHELL_ENDOFLINE;
                }
                if ($readControl > 25) {
                    echo __FUNCTION__ . __LINE__ . "Demasiadas inversiones completadas ya existentes, forzando salida";
                    $reading = false;
                    echo 'Break';
                    break;
                } else {
                    // echo 'Add:<br>';  
                    $this->investmentDeletedList = $this->marketplaceLoanIdWinvestifyPfpComparation($this->investmentDeletedList, $tempArray);
                    $totalArray[] = $tempArray;
                    unset($tempArray);
                    /* echo 'Total<br>';
                      $this->print_r2($totalArray);
                      echo 'Added : <br>';
                      $this->print_r2($tempArray); */
                    //echo __FILE__ . " " . __LINE__ . "<br>";
                }
            }
            if($reading == false){
                echo "Exit zank";
                break;
            }
        }


        foreach ($this->investmentDeletedList as $key => $id) {
            if (empty($id)) {
                unset($this->investmentDeletedList[$key]);
            }
        }
        echo 'Search this investments: ' . SHELL_ENDOFLINE;
        $this->print_r2($this->investmentDeletedList);
        $hiddenInvestments = $this->readHiddenInvestment($this->investmentDeletedList);
        echo 'Hidden: ' . SHELL_ENDOFLINE;
        $this->print_r2($hiddenInvestments);

        $this->companyUserLogout();
        $totalArray = array_merge($totalArray, $hiddenInvestments);
        //$this->print_r2($totalArray);  
        foreach($totalArray as $keyInvestment => $investment){
            if(empty($investment)){
                unset($totalArray[$keyInvestment]);            
            }
        }
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /*     * Read hidden investment.
     * 
     * @param array $investmentDeletedList loan id list
     * @return array investments info list
     */

    function readHiddenInvestment($investmentDeletedList) {

        $url = array_shift($this->urlSequence);

        $tempArray = array();
        $newTotalArray = array();
        //Read investment info
        foreach ($investmentDeletedList as $loanId) {
            if (empty($loanId)) {
                continue;
            }
            echo 'loan id: ' . substr($loanId, 3) . SHELL_ENDOFLINE;
            echo $url . substr($loanId, 3) . SHELL_ENDOFLINE;
            $str = $this->getCompanyWebpage($url . substr($loanId, 3));
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($str);

            $container = $this->getElements($dom, 'div', 'class', 'col-lg-12 col-md-12 col-sm-12 col-xs-12 col-bottom-box col-bottom-box-interno');
            foreach ($container as $div) {
                $subdivs = $div->getElementsByTagName('div');
                /* foreach($subdivs as $subkey => $subdiv){
                  echo 'Div: ' . HTML_ENDOFLINE;
                  echo $subkey . " => " . $subdiv->nodeValue . HTML_ENDOFLINE;
                  } */
                $tempArray['marketplace_country'] = 'ES'; //Zank is in spain
                $tempArray['marketplace_loanReference'] = $loanId;
                //$tempArray['marketplace_category'] = $subdivs[31]->nodeValue;
                $tempArray['marketplace_rating'] = trim($subdivs[31]->nodeValue);
                $tempArray['marketplace_interestRate'] = $this->getPercentage($subdivs[35]->nodeValue);
                list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue(trim($subdivs[23]->nodeValue));
                $tempArray['marketplace_statusLiteral'] = trim($subdivs[15]->nodeValue);
                $status = $tempArray['marketplace_statusLiteral'];
                if ($status == 'Completado') {
                    $tempArray['marketplace_status'] = PERCENT;
                    $tempArray['marketplace_subscriptionProgress'] = 10000;
                } else if ($status == 'Amortizado' || $status == 'Retrasado') {
                    $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                    $tempArray['marketplace_subscriptionProgress'] = 10000;
                } else if (strpos($status, 'mortiza') != false) {
                    $tempArray['marketplace_status'] = CONFIRMED;
                    $tempArray['marketplace_subscriptionProgress'] = 10000;
                } else if ($status == 'Publicado') {
                    $tempArray['marketplace_subscriptionProgress'] = $subdivs[39]->nodeValue;
                } else if ($status == 'Cancelado') {
                    $tempArray['marketplace_subscriptionProgress'] = 0;
                    $tempArray['marketplace_status'] = REJECTED;
                }

                $tempArray['marketplace_sector'] = $subdivs[124]->getElementsByTagName('h4')[0]->nodeValue;
                $tempArray['marketplace_purpose'] = $subdivs[124]->getElementsByTagName('p')[0]->nodeValue;

                echo $subdivs[126]->nodeValue . SHELL_ENDOFLINE;
                $tds = $subdivs[126]->getElementsByTagName('td');
                $tempArray['marketplace_requestorLocation'] = $tds[5]->nodeValue;
            }
            echo 'Hidden investment: ' . SHELL_ENDOFLINE;
            echo print_r($tempArray) . SHELL_ENDOFLINE;
            $newTotalArray[] = $tempArray;
            unset($tempArray);
        }
        /* echo 'return new array: ' . HTML_ENDOFLINE;
          print_r($newTotalArray); */
        return $newTotalArray;
    }

    /**
     * collect all investment
     * @param Array $structure
     * @param Int $start
     * @return Array
     */
    function collectHistorical($structure, $start) {
        /**
         * ID FOR TEST
         * F0039675
         * F0042205
         * F0042193
         */
        $totalArray = array();
        $result = $this->companyUserLogin($this->config['company_username'], $this->config['company_password']);

        if (!$result) {   // Error while logging in
            echo __FUNCTION__ . __LINE__ . "login fail" . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $tracings = "Tracing: " . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $tracings .= __FILE__ . " " . __LINE__ . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $tracings .= HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $msg = "Error while entering user's portal. Wrong userid/password" . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $msg = $msg . $tracings . HTML_ENDOFLINE . SHELL_ENDOFLINE;
            $this->logToFile("Warning", $msg);
            exit;
        }

        $url = array_shift($this->urlSequence);
        echo 'url: ' . $url . HTML_ENDOFLINE . SHELL_ENDOFLINE;
        $str = $this->getCompanyWebpage($url);
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        // if ($totalArray !== false) {
        /* Zank pagination must be done using curl, form are parameters sent in curl */

        $form = [
            "length" => 25, //Number of investment for page
            "start" => $start, //First investment of the page
        ];

        $str = $this->getCompanyWebpage($url, $form); //Data reading

        $totalArray = array();
        $pos1 = stripos($str, '[');
        $pos2 = stripos($str, ']');
        $resultPreJSON = substr($str, $pos1, ($pos2 - $pos1 + 1));

        $jsonResults = json_decode($resultPreJSON, true);
        $numberOfInversions = count($jsonResults);

        if ($numberOfInversions == $form['length']) {
            $reading = true;
            $form['start'] = $form['start'] + $form['length'];
        } else {
            $form['start'] = false;
        }


        foreach ($jsonResults as $jsonEntry) {

            if ($form['start'] == $form['length'] && $key == 0) { //Only compare the first entry
                $structureRevision = $this->jsonRevision($structure, $jsonEntry);
                if ($structureRevision[1]) { //Structural error
                    $totalArray = false; //Stop reading in error
                    $form['start'] = false;
                    break;
                }
            }


            $inversionReadController = 0; //Varible, unset a already existing and completed inversion in the backup

            $tempArray = array();
            $tempArray['marketplace_country'] = 'ES'; //Zank is in spain
            $tempArray['marketplace_loanReference'] = strip_tags($jsonEntry['Prestamo']);
            $tempArray['marketplace_category'] = strtoupper(strip_tags($jsonEntry['Categoria']));
            $tempArray['marketplace_rating'] = strtoupper(strip_tags($jsonEntry['Categoria']));
            $tempArray['marketplace_interestRate'] = $this->getPercentage(strip_tags($jsonEntry['Rentabilidad']));
            if (strtoupper(strip_tags($jsonEntry['Tipo'])) == 'F') {
                $tempArray['marketplace_productType'] = 3;
            } else if (strtoupper(strip_tags($jsonEntry['Tipo'])) == 'P') {
                $tempArray['marketplace_productType'] = 2;
            }
            $tempInformation = explode("€", strip_tags($jsonEntry['Informacion']));
            $tempArray['marketplace_amount'] = $this->getMonetaryValue($tempInformation[0]);

            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($tempInformation[1]);

            $dom = new DOMDocument;
            $dom->loadHTML($jsonEntry['Completado']);
            $dom->preserveWhiteSpace = false;
            $divs = $dom->getElementsByTagName('div');


            $index = 0;
            foreach ($divs as $div) {
                switch ($index) {
                    case 0:
                            $tempArray['marketplace_numberOfInvestors'] = str_replace(['+', '-'], '', filter_var($div->nodeValue, FILTER_SANITIZE_NUMBER_INT));
                        break;
                    case 1:
                        if (stristr(trim($div->nodeValue), "%") == true) {
                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($div->nodeValue);
                            $tempArray['marketplace_statusLiteral'] = 'En Proceso';
                        } else if ($div->nodeValue == 'Completado') {
                            $tempArray['marketplace_subscriptionProgress'] = 10000;
                            $tempArray['marketplace_statusLiteral'] = 'Completado';
                            $tempArray['marketplace_status'] = PERCENT;
                        } else if (strpos($div->nodeValue, 'mortiza') != false || $div->nodeValue == 'Amortizado' || $div->nodeValue == 'Retrasado') {
                            $tempArray['marketplace_subscriptionProgress'] = 10000;
                            $tempArray['marketplace_statusLiteral'] = $div->nodeValue;
                            if (strpos($div->nodeValue, 'mortiza') != false) {
                                $tempArray['marketplace_status'] = CONFIRMED;
                            } else if ($div->nodeValue == 'Amortizado' || $div->nodeValue == 'Retrasado') {
                                $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                            }
                        } else if (!$div->nodeValue) {
                            $tempArray['marketplace_subscriptionProgress'] = 0;
                            $tempArray['marketplace_statusLiteral'] = 'Cancelado';
                            $tempArray['marketplace_status'] = REJECTED;
                        }
                        break;
                    case 2:
                        // Error in HTML of ZANK website source. It generates and extra "/div" tag. Do not do anything
                        break;
                    case 4:  //
                        list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($div->nodeValue);
                        break;
                    case 3:  //
                        list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($div->nodeValue);
                        break;
                    default:
                }
                $index++;
            }

            $dom = new DOMDocument;
            $dom->loadHTML($jsonEntry['Finalidad']);
            $dom->preserveWhiteSpace = false;

            $as = $dom->getElementsByTagName('a');
            foreach ($as as $a) {
                $tempArray['marketplace_purpose'] = $a->getAttribute('data-original-title');
            }

            array_push($totalArray, $tempArray);
            //echo __FILE__ . " " . __LINE__ . "<br>";
            $this->print_r2($tempArray);
            unset($tempArray);
        }
        //}
        //echo 'AQUI ES ' . $reading;
        $this->companyUserLogout();
        return [$totalArray, $form['start'], null, $structureRevision[0], $structureRevision[2]]; //$form['start'] is the next page, return false if is the last page
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     * Collects the investment data of the user
     * 	
     * @param string $str   It is the web converted to string of the company
     * @return array	Data of each investment of the user as an element of an array
     */
    function collectUserInvestmentDataParallel($str) {


        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // needed so I can read the csrf code
                break;
            case 1:
                //Change account
                $this->credentials['_username'] = $this->user;
                $this->credentials['_password'] = $this->password;
                //$this->credentials['_username'] = "Klauskuk@gmail.com";
                //$this->credentials['_password'] = "P2Pes2017";
                // get login page
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                $this->verifyNodeHasElements($forms);
                $index = 0;
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }

                foreach ($forms as $form) {
                    $index = $index + 1;
                    if ($index == 1) {
                        continue;
                    }
                    $inputs = $form->getElementsByTagName('input');
                    $this->verifyNodeHasElements($inputs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }

                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('value'))) {  // look for the csrf code
                            $this->credentials[$name] = $input->getAttribute('value');
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                break;
            case 2:
                //This is an error because we don't verify if we have entered
                if ($str == 200 or $str == 103) {
                    //echo "CODE 103 or 200 received, so do it again , OK <br>";
                    $this->idForSwitch++;
                    $this->doCompanyLoginMultiCurl($this->credentials);
                    //$this->mainPortalPage = $str;
                    $this->resultMiZank = true;
                }
                break;
            case 3:

                $this->mainPortalPage = $str;

                //echo "user = $user and pw = $password<br>";
                if (!$this->resultMiZank) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Zank login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    //fix this problem
                    return $this->getError(__LINE__, __FILE__);
                }
                echo "LOGIN CONFIRMED";
                // We are at page: "MI ZANK". Look for the "internal user identification"
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                $scripts = $dom->getElementsByTagName('script');
                $this->verifyNodeHasElements($scripts);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }

                foreach ($scripts as $script) {
                    $position = stripos($script->nodeValue, "$.ajax");
                    if ($position !== false) {  // We found an entry
                        echo "ENTRY FOUND";
                        break;
                    }
                }
                $testArray = explode(":", $script->nodeValue);
                $this->print_r2($testArray);
                $this->userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

                if (!is_numeric($this->userId)) {
                    echo "<br>An error has occured, could not find internal userId<br>";
                }

                $needle = "kpi_panel";

                $index = 0;
                $ps = $dom->getElementsByTagName('p');
                /* if ($ps.length > 0) {
                  Verify there are som elements
                  } */
                $this->verifyNodeHasElements($ps);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($ps as $p) {
                    $class = trim($p->getAttribute('class'));
                    $position = stripos($class, $needle);
                    if ($position !== false) {  // found a kpi
                        switch ($index) {
                            case 0:
                                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            case 1:
                                $this->tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            case 2:
                                $this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            case 4:
                                $this->tempArray['global']['profitibility'] = $this->getPercentage($p->nodeValue);
                                break;
                        }
                        $index++;
                    }
                }
                // goto page "MI CARTERA"
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // load Webpage into a string variable so it can be parsed	
                break;

            case 4:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $needle = "kpi_panel";
                $index = 0;

                // Look for the kpi's 
                $ps = $dom->getElementsByTagName('p');
                $this->verifyNodeHasElements($ps);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($ps as $p) {
                    $class = trim($p->getAttribute('class'));
                    $position = stripos($class, $needle);
                    if ($position !== false) {  // found a kpi
                        switch ($index) {
                            case 0:
                                $this->tempArray['global']['totalInvestments'] = $this->getMonetaryValue($p->nodeValue); // Money still tied up in active investment(s)
                                break;
                            case 1:
                                $this->tempArray['global']['activeInvestments'] = $p->nodeValue; // The number of active investments
                                break;
                        }
                        $index++;
                    }
                }

                // Download the list of the individual investments of this user
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 5:
                $url = array_shift($this->urlSequence);
                $this->url = $url . $this->userId . "/0";
                //We put a postitem into the curl call because initialy we don't take the first investment
                //Nor more than 25 investment, so we alter those values
                $form = [
                    "length" => 100,
                    "start" => $this->start];

                $this->idForSwitch++;
                //Add here the Edu's adaption to the url, we add the form into the call
                $this->getCompanyWebpageMultiCurl($this->url, $form);
                break;
            case 6:
                $temp = json_decode($str, $assoc = true);
                $this->numberOfInvestments = 0;
                $numberJsonInvestments = count($temp['data']);
                foreach ($temp['data'] as $key => $item) {  // mapping of the data to a generic, own format.
                    // Keep all which don't have status == "amortizado"
                    //$this->data1[$key]['status'] = 10;    // dummy value											
                    if (strpos($item['Estado'], "Retrasado")) {
                        $this->data1[$key]['status'] = PAYMENT_DELAYED;
                    }
                    if (strpos($item['Estado'], "Amortizaci")) {
                        $this->data1[$key]['status'] = OK;
                    }

                    if (strpos($item['Estado'], "Amortizado")) {
                        $this->data1[$key]['status'] = TERMINATED_OK;
                    }

                    //		if (!($data1[$key]['status'] <> OK OR $data1[$key]['status'] <> PAYMENT_DELAYED)) {
                    //			continue;									// flush non required loans
                    //		echo "FLUSH";
                    //		}

                    if ($this->data1[$key]['status'] == TERMINATED_OK) {
                        unset($this->data1[$key]);
                        continue;
                    }

                    $this->numberOfInvestments++;
                    $day = 1; //substr($item['Fecha'],0,2);

                    if ($item['Plazo'] <= 50) {
                        $month = substr($item['Fecha'], 3, 2) + 1;

                        $year = substr($item['Fecha'], 6, 4);
                        if ($month == 13) {
                            $month = 1;
                            $year++;
                        }
                    }
                    if ($item['Plazo'] >= 50) {
                        $month = substr($item['Fecha'], 3, 2) - 1;

                        $year = substr($item['Fecha'], 6, 4);
                        if ($month == 0) {
                            $month = 1;
                        }
                    }

                    //if($month==13){$month=1; $year = $year+1; } 
                    $date = $year . "/" . $month . "/" . $day;
                    $date = date('d/m/Y', strtotime("+" . $item['Plazo'] . "months", strtotime($date)));
                    //$date = date('d/m/Y', strtotime("+".$item['Plazo']." months", strtotime($date)));

                    $this->data1[$key]['loanId'] = $item['Prestamo'];
                    $this->data1[$key]['dateOriginal'] = $item['Fecha'];
                    $this->data1[$key]['date'] = $date;
                    $this->data1[$key]['interest'] = $this->getPercentage($item['Rentabilidad']);
                    $this->data1[$key]['invested'] = $this->getMonetaryValue($item['Inversion']);
                    $this->data1[$key]['amortized'] = $this->getMonetaryValue($item['Amortizado']);
                    $this->data1[$key]['profitGained'] = $this->getPercentage($item['InteresesOrdinarios']);
                    $this->data1[$key]['duration'] = $item['Plazo'] . " Meses";
                    $this->data1[$key]['commission'] = $this->getMonetaryValue($item['Comision']);
                    $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$key]['invested'];
                }
                if ($numberJsonInvestments != 0 && $numberJsonInvestments % 100 == 0) {
                    //If investments are 100, we verify that there is no more, so we recall starting at 100 investments
                    $this->start = $this->start + 100;
                    $form = [
                        "length" => 100,
                        "start" => $this->start];

                    $this->idForSwitch = 6;
                    //Add here the Edu's adaption to the url
                    $this->getCompanyWebpageMultiCurl($this->url, $form);
                } else {
                    $this->data1 = array_values($this->data1);
                    $this->tempArray['global']['investments'] = count($this->data1);
                    //echo __FILE__ . " " . __LINE__ . "<br>";
                    $this->tempArray['investments'] = $this->data1;
                    $this->print_r2($this->tempArray);
                    return $this->tempArray;
                }
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($user, $password) {
// velascogestorpatrimonial@hotmail.com
// D547336  wrong
        $resultMiZank = $this->companyUserLogin($user, $password);
        echo "user = $user and pw = $password<br>";
        if (!$resultMiZank) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Zank login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            return;
        }
        echo "LOGIN CONFIRMED";
// We are at page: "MI ZANK". Look for the "internal user identification"
        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
        $dom->preserveWhiteSpace = false;

        $scripts = $dom->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $position = stripos($script->nodeValue, "$.ajax");
            if ($position !== false) {  // We found an entry
                echo "ENTRY FOUND";
                break;
            }
        }
        $testArray = explode(":", $script->nodeValue);
        $this->print_r2($testArray);
        $userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

        if (!is_numeric($userId)) {
            echo "<br>An error has occured, could not find internal userId<br>";
        }

        $needle = "kpi_panel";

        $index = 0;
        $ps = $dom->getElementsByTagName('p');
        foreach ($ps as $p) {
            $class = trim($p->getAttribute('class'));
            $position = stripos($class, $needle);

            if ($position !== false) {  // found a kpi
                switch ($index) {
                    case 0:
                        $tempArray['global']['myWallet'] = $this->getMonetaryValue($p->nodeValue);
                        break;
                    case 1:
                        $tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($p->nodeValue);
                        break;
                    case 2:
                        $tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($p->nodeValue);
                        break;
                    case 4:
                        $tempArray['global']['profitibility'] = $this->getPercentage($p->nodeValue);
                        break;
                }
                $index++;
            }
        }

// goto page "MI CARTERA"
        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $needle = "kpi_panel";
        $index = 0;

// Look for the kpi's 
        $ps = $dom->getElementsByTagName('p');
        foreach ($ps as $p) {
            $class = trim($p->getAttribute('class'));
            $position = stripos($class, $needle);
            if ($position !== false) {  // found a kpi
                switch ($index) {
                    case 0:
                        $tempArray['global']['totalInvestments'] = $this->getMonetaryValue($p->nodeValue); // Money still tied up in active investment(s)
                        break;
                    case 1:
                        $tempArray['global']['activeInvestments'] = $p->nodeValue; // The number of active investments
                        break;
                }
                $index++;
            }
        }

// Download the list of the individual investments of this user
        $str = $this->getCompanyWebpage();

// build the Web URL for downloading the list of the individual investments of this user
        $url = array_shift($this->urlSequence);
        $url = $url . $userId . "/0";
        //Start is the value from we get the investments
        $start = 0;
        $form = [
            "length" => 100,
            "start" => $start];
        //This value is 100 to enter in the while but when we are inside we change to the real number of investments
        $numberJsonInvestments = 100;
        while ($numberJsonInvestments % 100 == 0) {
            $str = $this->getCompanyWebpage($url, $form);
            $temp = json_decode($str, $assoc = true);
            //We take the real number of investments
            $numberJsonInvestments = count($temp['data']);

            $numberOfInvestments = 0;
            foreach ($temp['data'] as $key => $item) {  // mapping of the data to a generic, own format.										// Keep all which don't have status == "amortizado"
                //$data1[$key]['status'] = 10;  // dummy value											
                if (strpos($item['Estado'], "Retrasado")) {
                    $data1[$key]['status'] = PAYMENT_DELAYED;
                }
                if (strpos($item['Estado'], "Amortizaci")) {
                    $data1[$key]['status'] = OK;
                }
                if (strpos($item['Estado'], "Amortizado")) {
                    $data1[$key]['status'] = TERMINATED_OK;
                }

                //		if (!($data1[$key]['status'] <> OK OR $data1[$key]['status'] <> PAYMENT_DELAYED)) {
                //			continue;									// flush non required loans
                //		echo "FLUSH";
                //		}
                if ($data1[$key]['status'] == TERMINATED_OK) {
                    unset($data1[$key]);
                    continue;
                }

                $numberOfInvestments = $numberOfInvestments + 1;
                $day = 1; //substr($item['Fecha'],0,2);
                if ($item['Plazo'] <= 50) {
                    $month = substr($item['Fecha'], 3, 2) + 1;

                    $year = substr($item['Fecha'], 6, 4);
                    if ($month == 13) {
                        $month = 1;
                        $year++;
                    }
                }
                if ($item['Plazo'] >= 50) {
                    $month = substr($item['Fecha'], 3, 2) - 1;

                    $year = substr($item['Fecha'], 6, 4);
                    if ($month == 0) {
                        $month = 1;
                    }
                }


                //if($month==13){$month=1; $year = $year+1; } 
                $date = $year . "/" . $month . "/" . $day;
                $date = date('d/m/Y', strtotime("+" . $item['Plazo'] . "months", strtotime($date)));
                //$date = date('d/m/Y', strtotime("+".$item['Plazo']." months", strtotime($date)));
                $data1[$key]['loanId'] = $item['Prestamo'];
                $data1[$key]['dateOriginal'] = $item['Fecha'];
                $data1[$key]['date'] = $date;
                $data1[$key]['interest'] = $this->getPercentage($item['Rentabilidad']);
                $data1[$key]['invested'] = $this->getMonetaryValue($item['Inversion']);
                $data1[$key]['amortized'] = $this->getMonetaryValue($item['Amortizado']);
                $data1[$key]['profitGained'] = $this->getPercentage($item['InteresesOrdinarios']);
                $data1[$key]['duration'] = $item['Plazo'] . " Meses";
                $data1[$key]['commission'] = $this->getMonetaryValue($item['Comision']);
                $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
            }
            //If the investments are 100, we repeat the process to take more investments
            //And increasea start by 100
            if ($numberJsonInvestments != 0 && $numberJsonInvestments % 100 == 0) {
                $start = $start + 100;
            }
        }

        $data1 = array_values($data1);
        $tempArray['global']['investments'] = count($data1);
        $tempArray['investments'] = $data1;

        $this->print_r2($tempArray);
        return $tempArray;
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
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // needed so I can read the csrf code
                break;
            case 1:
                //Change account
                $this->credentials['_username'] = $this->user;
                $this->credentials['_password'] = $this->password;
                //$this->credentials['_username'] = "Klauskuk@gmail.com";
                //$this->credentials['_password'] = "P2Pes2017";
                // get login page
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                $this->verifyNodeHasElements($forms);
                $index = 0;
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                foreach ($forms as $form) {
                    $index = $index + 1;
                    if ($index == 1) {
                        continue;
                    }
                    $inputs = $form->getElementsByTagName('input');
                    $this->verifyNodeHasElements($inputs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }

                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('value'))) {  // look for the csrf code
                            $this->credentials[$name] = $input->getAttribute('value');
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                break;
            case 2:
                //This is an error because we don't verify if we have entered
                if ($str == 200 or $str == 302) {
                    //echo "CODE 103 or 200 received, so do it again , OK <br>";
                    $this->idForSwitch++;
                    $this->doCompanyLoginMultiCurl($this->credentials);
                    //$this->mainPortalPage = $str;
                    $this->resultMiZank = true;
                }
                break;
            case 3:

                $this->mainPortalPage = $str;

                //echo "user = $user and pw = $password<br>";
                if (!$this->resultMiZank) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Zank login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    //fix this problem
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }
                echo "LOGIN CONFIRMED";
                // We are at page: "MI ZANK". Look for the "internal user identification"
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                $scripts = $dom->getElementsByTagName('script');
                $this->verifyNodeHasElements($scripts);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                foreach ($scripts as $script) {
                    $position = stripos($script->nodeValue, "$.ajax");
                    if ($position !== false) {  // We found an entry
                        echo "ENTRY FOUND";
                        break;
                    }
                }
                $testArray = explode(":", $script->nodeValue);
                $this->print_r2($testArray);
                $this->userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

                if (!is_numeric($this->userId)) {
                    echo "<br>An error has occured, could not find internal userId<br>";
                }

                $needle = "kpi_panel";

                $index = 0;
                $ps = $dom->getElementsByTagName('p');
                $this->verifyNodeHasElements($ps);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($ps as $p) {
                    $class = trim($p->getAttribute('class'));
                    $position = stripos($class, $needle);
                    if ($position !== false) {  // found a kpi
                        switch ($index) {
                            case 0:
                                $this->tempArray['global']['myWallet'] = $p->nodeValue;
                                break;
                            case 1:
                                $this->tempArray['global']['outstandingPrincipal'] = $p->nodeValue;
                                break;
                           /* case 2:
                                $this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($p->nodeValue);
                                break;
                            /*case 4:
                                $this->tempArray['global']['yield'] = $this->getPercentage($p->nodeValue);
                                break;*/
                        }
                        $index++;
                    }
                }
                // goto page "MI CARTERA"
                $url = array_shift($this->urlSequence) . $this->userId;
                echo "investment url: " . $url;
                $this->idForSwitch++;
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->investmentHeader;
                $this->getPFPFileMulticurl($url, null, false, false, $this->fileName);  // load Webpage into a string variable so it can be parsed	
                break;
            case 4:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
                }
                $path = $this->getFolderPFPFile();
                $file = $path . DS . $this->fileName;
                $newFile = $path . DS . $this->nameFileExpiredLoan . $this->numFileExpiredLoan . "." . $this->typeFileExpiredLoan;
                $this->copyFile($file, $newFile);
                echo 'URL SEQUENCE FLOW: ' . SHELL_ENDOFLINE;
                print_r($this->urlSequence);
                $url = array_shift($this->urlSequence) . $this->userId;

                echo "Cash Flow Url: " . SHELL_ENDOFLINE;
                echo $url;
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->headerComparation = $this->transactionHeader;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, false, $this->fileName);  // load Webpage into a string variable so it can be parsed	
                break;
            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break; 
            case 6: 
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $divs = $dom->getElementsByTagName('div');
                $this->verifyNodeHasElements($divs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                /*foreach($divs as $key => $div){
                    if($div->getAttribute('class') == 'panel-body'){
                        echo " " . $key . "=>" . $div->nodeValue . " ";
                    }
                }*/
                $this->tempArray['global']['activeInvestment'] = $divs[28]->nodeValue;
                return $this->tempArray; 
        }
    }

    /**
     * Get amortization tables of user investments
     * 
     * @param string $str It is the web converted to string of the company.
     * @return array html of the tables
     */
    function collectAmortizationTablesParallel($str = null) {
        switch ($this->idForSwitch) {
            case 0:
                $this->loanTotalIds = $this->loanIds;
                $this->loanKeys = array_keys($this->loanIds);
                $this->loanIds = array_values($this->loanIds);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // needed so I can read the csrf code
                break;
            case 1:
                //Change account
                $this->credentials['_username'] = $this->user;
                $this->credentials['_password'] = $this->password;
                //$this->credentials['_username'] = "Klauskuk@gmail.com";
                //$this->credentials['_password'] = "P2Pes2017";
                // get login page
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                $this->verifyNodeHasElements($forms);
                $index = 0;
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }

                foreach ($forms as $form) {
                    $index = $index + 1;
                    if ($index == 1) {
                        continue;
                    }
                    $inputs = $form->getElementsByTagName('input');
                    $this->verifyNodeHasElements($inputs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }

                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('value'))) {  // look for the csrf code
                            $this->credentials[$name] = $input->getAttribute('value');
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials);
                break;
            case 2:
                //This is an error because we don't verify if we have entered
                if ($str == 200 or $str == 302) {
                    //echo "CODE 103 or 200 received, so do it again , OK <br>";
                    $this->idForSwitch++;
                    $this->doCompanyLoginMultiCurl($this->credentials);
                    //$this->mainPortalPage = $str;
                    $this->resultMiZank = true;
                }
                break;
            case 3:

                $this->mainPortalPage = $str;

                //echo "user = $user and pw = $password<br>";
                if (!$this->resultMiZank) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Zank login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    //fix this problem
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                echo "LOGIN CONFIRMED";
                // We are at page: "MI ZANK". Look for the "internal user identification"
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                $scripts = $dom->getElementsByTagName('script');
                $this->verifyNodeHasElements($scripts);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }

                foreach ($scripts as $script) {
                    $position = stripos($script->nodeValue, "$.ajax");
                    if ($position !== false) {  // We found an entry
                        echo "ENTRY FOUND";
                        break;
                    }
                }
                $testArray = explode(":", $script->nodeValue);
                $this->print_r2($testArray);
                $this->userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

                if (!is_numeric($this->userId)) {
                    echo "<br>An error has occured, could not find internal userId<br>";
                }

                $needle = "kpi_panel";

                $index = 0;
                $ps = $dom->getElementsByTagName('p');
                $this->verifyNodeHasElements($ps);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // load Webpage into a string variable so it can be parsed	
                break;

            case 4:
                if (empty($this->tempUrl['investmentUrl'])) {
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);
                }
                echo "Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url = $this->tempUrl['investmentUrl'] . substr($this->loanIds[$this->i],1);
                echo "the table url is: " . $url;
                $this->i++;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url);  // Read individual investment
                break;

            case 5:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
               
                $tables = $dom->getElementsByTagName('table');
                $this->verifyNodeHasElements($tables);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($tables as $table) {
                    echo "Read table: ";
                    if ($table->getAttribute('id') == 'parte' || $table->getAttribute('id') == 'todo') {
                        $AmortizationTable = new DOMDocument();
                        $clone = $table->cloneNode(TRUE); //Clene the table
                        $AmortizationTable->appendChild($AmortizationTable->importNode($clone, TRUE));
                        $AmortizationTableString = $AmortizationTable->saveHTML();
                        $revision = $this->structureRevisionAmortizationTable($AmortizationTableString,$this->tableStructure);
                        if ($revision) {
                            echo "Comparation ok";
                            $this->tempArray['tables'][$this->loanIds[$this->i - 1]] = $AmortizationTableString; //Save the html string in temp array
                            $this->tempArray['correctTables'][$this->loanKeys[$this->i - 1]] = $this->loanIds[$this->i - 1];
                        } else {
                            echo 'Not so ok';
                            $this->tempArray['errorTables'][$this->loanKeys[$this->i - 1]] = $this->loanIds[$this->i - 1];
                        }
                        $this->tempArray[$this->loanIds[$this->i - 1]] = $AmortizationTableString;
                    }
                }
                if ($this->i < $this->maxLoans) {
                    $this->idForSwitch = 4;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentUrl'] . $this->loanIds[$this->i - 1]);
                    break;
                } else {
                    return $this->tempArray;
                    break;
                }
        }
    }

    /**
     *
     * 	Checks if the user can login to its portal. Typically used for linking a company account
     * 	to our account.
     * 	For Zank we actually have to do a "double" login. The first login returns a 200 OK
     * 	and the 
     * 	
     * 	@return boolean	true: user has succesfully logged in 
     * 					false: user could not log in
     * 	
     */
    function companyUserLogin($user, $password) {
        $totalArray = array();
        $credentials = array();

        $credentials['_username'] = $user;
        $credentials['_password'] = $password;

// get login page
        $str = $this->getCompanyWebpage();  // needed so I can read the csrf code
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $dom->getElementsByTagName('form');

        $index = 0;

        foreach ($forms as $form) {
            $index = $index + 1;
            if ($index == 1) {
                continue;
            }
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('value'))) {  // look for the csrf code
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }
            }
        }

        $str = $this->doCompanyLogin($credentials);

        /* $stri = $this->getCompanyWebpage('https://www.zank.com.es/investor/overview');
          $dom = new DOMDocument;
          $dom->loadHTML($stri);
          $dom->preserveWhiteSpace = false;
          $login = $this->getElements($dom, "a");
          //print_r($login); */

        if ($str == 302 || $str == 200 || $str == 103) {

//		echo "CODE 103 or 200 received, so do it again , OK <br>";
            $str = $this->doCompanyLogin($credentials);
            $this->mainPortalPage = $str;
            return true;
        }

        return false;
    }

    /**
     *
     * 	Logout of user from to company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout() {

        //$str = $this->doCompanyLogout();
        $this->getCompanyWebpage();
        return true;
    }

    /**
     * Dom clean for structure revision
     * 
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {
        $node1 = $this->clean_dom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('src', 'alt', 'href', 'style', 'id', 'title'));

        $node1 = $this->clean_dom($node1, array(//We only want delete class of the td tag, not class of the other tags
            array('typeSearch' => 'element', 'tag' => 'td'),
                ), array('class'));

        $node2 = $this->clean_dom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'img'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('src', 'alt', 'href', 'style', 'id', 'title'));

        $node2 = $this->clean_dom($node2, array(//We only want delete class of the td tag, not class of the other tags
            array('typeSearch' => 'element', 'tag' => 'td'),
                ), array('class'));

        $structureRevision = $this->verify_dom_structure($node1, $node2);
        return $structureRevision;
    }
    
    /**
     * Function to translate loan type for Zank file
     * 
     * @param string $inputData It is string to convert to integer
     * @return int It is the loan type converted to integer
     */
    public function translateTypeOfInvestment($inputData) {
        $data = WIN_INVESTMENT_TYPE_MANUALINVESTMENT;
        $inputData = mb_strtoupper($inputData, "UTF-8");
        switch ($inputData) {
            case "AUTO":
                $data = WIN_INVESTMENT_TYPE_AUTOMATEDINVESTMENT;
                break;
        }
        return $data;
    }
    
     /**
     * Function to translate the company specific loan status to the Winvestify standardized
     * loan type
      * 
     * @param string $inputData     company specific loan status
     * @return int                  Winvestify standardized loan status
     */ 
    public function translateLoanStatus($inputData){
        $data = WIN_LOANSTATUS_UNKNOWN;
        $inputData = mb_strtoupper(trim($inputData), "UTF-8");
        $input = explode(" ", $inputData);
         switch ($input[0]) {
            case "PUBLICADO":
                $data = WIN_LOANSTATUS_WAITINGTOBEFORMALIZED;
                break;
            case "CANCELADO":
                $data = WIN_LOANSTATUS_WAITINGTOBEFORMALIZED;
                break;
            case "COMPLETADO":
                $data = WIN_LOANSTATUS_WAITINGTOBEFORMALIZED;
                break;
            case "RETRASADO":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "AMORTIZACIÓN":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;    
            case "AMORTIZADO":
                $data = WIN_LOANSTATUS_FINISHED;
                break;
        }
        return $data;
    }
    
    /**
     * Function to translate the company specific loan type to the Winvestify standardized
     * loan type
     * 
     * @param string $inputData     company specific loan type
     * @return int                  Winvestify standardized loan type
     */
    public function translateLoanType($inputData) {

    }
    
    /**
     * Function to translate the company specific amortization method to the Winvestify standardized
     * amortization type
     * 
     * @param string $inputData     company specific amortization method
     * @return int                  Winvestify standardized amortization method
     */
    public function translateAmortizationMethod($inputData) {

    }   
     
    /**
     * Function to translate the company specific payment frequency to the Winvestify standardized
     * payment frequency
     * 
     * @param string $inputData     company specific payment frequency
     * @return int                  Winvestify standardized payment frequency
     */
    public function translatePaymentFrequency($inputData) {
        
    }
        
    /**
     * Function to translate the type of investment market to an to the Winvestify standardized
     * investment market concept
     * 
     * @param string $inputData     company specific investment market concept
     * @return int                  Winvestify standardized investment marke concept
     */
    public function translateInvestmentMarket($inputData) {
        
    }
    
    /**
     * Function to translate the company specific investmentBuyBackGuarantee to the Winvestify standardized
     * investmentBuyBackGuarantee
     * 
     * @param string $inputData     company specific investmentBuyBackGuarantee
     * @return int                  Winvestify standardized investmentBuyBackGuarantee
     */
    public function translateInvestmentBuyBackGuarantee($inputData) {
        
    }

    function structureRevisionAmortizationTable($node1, $node2) {

        $dom1 = new DOMDocument();
        $dom1->loadHTML($node1);

        $dom2 = new DOMDocument();
        $dom2->loadHTML($node2);

        $dom1 = $this->cleanDom($dom1, array(
            array('typeSearch' => 'element', 'tag' => 'table')), array('id', 'style'));
        $dom1 = $this->cleanDomTagNotFirst($dom1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'tr')));

        $dom2 = $this->cleanDom($dom2, array(
            array('typeSearch' => 'element', 'tag' => 'table')), array('id', 'style'));
        $dom2 = $this->cleanDomTagNotFirst($dom2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'tr')));

        
        echo 'compare structure';
        $structureRevision = $this->verifyDomStructure($dom1, $dom2);
        echo $structureRevision;
        return $structureRevision;
    }

}

