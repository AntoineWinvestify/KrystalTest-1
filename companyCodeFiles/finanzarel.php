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
    
    protected $valuesTransaction = [     // All types/names will be defined as associative index in array
        [
            "D" =>  [
                [
                    "type" => "date",                                               // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "D/M/Y",
                                ],
                    "functionName" => "normalizeDate",
                ] 
            ],
            "E" => [
                    "name" => "investment_loanId",                                            // This is an "empty variable name". So "type" is
            ], 
            "F" => [// NOT FINISHED YET
                [
                    "type" => "transactionDetail",                                      // Winvestify standardized name   OK
                    "inputData" => [                                                    // List of all concepts that the platform can generate
                                                                                        // format ["concept string platform", "concept string Winvestify"]
                                    "input3" => [
                                        0 => ["Provisión de fondos" => "Cash_deposit"],
                                        1 => [ "Retirada de fondos" => "Cash_withdrawal"],
                                        2 => ["Cargo por inversión en efecto" => "Primary_market_investment"],
                                        5 => ["Abono por cobro parcial de efecto" => "Partial_principal_repayment"],
                                        7 => ["Abono por cobro efecto" => "Principal_and_interest_payment"],
                                        9 => ["Intereses de demora" => "Delayed_interest_income"],
                                        14 => ["Retrocesión de comisiones" => "Compensation"],
                                        18 => ["Comisiones" => "Commission"],
                                        24 => ["IVA sobre Comisiones" => "Tax_VAT"],
                                        29 => ["Retiro de fondos" => "Cash_withdrawal"]
                                    ]
                            ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
            "G" => [
                [
                    "type" => "amount",                                            // This is an "empty variable name". So "type" is
                    "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                        // and its content is the result of the "getAmount" method
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "H" => [
                [
                    "type" => "balance",                                            // This is an "empty variable name". So "type" is
                    "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                        // and its content is the result of the "getAmount" method
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ],
            ]
        ],
        [
             "D" =>  [
                [
                    "type" => "date",                                               // Winvestify standardized name  OK
                    "inputData" => [
                                "input2" => "D/M/Y",
                                ],
                    "functionName" => "normalizeDate",
                ] 
            ],
            "E" => [
                "name" => "investment_loanId",                                            // This is an "empty variable name". So "type" is
            ], 
            "F" => [
                [
                    "type" => "transactionDetail",                                      // Winvestify standardized name   OK
                    "inputData" => [                                                    // List of all concepts that the platform can generate
                                                                                        // format ["concept string platform", "concept string Winvestify"]
                                    "input3" => [
                                        0 => ["Intereses" => "Regular_gross_interest_income"],
                                        1 => ["Efecto fallido" => ""]
                                    ]
                            ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
            "G" => [
                [
                    "type" => "amount",                                            // This is an "empty variable name". So "type" is
                    "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                        // and its content is the result of the "getAmount" method
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
        ],
        [
            "A" => [
                [
                    "type" => "investment_typeOfInvestment",                        
                    "inputData" => [                                            // Get the "original" Mintos concept, which is used later on
                                "input2" => " ",                                // 'input3' is NOT mandatory. 
                            ],
                    "functionName" => "getDefaultValue",
                ]
            ],
            "K" => [
                [
                    "type" => "amount",                                            // This is an "empty variable name". So "type" is
                    "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                        // and its content is the result of the "getAmount" method
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ]
        ]
    ];
    
    protected $valuesInvestment = [     // All types/names will be defined as associative index in array
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
                "name" => "investment_fullLoanAmount",                                           
            ], 
            "F" => [// NOT FINISHED YET
                [
                    "name" => "dummy_cuarter",                                      // Winvestify standardized name   OK
                    "inputData" => [                                                    // List of all concepts that the platform can generate
                                                                                        // format ["concept string platform", "concept string Winvestify"]
                                "input3" => [0 => ["Incoming client payment" => "Cash_deposit"],                // OK
                                            1 => ["Investment principal increase" => "Primary_market_investment"],
                                            2 => ["Investment share buyer pays to a seller" => "Secondary_market_investment"],
                                            3 => ["Investment principal repayment" => "Capital_repayment"],    //OK
                                            4 => ["Investment principal rebuy" => "Principal_buyback"],        // OK                               
                                            5 => ["Interest income on rebuy" => "Interest_income_buyback"],    // OK
                                            6 => ["Interest income" => "Regular_gross_interest_income"],       //
                                            7 => ["Delayed interest income" => "Delayed_interest_income"],     // OK
                                            8 => ["Late payment fee income" =>"Late_payment_fee_income"],      // OK                                       
                                            9 => ["Delayed interest income on rebuy" => "Delayed_interest_income_buyback"],  // OK
                                            10 => ["Discount/premium for secondary market" => "Income_secondary_market"],   // For seller
                                            11 => ["Discount/premium for secondary market" => "Cost_secondary_market"],     // for buyer
                                            ] 
                            ],
                    "functionName" => "getTransactionDetail",
                ]
            ],
            "G" => [
                [
                    "name" => "amount",                                            // This is an "empty variable name". So "type" is
                    "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                        // and its content is the result of the "getAmount" method
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
            "G" => [
                [
                    "type" => "balance",                                            // This is an "empty variable name". So "type" is
                    "inputData" => [                                                    // obtained from $parser->TransactionDetails['type']
                                "input2" => ".",                                         // and which BY DEFAULT is a Winvestify standardized variable name.
                                "input3" => ",",                                        // and its content is the result of the "getAmount" method
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ],
            ],
            "H" => [
                [
                    "type" => "investment.currency",                                    // Winvestify standardized name  OK
                    "functionName" => "getCurrency",
                ]
            ],
            "G" =>  [
                [
                    "type" => "investment.nextPaymentAmount",                           // Winvestify standardized name
                    "inputData" => [
				"input2" => ".",
                                "input3" => ",",
                                "input4" => 2
                                ],
                    "functionName" => "getAmount",
                ]
            ],
        ]
    ];

    protected $valuesAmortizationTable = [  // NOT FINISHED
            "A" =>  [
                "name" => "transaction_id"
             ],
        ];
    
    protected $transactionConfigParms = [
        [
            'offsetStart' => 1,
            'offsetEnd'     => 0,
            'separatorChar' => ";",
            'sortParameter' => array("investment_loanId") // used to "sort" the array and use $sortParameter(s) as prime index.               
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
                                    "M" => "Plusval?ainicial",
                                    "N" => "Fecha de vencimiento",
                                    "O" => "Estado",
                                    "P" => "Amortizaci?nPendiente",
                                    "Q" => " ");
    
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
                
                $this->tempArray['global']['myWallet'] = $controlVariablesArray[5];
                $this->tempArray['global']['outstandingPrincipal'] = $controlVariablesArray[2];
                //$this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($controlVariablesArray[11]);
                //Finanzarel doenst have number of investments
                $this->tempArray['reservedFunds'] = $this->getMonetaryValue($controlVariablesArray[6]); //They call it "Inversion neta comprometida"
                
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
                              
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
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
                $this->fileName = "expiredLoans" . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->expiredLoansHeader;
                $headers = array('Expect:');
                
                if (count($this->request) > 2) {
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
                      
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
                }
                //$credentials = array_shift($this->urlSequence);
                $credentialsFile = array(
                        'p_flow_id' => $this->credentialsGlobal['p_flow_id'],
                        'p_flow_step_id' => 1, 
                        'p_instance' => $this->credentialsGlobal['p_instance'],  
                        'p_debug' => '',
                        'p_request' => $this->request[2]);
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->investmentHeader;
                $this->numFileInvestment++;
                $headers = array('Expect:');
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($this->url,$this->referer, $credentialsFile, $headers, $this->fileName);
                break;
            case 6:
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
                        $this->credentialCashflow = $credentialCashflows[0];
                        echo "Found cashflow $this->credentialCashflow";
                        break;
                    }
                        
                }
                $url = array_shift($this->urlSequence);
                echo "The url of last is : ".$url;
                $url = strtr($url, array(
                            '{$p_instance}' => $this->credentialsGlobal['p_instance'],
                            '{$credentialCashflow}' => $this->credentialCashflow
                        ));
                echo "now the url is " . $url;
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array(
                            '{$p_flow_step_id}' => 11,
                            '{$p_instance}' => $this->credentialsGlobal['p_instance']
                        ));
                $headers = array('Expect:'/* 'Accept: "text/html,application/xhtml+xml,application/xml;q=0.9,*//*;q=0.8"', 'Accept-Language: "en-US,en;q=0.5"', 'Accept-Encoding: "gzip, deflate, br"'*/);
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
                $headerError = $this->compareHeader();
                if($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER){    
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if( $headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER){
                    $this->saveGearmanError(array('line' => __LINE__, 'file' => __file__, 'subtypeErrorId' => $headerError));
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
                return WIN_TYPEOFLOAN_PERSONAL;
                break;
            case "PAGARÉ":
                return WIN_TYPEOFLOAN_PAGARE;
                break;
            case "PAGARÉ N.O.":
                return WIN_TYPEOFLOAN_PAGARE;
                break; 
            case "CONFIRMING":
                return WIN_TYPEOFLOAN_CONFIRMING;
                break;
        }

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

}
