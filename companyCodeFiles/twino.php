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
 * 2017-08-23
 * Created
 * link account
 */
class twino extends p2pCompany {
    protected $statusDownloadUrl = null;
    
         
// TWINO
// Processing Date	Booking Date	Type	Description	Loan Number	amount
// 8/3/2017 20:39	8/3/2017 0:00	REPAYMENT	PRINCIPAL	06-185114001	1.0544
// 8/3/2017 18:52	8/3/2017 0:00	REPAYMENT	PRINCIPAL	06-337436001	5.2947

    protected $values_twino_cashflow = [     // All types/names will be defined as associative index in array
            "A" => [
                [
                    "type" => "date",                           // Winvestify standardized name 
                    "inputData" => [
				"input2" => "d/m/Y",		// Input parameters. The first parameter
                                                                // is ALWAYS the contents of the cell
                                ],
                    "functionName" => "normalizeDate",         
                ],
                
 
             ],

            "B" => [
                [
                    "type" => "purpose",                        // trick to get the complete cell data as purpose
                    "inputData" => [
                                "input2" => "",                 // May contain trailing spaces
                                "input3" => ",",
                            ],                   
                    "functionName" => "extractDataFromString", 
                ],
                [
                    "type" => "loanId",                         // Winvestify standardized name 
                    "functionName" => "getHash",                // An internal loanId is generated based on md5 hash of project name
                ]
            ],          

            "C" => [
                    "name" => "payment",
                ],
                
  /*              [
                    "type" => "transactionType",                // Complex format, calling external method
                    "inputData" => [                            // List of all concepts that the platform can generate
                                                                // format ["concept string platform", "concept string Winvestify"]
                                   "input2" => [["Amortización de capital(€)", "Principal_repayment"],
                                                ["Intereses brutos(€)", "Regular_interest_income"],
                                                ["Retención IRPF(€)", "Tax_income_withholding_tax"],
                                    ]   
                            ],
                    "functionName" => "getTransactionType",  
                ],
                [
                    "type" => "transactionDetail",              // Complex format, calling external method
                    "inputData" => [                            // List of all concepts that the platform can generate
                                                                // format ["concept string platform", "concept string Winvestify"]
                                   "input2" => [["Amortización de capital(€)", "Principal_repayment"],
                                                ["Intereses brutos(€)", "Regular_interest_income"],
                                                ["Retención IRPF(€)", "Tax_income_withholding_tax"],  
                                    ]   
                            ],
                    "functionName" => "getTransactionDetail",  
                ]
            ],
*/
            "D" => [                                            // Simply changing name of column to the Winvestify standardized name
                [
                    "type" => "amortization",                         
                    "inputData" => [
				"input2" => ".",                // Thousands seperator, typically "."
                                "input3" => ",",		// Decimal seperator, typically ","
                                "input4" => 5,                  // Number of required decimals, typically 5
                                                                // is ALWAYS the contents of the cell
                                ],
                    "functionName" => "getAmount"
                ]                    
            ],
            "E" => [                                            // Simply changing name of column to the Winvestify standardized name
                [
                    "type" => "interest",                         
                    "inputData" => [
				"input2" => ".",                // Thousands seperator, typically "."
                                "input3" => ",",		// Decimal seperator, typically ","
                                "input4" => 5,                  // Number of required decimals, typically 5
                                                                // is ALWAYS the contents of the cell
                                ],
                    "functionName" => "getAmount"
                ]                    
            ],
            "F" => [                                            // Simply changing name of column to the Winvestify standardized name
                [
                    "type" => "retencionTax",                         
                    "inputData" => [
				"input2" => ".",                // Thousands seperator, typically "."
                                "input3" => ",",		// Decimal seperator, typically ","
                                "input4" => 5,                  // Number of required decimals, typically 5
                                                                // is ALWAYS the contents of the cell
                                ],
                    "functionName" => "getAmount"
                ]                    
            ], 
            "G" => [                                            // Simply changing name of column to the Winvestify standardized name
                [
                    "type" => "total",                         
                    "inputData" => [
				"input2" => ".",                // Thousands seperator, typically "."
                                "input3" => ",",		// Decimal seperator, typically ","
                                "input4" => 5,                  // Number of required decimals, typically 5
                                                                // is ALWAYS the contents of the cell
                                ],
                    "functionName" => "getAmount"
                ]
            ]
        ];       
      
       
// Not finished
    protected $values_twino_investment = [                            // All types/names will be defined as associative index in array
            
            "A" => [
                    "name" => "origin.loan",
                ],
            "B" => [
                    "name" => "loanId",
                ],           
            "C" => [
                [
                    "type" => "origin.date",                           // Winvestify standardized name 
                    "inputData" => [
				"input2" => "m/d/Y",		// Input parameters. The first parameter
                                                                // is ALWAYS the contents of the cell
                                ],
                    "functionName" => "normalizeDate",         
                ],
             ],
            "D" => [
                    "name" => "riskclass",             
                ],
            "E" => [
                    "name" => "status",             
                ]
        ];
       
   
    
    
    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    
    
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


        if ($str == true) {
            $confirm = true;
        }


