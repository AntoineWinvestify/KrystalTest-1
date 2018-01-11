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
 * 
 * 2017-12-12
 * Generate and download in time period
 * 
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
        [
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
        ]
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
    
    protected $amortizationConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 1,
            //       'separatorChar' => ";",
            //'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );
    protected $credentials = array(
        "NewReports[0].ReportType" => 'InvestmentsListV2',
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

    protected $investmentHeader = array(
        'A' => 'LoanId',
        'B' => 'PartyId',
        'C' => 'note_id',
        'D' => 'AuctionId',
        'E' => 'AppliedAmount',
        'F' => 'Amount',
        'G' => 'BiddingStartedOn',
        'H' => 'ListedOnUTC',
        'I' => 'BidPrincipal',
        'J' => 'BoughtFromResale_Date',
        'K' => 'PurchasePrice',
        'L' => 'SoldInResale_Date',
        'M' => 'SoldInResale_Price',
        'N' => 'SoldInResale_Principal',
        'O' => 'OnSaleSince',
        'P' => 'LoanNumber',
        'Q' => 'AuctionNumber',
        'R' => 'AuctionBidNumber',
        'S' => 'AuctionName',
        'T' => 'UserName',
        'U' => 'NewCreditCustomer',
        'V' => 'LoanApplicationStartedDate',
        'W' => 'ContractEndDate',
        'X' => 'FirstPaymentDate',
        'Y' => 'MaturityDate_Original',
        'Z' => 'MaturityDate_Last',
        'AA' => 'ApplicationSignedHour',
        'AB' => 'ApplicationSignedWeekday',
        'AC' => 'Interest',
        'AD' => 'LoanDuration',
        'AE' => 'VerificationType',
        'AF' => 'LanguageCode',
        'AG' => 'Age',
        'AH' => 'DateOfBirth',
        'AI' => 'Gender',
        'AJ' => 'Country',
        'AK' => 'County',
        'AL' => 'City',
        'AM' => 'CreditScoreEsMicroL',
        'AN' => 'CreditScoreEsEquifaxRisk',
        'AO' => 'CreditScoreFiAsiakasTietoRiskGrade',
        'AP' => 'CreditScoreEeMini',
        'AQ' => 'UseOfLoan',
        'AR' => 'Education',
        'AS' => 'EmploymentDurationCurrentEmployer',
        'AT' => 'EmploymentPosition',
        'AU' => 'EmploymentStatus',
        'AV' => 'MaritalStatus',
        'AW' => 'NrOfDependants',
        'AX' => 'WorkExperience',
        'AY' => 'OccupationArea',
        'AZ' => 'HomeOwnershipType',
        'BA' => 'IncomeFromPrincipalEmployer',
        'BB' => 'IncomeFromPension',
        'BC' => 'IncomeFromFamilyAllowance',
        'BD' => 'IncomeFromSocialWelfare',
        'BE' => 'IncomeFromLeavePay',
        'BF' => 'IncomeFromChildSupport',
        'BG' => 'IncomeOther',
        'BH' => 'IncomeTotal',
        'BI' => 'ExistingLiabilities',
        'BJ' => 'RefinanceLiabilities',
        'BK' => 'LiabilitiesTotal',
        'BL' => 'DebtToIncome',
        'BM' => 'MonthlyPayment',
        'BN' => 'MonthlyPaymentDay',
        'BO' => 'FreeCash',
        'BP' => 'CurrentDebtDaysPrimary',
        'BQ' => 'CurrentDebtDaysSecondary',
        'BR' => 'DebtOccuredOn',
        'BS' => 'DebtOccuredOnForSecondary',
        'BT' => 'DefaultDate',
        'BU' => 'LoanStatusActiveFrom',
        'BV' => 'Status',
        'BW' => 'ActiveLateCategory',
        'BX' => 'WorseLateCategory',
        'BY' => 'ActiveLateLastPaymentCategory',
        'BZ' => 'ActiveScheduleFirstPaymentReached',
        'CA' => 'LoanCancelled',
        'CB' => 'Restructured',
        'CC' => 'PrincipalRecovery',
        'CD' => 'InterestRecovery',
        'CE' => 'PlannedPrincipalPostDefault',
        'CF' => 'PlannedInterestPostDefault',
        'CG' => 'PlannedPrincipalTillDate',
        'CH' => 'PlannedInterestTillDate',
        'CI' => 'RecoveryStage',
        'CJ' => 'StageActiveSince',
        'CK' => 'ModelVersion',
        'CL' => 'ExpectedLoss',
        'CM' => 'ExpectedReturn',
        'CN' => 'LossGivenDefault',
        'CO' => 'ProbabilityOfDefault',
        'CP' => 'Rating',
        'CQ' => 'EL_V0',
        'CR' => 'EL_V1',
        'CS' => 'EL_V2',
        'CT' => 'Rating_V0',
        'CU' => 'Rating_V1',
        'CV' => 'Rating_V2',
        'CW' => 'PrincipalOverdueBySchedule',
        'CX' => 'PrincipalPaymentsMade',
        'CY' => 'InterestAndPenaltyPaymentsMade',
        'CZ' => 'PrincipalWriteOffs',
        'DA' => 'InterestAndPenaltyWriteOffs',
        'DB' => 'PrincipalDebtServicingCost',
        'DC' => 'InterestAndPenaltyDebtServicingCost',
        'DD' => 'PrincipalBalance',
        'DE' => 'InterestAndPenaltyBalance',
        'DF' => 'InterestLateAmount',
        'DG' => 'PenaltyLateAmount',
        'DH' => 'NoteLoanTransfersMainAmount',
        'DI' => 'NoteLoanTransfersInterestAmount',
        'DJ' => 'NoteLoanLateChargesPaid',
        'DK' => 'NoOfPreviousLoans',
        'DL' => 'AmountOfPreviousLoans',
        'DM' => 'PreviousRepayments',
        'DN' => 'PreviousEarlyRepayments',
        'DO' => 'PreviousEarlyRepaymentsCount',
        'DP' => 'GracePeriodStart',
        'DQ' => 'GracePeriodEnd',
        'DR' => 'ReScheduledOn',
        'DS' => 'NextPaymentDate',
        'DT' => 'NextPaymentNr',
        'DU' => 'NrOfScheduledPayments',
        'DV' => 'NextPaymentSum',
        'DW' => 'LastPaymentOn',
        'DX' => 'LoanDate',
        'DY' => 'EAD1',
        'DZ' => 'EAD2'
    );
    
    protected $transactionHeaders = array(
        'A' => 'TransferDate',
        'B' => 'Currency',
        'C' => 'Amount',
        'D' => 'Number',
        'E' => 'Description',
        'F' => 'LoanNumber',
        'G' => 'Counterparty',
        'H' => 'BalanceAfterPayment'
    );
    
    
    function __construct() {
        parent::__construct();
        $this->i = 0;
        $this->typeFileTransaction = "xlsx";
        $this->typeFileInvestment = "xlsx";
        $this->typeFileExpiredLoan = "xlsx";
        $this->typeFileAmortizationtable = "html";
        $this->unoconv = Unoconv\Unoconv::create();
        
        
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


        $confirm = false;

        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            //echo $span->nodeValue . HTML_ENDOFLINE;
            if (trim($span->nodeValue) == 'Dashboard') {
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
                $this->numberOfFiles = 0;
                $this->investmentNumber = 0;
                $this->transactionNumber = 0;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to page of the company
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
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($spans as $span) {
                    echo $span->nodeValue . SHELL_ENDOFLINE;
                    if (trim($span->nodeValue) == 'Dashboard') {
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
                $this->tempUrl['getToken'] = array_shift($this->urlSequence);       //Url to read that contains the page with the security token to generate the reports.
                $this->tempUrl['generateReport'] = array_shift($this->urlSequence); //Url that generate the report.
                $this->getCompanyWebpageMultiCurl($this->tempUrl['getToken']);
                break;
            case 4:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['getToken']);
                break;
            case 5:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $inputs = $dom->getElementsByTagName('input'); //Get all inputs in the page, with this we get the token
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $key => $input) {
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $continue = $this->downloadTimePeriod("20171109"/* $this->dateInit */, $this->period);
                $this->dateInitBondora = date('d/m/Y', strtotime($this->dateInitPeriod));
                if ((int) explode("/", $this->dateInitBondora)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $this->dateInitBondora = '01/01/2009';
                }

                if ($this->investmentNumber === 0) { //Max day in bondora is yesterday
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod . " " . -1 . " days"));
                } else {
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod));
                }


                $credentials = $this->credentials; //Credentials to generate the investments
                $credentials['__RequestVerificationToken'] = $inputsValue['__RequestVerificationToken'];
                $credentials['NewReports[0].ReportType'] = 'InvestmentsListV2';
                $credentials["NewReports[0].DateFilterRequired"] = 'False';
                $credentials["NewReports[0].DateFilterShown"] = 'True';
                $credentials["NewReports[0].Selected"] = 'true';
                $credentials["NewReports[0].DateFilterSelected"] = 'true';
                $credentials["NewReports[0].StartDate"] = $this->dateInitBondora;
                $credentials["NewReports[0].EndDate"] = $this->dateFinishBondora;

                echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->investmentNumber = $this->numberOfFiles;
                if ($continue !== false) {
                    $this->idForSwitch--;
                } else {
                    $this->idForSwitch++;
                    $this->numberOfFiles = 0;
                }

                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;
            case 6:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['getToken']);
                break;
            case 7:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $continue = $this->downloadTimePeriod("20171109"/* $this->dateInit */, $this->period);
                $this->dateInitBondora = date('d/m/Y', strtotime($this->dateInitPeriod));
                if ((int) explode("/", $this->dateInitBondora)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $this->dateInitBondora = '01/01/2009';
                }

                if ($this->transactionNumber === 0) { //Max day in bondora is yesterday
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod . " " . -1 . " days"));
                } else {
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod));
                }

                $inputs = $dom->getElementsByTagName('input');  //Get all input, we get the token with this
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($inputs as $key => $input) {
                    $inputsValue[$input->getAttribute('name')] = $input->getAttribute('value');
                }

                $credentials = $this->credentials; //Credentials to generate transactons report
                $credentials['__RequestVerificationToken'] = $inputsValue['__RequestVerificationToken'];
                $credentials["NewReports[5].ReportType"] = 'AccountStatement';
                $credentials["NewReports[5].DateFilterRequired"] = 'False';
                $credentials["NewReports[5].DateFilterShown"] = 'True';
                $credentials["NewReports[5].Selected"] = 'true';
                $credentials["NewReports[5].DateFilterSelected"] = 'true';
                $credentials["NewReports[5].StartDate"] = $this->dateInitBondora;
                $credentials["NewReports[5].EndDate"] = $this->dateFinishBondora;

                echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->transactionNumber = $this->numberOfFiles;
                if ($continue !== false) {
                    $this->idForSwitch--;
                } else {
                    $this->idForSwitch++;
                }
                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;


            case 8:
                //echo $str . SHELL_ENDOFLINE;
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
                $this->numberOfFiles = 0;
                $this->investmentNumber = 0;
                $this->transactionNumber = 0;
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
                if ($this->investmentNumber === 0 && $this->timerInvestment === 0) {  //Only get globlal variables the first time
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
                        if (trim($span->nodeValue) == 'Dashboard') {
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
                } else if ( $this->investmentNumber >= 1){
                    if (!$this->verifyFileIsCorrect()) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                    }
                    
                    $this->unoconv->transcode($this->getFolderPFPFile() . DS . $this->fileName, 'xlsx', $this->getFolderPFPFile() . DS . $this->fileName);
                    $headerError = $this->compareHeader();
                    if($headerError){
                        $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                    }
                }
                $this->idForSwitch++;
                if (empty($this->tempUrl['tokenUrl'])) {
                    $this->tempUrl['tokenUrl'] = array_shift($this->urlSequence);  //Page that contain the security token necesary to generate the reports
                    $this->tempUrl['reportUrl'] = array_shift($this->urlSequence);  //page that contains the report list
                    $this->tempUrl['generateReport'] = array_shift($this->urlSequence); //Get url for generate reports if we dont have it.
                    $this->tempUrl['baseDownloadDelete'] = array_shift($this->urlSequence); //Base url that download and delete the reports.
                }
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;

            case 4:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $searchInvestment = false;

                $trs = $dom->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                $continue = $this->downloadTimePeriod("20171109"/* $this->dateInit */, $this->period);
                $this->dateInitBondora = date('d/m/Y', strtotime($this->dateInitPeriod));
                if ((int) explode("/", $this->dateInitBondora)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $this->dateInitBondora = '01/01/2009';
                }

                if ($this->investmentNumber === 0) { //max date is yesterday
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod . " " . -1 . " days"));
                } else {
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod));
                }

                $waitInvestment = false;
                echo "Search this: init-" . $this->dateInitBondora . "     finish-" . $this->dateFinishBondora;
                //Search investment report
                foreach ($trs as $tr) { //Search the report
                    echo $tr->nodeValue . SHELL_ENDOFLINESHELL_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Investments list") && strpos($tr->nodeValue, "Please refresh your browser after a few minutes.")) { //If the report is not genrated yet, wait.-
                        echo 'waiting report';
                        sleep(10);
                        $this->timerInvestment++;
                        $waitInvestment = true;
                        break;
                    } else if (strpos($tr->nodeValue, "Investments list") && strpos($tr->nodeValue, $this->dateInitBondora) && strpos($tr->nodeValue, $this->dateFinishBondora)) { //Search if we have the invesment in the reports
                        echo 'Investment found';
                        $urls = $tr->getElementsByTagName('a');
                        $this->verifyNodeHasElements($urls);
                        if (!$this->hasElements) {
                            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                        }
                        //Get urls
                        foreach ($urls as $value) {
                            if (strpos($value->getAttribute('href'), "downloaddataexport")) {  //Save the download url for that report
                                $this->tempUrl['downloadInvestment'] = $value->getAttribute('href');
                            } else if (strpos($value->getAttribute('href'), "deletereport")) {
                                $this->tempUrl['deleteInvestment'] = $value->getAttribute('href');
                            }
                        }
                        $searchInvestment = true;
                        break;
                    }
                }

                if ($waitInvestment == true) { //Wait the report generation
                    if ($this->timerInvestment > 12) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    $this->idForSwitch = 3;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                    break;
                }

                if (!$searchInvestment) { //If we doesnt find the report, we generate it.
                    echo "file not found";
                    $this->idForSwitch = 11;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['tokenUrl']);
                    break;
                }

                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadInvestment']; //Build the report download url
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "_" . $this->numPartFileInvestment . "." . $this->typeFileInvestment;
                $this->numPartFileInvestment++;
                $this->investmentNumber = $this->numberOfFiles;

                if ($continue !== false) {
                    $this->idForSwitch--;
                } else {
                    $this->numberOfFiles = 0;
                    $this->idForSwitch++;
                }

                if (empty($this->tempUrl['investmentReferer'])) { //Header and referer for download
                    $this->tempUrl['investmentReferer'] = array_shift($this->urlSequence);
                    $this->tempUrl['investmentHeaders'] = array_shift($this->urlSequence);
                }
                $headerJsonDecode = json_decode(mb_convert_encoding($this->tempUrl["investmentHeaders"], 'UTF-8'), true); //HEADERS FOR CURL
                $this->headerComparation = $this->investmentHeader;//$this->investmetHeader; //HEADER OF THE FILE
                header('Content-type: application/xml');
                $this->getPFPFileMulticurl($url, $this->tempUrl['investmentReferer'], false, $headerJsonDecode, $this->fileName); //Download the report.
                break;

            case 5:
                
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                
               
                $this->unoconv->transcode($this->getFolderPFPFile() . DS . $this->fileName, 'xlsx', $this->getFolderPFPFile() . DS . $this->fileName);
                $headerError = $this->compareHeader();
                if($headerError){
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;

            case 6:
                /**
                 * This is like case 4 but we download the cashflow reports instead investments reports.
                 */
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $searchTransactions = false;

                $trs = $dom->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                $continue = $this->downloadTimePeriod("20171109"/* $this->dateInit */, $this->period);
                $this->dateInitBondora = date('d/m/Y', strtotime($this->dateInitPeriod));
                if ((int) explode("/", $this->dateInitBondora)[2] < 2009) { //Minimum date for bondora is 1/1/2009
                    $this->dateInitBondora = '01/01/2009';
                }

                if ($this->transactionNumber === 0) {
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod . " " . -1 . " days"));
                } else {
                    $this->dateFinishBondora = date("d/m/Y", strtotime($this->dateFinishPeriod));
                }

                echo 'search: init-' . $this->dateInitBondora . "         finish-" . $this->dateFinishBondora;
                $waitTransaction = false;
                foreach ($trs as $tr) {
                    echo $tr->nodeValue . SHELL_ENDOFLINE;
                    if (strpos($tr->nodeValue, "Account statement") && strpos($tr->nodeValue, "Please refresh your browser after a few minutes.")) {
                        echo 'waiting report';
                        sleep(10);
                        $this->timerTransaction++;
                        $waitTransaction = true;
                        break;
                    } else if (strpos($tr->nodeValue, "Account statement") && strpos($tr->nodeValue, $this->dateInitBondora) && strpos($tr->nodeValue, $this->dateFinishBondora)) {
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
                    $this->idForSwitch = 5;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                    break;
                }

                if (!$searchTransactions) {
                    echo "file not found";
                    $this->idForSwitch = 12;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['tokenUrl']);
                    break;
                }
                $this->transactionNumber = $this->numberOfFiles;
                if ($continue !== false) {
                    $this->idForSwitch--;
                } else {
                    $this->numberOfFiles = 0;
                    $this->idForSwitch++;
                }

                $url = $this->tempUrl['baseDownloadDelete'] . $this->tempUrl['downloadCashFlow'];
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "_" . $this->numPartFileTransaction . "." . $this->typeFileTransaction;
                $this->numPartFileTransaction++;

                if (empty($this->tempUrl['transactionReferer'])) {
                    $this->tempUrl['transactionReferer'] = array_shift($this->urlSequence);
                    $this->tempUrl['transactionHeaders'] = array_shift($this->urlSequence);
                }
                $this->headerComparation = $this->transactionHeaders;//$this->investmetHeader; //HEADER OF THE FILE
                $headerJsonDecode = json_decode(mb_convert_encoding($this->tempUrl["transactionHeaders"], 'UTF-8'), true);
                $this->getPFPFileMulticurl($url, $this->tempUrl['transactionReferer'], false, $headerJsonDecode, $this->fileName);
                break;

            case 7:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $this->unoconv->transcode($this->getFolderPFPFile() . DS . $this->fileName, 'xlsx', $this->getFolderPFPFile() . DS . $this->fileName);
                $headerError = $this->compareHeader();
                if($headerError){
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $this->idForSwitch++;
                array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl();
                break;

            case 8:
                $dom = new DOMDocument;  //Check if works
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $tds = $dom->getElementsByTagName('td');
                $this->verifyNodeHasElements($tds);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                $this->tempArray['global']['outstandingPrincipal'] = $tds[14]->nodeValue;  //Capital vivo
                $this->tempArray['global']['myWallet'] = $tds[2]->nodeValue; //My wallet

                $spans = $dom->getElementsByTagName('span');
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                //$this->tempArray['global']['totalEarnedInterest'] = $this->getMonetaryValue($spans[3]->getAttribute('title'));

                print_r($this->tempArray);
                return $this->tempArray;
                break;
            /**
             * Extra cases to generate the report if the user delete it before we download it.
             */
            case 9:
                sleep(10);
                $this->idForSwitch = 4;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;
            case 10:
                sleep(10);
                $this->idForSwitch = 6;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['reportUrl']);
                break;
            case 11:

                echo "file not found";

                $dom = new DOMDocument;
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

                $credentials = $this->credentials; //Credentials to generate the investments
                $credentials['__RequestVerificationToken'] = $inputsValue['__RequestVerificationToken'];
                $credentials['NewReports[0].ReportType'] = 'InvestmentsListV2';
                $credentials["NewReports[0].DateFilterRequired"] = 'False';
                $credentials["NewReports[0].DateFilterShown"] = 'True';
                $credentials["NewReports[0].Selected"] = 'true';
                $credentials["NewReports[0].DateFilterSelected"] = 'true';
                $credentials["NewReports[0].StartDate"] = $this->dateInitBondora;
                $credentials["NewReports[0].EndDate"] = $this->dateFinishBondora;

                /*echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;*/
                $this->idForSwitch = 9;
                //echo "go to: " . $this->tempUrl['generateReport'];
                $this->genrateReport = true;
                $this->getCompanyWebpageMultiCurl($this->tempUrl['generateReport'], $credentials);
                break;
            case 12:
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

                $credentials = $this->credentials;
                $credentials['__RequestVerificationToken'] = $inputsValue['__RequestVerificationToken'];
                $credentials["NewReports[5].ReportType"] = 'AccountStatement';
                $credentials["NewReports[5].DateFilterRequired"] = 'False';
                $credentials["NewReports[5].DateFilterShown"] = 'True';
                $credentials["NewReports[5].Selected"] = 'true';
                $credentials["NewReports[5].DateFilterSelected"] = 'true';
                $credentials["NewReports[5].StartDate"] = $this->dateInitBondora;
                $credentials["NewReports[5].EndDate"] = $this->dateFinishBondora;

                /*echo "CREDENTIALS VALUE" . SHELL_ENDOFLINE;
                $this->print_r2($credentials);
                echo "END CREDENTIALS VALUE" . SHELL_ENDOFLINE;*/
                $this->idForSwitch = 10;
                $this->genrateReport = true;
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
                $this->verifyNodeHasElements($inputs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
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
                $this->verifyNodeHasElements($tables);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
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
