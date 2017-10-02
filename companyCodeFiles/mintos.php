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
 * Contains the code required for accessing the website of "Mintos".
 *
 * 
 * @author 
 * @version 0.1
 * @date 2017-08-16
 * @package  
 * 
 * 2017-08-23
 * link account
 * 
 * 2017-09-28
 * Added configuration files so we can analyze "investment_X.xls", transactions_X.xls"
 * 
 */

/**
 * Description of Mintos
 *
 */
class mintos extends p2pCompany {   
    protected $valuesMintosTransaction = [     // All types/names will be defined as associative index in array
            "A" =>  [
                "name" => "transactionId"                                               // Winvestify standardized name 
             ],
            "B" => [
                [
                    "type" => "date",                                                   // Winvestify standardized name 
                    "inputData" => [
				"input2" => "Y-M-D",
                                ],
                    "functionName" => "normalizeDate",         
                ]
            ],
            "C" => [// NOT FINISHED YET
                [
                    "type" => "loanId",                         // trick to get the complete cell data as purpose
                    "inputData" => [
                                "input2" => "Loan ID: ",                 // May contain trailing spaces
                                "input3" => ",",
                            ],                   
                    "functionName" => "extractDataFromString", 
                ],
                
                [
                    "type" => "transactionType",                // Complex format, calling external method
                    "inputData" => [                            // List of all concepts that the platform can generate
                                                                // format ["concept string platform", "concept string Winvestify"]
                                   "input5" => ["Incoming client payment" => "Cash_deposit",
                                                "Investment principal increase" => "Primary_market_investment",
                                                "Investment principal repayment" => "Principal_repayment",
                                                "Investment principal rebuy" => "Principal_buyback",
                                                "Interest income" => "Regular_interest_income",
                                                "Delayed interest income" => "Delayed_interest_income",
                                                "Late payment fee income" => "Late_payment_fee_income",
                                                "Interest income on rebuy" => "Interest_income_buyback",
                                                "Delayed interest income on rebuy" => "Delayed_interest_income_buyback"]
                                    
                            ],
                    "functionName" => "getTransactionType",  
                ],
                [
                    "type" => "transactionDetail",              // Complex format, calling external method
                    "inputData" => [                            // List of all concepts that the platform can generate
                                                                // format ["concept string platform", "concept string Winvestify"]
                                   "input8" => ["Incoming client payment" => "Cash_deposit",
                                                "Investment principal increase" => "Primary_market_investment",
                                                "Investment principal repayment" => "Principal_repayment",
                                                "Investment principal rebuy" => "Principal_buyback",
                                                "Interest income" => "Regular_interest_income",
                                                "Delayed interest income" => "Delayed_interest_income",
                                                "Late payment fee income" =>"Late_payment_fee_income",
                                                "Interest income on rebuy" => "Interest_income_buyback",
                                                "Delayed interest income on rebuy" => "Delayed_interest_income_buyback"]
                                        
                                       
                            ],
                    "functionName" => "getTransactionDetail",  
                ]
            ],
            "D" => [                                          
                [
                    "type" => "investment.loanAmount",                             // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",                                         
                                "input3" => ",",                                        
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]  
            ],
            "E" => [
                [
                    "type" => "investment.totalLoanAmount",                             // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",                                         
                                "input3" => ",",                                        
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]              
            ],
            "F" => [
                [
                    "type" => "currency",                                               // Winvestify standardized name 
                    "functionName" => "getCurrency",  
                ]
            ],  
        ]; 
    