        //$this->companyUserLogout($url);
        if ($confirm) {
            return true;
        }
        return false;
    }

    /**
     * Download the file with the user investment
     * @param string $user
     * @param string $password
     */
    function collectUserInvestmentDataParallel($str) {


        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                $credentials['name'] = $this->user;
                $credentials['password'] = $this->password;
                //$credentials['googleAnalyticClientId'] = '1778227581.1503479723';
                $payload = json_encode($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl(array(),$payload);
                break;
            case 1:
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 2:
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                echo $str;
                if (!$str) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Twino login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }else{
                    echo 'twino login ok';
                }
                
                $credentialsFile = '{"page":1,"pageSize":20,"query":{"sortOption":{"propertyName":"created","direction":"DESC"},"loanStatuses":["CURRENT","EXTENDED","DELAYED","DEFAULTED"]}}';
                $this->idForSwitch++;
                $next = $this->getCompanyWebpageMultiCurl(null, $credentialsFile,true);
                break;
            case 4:
                echo $str;
                $response = json_decode($str, true);
                print_r($response);
                if(empty($this->statusDownloadUrl)){
                    echo 'Reading download status: ' . HTML_ENDOFLINE;
                    $this->statusDownloadUrl = array_shift($this->urlSequence);
                    $this->idForSwitch++;
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId']. '/status');
                }       
                $this->idForSwitch++;
                break;
            case 5:
                echo $str;
                $response = json_decode($str, true);   
                print_r($response);
                if($response['reportReady'] == true){
                    echo 'Status true, downloading';
                    $this->idForSwitch++;
                    $this->getPFPFileMulticurl($this->statusDownloadUrl . $response['reportId'] . '/download', null, false, 'Invesment');
                }else{
                    echo 'Not ready yet';
                    $next = $this->getCompanyWebpageMultiCurl($this->statusDownloadUrl . $response['reportId']. '/status');
                    $this->idForSwitch--;
                    echo 'Repeat Case: ' . $this->idForSwitch;
                }
                break;

                /*echo 'NOW:' . $str . HTML_ENDOFLINE;
                $fileUrl = array_shift($this->urlSequence);
                echo 'Download url: ' . $fileUrl . HTML_ENDOFLINE;
                
                $fileName = 'Investment';
                $fileType = 'xlsx';
                $pfpBaseUrl = 'https://www.twino.eu';
                $referer = 'https://www.twino.eu/en/profile/investor/my-investments/individual-investments';
                $this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Twino', 'prueba', $credentialsFile,$referer);
                //echo 'Downloaded';
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();*/
            /* case 3:
              echo $this->idForSwitch . HTML_ENDOFLINE;
              $dom = new DOMDocument;  //Check if works
              libxml_use_internal_errors(true);
              $dom->loadHTML($str);
              $dom->preserveWhiteSpace = false;
              //echo $str;
              $resultLogin = false;
              echo 'CHeck login' . HTML_ENDOFLINE;
              $as = $dom->getElementsByTagName('a');
              foreach ($as as $a) {
              echo $a->nodeValue . HTML_ENDOFLINE;
              if (trim($a->nodeValue) == 'Overview') {
              echo 'Find' . HTML_ENDOFLINE;
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
              exit;
              }

              $this->idForSwitch++;
              $next = $this->getCompanyWebpageMultiCurl();
              echo 'Next: ' . $next . HTML_ENDOFLINE;
              break;
              ////////DOWNLOAD FILE
              case 4:
              echo $this->idForSwitch . HTML_ENDOFLINE;
              echo 'Login ok';
              $fileUrl = array_shift($this->urlSequence);
              echo $fileUrl . HTML_ENDOFLINE;
              $credentialsFile = 'purchased_from=&purchased_till=&statuses%5B%5D=256&statuses%5B%5D=512&statuses%5B%5D=1024&statuses%5B%5D=2048&statuses%5B%5D=8192&statuses%5B%5D=16384&+=256&+=512&+=1024&+=2048&+=8192&+=16384&listed_for_sale_status=&min_interest=&max_interest=&min_term=&max_term=&with_buyback=&min_ltv=&max_ltv=&loan_id=&sort_field=&sort_order=DESC&max_results=20&page=1&include_manual_investments=';
              $fileName = 'Investment';
              $fileType = 'xlsx';
              $pfpBaseUrl = 'https://www.mintos.com';
              $referer = 'https://www.mintos.com/en/my-investments/?currency=978&statuses[]=256&statuses[]=512&statuses[]=1024&statuses[]=2048&statuses[]=8192&statuses[]=16384&sort_order=DESC&max_results=20&page=1';
              $this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Mintos', 'prueba', $credentialsFile, $referer);
              //echo 'Downloaded';
              $this->idForSwitch++;
              $this->getCompanyWebpageMultiCurl();
              break;
              case 5:
              $fileUrl = array_shift($this->urlSequence);
              $credentialsFile = "account_statement_filter[fromDate]=12.09.2017&account_statement_filter[toDate]=12.09.2017&account_statement_filter[maxResults]=20";
              $fileName = 'CashFlow';
              $fileType = 'xlsx';
              $pfpBaseUrl = 'https://www.mintos.com';
              $referer = "https://www.mintos.com/en/account-statement/?account_statement_filter[fromDate]=12.09.2017&account_statement_filter[toDate]=12.09.2017&account_statement_filter[maxResults]=20";
              $this->downloadPfpFile($fileUrl, $fileName, $fileType, $pfpBaseUrl, 'Mintos', 'prueba', $credentialsFile, $referer);
              $this->idForSwitch++;
              $this->getCompanyWebpageMultiCurl();
              break;
              //////LOGOUT
              case 6:
              echo $this->idForSwitch . HTML_ENDOFLINE;
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
              $this->getCompanyWebpageMultiCurl($logoutUrl); //Logout
              break; */
        }
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

}
