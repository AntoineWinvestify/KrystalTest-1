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
 * 2017-08-25
 * Created
 * 
 * 2017-10-24 version_0.2
 * Integration of parsing amortization tables with Gearman and fileparser
 *
 * 
 * 2017-03-11 version 0.3
 * Header added in amortization tables.
 * 
 * 2017-16-11 version 0.4
 * Header added in amortization tables header fix. 
 */

/**
 * Contains the code required for accessing the website of "Finbee".
 * Parser AmortizationTables                                            [OK, tested]
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[Not OK]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Ok, not tested]
 * parallelization                                                      [OK, tested]
 */
class finbee extends p2pCompany {
    
    protected $valuesTransaction = [
        "A" => [
            [
                "type" => "date", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "Y-M-D",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        "B" => [
            [
                    "type" => "investment_loanId",                              // Winvestify standardized name   OK
                    "inputData" => [                                            // trick to get the complete cell data as purpose
                                "input2" => "from ",                        // May contain trailing spaces
                                "input3" => " for",
                                "input4" => 0                                  // 'input3' is mandatory. If not found then return "global_xxxxxx"
                            ],
                    "functionName" => "extractDataFromString",
            ],
            [
                "type" => "transactionDetail", // Winvestify standardized name   OK
                "inputData" => [// List of all concepts that the platform can generate
                    // format ["concept string platform", "concept string Winvestify"]
                    "input2" => [
                        0 => ["Deposit" => "Cash_deposit"], // OK
                        1 => ["Loan disbursal" => "Primary_market_investment"],
                        2 => ["Principal repayment from" => "Capital_repayment"], //OK
                        3 => ["Interest payment from" => "Regular_gross_interest_income"], //
                        4 => ["Secondary market difference for" => "Income_secondary_market"], // For seller
                        5 => ["Secondary market difference for" => "Cost_secondary_market"], // for buyer
                        6 => ["Withdrawal" => "Cash_withdrawal"],
                        7 => ["Interest payment to" => "Interest_payment_secondary_market_purchase"]
                    ]
                ],
                "functionName" => "getTransactionDetail",
            ]
        ],
        "D" => [
            [
                "type" => "amount",                                     // This is *mandatory* field which is required for the 
                "inputData" => [                                        // "transactionDetail"
                            "input2" => "",                             // and which BY DEFAULT is a Winvestify standardized variable name.
                            "input3" => ".",                            // and its content is the result of the "getAmount" method
                            "input4" => 2
                            ],
                "functionName" => "getAmount",
            ]
        ],
        "F" => [
            [
                "type" => "transaction_balance",                            // Winvestify standardized name
                "inputData" => [
                            "input2" => "",
                            "input3" => ".",
                            "input4" => 2
                            ],
                "functionName" => "getAmount",
            ]
        ],   
        
    ];
    
    protected $valuesInvestment = [
        0 => [
            [
                "A" => [
                    "name" => "investment_purpose"
                ],
                "B" => [
                    [
                        "type" => "investment_debtor",
                        "inputData" => [
                            "input2" => "",
                            "input3" => "",
                        ],
                        "functionName" => "extractDataFromString",
                        
                    ],
                    [
                        "type" => "investment_loanId",
                        "inputData" => [
                            "input2" => "",
                            "input3" => "",
                        ],
                        "functionName" => "extractDataFromString",
                    ]
                ],
                "C"=> [
                    "name" => "investment_loanType"
                ],
                "D" => [
                    [
                        "type" => "investment_fullLoanAmount",                                            // This is an "empty variable name". So "type" is
                        "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']   
                            "input2" => "",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                            "input3" => ",",                                        // and its content is the result of the "getAmount" method
                            "input4" => 2
                        ],
                        "functionName" => "getAmount",
                    ]
                ],
                "E" => [
                    "name" => "investment_originalDuration"
                ],
                "H" => [
                    "name" => "investment_originalState",
                ]
                
            ],
            [
                "A" => [
                    "name" => "investment_loanId"
                ],
                "B" => [
                    [
                        "type" => "investment_investmentDate", // Winvestify standardized name 
                        "inputData" => [
                            "input2" => "d.m.Y", // Input parameters. The first parameter
                        // is ALWAYS the contents of the cell
                        ],
                        "functionName" => "normalizeDate",
                    ],
                ],
                "C" => [
                    [
                        "type" => "investment_debtor",
                        "inputData" => [
                            "input2" => "",
                            "input3" => "",
                        ],
                        "functionName" => "extractDataFromString",
                        
                    ]
                ],
                "D" => [
                    "name" => "investment_originalDuration"
                ],
                "E" => [
                    [
                        "type" => "investment_nominalInterestRate",                                            // This is an "empty variable name". So "type" is
                        "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']   
                            "input2" => "",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                            "input3" => ".",                                        // and its content is the result of the "getAmount" method
                            "input4" => 2
                        ],
                        "functionName" => "getAmount",
                    ]
                ],
                "F" => [
                    [
                        "type" => "investment_myInvestment",                        // Winvestify standardized name   OK
                        "inputData" => [
                                    "input2" => "",
                                    "input3" => ".",
                                    "input4" => 2
                                    ],
                        "functionName" => "getAmount",
                    ]
                ],
                "G" => [
                    [
                        "type" => "investment_capitalRepaymentFromP2P",                        // Winvestify standardized name   OK
                        "inputData" => [
                                    "input2" => "",
                                    "input3" => ".",
                                    "input4" => 2
                                    ],
                        "functionName" => "getAmount",
                    ]
                ]
            ],
            [
                "B" => [
                    "name" => "investment_debtor"
                ]
            ],
            [
                "C" => [
                    "name" => "investment_debtor"
                ]
            ],
            [
                "E" => [
                    "name" => "investment_debtor"
                ]
            ],
            [
                "F" => [
                    "name" => "investment_debtor"
                ]
            ]
        ],
        1 => [
            "A" => [
                "name" => "investment_loanId"
            ],
            "E" => [
                "name" => "investment_debtor"
            ]
        ]
    ];