    protected $valuesMintosInvestment = [     // All types/names will be defined as associative index in array
            "A" =>  [
                "name" => "investment.loanOrigin"                                       // Winvestify standardized name 
             ],       
            "B" =>  [
                "name" => "investment.loanId"                                           // Winvestify standardized name 
             ],
            "C" =>  [
                [
                    "type" => "investment.issueDate",                                   // Winvestify standardized name 
                    "inputData" => [
				"input2" => "D.M.Y",                                    
                                                                                        
                                ],
                    "functionName" => "normalizeDate",         
                ]
             ],       
            "D" =>  [
                "name" => "investment.loanType"                                         // Winvestify standardized name
             ],        
            "E" =>  [
                "name" => "investment.amortizationMethod"                               // Winvestify standardized name
             ],       
            "F" =>  [
                "name" => "investment.loanOriginator"                                   // Winvestify standardized name
             ],
            "G" =>  [
                [
                    "type" => "investment.totalLoanAmount",                             // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",                                         
                                "input3" => ",",                                        
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],       
            "H" =>  [
                [
                    "type" => "investment.remainingPrincipalTotalLoan",                 // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",                                         
                                "input3" => ",",      
                                "input4" => 5,
                                ],
                    "functionName" => "getAmount",         
                ]
             ],
            "I" =>  [
                [
                    "type" => "investment.nextPaymentDate",                             // Winvestify standardized name 
                    "inputData" => [
				"input2" => "D.M.Y",       
                                                            
                                ],
                    "functionName" => "normalizeDate",         
                ]
             ],
            "J" =>  [
                [
                    "type" => "investment.nextPaymentAmount",                           // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",       
                                "input3" => ",",  
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],        
            "K" =>  [
                "name" => "investment.LTV"                                              // Winvestify standardized name 
             ],
            "L" =>  [
                [
                    "type" => "investment.interestRate",                                // Winvestify standardized name 
                    "inputData" => [
				"input2" => "D.M.Y",           
                                ],
                    "functionName" => "normalizeDate",         
                ]
             ],        
            "M" =>  [
                "name" => "investment.totalInstalments"                                 // Winvestify standardized name 
             ],       
            "N" =>  [
                "name" => "investment.paidInstalments"                                  // Winvestify standardized name 
             ],       
            "P" =>  [
                "name" => "investment.loanStatus"                                       // Winvestify standardized name 
             ],
            "P" =>  [
                "name" => "investment.buyBackGuarantee"                                 // Winvestify standardized name 
             ],
            "Q" =>  [
                [
                    "type" => "investment.myInvestment",                                // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",    
                                "input3" => ",",    
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],
            "R" =>  [
                                [
                    "type" => "investment.purchaseDate",                                // Winvestify standardized name 
                    "inputData" => [
				"input2" => "D.M.Y",  
                                ],
                    "functionName" => "normalizeDate",         
                ]
             ],              
            "S" =>  [
                [
                    "type" => "investment.receivedPayments",                            // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",       
                                "input3" => ",",
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],
            "T" =>  [
                [
                    "type" => "investment.outstandingPrincipal",                        // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",   
                                "input3" => ",",    
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],       
            "U" =>  [
                [
                    "type" => "investment.amountSecondaryMarket",                       // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",   
                                "input3" => ",",   
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],
            "V" =>  [
                [
                    "type" => "investment.price",                                       // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",  
                                "input3" => ",",    
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],      
            "W" =>  [
                [
                    "type" => "investment.discount_premium",                            // Winvestify standardized name 
                    "inputData" => [
				"input2" => "",    
                                "input3" => ",",    
                                "input4" => 5 
                                ],
                    "functionName" => "getAmount",         
                ]
             ],       
            "X" =>  [
                [
                    "type" => "currency",                                               // Winvestify standardized name 
                    "functionName" => "getCurrency",  
                ]
             ],        
        ];
    
     protected $valuesMintosAmortization = [     // All types/names will be defined as associative index in array
            "A" =>  [
                "name" => "transaction_id"
             ],   
        ];     
     
     
    
    function __construct() {
        parent::__construct();
        $this->i = 0;
        //$this->loanIdArray = array("15058-01","12657-02 ","14932-01 ");     
        $this->maxLoans = count($this->loanIdArray);
        // Do whatever is needed for this subsclass
    }
    
                    
  
    public function getParserConfigTransactionFile() {
        return $this->valuesMintosTransaction;
    }
 
     public function getParserConfigInvestmentFile() {
        return $this->valuesMintosInvestment;
    }
    
    public function getParserConfigAmortizationTableFile() {
        return $this->valuesMintosAmortization;
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
          FIELDS USED BY ECROWDINVEST DURING LOGIN PROCESS
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
     * Function to download every file that is needed to read the investment of an investor
     * @param string $str It is the html of the last url we accessed
     */
    function collectUserGlobalFilesParallel($str = null) {

        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
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
                echo 'CHeck login' . SHELL_ENDOFLINE;
                $as = $dom->getElementsByTagName('a');
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
                    return $this->getError(__LINE__, __FILE__);
                }
                
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            ////////DOWNLOAD FILE
            case 4:
                //$credentialsFile = 'purchased_from=&purchased_till=&statuses%5B%5D=256&statuses%5B%5D=512&statuses%5B%5D=1024&statuses%5B%5D=2048&statuses%5B%5D=8192&statuses%5B%5D=16384&+=256&+=512&+=1024&+=2048&+=8192&+=16384&listed_for_sale_status=&min_interest=&max_interest=&min_term=&max_term=&with_buyback=&min_ltv=&max_ltv=&loan_id=&sort_field=&sort_order=DESC&max_results=20&page=1&include_manual_investments=';
                $fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $url = array_shift($this->urlSequence);
                $referer = array_shift($this->urlSequence);
                $credentials = array_shift($this->urlSequence);
                $headersJson = array_shift($this->urlSequence);               
                $headers = strtr($headersJson, array('{$baseUrl}' => $this->baseUrl));
                echo $headers;
                $headers = json_decode($headers, true);
                echo "JSON ERROR: " . json_last_error();
                echo 'HEADERS';
                var_dump($headers);
                //$referer = 'https://www.mintos.com/en/my-investments/?currency=978&statuses[]=256&statuses[]=512&statuses[]=1024&statuses[]=2048&statuses[]=8192&statuses[]=16384&sort_order=DESC&max_results=20&page=1';
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, $referer, $credentials, $headers, $fileName);
                //echo 'Downloaded';
                break;
            case 5:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 6:
                //This two variables should disappear
                $yesterday = date('d.m.Y',strtotime("-1 days"));
                $today = date("d.m.Y");  
                //$credentialsFile = "account_statement_filter[fromDate]={$today}&account_statement_filter[toDate]={$today}&account_statement_filter[maxResults]=20";
                $url = array_shift($this->urlSequence);
                $referer = array_shift($this->urlSequence);
                $referer = strtr($referer, array('{$date1}' => $yesterday));
                $referer = strtr($referer, array('{$date2}' => $today));
                $credentials = array_shift($this->urlSequence);
                $credentials = strtr($credentials, array('{$date1}' => $yesterday));
                $credentials = strtr($credentials, array('{$date2}' => $today));
                $headersJson = array_shift($this->urlSequence);
                $headers = strtr($headersJson, array('{$baseUrl}' => $this->baseUrl));
                $headers = json_decode($headers, true);
                $fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                //$referer ="https://www.mintos.com/en/account-statement/?account_statement_filter[fromDate]={$today}&account_statement_filter[toDate]={$today}&account_statement_filter[maxResults]=20";
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, $referer, $credentials, $headers, $fileName);
                break;
            case 7:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;           
            //////LOGOUT
            case 8: 
                echo "Read Globals";
                //echo $str;
                $dom = new DOMDocument;  //Check if works
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                
                $boxes = $this->getElements($dom, 'ul', 'id', 'mintos-boxes'); 
                foreach($boxes as $keyBox=>$box){
                    //echo $box->nodeValue;
                    //echo "BOX NUMBER: =>" . $keyBox;
                    $tds = $box->getElementsByTagName('td');
                    foreach($tds as $key=>$td){
                        //echo $key . " => " . $td->nodeValue . SHELL_ENDOFLINE;
                        $tempArray["global"]["myWallet"] = $this->getMonetaryValue($tds[1]->nodeValue);
                        $tempArray["global"]["activeInInvestments"] = $this->getMonetaryValue($tds[23]->nodeValue);
                        $tempArray["global"]["totalEarnedInterest"] = $this->getMonetaryValue($tds[21]->nodeValue);
                        
                    
                    }
                    $divs = $box->getElementsByTagName('div');
                    foreach($divs as $key => $div){
                        //echo $key . " => " . $div->nodeValue . SHELL_ENDOFLINE;
                        $tempArray["global"]["profitibility"] = $this->getPercentage($divs[6]->nodeValue);
                    }

                }
                
                print_r($tempArray["global"]);
                return $tempArray["global"];
        }
    }
    
    
    function collectAmortizationTablesParallel($str){
        switch ($this->idForSwitch){
               case 0:
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
                echo 'CHeck login' . SHELL_ENDOFLINE;
                $as = $dom->getElementsByTagName('a');
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
                    return $this->getError(__LINE__, __FILE__);
                }
                
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 4:
                
                if(empty($this->tempUrl['investmentUrl'])){
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);    
                }
                echo "Loan number " . $this->i . " is " . $this->loanIdArray[$this->i];
                $url =  $this->tempUrl['investmentUrl'] . $this->loanIdArray[$this->i];
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
                
                  foreach($tables as $table){     
                    if($table->getAttribute('class') == 'loan-table'){
                        $AmorTable = new DOMDocument();
                        $clone = $table->cloneNode(TRUE); //Clene the table
                        $AmorTable->appendChild($AmorTable->importNode($clone,TRUE));
                        $AmorTableString =  $AmorTable->saveHTML();
                        //echo $AmorTableString;
                    }
                }
                
                echo "Is " . $this->i . " and limit is " . $this->maxLoans;
                if($this->i < $this->maxLoans){
                    echo "Read again";
                    $this->idForSwitch = 4;
                    $next = $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentUrl'] . $this->loanIdArray[$this->i]);
                    break;               
                }
                else{
                    return $this->tempArray;
                    break;
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

}
