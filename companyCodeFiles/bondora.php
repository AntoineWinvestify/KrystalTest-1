<?php

/**
 * +----------------------------------------------------------------------------+
 * | Copyright (C) 2017, http://www.winvestify.com                   	  	|
 * +----------------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify 		|
 * | it under the terms of the GNU General Public License as published by       |
 * | the Free Software Foundation; either version 2 of the License, or  	|
 * | (at your option) any later version.                                      	|
 * | This file is distributed in the hope that it will be useful   		|
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of    		|
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the           	|
 * | GNU General Public License for more details.        			|
 * +----------------------------------------------------------------------------+
 *
 *
 * @author
 * @version 0.1
 * @date 2017-08-25
 * @package
 *
 * 
 * 
 * 2017-08-24
 * Created
 * link account
 */

/**
 * Contains the code required for accessing the website of "Bondora".
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[Not OK]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Ok, not tested]
 * parallelization                                                      [OK, tested]
 */
class bondora extends p2pCompany {

    protected $valuesTransaction = [// All types/names will be defined as associative index in array
        "A" => [
            [
                "type" => "date", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "D/M/Y",
                ],
                "functionName" => "normalizeDate",
            ]                           // Winvestify standardized name
        ],
        "B" => [
            [
                "type" => "currency", // Winvestify standardized name  OK
                "functionName" => "getCurrency",
            ]
        ],
        "C" => [
            [
                "type" => "amount", // This is *mandatory* field which is required for the 
                "inputData" => [// "transactionDetail"
                    "input2" => "", // and which BY DEFAULT is a Winvestify standardized variable name.
                    "input3" => ".", // and its content is the result of the "getAmount" method
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        "D" => [
            [
                "name" => "transaction_transactionId"                             // Winvestify standardized name
            ]
        ],
        "E" => [
            [
                "type" => "transactionDetail", // Winvestify standardized name   OK
                "inputData" => [// List of all concepts that the platform can generate
                    // format ["concept string platform", "concept string Winvestify"]
                    "input3" => [
                        0 => ["TransferPartialMainRepaiment" => "Partial_principal_repayment"],
                        1 => ["TransferExtraInterestRepaiment" => "Delayed_interest_income"],
                        2 => ["TransferWithdraw" => "Cash_withdrawal"],
                        3 => ["TransferMainRepaiment" => "Capital_repayment"], //OK
                        4 => ["TransferLatePenaltyRepaiment" => "Late_payment_fee_income"],
                        5 => ["TransferInterestRepaiment" => "Regular_gross_interest_income"], //
                    ]
                ],
                "functionName" => "getTransactionDetail",
            ]
        ],
        "F" => [
            [
                "name" => "investment_loanId"                             // Winvestify standardized name
            ]
        ],
    ];
    protected $valuesAmortizationTable = [// NOT FINISHED
        //See this value, it has two, the scheduledDate and the paymentDate
        0 => [
            [
                "type" => "amortizationtable_scheduledDate", // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "D/M/Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        1 => [
            [
                "type" => "amortizationtable_capitalAndInterestPayment", // Winvestify standardized name  OK
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
                "type" => "amortizationtable_capitalRepayment", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
        3 => [
            [
                "type" => "amortizationtable_interest", // Winvestify standardized name  OK
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
                "type" => "amortizationtable_latePaymentFee", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ".",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ],
    ];
    protected $transactionConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 0,
        //        'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );
    protected $investmentConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 0,
        //       'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );
    protected $amortizationConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 1,
            //       'separatorChar' => ";",
            //'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );

    function __construct() {
        parent::__construct();
        $this->i = 0;
        $this->typeFileTransaction = "xlsx";
        $this->typeFileInvestment = "xlsx";
        $this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";
        //$this->loanIdArray = array("6b3649c5-9a6b-4cee-ac05-a55500ef480a");
        //$this->maxLoans = count($this->loanIds);
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
          FIELDS USED BY Bondora DURING LOGIN PROCESS
          $credentials['*'] = "XXXXX";
         */

        //First we need get te token
        $str = $this->getCompanyWebpage();
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;


        $inputs = $dom->getElementsByTagName('input');
        foreach ($inputs as $key => $input) {
            //echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . HTML_ENDOFLINE;
            if ($key == 0) {
                continue;
            }
            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
        }

        $credentials['Email'] = $user;
        $credentials['Password'] = $password;

        //print_r($credentials);

        $str = $this->doCompanyLogin($credentials); //do login
        $dom = new DOMDocument;  //Check if works
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        //echo $str;

        $confirm = false;

        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            //echo $span->nodeValue . HTML_ENDOFLINE;
            if (trim($span->nodeValue) == 'Account value') {
                $confirm = true;
                break;
            }
        }

        if ($confirm) {
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
    function companyUserLogout($url = null) {
        //$this->doCompanyLogout();
        $this->getCompanyWebpage();
        return true;
    }

    /**
     *  Generate report to download.
     * @param type $str
     */
    function generateReportParallel($str = null) {
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to page of the company
                break;
            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . SHELL_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials['Email'] = $this->user;
                $credentials['Password'] = $this->password;

                print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;
            case 2:
                echo 'Doing loging' . SHELL_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $spans = $dom->getElementsByTagName('span');
                foreach ($spans as $span) {
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . SHELL_ENDOFLINE;
                        $confirm = true;
                        break;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Bondora login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $this->tempUrl['getToken'] = array_shift($this->urlSequence);
                $this->tempUrl['generateReport'] = array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl($this->tempUrl['getToken']);
                break;
            case 4:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    echo 'error';
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $key => $input) {
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $dateInit = date("d/m/Y", strtotime($this->dateInit));
                if ((int) explode("/", $dateInit)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $dateInit = '01/01/2009';
                }
                $dateFinish = date('d/m/Y', strtotime($this->dateFinish - 1));
                $credentials = array(
                    '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                    'NewReports[0].ReportType' => 'InvestmentsListV2',
                    "NewReports[0].DateFilterRequired" => 'False',
                    "NewReports[0].DateFilterShown" => 'True',
                    "NewReports[0].Selected" => 'true',
                    "NewReports[0].DateFilterSelected" => 'true',
                    "NewReports[0].StartDate" => $dateInit,
                    "NewReports[0].EndDate" => $dateFinish,
                    "NewReports[1].ReportType" => "Repayments",
                    "NewReports[1].DateFilterRequired" => 'False',
                    "NewReports[1].DateFilterShown" => 'True',
                    "NewReports[1].Selected" => 'false',
                    "NewReports[1].DateFilterSelected" => 'false',
                    "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                    "NewReports[2].DateFilterRequired" => 'False',
                    "NewReports[2].DateFilterShown" => 'True',
                    "NewReports[2].Selected" => 'false',
                    "NewReports[2].DateFilterSelected" => 'false',
                    "NewReports[3].ReportType" => 'SecondMarketArchive',
                    "NewReports[3].DateFilterRequired" => 'False',
                    "NewReports[3].DateFilterShown" => 'True',
                    "NewReports[3].Selected" => 'false',
                    "NewReports[3].DateFilterSelected" => 'false',
                    "NewReports[4].ReportType" => 'MonthlyOverview',
                    "NewReports[4].DateFilterRequired" => 'False',
                    "NewReports[4].DateFilterShown" => 'True',
                    "NewReports[4].Selected" => 'false',
                    "NewReports[4].DateFilterSelected" => 'false',
                    "NewReports[5].ReportType" => 'AccountStatement',
                    "NewReports[5].DateFilterRequired" => 'False',
                    "NewReports[5].DateFilterShown" => 'True',
                    "NewReports[5].Selected" => 'false',
                    "NewReports[5].DateFilterSelected" => 'false',
                    "NewReports[6].ReportType" => 'IncomeReport',
                    "NewReports[6].DateFilterRequired" => 'False',
                    "NewReports[6].DateFilterShown" => 'True',
                    "NewReports[6].DateFilterSelected" => 'True',
                    "NewReports[6].Selected" => 'false',
                    "NewReports[7].ReportType" => 'TaxReportPdf',
                    "NewReports[7].DateFilterRequired" => 'True',
                    "NewReports[7].DateFilterShown" => 'True',
                    "NewReports[7].DateFilterSelected" => 'True',
                    "NewReports[7].Selected" => 'false',
                    "NewReports[8].ReportType" => 'AccountValue',
                    "NewReports[8].DateFilterRequired" => 'False',
                    "NewReports[8].DateFilterShown" => 'True',
                    "NewReports[8].Selected" => 'false',
                    "NewReports[8].DateFilterSelected" => 'false',
                );
                $this->idForSwitch++;
                $this->documents = 1;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;
            case 5:

                $dateInit = date('d/m/Y', strtotime($this->dateInit - 1));
                if ((int) explode("/", $dateInit)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $dateInit = '01/01/2009';
                }
                $dateFinish = date("d/m/Y", strtotime($this->dateFinish));

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['getToken']);
            case 6:


                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $key => $input) {
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }


                $credentials = array(
                    '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                    'NewReports[0].ReportType' => 'InvestmentsListV2',
                    "NewReports[0].DateFilterRequired" => 'False',
                    "NewReports[0].DateFilterShown" => 'True',
                    "NewReports[0].Selected" => 'false',
                    "NewReports[0].DateFilterSelected" => 'false',
                    "NewReports[1].ReportType" => "Repayments",
                    "NewReports[1].DateFilterRequired" => 'False',
                    "NewReports[1].DateFilterShown" => 'True',
                    "NewReports[1].Selected" => 'false',
                    "NewReports[1].DateFilterSelected" => 'false',
                    "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                    "NewReports[2].DateFilterRequired" => 'False',
                    "NewReports[2].DateFilterShown" => 'True',
                    "NewReports[2].Selected" => 'false',
                    "NewReports[2].DateFilterSelected" => 'false',
                    "NewReports[3].ReportType" => 'SecondMarketArchive',
                    "NewReports[3].DateFilterRequired" => 'False',
                    "NewReports[3].DateFilterShown" => 'True',
                    "NewReports[3].Selected" => 'false',
                    "NewReports[3].DateFilterSelected" => 'false',
                    "NewReports[4].ReportType" => 'MonthlyOverview',
                    "NewReports[4].DateFilterRequired" => 'False',
                    "NewReports[4].DateFilterShown" => 'True',
                    "NewReports[4].Selected" => 'false',
                    "NewReports[4].DateFilterSelected" => 'false',
                    "NewReports[5].ReportType" => 'AccountStatement',
                    "NewReports[5].DateFilterRequired" => 'False',
                    "NewReports[5].DateFilterShown" => 'True',
                    "NewReports[5].Selected" => 'true',
                    "NewReports[5].DateFilterSelected" => 'true',
                    "NewReports[5].StartDate" => $dateInit,
                    "NewReports[5].EndDate" => $dateFinish,
                    "NewReports[6].ReportType" => 'IncomeReport',
                    "NewReports[6].DateFilterRequired" => 'True',
                    "NewReports[6].DateFilterShown" => 'True',
                    "NewReports[6].DateFilterSelected" => 'True',
                    "NewReports[6].Selected" => 'false',
                    "NewReports[7].ReportType" => 'TaxReportPdf',
                    "NewReports[7].DateFilterRequired" => 'True',
                    "NewReports[7].DateFilterShown" => 'True',
                    "NewReports[7].DateFilterSelected" => 'True',
                    "NewReports[7].Selected" => 'false',
                    "NewReports[8].ReportType" => 'AccountValue',
                    "NewReports[8].DateFilterRequired" => 'False',
                    "NewReports[8].DateFilterShown" => 'True',
                    "NewReports[8].Selected" => 'false',
                    "NewReports[8].DateFilterSelected" => 'false',
                );
                echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                if (!$this->control) {
                    $this->idForSwitch--;
                    $this->documents++;
                } else {
                    $this->idForSwitch++;
                }
                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;


            case 7:
                echo $str . SHELL_ENDOFLINE;
                return $tempArray = 'Generando informe';
        }
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
                $this->timerInvestment = 0;
                $this->timerTransaction = 0;
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

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . SHELL_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $this->credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $this->credentials['Email'] = $this->user;
                $this->credentials['Password'] = $this->password;

                print_r($this->credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($this->credentials); //do login
                break;

            case 2:
                echo 'Doing loging' . SHELL_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $spans = $dom->getElementsByTagName('span');
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($spans as $span) {
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . SHELL_ENDOFLINE;
                        $confirm = true;
                        break;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Bondora login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                //Get global data
                $this->tempArray['global'] = "";
                $spans = $dom->getElementsByTagName("span");
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                echo "GLOBAL DATA: ";
                foreach ($spans as $globalDataKey => $span) {
                    echo $globalDataKey . " IS " . $span->getAttribute('data-original-title');
                }

                $this->idForSwitch++;
                $this->tempUrl['tokenUrl'] = array_shift($this->urlSequence);
                $this->tempUrl['reportUrl'] = array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;

            case 4:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $searchInvestment = false;
                $searchTransactions = false;

                $trs = $dom->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                $dateInit = date("d/m/Y", strtotime($this->dateInit));
                if ((int) explode("/", $dateInit)[2] < 2009) {//Minimum date for bondora is 1/1/2009
                    $dateInit = '01/01/2009';
                }
                $dateFinish = date('d/m/Y', strtotime($this->dateFinish - 1));

                if (empty($this->tempUrl['generateReport'])) {
                    $this->tempUrl['generateReport'] = array_shift($this->urlSequence); //Get url for generate reports if we dont have it.
                }
                $waitInvestment = false;

                //Search investment report
                foreach ($trs as $tr) {
                    echo $tr->nodeValue . SHELL_ENDOFLINESHELL_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Investments list") && strpos($tr->nodeValue, "Please refresh your browser after a few minutes.")) {
                        echo 'waiting report';
                        sleep(10);
                        $this->timerInvestment++;
                        $waitInvestment = true;
                        break;
                    } else if (strpos($tr->nodeValue, "Investments list") && strpos($tr->nodeValue, $dateInit) && strpos($tr->nodeValue, $dateFinish)) { //Search if we have the invesment in the reports
                        echo 'Investment found';
                        $urls = $tr->getElementsByTagName('a');
                        $this->verifyNodeHasElements($urls);
                        if (!$this->hasElements) {
                            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                        }
                        //Get urls
                        foreach ($urls as $value) {
                            if (strpos($value->getAttribute('href'), "downloaddataexport")) {
                                $this->tempUrl['downloadInvestment'] = $value->getAttribute('href');
                            } else if (strpos($value->getAttribute('href'), "deletereport")) {
                                $this->tempUrl['deleteInvestment'] = $value->getAttribute('href');
                            }
                        }
                        $searchInvestment = true;
                        break;
                    }
                }

                if ($waitInvestment == true) {
                    if ($this->timerInvestment > 6) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    $this->idForSwitch = 4;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                    break;
                }

                if (!$searchInvestment) {
                    echo "file not found";
                    $this->idForSwitch = 12;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['tokenUrl']);
                    break;
                }

                $waitTransaction = false;
                foreach ($trs as $tr) {
                    echo $tr->nodeValue . SHELL_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Account statement") && strpos($tr->nodeValue, "Please refresh your browser after a few minutes.")) {
                        echo 'waiting report';
                        sleep(10);
                        $this->timerTransaction++;
                        $waitTransaction = true;
                        break;
                    } else if (strpos($tr->nodeValue, "Account statement") && strpos($tr->nodeValue, $dateInit) && strpos($tr->nodeValue, $dateFinish)) {
                        $urls = $tr->getElementsByTagName('a');
                        $this->verifyNodeHasElements($urls);
                        if (!$this->hasElements) {
                            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                        }
                        //Get urls
                        foreach ($urls as $value) {
                            if (strpos($value->getAttribute('href'), "downloaddataexport")) {
                                $this->tempUrl['downloadCashFlow'] = $value->getAttribute('href');
                            } else if (strpos($value->getAttribute('href'), "deletereport")) {
                                $this->tempUrl['deleteCashFlow'] = $value->getAttribute('href');
                            }
                        }
                        $searchTransactions = true;
                        break;
                    }
                }

                if ($waitTransaction == true) {
                    if ($this->timerTransaction > 6) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    $this->idForSwitch = 4;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                    break;
                }

                if (!$searchTransactions) {
                    echo "file not found";
                    $this->idForSwitch = 13;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['tokenUrl']);
                    break;
                }

                if (empty($this->downloadDeleteUrl)) {
                    $this->tempUrl['baseDownloadDelete'] = array_shift($this->urlSequence);
                }

                print_r($this->tempUrl);

                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadInvestment'];
                echo 'Investment url' . $url;
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, null, $this->fileName);
                break;

            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadCashFlow'];
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($url, null, false, null, $this->fileName);
                break;

            case 6:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                echo 'cashflow downloaded';
                $this->idForSwitch++;
                $this->tempUrl['DeleteCredentialPage'] = array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl($this->tempUrl['DeleteCredentialPage']);
                exit;
                break;

            case 7:

                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $scripts = $dom->getElementsByTagName('script');
                $this->verifyNodeHasElements($scripts);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($scripts as $script) {
                    //echo "search scripts: " . SHELL_ENDOFLINE;
                    //echo $script->nodeValue . SHELL_ENDOFLINE;
                    if (strpos($script->nodeValue, "RequestVerificationToken") != false) {
                        echo 'Finded: ' . SHELL_ENDOFLINE;
                        $deleteTokenArray = explode('"', $script->nodeValue);
                        $this->print_r2($deleteTokenArray);
                        $this->deleteToken = $deleteTokenArray[7];
                        echo "---___--- " . $this->deleteToken . " ---___---";
                    }
                }



                //$url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['deleteInvestment']; //URL FOR DELETE
                //echo "delete: " . $url . SHELL_ENDOFLINE;
                $this->idForSwitch++;
                //$this->headers = array("__RequestVerificationToken: " . $this->deleteToken, ":Type: POST", 'Host: www.bondora.com', 'Accept: */*', 'Accept-Language: en-US,en;q=0.5', 'Accept-Encoding: gzip, deflate, br', 'X-Requested-With: XMLHttpRequest', 'Connection: keep-alive', "content-length: 0", "Retry-After: 120");
                $this->getCompanyWebpageMultiCurl($this->tempUrl['DeleteCredentialPage']); //Delete don't work, go to another url
                //unset($this->headers);
                break;

            case 8:
                // echo $str . SHELL_ENDOFLINE;
                //$url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['deleteCashFlow'];
                $this->idForSwitch++;
                //$this->headers = array("__RequestVerificationToken: " . $this->deleteToken, 'Host: www.bondora.com', 'Accept: */*', 'Accept-Language: en-US,en;q=0.5', 'Accept-Encoding: gzip, deflate, br', 'X-Requested-With: XMLHttpRequest', 'Connection: keep-alive');
                $this->getCompanyWebpageMultiCurl($this->tempUrl['DeleteCredentialPage']); //Delete don't work, go to another url
                //unset($this->headers);
                break;

            case 9:
                echo $str . SHELL_ENDOFLINE;
                //return $tempArray = 'DEscargando fichero';
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 10:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $tds = $dom->getElementsByTagName('td');
                $this->verifyNodeHasElements($tds);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                /* foreach($tds as $key=>$td){
                  echo $key . " is " . $td->nodeValue;
                  } */

                $this->tempArray['global']['outstandingPrincipal'] = $tds[14]->nodeValue;  //Capital vivo
                $this->tempArray['global']['myWallet'] = $tds[2]->nodeValue; //My wallet

                $spans = $dom->getElementsByTagName('span');
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                /* foreach($spans as $key=>$span){
                  echo $key . " is " . $span->getAttribute('title');
                  } */

                //$this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($spans[3]->getAttribute('title'));

                print_r($this->tempArray);
                return $this->tempArray;
                break;
            case 11:
                sleep(10);
                $this->idForSwitch = 4;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;
            case 12:

                echo "file not found";

                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    echo 'error';
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $key => $input) {
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }
                /* echo "INPUTS VALUE" . SHELL_ENDOFLINE;
                  $this->print_r2($inputsValue);
                  echo "ENDS INPUTS VALUE" . SHELL_ENDOFLINE; */
                $dateInit = date("d/m/Y", strtotime($this->dateInit));
                if ((int) explode("/", $dateInit)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $dateInit = '01/01/2009';
                }
                $dateFinish = date('d/m/Y', strtotime($this->dateFinish - 1));
                $credentials = array(
                    '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                    'NewReports[0].ReportType' => 'InvestmentsListV2',
                    "NewReports[0].DateFilterRequired" => 'False',
                    "NewReports[0].DateFilterShown" => 'True',
                    "NewReports[0].Selected" => 'true',
                    "NewReports[0].DateFilterSelected" => 'true',
                    "NewReports[0].StartDate" => $dateInit,
                    "NewReports[0].EndDate" => $dateFinish,
                    "NewReports[1].ReportType" => "Repayments",
                    "NewReports[1].DateFilterRequired" => 'False',
                    "NewReports[1].DateFilterShown" => 'True',
                    "NewReports[1].Selected" => 'false',
                    "NewReports[1].DateFilterSelected" => 'false',
                    "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                    "NewReports[2].DateFilterRequired" => 'False',
                    "NewReports[2].DateFilterShown" => 'True',
                    "NewReports[2].Selected" => 'false',
                    "NewReports[2].DateFilterSelected" => 'false',
                    "NewReports[3].ReportType" => 'SecondMarketArchive',
                    "NewReports[3].DateFilterRequired" => 'False',
                    "NewReports[3].DateFilterShown" => 'True',
                    "NewReports[3].Selected" => 'false',
                    "NewReports[3].DateFilterSelected" => 'false',
                    "NewReports[4].ReportType" => 'MonthlyOverview',
                    "NewReports[4].DateFilterRequired" => 'False',
                    "NewReports[4].DateFilterShown" => 'True',
                    "NewReports[4].Selected" => 'false',
                    "NewReports[4].DateFilterSelected" => 'false',
                    "NewReports[5].ReportType" => 'AccountStatement',
                    "NewReports[5].DateFilterRequired" => 'False',
                    "NewReports[5].DateFilterShown" => 'True',
                    "NewReports[5].Selected" => 'false',
                    "NewReports[5].DateFilterSelected" => 'false',
                    "NewReports[6].ReportType" => 'IncomeReport',
                    "NewReports[6].DateFilterRequired" => 'False',
                    "NewReports[6].DateFilterShown" => 'True',
                    "NewReports[6].DateFilterSelected" => 'True',
                    "NewReports[6].Selected" => 'false',
                    "NewReports[7].ReportType" => 'TaxReportPdf',
                    "NewReports[7].DateFilterRequired" => 'True',
                    "NewReports[7].DateFilterShown" => 'True',
                    "NewReports[7].DateFilterSelected" => 'True',
                    "NewReports[7].Selected" => 'false',
                    "NewReports[8].ReportType" => 'AccountValue',
                    "NewReports[8].DateFilterRequired" => 'False',
                    "NewReports[8].DateFilterShown" => 'True',
                    "NewReports[8].Selected" => 'false',
                    "NewReports[8].DateFilterSelected" => 'false',
                );
                echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->idForSwitch = 11;
                echo "go to: " . $this->tempUrl['generateReport'];
                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;
            case 13:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input');
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $key => $input) {
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }
                /* echo "INPUTS VALUE" . SHELL_ENDOFLINE;
                  $this->print_r2($inputsValue);
                  echo "ENDS INPUTS VALUE" . SHELL_ENDOFLINE; */
                $dateInit = date("d/m/Y", strtotime($this->dateInit));
                if ((int) explode("/", $dateInit)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $dateInit = '01/01/2009';
                }
                $dateFinish = date('d/m/Y', strtotime($this->dateFinish - 1));
                $credentials = array(
                    '__RequestVerificationToken' => $inputsValue['__RequestVerificationToken'],
                    'NewReports[0].ReportType' => 'InvestmentsListV2',
                    "NewReports[0].DateFilterRequired" => 'False',
                    "NewReports[0].DateFilterShown" => 'True',
                    "NewReports[0].Selected" => 'false',
                    "NewReports[0].DateFilterSelected" => 'false',
                    "NewReports[1].ReportType" => "Repayments",
                    "NewReports[1].DateFilterRequired" => 'False',
                    "NewReports[1].DateFilterShown" => 'True',
                    "NewReports[1].Selected" => 'false',
                    "NewReports[1].DateFilterSelected" => 'false',
                    "NewReports[2].ReportType" => 'PlannedFutureCashflows',
                    "NewReports[2].DateFilterRequired" => 'False',
                    "NewReports[2].DateFilterShown" => 'True',
                    "NewReports[2].Selected" => 'false',
                    "NewReports[2].DateFilterSelected" => 'false',
                    "NewReports[3].ReportType" => 'SecondMarketArchive',
                    "NewReports[3].DateFilterRequired" => 'False',
                    "NewReports[3].DateFilterShown" => 'True',
                    "NewReports[3].Selected" => 'false',
                    "NewReports[3].DateFilterSelected" => 'false',
                    "NewReports[4].ReportType" => 'MonthlyOverview',
                    "NewReports[4].DateFilterRequired" => 'False',
                    "NewReports[4].DateFilterShown" => 'True',
                    "NewReports[4].Selected" => 'false',
                    "NewReports[4].DateFilterSelected" => 'false',
                    "NewReports[5].ReportType" => 'AccountStatement',
                    "NewReports[5].DateFilterRequired" => 'False',
                    "NewReports[5].DateFilterShown" => 'True',
                    "NewReports[5].Selected" => 'true',
                    "NewReports[5].DateFilterSelected" => 'true',
                    "NewReports[5].StartDate" => $dateInit,
                    "NewReports[5].EndDate" => $dateFinish,
                    "NewReports[6].ReportType" => 'IncomeReport',
                    "NewReports[6].DateFilterRequired" => 'True',
                    "NewReports[6].DateFilterShown" => 'True',
                    "NewReports[6].DateFilterSelected" => 'True',
                    "NewReports[6].Selected" => 'false',
                    "NewReports[7].ReportType" => 'TaxReportPdf',
                    "NewReports[7].DateFilterRequired" => 'True',
                    "NewReports[7].DateFilterShown" => 'True',
                    "NewReports[7].DateFilterSelected" => 'True',
                    "NewReports[7].Selected" => 'false',
                    "NewReports[8].ReportType" => 'AccountValue',
                    "NewReports[8].DateFilterRequired" => 'False',
                    "NewReports[8].DateFilterShown" => 'True',
                    "NewReports[8].Selected" => 'false',
                    "NewReports[8].DateFilterSelected" => 'false',
                );
                echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->idForSwitch = 11;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;
        }
    }

    /**
     * Get amortization tables of user investments
     * @param string $str It is the web converted to string of the company.
     * @return array html of the tables
     */
    function collectAmortizationTablesParallel($str = null) { //{"loanIds":{"702":["7e89377c-15fc-4de3-8b65-a55500ef6a1b","6b3649c5-9a6b-4cee-ac05-a55500ef480a"]}} example in queue_info
        switch ($this->idForSwitch) {
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;

            case 1:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $inputs = $dom->getElementsByTagName('input');

                foreach ($inputs as $key => $input) {
                    echo $key . "=>" . $input->getAttribute('value') . " " . $input->getAttribute('name') . SHELL_ENDOFLINE;
                    if ($key == 0) {
                        continue;
                    }
                    $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials['Email'] = $this->user;
                $credentials['Password'] = $this->password;

                print_r($credentials);
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials); //do login
                break;

            case 2:
                echo 'Doing loging' . SHELL_ENDOFLINE;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;

            case 3:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;


                $confirm = false;

                $spans = $dom->getElementsByTagName('span');
                foreach ($spans as $span) {
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Account value') {
                        echo 'Login ok' . SHELL_ENDOFLINE;
                        $confirm = true;
                        break;
                    }
                }

                if (!$confirm) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "Bondora login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 4:

                if (empty($this->tempUrl['investmentUrl'])) {
                    $this->tempUrl['investmentUrl'] = array_shift($this->urlSequence);
                }
                echo "Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url = $this->tempUrl['investmentUrl'] . $this->loanIds[$this->i];
                echo "the table url is: " . $url;
                $this->i++;
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
                    if ($table->getAttribute('class') == 'table') {
                        $AmortizationTable = new DOMDocument();
                        $clone = $table->cloneNode(TRUE); //Clene the table

                        $clone = $this->cleanDomTag($clone, array(
                            array('typeSearch' => 'tagElement', 'tag' => 'tr', 'attr' => 'class', 'value' => 'white'),
                            array('typeSearch' => 'tagElement', 'tag' => 'tr', 'attr' => 'class', 'value' => 'gray'), //Delete rows that we don't want
                        ));


                        $AmortizationTable->appendChild($AmortizationTable->importNode($clone, TRUE));
                        $AmortizationTableString = $AmortizationTable->saveHTML();
                        $this->tempArray[$this->loanIds[$this->i - 1]] = $AmortizationTableString;
                        echo $AmortizationTableString;
                    }
                }


                if ($this->i < $this->maxLoans) {
                    $this->idForSwitch = 4;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['investmentUrl'] . $this->loanIds[$this->i - 1]);
                    break;
                } else {
                    return $this->tempArray;
                }
        }
    }

    //BONDORA DOESNT HAVE CALLBACKS
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
