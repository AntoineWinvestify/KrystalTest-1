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
 * @version 0.1
 * @date 2016-10-25
 * @package
 *
 *
 * 
 * 2017-08-23 version_0.2
 * Created
 * link account
 * 
 * 2017-10-24 version_0.3
 * Integration of parsing amortization tables with Gearman and fileparser
 * 
 * 2017-03-11 version 0.4
 * Header added in amortization tables.
 * 
 * 2017-06-11 version 0.5
 * Finalized investment download in getPfpFiles
 *  
 */

/**
 * Contains the code required for accessing the website of "Twino".
 * Parser AmortizationTables                                            [OK, tested]
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[Not OK]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Ok, not tested]
 * parallelization                                                      [OK, tested]
 */
class twino extends p2pCompany {

    protected $statusDownloadUrl = null;
// TWINO
// Processing Date	Booking Date	Type	Description	Loan Number	amount
// 8/3/2017 20:39	8/3/2017 0:00	REPAYMENT	PRINCIPAL	06-185114001	1.0544
// 8/3/2017 18:52	8/3/2017 0:00	REPAYMENT	PRINCIPAL	06-337436001	5.2947

    protected $valuesTransaction = [// All types/names will be defined as associative index in array
        [
            "A" => [
                [
                    "type" => "date", // Winvestify standardized name 
                    "inputData" => [
                        "input2" => "d/m/Y", // Input parameters. The first parameter
                    // is ALWAYS the contents of the cell
                    ],
                    "functionName" => "normalizeDate",
                ],
            ],
            "C" => [
                "name" => "transactionDetail",
            ],
            "D" => [// Simply changing name of column to the Winvestify standardized name
                [
                    "type" => "transactionDetail",
                    "inputData" => [
                        "input2" => [
                            0 => ["FUNDING" => "Cash_deposit"], // OK
                            1 => ["PRINCIPAL BUY_SHARES" => "Primary_market_investment"],
                            2 => ["PRINCIPAL EARLY_FULL_REPAYMENT" => "Capital_repayment"],
                            3 => ["PRINCIPAL REPAYMENT" => "Capital_repayment"], //OK
                            4 => ["PRINCIPAL BUYBACK" => "Principal_buyback"], // OK    
                            5 => ["INTEREST BUYBACK" => "Interest_income_buyback"], // OK
                            6 => ["INTEREST REPAYMENT" => "Regular_gross_interest_income"], //
                            7 => ["INTEREST SCHEDULE" => "Regular_gross_interest_income"],
                            8 => ["PENALTY REPAYMENT" => "Late_payment_fee_income"], // OK                                       
                            9 => ["INTEREST EXTENSION" => "Incentive_and_bonus"],
                            10 => ["PRINCIPAL REPURCHASE" => "Principal_buyback"],
                            11 => ["INTEREST REPURCHASE" => "Interest_income_buyback"],
                            12 => ["INTEREST EARLY_FULL_REPAYMENT" => "Regular_gross_interest_income"],
                            13 => ["INTEREST ACCRUED" => "Regular_gross_interest_income"],
                            //TAKE INTO ACCOUNT THAT IT COULD BE NEGATIVE
                            //NEEDS FURTHER INFORMATION, SPEAK WITH ANTOINE
                            14 => ["PRINCIPAL CURRENCY_FLUCTUATION" => "Currency_fluctuation_positive"],
                            15 => ["PRINCIPAL RECOVERY" => "Recoveries"],
                            16 => ["PRINCIPAL WRITEOFF" => "Write-off"],
                            17 => ["WITHDRAWAL" => "Cash_withdrawal"]
                        ], // Thousands seperator, typically "."
                        "input3" => "#current.transactionDetail", // Decimal seperator, typically ","
                    // is ALWAYS the contents of the cell
                    ],
                    "functionName" => "getMultipleInputTransactionDetail"
                ]
            ],
            "E" => [// Simply changing name of column to the Winvestify standardized name
                "name" => "investment_loanId"
            ],
            "F" => [// Simply changing name of column to the Winvestify standardized name
                [
                    "type" => "amount", // This is *mandatory* field which is required for the 
                    "inputData" => [// "transactionDetail"
                        "input2" => "", // and which BY DEFAULT is a Winvestify standardized variable name.
                        "input3" => ".", // and its content is the result of the "getAmount" method
                        "input4" => 4
                    ],
                    "functionName" => "getAmount",
                ]
            ]
        ]
    ];
// Not finished
    protected $valuesInvestment = [// All types/names will be defined as associative index in array
        [
            "A" => [
                "name" => "investment_country"                              // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "loanId",
            ],
            "C" => [
                [
                    "type" => "investment_investmentDate", // Winvestify standardized name 
                    "inputData" => [
                        "input2" => "m/d/Y", // Input parameters. The first parameter
                    // is ALWAYS the contents of the cell
                    ],
                    "functionName" => "normalizeDate",
                ],
            ],
            "D" => [
                "name" => "investment_riskRating",
            ],
            "E" => [
                "name" => "investment_originalState",
            ],
            "F" => [
                "name" => "investment_nominalInterestRate"
            ],
            "G" => [
                "name" => "investment_expectedAnnualYield"
            ],
            //"H" => ASK ANTOINE Remaining Term
            "I" => [
                "name" => "investment_originalDuration"
            ],
            //"J" => ASK ANTOINE Extended
            //"K" => IT IS NEXT PAYMENT, ASK ANTOINE IF IT NEEDED TO TAKE 
            "L" => [
                "name" => "investment_capitalRepaymentFromP2P"
            ],
            "M" => [
                [
                    "type" => "investment_myInvestment", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ".",
                        "input4" => 4
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            //"N" => ASK ANTOINE Interest income
            "O" => [
                "name" => "investment_outstandingPrincipalFromP2P"
            ],
            "P" => [
                "name" => "investment_forSale"
            ]
        ]
    ];
    protected $valuesAmortizationTable = [
        3 => [
            [
                "type" => "amortizationtable_scheduledDate", // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "Y-M-D",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        4 => [
            [
                "type" => "amortizationtable_capitalAndInterestPayment", // Winvestify standardized name  OK
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
                "type" => "amortizationtable_capitalRepayment", // Winvestify standardized name  OK
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
                "type" => "amortizationtable_interest", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        12 => [
            "name" => "amortizationtable_paymentStatus"
        ]
    ];
    
    protected $transactionConfigParms = [
        [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
            'sortParameter' => array("date","investment_loanId") // used to "sort" the array and use $sortParameter(s) as prime index.               
        ]
    ];
    
    protected $investmentConfigParms = [
        [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
            'sortParameter' => array("investment_loanId")  // used to "sort" the array and use $sortParameter as prime index.
       ]
    ]; 
    
    protected $amortizationConfigParms = array('OffsetStart' => 0,
        'offsetEnd' => 0,
        //       'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );
    
    protected $callbacks = [
        "investment" => [
            "parserDataCallback" => [
                "status" => "translateLoanStatus"
            ]
        ]
    ];
    protected $investmentHeader = array('A' => "Country",
        'B' => "No.",
        'C' => "Date of investment",
        'D' => "Risk class",
        'E' => "Status",
        'F' => "Rate",
        'G' => "Expected return",
        'H' => "Remaining Term",
        'I' => "Agreement Term",
        'J' => "Extended",
        'K' => "Next payment",
        'L' => "Repaid principal, €",
        'M' => "Bought shares, €",
        'N' => "Interest income, €",
        'O' => "Loan balance, €",
        'P' => "For sale, €");
    
    protected $transactionHeader = array(
        'A' => "Processing Date",
        'B' => "Booking Date",
        'C' => "Type",
        'D' => "Description",
        'E' => "Loan Number",
        'F' => "amount");
    
    protected $expiredLoansHeader = array('A' => "Country",
        'B' => "No.",
        'C' => "Date of investment",
        'D' => "Risk class",
        'E' => "Status",
        'F' => "Rate",
        'G' => "Expected return",
        'H' => "Remaining Term",
        'I' => "Agreement Term",
        'J' => "Extended",
        'K' => "Next payment",
        'L' => "Repaid principal, €",
        'M' => "Bought shares, €",
        'N' => "Interest income, €",
        'O' => "Loan balance, €",
        'P' => "For sale, €");
    
    protected $tableStructure = '<table><tr><td>id</td><td>status</td><td>statusDetail</td><td>startDate</td><td>dueDate</td><td>closedDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>statusDetail</td><td>startDate</td><td>dueDate</td><td>closedDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>statusDetail</td><td>startDate</td><td>dueDate</td><td>closedDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>statusDetail</td><td>startDate</td><td>dueDate</td><td>closedDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>id</td><td>status</td><td>startDate</td><td>dueDate</td><td>openAmount</td><td>openPrincipal</td><td>openInterest</td><td>invoicedAmount</td><td>invoicedPrincipal</td><td>invoicedInterest</td><td>extensionsCount</td><td>delayed</td><td>performanceStatus</td></tr><tr><td>2478590</td><td>CLOSED</td><td>PAID</td><td>2017-9-25</td><td>2017-10-25</td><td>Array</td><td>0</td><td>0</td><td>0</td><td>111.26</td><td>100.71</td><td>10.55</td><td>0</td><td></td><td>PAID_IN_TIME</td></tr><tr><td>2478591</td><td>CLOSED</td><td>PAID</td><td>2017-10-26</td><td>2017-11-25</td><td>Array</td><td>0</td><td>0</td><td>0</td><td>111.26</td><td>101.55</td><td>9.71</td><td>0</td><td></td><td>PAID_IN_TIME</td></tr><tr><td>2478592</td><td>CLOSED</td><td>PAID</td><td>2017-11-26</td><td>2017-12-25</td><td>Array</td><td>0</td><td>0</td><td>0</td><td>111.26</td><td>102.4</td><td>8.86</td><td>0</td><td></td><td>PAID_IN_TIME</td></tr><tr><td>2478593</
td><td>CLOSED</td><td>PAID</td><td>2017-12-26</td><td>2018-1-25</td><td>Array</td><td>0</td><td>0</td><td>0</td><td>111.25</td><td>103.25</td><td>8.01</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478594</td><td>OPEN</td><td>2018-1-26</td><td>2018-2-25</td><td>111.25</td><td>104.11</td><td>7.15</td><td>111.25</td><td>104.11</td><td>7.15</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478595</td><td>OPEN</td><td>2018-2-26</td><td>2018-3-25</td><td>111.25</td><td>104.98</td><td>6.28</td><td>111.25</td><td>104.98</td><td>6.28</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478596</td><td>OPEN</td><td>2018-3-26</td><td>2018-4-25</td><td>111.26</td><td>105.85</td><td>5.4</td><td>111.26</td><td>105.85</td><td>5.4</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478597</td><td>OPEN</td><td>2018-4-26</td><td>2018-5-25</td><td>111.25</td><td>106.73</td><td>4.52</td><td>111.25</td><td>106.73</td><td>4.52</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478598</td><td>OPEN</td><td>2018-5-26</td><td>2018-6-25</td><td>111.25</td><td>107.62</td><td>3.63</td><td>111.25</td><td>107.62</td><td>3.63</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478599</td><td>OPEN</td><td>2018-6-26</td><td>2018-7-25</td><td>111.25</td><td>108.52</td><td>2.74</td><td>111.25</td><td>108.52</td><td>2.74</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478600</td><td>OPEN</td><td>2018-7-26</td><td>2018-8-25</td><td>111.25</td><td>109.42</td><td>1.83</td><td>111.25</td><td>109.42</td><td>1.83</td><td>0</td><td></td><td>CURRENT</td></tr><tr><td>2478601</td><td>OPEN</td><td>2018-8-26</td><td>2018-9-25</td><td>111.25</td><td>110.33</td><td>0.92</td><td>111.25</td><td>110.33</td><td>0.92</td><td>0</td><td></td><td>CURRENT</td></tr></table>';
    

    function __construct() {
        parent::__construct();
        $this->i = 0;
        $this->typeFileTransaction = "xlsx";
        $this->typeFileInvestment = "xlsx";
        $this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";

        //$this->loanIdArray = array(629337, 629331, 629252);  
        //$this->maxLoans = count($this->loanIdArray);
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
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY twino DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */


        $credentials['name'] = $user;
        $credentials['password'] = $password;
        //$credentials['googleAnalyticClientId'] = '1778227581.1503479723';
        $payload = json_encode($credentials);

        //echo $payload;
        $this->doCompanyLoginRequestPayload($payload); //do login

        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $str = $this->getCompanyWebpage(); //This url return true if you are logged, false if not.
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;


        if ($str == "true") {
            $confirm = true;
        }


        //$this->companyUserLogout($url);
        if ($confirm) {
            return true;
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
            /////////////LOGIN
            case 0:
                $credentials['name'] = $this->user;
                $credentials['password'] = $this->password;
                //$credentials['googleAnalyticClientId'] = '1778227581.1503479723';
                $payload = json_encode($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($payload, true);
                break;
            case 1:
                echo $str;
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 2:
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                echo " Twino Login: " . $str;
                if ($str == "false") {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Twino login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                } else {
                    echo 'twino login ok' . SHELL_ENDOFLINE;
                }

                //Download
                $credentialsFile = '{"page":1,"pageSize":20,"query":{"sortOption":{"propertyName":"created","direction":"DESC"},"loanStatuses":["CURRENT","EXTENDED","DELAYED","DEFAULTED"]}}'; // ADD ,"REPAID","SOLD","RECOVERED" to download all investment
                $this->tempUrl['ExportInvestment'] = array_shift($this->urlSequence);
                echo $this->tempUrl['ExportInvestment'];
                print_r($this->urlSequence);
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl($this->tempUrl['ExportInvestment'], $credentialsFile, true);
                break;
            case 4:
                //Download investment
                echo $str;
                $response = json_decode($str, true);
                print_r($response);
                if (empty($this->statusDownloadUrl)) {
                    echo 'Reading download status: ' . SHELL_ENDOFLINE;
                    $this->statusDownloadUrl = array_shift($this->urlSequence);
                    $this->idForSwitch++;
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId'] . '/status');
                    break;
                }
                $this->idForSwitch++;
                break;
            case 5:
                echo $str . SHELL_ENDOFLINE;
                $response = json_decode($str, true);
                print_r($response);
                if ($response['reportReady'] == true) {
                    echo 'Status true, downloading' . SHELL_ENDOFLINE;
                    $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                    $this->headerComparation = $this->investmentHeader;
                    $this->tempUrl['refererInvestment'] = array_shift($this->urlSequence);
                    if ($this->originExecution == WIN_QUEUE_ORIGIN_EXECUTION_LINKACCOUNT) { //Only download expired loans the first time(in link account)
                        $this->idForSwitch++;
                    } else {
                        $this->idForSwitch = 9;
                    }

                    $this->getPFPFileMulticurl($this->statusDownloadUrl . $response['reportId'] . '/download', $this->tempUrl['refererInvestment'], false, false, $this->fileName);
                } else {
                    sleep(2);
                    echo 'Not ready yet' . SHELL_ENDOFLINE;
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId'] . '/status');
                    $this->idForSwitch--;
                    echo 'Repeat Case: ' . $this->idForSwitch . SHELL_ENDOFLINE;
                }
                break;

            //Dowanload finalized investment/expired loans
            case 6:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
                }
                //Download
                $credentialsFile = '{"page":1,"pageSize":20,"query":{"sortOption":{"propertyName":"created","direction":"DESC"},"loanStatuses":["REPAID","SOLD","RECOVERED"]}}'; // ADD ,"REPAID","SOLD","RECOVERED" to download all investment
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl($this->tempUrl['ExportInvestment'], $credentialsFile, true);

                break;
            case 7:
                //Download investment
                echo $this->idForSwitch;
                $response = json_decode($str, true);
                print_r($response);
                if ($response['reportReady'] == true) {
                    echo 'Status true, downloading' . SHELL_ENDOFLINE;
                    $this->numFileInvestment++;
                    $this->fileName = "expiredLoans." . $this->typeFileInvestment;
                    $this->headerComparation = $this->expiredLoansHeader;
                    $this->idForSwitch = 9;
                    $this->getPFPFileMulticurl($this->statusDownloadUrl . $response['reportId'] . '/download', $this->tempUrl['refererInvestment'], false, false, $this->fileName);
                } else {
                    sleep(2);
                    $this->controlVariable = false;
                    $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId'] . '/status');
                    $this->idForSwitch = 8;
                }
                break;

            case 8:
                echo $this->idForSwitch . SHELL_ENDOFLINE;
                $response = json_decode($str, true);
                print_r($response);
                if ($response['reportReady'] == true) {
                    echo 'Status true, downloading' . SHELL_ENDOFLINE;
                    $this->fileName = "expiredLoans." . $this->typeFileInvestment;
                    $this->headerComparation = $this->expiredLoansHeader;
                    $this->idForSwitch++;
                    echo 'downloading in ' . $this->statusDownloadUrl . $response['reportId'] . '/download';
                    $this->getPFPFileMulticurl($this->statusDownloadUrl . $response['reportId'] . '/download', $this->tempUrl['refererInvestment'], false, false, $this->fileName);
                } else {
                    sleep(2);
                    echo 'Not ready yet' . SHELL_ENDOFLINE;
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId'] . '/status');
                    $this->idForSwitch = 7;
                    echo 'Repeat Case: ' . $this->idForSwitch . SHELL_ENDOFLINE;
                }
                break;


            case 9:

                if (!$this->verifyFileIsCorrect()) {
                    echo 'error';
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
                }

                $this->continue = $this->downloadTimePeriod($this->dateInit, $this->period);

                echo 'Preparing cashflow download';
                //Download cash flow
                $dateInit = date("Y,m,d", strtotime($this->dateInitPeriod)); //date must be a string with the year,month,day format
                $dateFinish = date('Y,m,d', strtotime($this->dateFinishPeriod));
                $dateInitArray = explode(",", $dateInit);
                $dateFinishArray = explode(",", $dateFinish);
                $credentialsFile = '{"page":1,"pageSize":20,"sortDirection":"DESC","sortField":"created","processingDateFrom":[{$year1},{$month1},{$day1}],"processingDateTo":[{$year2},{$month2},{$day2}],"transactionTypeList":[{"transactionType":"REPAYMENT"},{"transactionType":"EARLY_FULL_REPAYMENT"},{"transactionType":"BUY_SHARES","positive":false},{"transactionType":"BUY_SHARES","positive":true},{"transactionType":"FUNDING","positive":true},{"transactionType":"FUNDING","positive":false},{"transactionType":"EXTENSION"},{"transactionType":"ACCRUED_INTEREST"},{"transactionType":"BUYBACK"},{"transactionType":"SCHEDULE"},{"transactionType":"RECOVERY"},{"transactionType":"REPURCHASE"},{"transactionType":"LOSS_ON_WRITEOFF"},{"transactionType":"WRITEOFF"},{"transactionType":"CURRENCY_FLUCTUATION"},{"transactionType":"BUY_OUT"}],"accountTypeList":[]}';
                $credentialsFile = strtr($credentialsFile, array('{$year1}' => (int) $dateInitArray[0]));
                $credentialsFile = strtr($credentialsFile, array('{$year2}' => (int) $dateFinishArray[0]));
                $credentialsFile = strtr($credentialsFile, array('{$month1}' => (int) $dateInitArray[1]));
                $credentialsFile = strtr($credentialsFile, array('{$month2}' => (int) $dateFinishArray[1]));
                $credentialsFile = strtr($credentialsFile, array('{$day1}' => (int) $dateInitArray[2]));
                $credentialsFile = strtr($credentialsFile, array('{$day2}' => (int) $dateFinishArray[2]));
                if (empty($this->tempUrl['transactionDownloadInit'])) {
                    $this->tempUrl['transactionDownloadInit'] = array_shift($this->urlSequence);
                    $this->tempUrl['transactionReferer'] = array_shift($this->urlSequence);
                }
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl($this->tempUrl['transactionDownloadInit'], $credentialsFile, true);
                break;
            case 10:
                echo $str;
                $response = json_decode($str, true);
                print_r($response);
                if ($response['reportReady'] == false) {
                    echo 'Not ready yet';
                    $this->idForSwitch++;
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId'] . '/status');
                    echo 'Repeat Case: ' . $this->idForSwitch;
                    break;
                } else {
                    echo 'Status true, downloading';
                    $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "_" . $this->numPartFileTransaction . "." . $this->typeFileTransaction;
                    $this->headerComparation = $this->transactionHeader;
                    $this->numPartFileTransaction++;
                    if ($this->continue) {
                        $this->idForSwitch = 9;
                    } else {
                        $this->idForSwitch = 12;
                    }
                    $this->getPFPFileMulticurl($this->statusDownloadUrl . $response['reportId'] . '/download', $this->tempUrl['transactionReferer'], false, false, $this->fileName);
                    break;
                }
            case 11:
                echo $str;
                $response = json_decode($str, true);
                print_r($response);
                if ($response['reportReady'] == true) {
                    echo 'Status true, downloading' . SHELL_ENDOFLINE;
                    $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "_" . $this->numPartFileTransaction . "." . $this->typeFileTransaction;
                    $this->headerComparation = $this->transactionHeader;
                    $this->numPartFileTransaction++;
                    if ($this->continue) {
                        $this->idForSwitch = 9;
                    } else {
                        $this->idForSwitch++;
                    }
                    $this->getPFPFileMulticurl($this->statusDownloadUrl . $response['reportId'] . '/download', $this->tempUrl['transactionReferer'], false, false, $this->fileName);
                } else {
                    echo 'Not ready yet' . SHELL_ENDOFLINE;
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId'] . '/status');
                    $this->idForSwitch--;
                    echo 'Repeat Case: ' . $this->idForSwitch . SHELL_ENDOFLINE;
                }
                break;
            case 12:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                //return $tempArray["global"] = "waiting_for_global";
                break;
            case 13:
                echo $str;
                $variables = json_decode($str, true);
                print_r($variables);

                $this->tempArray['global']['outstandingPrincipal'] = $variables['investments'];  //Capital vivo
                $this->tempArray['global']['myWallet'] = $variables['investmentBalance']; //My wallet
                //twino doesnt have number of investments-
                //$this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($variables['interest']); //Interest

                return $this->tempArray;
        }
    }

    /**
     * Get amortization tables of user investments
     * @param string $str It is the web converted to string of the company.
     * @return array html of the tables
     */
    function collectAmortizationTablesParallel($str = null) {
        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                $this->loanTotalIds = $this->loanIds;
                $this->loanKeys = array_keys($this->loanIds);
                $this->loanIds = array_values($this->loanIds);                
                $credentials['name'] = $this->user;
                $credentials['password'] = $this->password;
                //$credentials['googleAnalyticClientId'] = '1778227581.1503479723';
                $payload = json_encode($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($payload, true);
                break;
            case 1:
                echo $str;
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 2:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $containers = $dom->getElementsByTagName('section');
                /*$this->verifyNodeHasElements($containers);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }*/
                foreach ($containers as $container) {
                    $divs = $container->getElementsByTagName('div');
                    $this->verifyNodeHasElements($divs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    foreach ($divs as $key => $div) {
                        echo "Key " . $key . " is " . $div->nodeValue;
                    }
                }


                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                echo " Twino Login: " . $str;
                if ($str == "false") {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Twino login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }
                echo 'twino login ok' . SHELL_ENDOFLINE;

                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 4:
                if (empty($this->tempUrl['investmentUrl'])) {
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);
                }
                echo "---------------BASE URL TABLE: " . $this->tempUrl['investmentUrl'] . " -------------------";
                echo "////////////////////////////////////////Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url = $this->tempUrl['investmentUrl'] . $this->loanIds[$this->i];
                echo "the table url is: " . $url . " //////////////////////////////////////////////";
                $this->i++;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url);  // Read individual investment
                break;

            case 5:
                echo $str;
                echo "////////////////////////////////// Read table: //////////////////////////////////////";
                $arrayAmortizationTable = json_decode($str, true);

                for ($i = 0; $i < count($arrayAmortizationTable['scheduleItems']); $i++) {
                    $arrayAmortizationTable['scheduleItems'][$i]['startDate'] = implode("-", $arrayAmortizationTable['scheduleItems'][$i]['startDate']);
                    $arrayAmortizationTable['scheduleItems'][$i]['dueDate'] = implode("-", $arrayAmortizationTable['scheduleItems'][$i]['dueDate']);
                }
                //print_r($arrayAmortizationTable['scheduleItems']);
                $table = $this->arrayToTableConversion($arrayAmortizationTable['scheduleItems']); //Sent an array and return a html table
                $revision = $this->structureRevisionAmortizationTable($table,$this->tableStructure);
                        if ($revision) {
                            echo "Comparation ok";
                            $this->tempArray['tables'][$this->loanIds[$this->i - 1]] = $AmortizationTableString; //Save the html string in temp array
                            $this->tempArray['correctTables'][$this->loanKeys[$this->i - 1]] = $this->loanIds[$this->i - 1];
                        } else {
                            echo 'Not so ok';
                            $this->tempArray['errorTables'][$this->loanKeys[$this->i - 1]] = $this->loanIds[$this->i - 1];
                        }
                $this->tempArray[$this->loanIds[$this->i - 1]] = $table;
                echo "_-_-_-_-_-_-_-_table is : " . $table . "_-_-_-_-_-_-_-_";

                if ($this->i < $this->maxLoans) {
                    $this->idForSwitch = 4;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentUrl'] . $this->loanIds[$this->i - 1]);
                    break;
                } else {
                    //$this->verifyErrorAmortizationTable();
                    return $this->tempArray;
                    break;
                }
        }
    }

    /**
     * Function to translate the company specific loan status to the Winvestify standardized
     * loan type
     * @param string $inputData     company specific loan status
     * @return int                  Winvestify standardized loan status
     */
    public function translateLoanStatus($inputData) {
        $status = WIN_LOANSTATUS_UNKNOWN;
        $inputData = strtoupper(trim($inputData));
        switch ($inputData) {
            case "CURRENT":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "EXTENDED/BUYBACK":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "DELAYED":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "DEFAULTED":
                $data = WIN_LOANSTATUS_ACTIVE;
                break;
            case "SOLD":
                $data = WIN_LOANSTATUS_FINISHED;
                break;
            case "REPAID":
                $data = WIN_LOANSTATUS_FINISHED;
                break;
            case "RECOVERED":
                $data = WIN_LOANSTATUS_FINISHED;
                break;
        }
        return $data;
    }

    /**
     * Function to translate the company specific loan type to the Winvestify standardized
     * loan type
     * @param string $inputData     company specific loan type
     * @return int                  Winvestify standardized loan type
     */
    public function translateLoanType($inputData) {
        
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
     *
     * 	Logout of user from the company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout() {

        $str = $this->doCompanyLogout();
        return true;
    }

    function structureRevisionAmortizationTable($node1, $node2) {

        $dom1 = new DOMDocument();
        $dom1->loadHTML($node1);

        $dom2 = new DOMDocument();
        $dom2->loadHTML($node2);

        $dom1 = $this->cleanDomTagNotFirst($dom1, array(
        array('typeSearch' => 'tagElement', 'tag' => 'tr')));


        $dom2 = $this->cleanDomTagNotFirst($dom2, array(
        array('typeSearch' => 'tagElement', 'tag' => 'tr')));


        echo 'compare structure';
        $structureRevision = $this->verifyDomStructure($dom1, $dom2);
        echo $structureRevision;
        return $structureRevision;
    }

}