    protected $valuesAmortizationTable = [
            1 =>  [
                [
                    "type" => "amortizationtable_capitalAndInterestPayment",                      // Winvestify standardized name  OK
                    "inputData" => [
				"input2" => "",
                                "input3" => ",",
                                "input4" => 16
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            2 => [
                [
                    "type" => "amortizationtable_capitalRepayment",                      // Winvestify standardized name  OK
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
                    "type" => "amortizationtable_interest",                      // Winvestify standardized name  OK
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
                    "type" => "amortizationtable_scheduledDate",                         // Winvestify standardized name   OK
                    "inputData" => [
				"input2" => "Y-M-D",
                                ],
                    "functionName" => "normalizeDate",
                ]
            ]
        ];
    
    protected $transactionConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 0,
        //'separatorChar' => ";",
        'sortParameter' => array("investment_loanId")   // used to "sort" the array and use $sortParameter as prime index.
    );
    
    protected $investmentConfigParms = [
        "fileConfigParam" => [
            "sortParameter" => "investment_debtor"
        ],
        0 => [
            "sheetConfigParam" => [
                "sortParameter" => "investment_loanId"
            ],
            [
                'offsetStart' => 1,
                'offsetEnd' => 0,
                //'separatorChar' => ";",
                'sortParameter' => array("investment_debtor"),   // used to "sort" the array and use $sortParameter as prime index.
                'sheetName' => 'Active Loans'
            ],
            [
                'offsetStart' => 1,
                'offsetEnd' => 0,
                //'separatorChar' => ";",
                'sortParameter' => array("investment_debtor"),   // used to "sort" the array and use $sortParameter as prime index.
                'sheetName' => 'Active Loan Slices'
            ],
            /*[
                'offsetStart' => 1,
                'offsetEnd' => 0,
                //'separatorChar' => ";",
                'sortParameter' => "investment_debtor",   // used to "sort" the array and use $sortParameter as prime index.
                'sheetName' => 'Completed Loans'
            ],
            [
                'offsetStart' => 1,
                'offsetEnd' => 0,
                //'separatorChar' => ";",
                'sortParameter' => "investment_debtor",   // used to "sort" the array and use $sortParameter as prime index.
                'sheetName' => 'Completed Loan Parts'
            ],
            [
                'offsetStart' => 1,
                'offsetEnd' => 0,
                //'separatorChar' => ";",
                'sortParameter' => "investment_debtor",   // used to "sort" the array and use $sortParameter as prime index.
                'sheetName' => 'Sold Loan Parts'
            ],
            [
                'offsetStart' => 1,
                'offsetEnd' => 0,
                //'separatorChar' => ";",
                'sortParameter' => "investment_debtor",   // used to "sort" the array and use $sortParameter as prime index.
                'sheetName' => 'Bought Loan Parts'
            ]*/
        ],
        1 => [
            'offsetStart' => 1,
            'offsetEnd' => 0,
            //'separatorChar' => ";",
            'sortParameter' => "investment_debtor",   // used to "sort" the array and use $sortParameter as prime index.
            //'sheetName' => 'Bought Loan Parts'
        ]
    ];
    
    protected $amortizationConfigParms = array('offsetStart' => 0,
        'offsetEnd' => 0,
        //'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
        
    );

    function __construct() {
        $this->i = 0;
        $this->typeFileTransaction = "xlsx";
        $this->typeFileInvestment = "xlsx";
        $this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";
        $this->typeFileControlVariables = ".xlsx";
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    /**
     * Download investment, cash flow files and control variables
     * 
     * @param string $str It is the web converted to string of the company.
     * @return 
     */
    function collectUserGlobalFilesParallel($str = null) {
        switch ($this->idForSwitch) {
            //LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $input) {
                    //echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
                    $name = $input->getAttribute('name');
                    switch ($name) {
                        case 'option':
                            $option = $input->getAttribute('value');
                            break;
                        case 'view':
                            $view = $input->getAttribute('value');
                            break;
                        case 'op2':
                            $op2 = $input->getAttribute('value');
                            break;
                        case 'return':
                            $return = $input->getAttribute('value');
                            break;
                        case 'message':
                            $message = $input->getAttribute('value');
                            break;
                        case 'loginfrom':
                            $loginfrom = $input->getAttribute('value');
                            break;
                    }
                }


                $this->credentials['username'] = $this->user;
                $this->credentials['passwd'] = $this->password;
                $this->credentials['Submit'] = 'Log in';
                $this->credentials['option'] = $option;
                $this->credentials['view'] = $view;
                $this->credentials['op2'] = $op2;
                $this->credentials['return'] = $return;
                $this->credentials['message'] = $message;
                $this->credentials['loginfrom'] = $loginfrom;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                // echo $str;

                $confirm = false;
                $as = $dom->getElementsByTagName('a');

                foreach ($as as $key => $a) {
                    echo $key . " " . $a->nodeValue;
                    if (trim($a->nodeValue) == 'My Lending Account') {
                        $confirm = true;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    echo 'login fail';
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Finbee login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }
                echo 'login ok';
                //LOGIN END
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 4:
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $path = $this->getFolderPFPFile();
                $file = $path . DS . $this->fileName;
                $newFile = $path . DS . $this->nameFileExpiredLoan . $this->numFileExpiredLoan . "." . $this->typeFileExpiredLoan;
                $this->copyFile($file, $newFile);
                $fileName = $this->nameFileControlVariables . $this->typeFileControlVariables; //$this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 6:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $this->numFileInvestment++;
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 7:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $headersJson = '["accept-language: en-US,en;q=0.8"]';
                $headers = json_decode($headersJson,true);
                $this->getPFPFileMulticurl($url, false, false, $headers, $fileName);
                break;
            case 8:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
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
            //LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($inputs as $input) {
                    //echo $input->getAttribute . " " . $input->nodeValue . HTML_ENDOFLINE;
                    $name = $input->getAttribute('name');
                    switch ($name) {
                        case 'option':
                            $option = $input->getAttribute('value');
                            break;
                        case 'view':
                            $view = $input->getAttribute('value');
                            break;
                        case 'op2':
                            $op2 = $input->getAttribute('value');
                            break;
                        case 'return':
                            $return = $input->getAttribute('value');
                            break;
                        case 'message':
                            $message = $input->getAttribute('value');
                            break;
                        case 'loginfrom':
                            $loginfrom = $input->getAttribute('value');
                            break;
                    }
                }


                $this->credentials['username'] = $this->user;
                $this->credentials['passwd'] = $this->password;
                $this->credentials['Submit'] = 'Log in';
                $this->credentials['option'] = $option;
                $this->credentials['view'] = $view;
                $this->credentials['op2'] = $op2;
                $this->credentials['return'] = $return;
                $this->credentials['message'] = $message;
                $this->credentials['loginfrom'] = $loginfrom;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                // echo $str;

                $confirm = false;
                $as = $dom->getElementsByTagName('a');

                foreach ($as as $key => $a) {
                    echo $key . " " . $a->nodeValue;
                    if (trim($a->nodeValue) == 'My Lending Account') {
                        $confirm = true;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    echo 'login fail';
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Finbee login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }
                echo 'login ok';
                //LOGIN END
                $this->tempUrl['dummyUrl'] = array_shift($this->urlSequence);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['dummyUrl']);  // Go to home page of the company
                break;
            //END LOGIN
            case 4:
                if (empty($this->tempUrl['investmentUrl'])) {
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);
                }
                echo "Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url = strtr($this->tempUrl['investmentUrl'], array('{$loanId}' => $this->loanIds[$this->i]));
                echo "the table url is: " . $url;
                $this->i = $this->i + 1;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url);  // Read individual investment
                break;
            case 5:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                echo "Read table: ";
                $tables = $dom->getElementsByTagName('table');
                foreach ($tables as $table) {

                    if ($table->getAttribute('class') == 'table mb-none table-no-more') {
                        $trs = $dom->getElementsByTagName('tr');
                        $AmortizationHeaderTable = new DOMDocument();
                        $cloneHeader = $trs[0]->cloneNode(TRUE); //Clone the table header
                    }

                    if ($table->getAttribute('class') == 'table table-striped table-no-more') {
                        $AmortizationTable = new DOMDocument();
                        $table->appendChild($cloneHeader); //Add header to the table
                        $clone = $table->cloneNode(TRUE); //Clone the table
                        $AmortizationTable->appendChild($AmortizationTable->importNode($clone, TRUE));
                        $AmortizationTableString = $AmortizationTable->saveHTML();
                        $this->tempArray[$this->loanIds[$this->i - 1]] =  $AmortizationTableString;
                        echo $AmortizationTableString;
                    }
                }

                echo "Is " . $this->i . " and limit is " . $this->maxLoans;
                if ($this->i < $this->maxLoans) {
                    echo "Read again";
                    $this->idForSwitch = 4;
                    $next = $this->getCompanyWebpageMultiCurl($this->tempUrl['dummyUrl']);
                    break;
                } else {
                    return $this->tempArray;
                }
        }
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
          FIELDS USED BY finbee DURING LOGIN PROCESS
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
                case 'option':
                    $option = $input->getAttribute('value');
                    break;
                case 'view':
                    $view = $input->getAttribute('value');
                    break;
                case 'op2':
                    $op2 = $input->getAttribute('value');
                    break;
                case 'return':
                    $return = $input->getAttribute('value');
                    break;
                case 'message':
                    $message = $input->getAttribute('value');
                    break;
                case 'loginfrom':
                    $loginfrom = $input->getAttribute('value');
                    break;
            }
        }


        $credentials['username'] = $user;
        $credentials['passwd'] = $password;
        $credentials['Submit'] = 'Log in';
        $credentials['option'] = $option;
        $credentials['view'] = $view;
        $credentials['op2'] = $op2;
        $credentials['return'] = $return;
        $credentials['message'] = $message;
        $credentials['loginfrom'] = $loginfrom;

        $str = $this->doCompanyLogin($credentials); //do login


        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;
        $as = $dom->getElementsByTagName('a');

        $confirm = false;
        foreach ($as as $a) {

            if (trim($a->nodeValue) == 'My Lending Account') {
                $confirm = true;
            }
        }

        return $confirm;
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

}
