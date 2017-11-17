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
 * function calculateLoanCost()										[OK not tested]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * function companyUserLogout()										[OK, tested]
 * parallelization                                                                                         [OK, tested]
 *
 * 2016-10-05	  version 2016_0.1
 * Basic version
 *
 * 2017-04-27
 * Duration fixed
 *
 * 2017-05-16       version 2017_0.2
 * Added parallelization
 * Added logout
 * Added verification of dom elements
 * 
 *
 * 2017-08-01      version 0.3
 * Fixed error to take more than one investment on lines 364 and 395
 *
 * 2017-08-04
 * collectCompanyMarketplaceData - read completed investment
 * collectHistorical - added
 * 
 * 2017-08-16
 * Structure Revision added
 * Status definition added
 * 
 * 2017-08-24 version 0.6
 * Added Pagination to get more than 20 investments. A single page of growly shows 20 investment although in their web
 * shows that it should be ten. Take care of this
 * Added new url sequence for pagination
 * 
 * 2017-10-24 version_0.7
 * Integration of parsing amortization tables with Gearman and fileparser
 * 
 * Parser AmortizationTables                                            [OK, tested]
 * 
 * PENDING:
 *
 *
 */

/**
 * Contains the code required for accessing the website of "Loanbook".
 * function calculateLoanCost()						[Not OK]
 * function collectCompanyMarketplaceData()				[OK, tested]
 * function companyUserLogin()						[OK, tested]
 * function collectUserGlobalFilesParallel                              [OK, tested]
 * function collectAmortizationTablesParallel()                         [Ok, not tested]
 * parallelization                                                      [OK, tested]
 */
class loanbook extends p2pCompany {

    protected $valueTransaction = [
        "A" => [
            [
                "type" => "date", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "D/M/Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        "C" => [
            [
                "type" => "transactionDetail", // Winvestify standardized name   OK
                "inputData" => [// List of all concepts that the platform can generate                                                   // format ["concept string platform", "concept string Winvestify"]
                    "input2" => [
                        0 => ["Provisión de Fondos" => "Cash_deposit"],
                        1 => ["Retirada de Fondos" => "Cash_withdrawal"],
                        2 => ["Participación en préstamos" => "Primary_market_investment"],
                        3 => ["Pago de capital" => "Capital_repayment"],
                        4 => ["Pago Intereses brutos" => "Regular_gross_interest_income"],
                        5 => ["Retención de intereses (IRPF)" => "Tax_income_withholding_tax"],
                        6 => ["Compensación por incidencia administrativa" => "Compensation"],
                        7 => ["Comisión pago por tarjeta" => "Bank_charges"],
                        8 => ["Participación en pagaré" => "Primary_market_investment"],
                        9 => ["Provisión de Fondos (por TPV)" => "Cash_deposit"]
                    ]
                ],
                "functionName" => "getTransactionDetail",
            ]
        ],
        "D" => [
            [
                "type" => "amount", // This is an "empty variable name". So "type" is
                "inputData" => [// obtained from $parser->TransactionDetails['type']
                    //MORE STUDY: I (antonio) assume that the thousands is with comma because the point is for decimals        
                    "input2" => ",", // and which BY DEFAULT is a Winvestify standardized variable name.
                    "input3" => ".", // and its content is the result of the "getAmount" method
                    "input4" => 2
                ],
                "functionName" => "getAmount",
            ]
        ],
        "E" => [
            "name" => "investment_loanId",
        ]
    ];
// NOT FINISHED
    protected $valuesInvestment = [// All types/names will be defined as associative index in array
        "A" => [
            "name" => "loanId"                                          // Winvestify standardized name
        ],
        "B" => [
            "name" => "investment_debtor",                           // Winvestify standardized name  OK
        ],
        "C" => [
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
        //"D" SPEAK WITH ANTOINE 
        "E" => [
            "name" => "investment_riskRating",
        ],  
        "F" => [
            [
                "type" => "investment_expectAnnualYield",                    // Winvestify standardized name   OK
                "functionName" => "getPercentage",
            ]     
        ],
        "G" => [
            //tiME LEFT, HOW TO TAKE
        ],
        "H" => [
            "type" => "investment_typeOfInvestment"
        ],
        "J" => [
            "name" => "investment_nominalInterestRate"
        ],
    ];
       
    protected $valuesAmortizationTable = [
        2 => [
            [
                "type" => "amortizationtable_scheduledDate", // Winvestify standardized name   OK
                "inputData" => [
                    "input2" => "D-M-Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
        3 => [
            "name" => "amortizationtable_paymentStatus"
        ],
        4 => [
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
        5 => [
            [
                "type" => "amortizationtable_interest", // Winvestify standardized name  OK
                "inputData" => [
                    "input2" => "",
                    "input3" => ",",
                    "input4" => 16
                ],
                "functionName" => "getAmount",
            ]
        ]
    ];
    protected $transactionConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 0,
        //'separatorChar' => ";",
        'sortParameter' => array("date", "investment_loanId")   // used to "sort" the array and use $sortParameter as prime index.
    );
    protected $investmentConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 0,
        //'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );
    protected $amortizationConfigParms = array('offsetStart' => 1,
        'offsetEnd' => 1,
        //'separatorChar' => ";",
        'sortParameter' => "investment_loanId"   // used to "sort" the array and use $sortParameter as prime index.
    );

     protected $callbacks = [
        "investment" => [
            "investment_loanType" => "translateLoanType",
            "investment_amortizationMethod" => "translateAmortizationMethod",
            "investment_buyBackGuarantee" => 'translateInvestmentBuyBackGuarantee'
        ]
    ];
     
     
    
    function __construct() {
        parent::__construct();
        $this->i = 0;
        $this->j = 0;
        $this->loanArray;
        $this->UserLoansId = array();
        //$this->loanIdArray = array(472);
        //$this->maxLoans = count($this->loanIdArray);
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how must it will cost in total to obtain a loan for a certain amount
     * 	from a company
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in month) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked:xx-xx-xxxx

        $minimumCommission = 12000;   // in  €cents

        $fixedCost = 3 * $amount / 100;
        if ($fixedCost < $minimumCommission) {
            $fixedCost = $minimumCommission;
        }

        $interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12);
        $totalCost = $fixedCost + $interest + $amount;
        return $fixedCost + $interest + $amount;
    }

    /**
     * Collects the marketplace data.
     * @param Array $companyBackup
     * @param Array $structure
     * @return array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure, $loanIdList) { //loanbook doesnt have pagination, it uses one table
        //LOGIN
        $this->companyUserLogin($this->config['company_username'], $this->config['company_password']);

        //Marketplace
        $this->investmentDeletedList = $loanIdList;
        $readController = 0;
        $investmentController = false;
        $dontRepeat = true;
        $totalArray = array();
        //$url = "https://www.loanbook.es/marketplace2/index-subastas?params=%7B%22exposition%22%3A%7B%7D%2C%22Rating%22%3A%7B%7D%2C%22typeofop%22%3A%7B%7D%2C%22remaining_duration%22%3A%7B%22plazo_restante%22%3A%2224%22%7D%2C%22accrual_period%22%3A%7B%7D%2C%22pnlsales%22%3A%7B%7D%2C%22nemployees%22%3A%7B%7D%2C%22borrower_age%22%3A%7B%7D%2C%22Sectores%22%3A%7B%7D%7D";
        $this->headers[] = "X-Requested-With: XMLHttpRequest";
        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;
        $clone = $dom->cloneNode($dom);

        $loans = $dom->getElementsByTagName('div');

        foreach ($loans as $key => $loan) {
            if ($totalArray !== false) {
                if ($loan->getAttribute('class') == 'mp_fila col-sm-12 lb_boxrow_info nomargin text-left') {
                    //echo $key;
                    //echo $loan->nodeValue . HTML_ENDOFLINE;

                    if ($key == 0 && $dontRepeat == true) { //Compare structures, olny compare the first element     
                        echo 'Comparo';
                        $structureRevision = $this->htmlRevision($structure, 'div', null, 'class', 'mp_fila', array('dom' => $dom, 'tag' => 'div'), 0, 0);
                        $dontRepeat = false;
                        if ($structureRevision[1]) {
                            $totalArray = false; //Stop reading in error                 
                            break;
                        }
                    }
                    $tempArray['marketplace_country'] = 'ES';
                    $loanData = $loan->getElementsByTagName('div');
                    foreach ($loanData as $index => $datum) {
                        //echo HTML_ENDOFLINE . $index . " => " . $datum->nodeValue . HTML_ENDOFLINE;
                        switch ($index) {
                            case 0:
                                //Rating
                                $tempArray['marketplace_rating'] = trim($datum->getElementsByTagName('span')[0]->nodeValue);
                                //Loan Type
                                if (trim($datum->getElementsByTagName('span')[1]->nodeValue == "PRÉSTAMO")) {
                                    $tempArray['marketplace_productType'] = LOAN;
                                } else if (trim($datum->getElementsByTagName('span')[1]->nodeValue == "PAGARÉ")) {
                                    $tempArray['marketplace_productType'] = PAGARE;
                                }
                                //Interest
                                $tempArray['marketplace_interestRate'] = $this->getPercentage($datum->getElementsByTagName('span')[2]->nodeValue);
                                //Time left and duration
                                $tempArray['marketplace_timeLeft'] = trim($datum->getElementsByTagName('span')[3]->nodeValue);
                                $durationArray = explode("(", $datum->nodeValue);
                                list($tempArray['marketplace_duration'], $tempArray['marketplace_marketplace_durationUnit'] ) = $this->getDurationValue($durationArray[1]);
                                $tempArray['marketplace_timeLeftUnit'] = $tempArray['marketplace_marketplace_durationUnit'];
                                break;
                            case 7:
                                //Purpose and ID
                                $a = $datum->getElementsByTagName('a')[0];
                                $tempArray['marketplace_purpose'] = trim($a->nodeValue);
                                $tempArray['marketplace_loanReference'] = $a->getAttribute('data-id');
                                //Amount
                                $tempArray['marketplace_amount'] = $this->getMonetaryValue($datum->getElementsByTagName('span')[2]->nodeValue);
                                //Location
                                $tempArray['marketplace_requestorLocation'] = trim($datum->getElementsByTagName('span')[3]->nodeValue);
                                //Progress
                                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($datum->getElementsByTagName('span')[5]->nodeValue);
                                //print_r($tempArray);
                                break;
                            case 10:
                                $time = explode(" ", trim($datum->nodeValue))[0];
                                if ($tempArray['marketplace_subscriptionProgress'] == 10000 && $time > 0) {
                                    $tempArray['marketplace_status'] = PERCENT;
                                    $tempArray['marketplace_statusLiteral'] = 'Completado/Con tiempo';
                                } else if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                                    $tempArray['marketplace_status'] = CONFIRMED;
                                    $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
                                } else {
                                    $tempArray['marketplace_statusLiteral'] = 'En proceso';
                                }
                                break;
                            case 22:
                                $tempArray['marketplace_sector'] = trim($datum->nodeValue);
                                break;
                        }
                    }

                    foreach ($companyBackup as $inversionBackup) { //If completed and already in db, dont save
                        if ($tempArray['marketplace_subscriptionProgress'] == 10000 && $tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_status'] == $tempArray['marketplace_status']) {
                            $readController++;
                            $investmentController = true;
                        } else if ($tempArray['marketplace_subscriptionProgress'] == $inversionBackup['Marketplacebackup']['marketplace_subscriptionProgress'] && $tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference']) {
                            $this->investmentDeletedList = $this->marketplaceLoanIdWinvestifyPfpComparation($this->investmentDeletedList, $tempArray);
                            unset($tempArray);
                        }
                    }



                    if ($investmentController) { //Don't save a already existing investment
                        unset($tempArray);
                        $investmentController = false;
                    } else if (!empty($tempArray)) {
                        $totalArray[] = $tempArray;
                        unset($tempArray);
                    }
                }
                if ($readController > 15) { //If we fin more than two completed investment existing in the backpup, stop reading
                    echo 'Stop reading';
                    break;
                }
            }
        }

        $this->print_r2($this->investmentDeletedList);
        $hiddenInvestments = $this->readHiddenInvestment($this->investmentDeletedList);
        echo 'Hidden: ' . SHELL_ENDOFLINE;
        $this->print_r2($hiddenInvestments);

        //$this->print_r2($totalArray);
        foreach ($totalArray as $key => $investment) { //Delete empy lines
            if (!$investment['marketplace_loanReference'] || !$investment['marketplace_loanReference'] = null || !$investment['marketplace_loanReference'] = '') {
                unset($totalArray[$key]);
            }
        }

        $totalArray = array_merge($totalArray, $hiddenInvestments);
        //$this->print_r2($totalArray);
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
            echo "Next Investment Url: " . $url . $loanId . HTML_ENDOFLINE;
            $str = $this->getCompanyWebpage($url . $loanId);
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            $dom->loadHTML($str);
            $tempArray['marketplace_country'] = 'ES'; //Loanbook is in spain
            $tempArray['marketplace_loanReference'] = $loanId;

            $divs = $this->getElements($dom, 'div', 'class', 'row');
            $tempValues = explode(" ", $divs[6]->nodeValue);
            $tempArray['marketplace_rating'] = trim($tempValues[0]);
            $tempArray['marketplace_interestRate'] = $this->getPercentage($tempValues[1]);
            $tempArray['marketplace_timeLeft'] = trim($tempValues[5]);
            $tempArray['marketplace_subscriptionProgress'] = 10000;
            $table = $dom->getElementById("table-1");
            $tds = $table->getElementsByTagName('td');
            foreach ($tds as $keyTd => $td) {
                echo "TD VALUE: " . $keyTd . " " . $td->nodeValue . HTML_ENDOFLINE;
                switch ($keyTd) {
                    case 1:
                        switch ($td->nodeValue) {
                            case "Reembolsado":
                                $tempArray['marketplace_status'] = CONFIRMED;
                                $tempArray['marketplace_statusLiteral'] = $td->nodeValue;
                                break;
                            case "Cancelado":
                                $tempArray['marketplace_status'] = REJECTED;
                                $tempArray['marketplace_statusLiteral'] = $td->nodeValue;
                                break;
                            case "En curso (EXISTENTE)":
                                $tempArray['marketplace_statusLiteral'] = $td->nodeValue;
                                break;
                            case "Subasta":
                                $tempArray['marketplace_statusLiteral'] = $td->nodeValue;
                                break;
                        }
                        break;
                    case 3:
                        if (trim($td->nodeValue == "Préstamo")) {
                            $tempArray['marketplace_productType'] = LOAN;
                        } else if (trim($td->nodeValue == "Pagaré")) {
                            $tempArray['marketplace_productType'] = PAGARE;
                        }
                        break;
                    case 5:
                        $tempArray['marketplace_amount'] = $this->getMonetaryValue($td->nodeValue);
                        break;
                }
            }
            $newTotalArray[] = $tempArray;
        }
        return $newTotalArray;
    }

    /*     * ************************************************************ */
    /* LOANBOOK MARKETPLACE CHANGE ELIMIMATED THE HISTORICAL TABLE */
    /*     * ************************************************************ */
    /**
     * collect all investment
     * @param Array $structure
     * @return Array
     */
    /* function collectHistorical($structure) { //loanbook doesnt have pagination
      $totalArray = array();
      $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed
      $dontRepeat = true;
      $dom = new DOMDocument;
      $dom->loadHTML($str);
      $dom->preserveWhiteSpace = false;

      $sections = $dom->getElementsByTagName('tbody');
      foreach ($sections as $section) {

      $trs = $section->getElementsByTagName('tr');
      if ($totalArray !== false) {
      foreach ($trs as $key => $tr) {

      if ($key == 0 && $dontRepeat == true) { //Compare structures, olny compare the first element
      $structureRevision = $this->htmlRevision($structure, 'tr', $section, 'class', 'fila_subasta', null, 0, 1);
      $dontRepeat = false;
      if ($structureRevision[1]) {
      $totalArray = false; //Stop reading in error
      break;
      }
      }

      $tempAttribute = $tr->getAttribute('class');
      if ($tempAttribute == 'fila_subasta' || $tempAttribute == 'fila_subasta tablesorter-childRow') {

      $tds = $tr->getElementsByTagName('td');
      $index = -1;
      foreach ($tds as $td) {
      $index++;
      switch ($index) {
      case 0:
      $tempArray['marketplace_country'] = 'ES';
      break;
      case 1:
      $divs = $td->getElementsByTagName('div');
      foreach ($divs as $div) {

      $tempData = explode(",", $div->nodeValue);
      $tempDataAmount = explode(" ", $tempData[count($tempData) - 1]);

      for ($i = 1; $i < count($tempData); $i++) { //If the purpose have one or more ',' we need fix our array.
      if ($i != count($tempData) - 1) {
      $tempData[0] = $tempData[0] . $tempData[$i];
      }
      if ($i == count($tempData) - 1) {
      $tempData[1] = $tempData[count($tempData) - 1];
      }
      }

      $loanReference = explode("€", str_replace(" ", "", $tempData[1]));

      echo 'loan id : <br>';
      /* $this->print_r2($tempData);
      $this->print_r2($tempData[1]);
      $this->print_r2($loanReference); */

    //print_r($tempData);
    /* $tempDataAux = explode(" ", $tempData[0]);

      $max = count($tempDataAux);
      foreach ($tempDataAux as $key => $tmp) {
      //echo 'Ascii ' .$key . " :". ord($locationArray) . '/';
      if (!$tmp) {
      unset($tempDataAux[$key]);
      }
      }
      unset($tempDataAux[0]);

      $sector = '';
      $auxKey = 0;

      foreach ($tempDataAux as $key => $sectorArray) {
      if (ord($sectorArray) == LINE_FEED) {
      $auxKey = $key;
      break;
      }

      $sector = $sector . $sectorArray . ' ';
      }

      $location = '';
      for ($i = $auxKey + 1; $i <= $max; $i++) {
      echo $i . ': ' . $tempDataAux[$i];
      if ($tempDataAux[$i]) {
      $location = $location . $tempDataAux[$i] . ' ';
      }
      }



      //$tempArray['marketplace_sector'] = $sector;
      $tempArray['marketplace_requestorLocation'] = $location;
      $tempArray['marketplace_amount'] = $this->getMonetaryValue($tempDataAmount[1]);
      $tempArray['marketplace_loanReference'] = trim($loanReference[1]);

      $as = $div->getElementsByTagName('a');  //just one is found
      foreach ($as as $a) {
      $tempArray['marketplace_purpose'] = trim($a->nodeValue);
      }

      break;
      }
      break;
      case 2:
      $tempProductType = trim($td->nodeValue);
      if (stripos($tempProductType, "stamo")) {  // LOAN
      $tempArray['marketplace_productType'] = LOAN;
      }
      if (stripos($tempProductType, "agar")) {  // PAGARÉ
      $tempArray['marketplace_productType'] = PAGARE;
      }
      break;
      case 3:
      $tempArray['marketplace_rating'] = trim($td->nodeValue);
      break;
      case 4:
      break;
      case 5:
      $tempArray['marketplace_interestRate'] = $this->getPercentage($td->nodeValue);
      break;
      case 7:
      list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($td->nodeValue);
      break;
      case 6:
      break;
      case 8:
      $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($td->nodeValue);
      break;
      case 9:
      list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) = $this->getDurationValue($td->nodeValue);
      break;
      }
      }

      if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
      if ($tempArray['marketplace_timeLeft']) {
      $tempArray['marketplace_statusLiteral'] = 'Completado/Con tiempo';
      $tempArray['marketplace_status'] = PERCENT;
      } else {
      $tempArray['marketplace_statusLiteral'] = 'Completado/Sin tiempo';
      $tempArray['marketplace_status'] = CONFIRMED;
      $tempArray['marketplace_timeLeft'] = 0;
      }
      } else {
      $tempArray['marketplace_statusLiteral'] = 'En proceso';
      }
      }
      if ($tempArray) {
      $totalArray[] = $tempArray;
      }
      unset($tempArray);
      }
      }
      }
      foreach ($totalArray as $key => $investment) { //Delete empy lines
      if (!$investment['marketplace_loanReference'] || !$investment['marketplace_loanReference'] = null || !$investment['marketplace_loanReference'] = '') {
      unset($totalArray[$key]);
      }
      }
      return [$totalArray, false, null, $structureRevision[0], $structureRevision[2]]; //false -> Loanbook doesnt have pagination
      //$totalarray Contain the pfp investment or is false if we have an error
      //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
      //$structureRevision[2] return the type of error
      } */

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentDataParallel($str) {


        switch ($this->idForSwitch) {
            case 0:
                /*
                  FIELDS USED BY LOANBOOK DURING LOGIN PROCESS

                  csrf		539d6241ffbb10437f4fe6e27552bfe9
                  password	cede_4040
                  signin		Login
                  username	antoine.de.poorter@gmail.com
                 */
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Click "login" needed so I can read the csrf code
                break;
            case 2:
                $credentials['username'] = $this->user;
                $credentials['password'] = $this->password;
                $credentials['signin'] = "Login";
                $dom = new DOMDocument;
                //echo $str;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                /* $this->verifyNodeHasElements($forms);
                  if (!$this->hasElements) {
                  return $this->getError(__LINE__, __FILE__);
                  } */
                $index = 0;
                foreach ($forms as $form) {
                    $index = $index + 1;
                    $inputs = $form->getElementsByTagName('input');
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('name'))) {  // check all hidden input fields, like csrf
                            if ($input->getAttribute('name') == "csrf") {
                                echo "AAAA" . $credentials[$input->getAttribute('name')] . "<br>";
                                $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                                break 2;
                            }
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 3:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $resultMiLoanbook = false; // Could not login, credential error
                $uls = $dom->getElementsByTagName('ul');
                /* if (!$this->hasElements) {
                  return $this->getError(__LINE__, __FILE__);
                  } */
                foreach ($uls as $ul) {

                    $as = $ul->getElementsByTagName('a');
                    $this->verifyNodeHasElements($as);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $index = 0;
                    foreach ($as as $a) {
                        if (strcasecmp(trim($a->nodeValue), "RESUMEN") == 0) {
                            $this->mainPortalPage = $str;
                            $resultMiLoanbook = true;
                            break 2;
                        }
                        $index++;
                    }
                }
                if (!$resultMiLoanbook) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
                    $tracings .= "Loanbook login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }

                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                // Read the global investment data of this user
                $globals = $this->getElements($dom, "span", "class", "lb_main_menu_bold");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($globals[0]->nodeValue);

                $globals = $this->getElements($dom, "div", "id", "lb_cartera_data_3");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $spans = $globals[0]->getElementsByTagName('span');
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                //$this->tempArray['global']['profitibility'] = $this->getPercentage(trim($spans[0]->nodeValue));

                $globals = $this->getElements($dom, "div", "id", "lb_cartera_data_1");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $spans = $globals[0]->getElementsByTagName('span');
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $this->tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($spans[0]->nodeValue);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  //str1 load Webpage into a string variable so it can be parsed	
                break;
            case 4:
                $this->idForSwitch++;
                //array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl();  //str2 load Webpage into a string variable so it can be parsed
                break;
            case 5:
                $this->idForSwitch++;
                //array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl();  //str3 load Webpage into a string variable so it can be parsed	
                break;
            case 6:
                $this->idForSwitch++;
                //array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl();  //str4 load Webpage into a string variable so it can be parsed	
                break;
            case 7:
                $this->idForSwitch++;
                //array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl();  //str5 load Webpage into a string variable so it can be parsed	
                break;
            case 8:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  //str6 load Webpage into a string variable so it can be parsed	
                break;
            case 9:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $trs = $dom->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                // Get information about each individual transaction
                $this->numberOfInvestments = 0;
                for ($key = 0; $key < count($trs); $key++) {
                    if ($trs[$key]->getAttribute("class") <> "expander") {
                        continue;
                    }

                    $this->numberOfInvestments++;
                    $tds = $this->getElements($trs[$key], "td");

                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $spans = $this->getElements($tds[0], "span");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $this->data1[$key]['loanId'] = $spans[1]->nodeValue;

                    //Duration. The unit (=días) is hardcoded
                    $temp = explode("              ", trim($tds[4]->nodeValue));
                    $this->data1[$key]['date'] = trim($temp[0]);
                    $tempDuration = trim($temp[1]);
                    $this->data1[$key]['duration'] = filter_var($tempDuration, FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";
                    $this->data1[$key]['invested'] = $this->getMonetaryValue($tds[5]->nodeValue);
                    $this->data1[$key]['commission'] = 0;
                    $this->data1[$key]['interest'] = $this->getPercentage($tds[6]->nodeValue);

                    // Get amortization table. first get base URL for amortization table
                    if (empty($baseUrl)) {
                        $baseUrl = array_shift($this->urlSequence);
                    }
                    $as = $tds[0]->getElementsByTagName('a');   // only 1 will be found
                    $this->verifyNodeHasElements($as);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $dataId = $as[0]->getAttribute("data-id");
                    $this->tempUrl[$key] = $baseUrl . "/" . $dataId;
                    // Deal with the amortization table
                    //$strAmortizationTable = $this->getCompanyWebpage($baseUrl . "/" . $dataId);
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->accountPosition]);     // Deal with the amortization table
                break;
            case 10:
                $strAmortizationTable = $str;
                $domAmortizationTable = new DOMDocument;
                libxml_use_internal_errors(true);
                $domAmortizationTable->loadHTML($strAmortizationTable);
                $domAmortizationTable->preserveWhiteSpace = false;
                $amortizationData = $this->getElements($domAmortizationTable, "tr", "class", "detail"); // only 1 found
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                // Convert into table
                $mainIndex = -1;

                // map status to Winvestify normalized status, (PENDING), OK, DELAYED, DEFAULTED	
                $this->data1[$this->accountPosition]['status'] = 0;

                // prepare amortization table and normalize (payment) status (PENDING), OK, DELAYED, DEFAULTED
                // and get the "real" status of theloan. Index 2 of table represents the loan state
                foreach ($amortizationData as $key1 => $trAmortizationTable) {
                    $mainIndex = $mainIndex + 1;
                    $subIndex = -1;
                    $tdsAmortizationTable = $trAmortizationTable->getElementsByTagName('td');
                    $this->verifyNodeHasElements($tdsAmortizationTable);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    foreach ($tdsAmortizationTable as $tdAmortizationTable) {
                        $subIndex++;
                        if ($subIndex == 3) {   // normalize the status, needed for payment calculations
                            $is = $tdAmortizationTable->getElementsByTagName('i');
                            $this->verifyNodeHasElements($is);
                            if (!$this->hasElements) {
                                return $this->getError(__LINE__, __FILE__);
                            }
                            $actualState = $is[0]->getAttribute("title");
                            $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
                        } else {
                            $amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
                        }
                    }
                }
                $this->data1[$this->accountPosition]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 3, 2);
                $this->data1[$this->accountPosition]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 4, 2);

                $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] +
                        $this->data1[$this->accountPosition]['profitGained'];
                $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $this->data1[$this->accountPosition]['invested'];
                if ($this->accountPosition < $this->numberOfInvestments - 1) {
                    $this->idForSwitch = 10;
                    $this->accountPosition++;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->accountPosition]);
                    break;
                } else {
                    $this->tempArray['global']['investments'] = $this->numberOfInvestments;
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

        $resultMiLoanbook = $this->companyUserLogin($user, $password);

        if (!$resultMiLoanbook) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
            $tracings .= "Loanbook login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }


        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // obtained in the function	"companyUserLogin"	
        $dom->preserveWhiteSpace = true;
// Read the global investment data of this user
        $globals = $this->getElements($dom, "span", "class", "lb_main_menu_bold");

        $tempArray['global']['myWallet'] = $this->getMonetaryValue($globals[0]->nodeValue);

        $globals = $this->getElements($dom, "div", "id", "lb_cartera_data_3");

        $spans = $globals[0]->getElementsByTagName('span');
        $tempArray['global']['profitibility'] = $this->getPercentage(trim($spans[0]->nodeValue));

        $globals = $this->getElements($dom, "div", "id", "lb_cartera_data_1");
        $spans = $globals[0]->getElementsByTagName('span');
        $tempArray['global']['activeInInvestments'] = $this->getMonetaryValue($spans[0]->nodeValue);


        $str1 = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	
        $str2 = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed
        $str3 = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	
        $str4 = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	
        $str5 = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	
        $str6 = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed	

        $dom = new DOMDocument;
        $dom->loadHTML($str6); // obtained in the function	"companyUserLogin"	
        $dom->preserveWhiteSpace = true;
        $trs = $dom->getElementsByTagName('tr');
// Get information about each individual transaction
        $numberOfInvestments = 0;
        foreach ($trs as $key => $tr) {
            if ($tr->getAttribute("class") <> "expander") {
                continue;
            }

            $numberOfInvestments = $numberOfInvestments + 1;
            $tds = $this->getElements($tr, "td");

            $spans = $this->getElements($tds[0], "span");
            $data1[$key]['loanId'] = $spans[1]->nodeValue;

//Duration. The unit (=días) is hardcoded
            $temp = explode("              ", trim($tds[4]->nodeValue));
            $data1[$key]['date'] = trim($temp[0]);
            $tempDuration = trim($temp[1]);
            $data1[$key]['duration'] = filter_var($tempDuration, FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";
            $data1[$key]['invested'] = $this->getMonetaryValue($tds[5]->nodeValue);
            $data1[$key]['commission'] = 0;
            $data1[$key]['interest'] = $this->getPercentage($tds[6]->nodeValue);

// Get amortization table. first get base URL for amortization table
            $baseUrl = array_shift($this->urlSequence);
            $as = $tds[0]->getElementsByTagName('a');   // only 1 will be found
            $dataId = $as[0]->getAttribute("data-id");

// Deal with the amortization table
            $strAmortizationTable = $this->getCompanyWebpage($baseUrl . "/" . $dataId);
            $domAmortizationTable = new DOMDocument;
            $domAmortizationTable->loadHTML($strAmortizationTable);
            $domAmortizationTable->preserveWhiteSpace = false;
            $amortizationData = $this->getElements($domAmortizationTable, "tr", "class", "detail"); // only 1 found
// Convert into table
            $mainIndex = -1;

// map status to Winvestify normalized status, (PENDING), OK, DELAYED, DEFAULTED	
            $data1[$key]['status'] = 0;

// prepare amortization table and normalize (payment) status (PENDING), OK, DELAYED, DEFAULTED
// and get the "real" status of theloan. Index 2 of table represents the loan state
            foreach ($amortizationData as $key1 => $trAmortizationTable) {
                $mainIndex = $mainIndex + 1;
                $subIndex = -1;
                $tdsAmortizationTable = $trAmortizationTable->getElementsByTagName('td');

                foreach ($tdsAmortizationTable as $tdAmortizationTable) {
                    $subIndex = $subIndex + 1;
                    if ($subIndex == 3) {   // normalize the status, needed for payment calculations
                        $is = $tdAmortizationTable->getElementsByTagName('i');
                        $actualState = $is[0]->getAttribute("title");
                        $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
                    } else {
                        $amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
                    }
                }
            }
            $data1[$key]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 3, 2);
            $data1[$key]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 4, 2);

            $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
                    $data1[$key]['profitGained'];
            $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
        }
        $tempArray['global']['investments'] = $numberOfInvestments;
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
                /*
                  FIELDS USED BY LOANBOOK DURING LOGIN PROCESS

                  csrf		539d6241ffbb10437f4fe6e27552bfe9
                  password	cede_4040
                  signin		Login
                  username	antoine.de.poorter@gmail.com
                 */
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Click "login" needed so I can read the csrf code
                break;
            case 2:
                $credentials['username'] = $this->user;
                $credentials['password'] = $this->password;
                $credentials['signin'] = "Login";
                $dom = new DOMDocument;
                //echo $str;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                $this->verifyNodeHasElements($forms);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $index = 0;
                foreach ($forms as $form) {
                    $index = $index + 1;
                    $inputs = $form->getElementsByTagName('input');
                    $this->verifyNodeHasElements($inputs);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('name'))) {  // check all hidden input fields, like csrf
                            if ($input->getAttribute('name') == "csrf") {
                                echo "AAAA" . $credentials[$input->getAttribute('name')] . "<br>";
                                $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                                break 2;
                            }
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 3:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $resultMiLoanbook = false; // Could not login, credential error
                $uls = $dom->getElementsByTagName('ul');
                $this->verifyNodeHasElements($uls);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($uls as $ul) {

                    $as = $ul->getElementsByTagName('a');
                    $this->verifyNodeHasElements($as);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    $index = 0;
                    foreach ($as as $a) {
                        if (strcasecmp(trim($a->nodeValue), "RESUMEN") == 0) {
                            $this->mainPortalPage = $str;
                            $resultMiLoanbook = true;
                            break 2;
                        }
                        $index++;
                    }
                }
                if (!$resultMiLoanbook) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
                    $tracings .= "Loanbook login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  //str1 load Webpage into a string variable so it can be parsed	
                break;
            case 4:

                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str); // obtained in the function	"companyUserLogin"	
                $dom->preserveWhiteSpace = false;

                // Read the global investment data of this user
                $spans = $dom->getElementsByTagName('span');
                $this->verifyNodeHasElements($spans);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($spans as $span) {
                    if ($span->getAttribute('class') == 'lb_main_menu_bold') {
                        $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($span->nodeValue);
                        echo $this->tempArray['global']['myWallet'];
                        break; //myWallet is only the first span
                    }
                }

                $divs = $dom->getElementsByTagName('div');
                $this->verifyNodeHasElements($divs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($divs as $div) {
                    if ($div->getAttribute('id') == 'lb_cartera_data_2') {
                        $this->tempArray['global']['activeInvestments'] = trim($div->nodeValue);
                        echo $div->nodeValue;
                    }
                }


                $outstanding = $this->getElements($dom, 'div', 'class', 'lb_textlist_right lb_blue')[0]->nodeValue;
                $this->tempArray['global']['outstandingPrincipal'] = $this->getMonetaryValue($outstanding); //$this->getMonetaryValue($spans[0]->nodeValue);


                print_r($this->tempArray);

                $this->idForSwitch++;
                $url = array_shift($this->urlSequence);
                $dateInit = strtotime($this->dateInit);
                $dateFinish = strtotime($this->dateFinish);
                $url = strtr($url, array('{$date1}' => $dateInit)); //Date in seconds
                $url = strtr($url, array('{$date2}' => $dateFinish));
                $fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->getPFPFileMulticurl($url, false, false, false, $fileName);
                break;
            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 6:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $trs = $dom->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }

                foreach ($trs as $tr) {

                    $as = $dom->getElementsByTagName('a');
                    $this->verifyNodeHasElements($as);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                    }
                    foreach ($as as $a) {
                        if (!empty($a->getAttribute('data-id'))) {
                            $this->UserLoansId[] = $a->getAttribute('data-id');
                            if (!$this->hasElements) {
                                return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                            }
                            break;
                        }
                    }
                }

                $this->UserLoansId = array_unique($this->UserLoansId); //We have duplicate loans because a tag, we use this for delete duplicated loans
                $this->UserLoansId = array_values($this->UserLoansId);


                echo 'Loans id: ';
                print_r($this->UserLoansId);
                $this->maxUserLoans = count($this->UserLoansId);
                $this->idForSwitch++;
                $this->tempUrl['dummy'] = array_shift($this->urlSequence);
                $this->getCompanyWebpageMultiCurl($this->tempUrl['dummy']);
                break;
            case 7:
                if (empty($this->tempUrl['InvesmentUrl'])) {
                    $this->tempUrl['InvesmentUrl'] = array_shift($this->urlSequence);
                }
                $url = $this->tempUrl['InvesmentUrl'] . $this->UserLoansId[$this->j];
                $this->j++;
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($url);
                break;
            case 8:
                //echo $str;
                $this->loanArray[$this->j - 1]['A'] = $this->UserLoansId[$this->j - 1]; //A is loan id

                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $top = $this->getElements($dom, 'div', 'class', 'loantop')[0];
                $divs = $top->getElementsByTagName('div');
                $this->verifyNodeHasElements($divs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($divs as $key => $div) {
                    echo 'Entro ' . $key;
                    //echo $key . " is " . trim($div->nodeValue) . SHELL_ENDOFLINE;
                    switch ($key) {
                        case 7:
                            $str = explode(",", mb_convert_encoding($div->nodeValue, "utf8", "auto"));
                            $this->loanArray[$this->j - 1]['B'] = $str[0]; //Loan Purpose
                            $this->loanArray[$this->j - 1]['C'] = $str[1]; //Loan Price target
                            $this->loanArray[$this->j - 1]['D'] = explode("(", $str[2])[0]; //Loan Location
                            break;
                        case 8:
                            $str = explode(" ", trim($div->nodeValue));
                            $this->loanArray[$this->j - 1]['E'] = $str[0]; //Loan Rating
                            break;
                        case 13:
                            $this->loanArray[$this->j - 1]['F'] = trim($div->nodeValue); //Initial TAE
                            break;
                        case 19:
                            $this->loanArray[$this->j - 1]['G'] = trim($div->nodeValue); //Time left
                    }
                }

                $tables = $dom->getElementsByTagName('table');
                $this->verifyNodeHasElements($tables);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                foreach ($tables as $table) {
                    if ($table->getAttribute("id") == "table-1") {
                        $tds = $table->getElementsByTagName('td');
                        $this->verifyNodeHasElements($tds);
                        if (!$this->hasElements) {
                            return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                        }
                        foreach ($tds as $subkey => $td) {
                            echo $subkey . " is " . trim($td->nodeValue) . SHELL_ENDOFLINE;
                            switch ($subkey) {
                                case 3:
                                    $this->loanArray[$this->j - 1]['H'] = trim($td->nodeValue); //Type
                                    break;
                                /* case 7:
                                  $this->loanArray[$this->j - 1]['H'] = trim($td->nodeValue); //Loan Type
                                  break; */
                                case 9:
                                    $this->loanArray[$this->j - 1]['I'] = trim($td->nodeValue); //Frecuencia pago
                                    break;
                                case 11:
                                    $this->loanArray[$this->j - 1]['J'] = trim($td->nodeValue); //Interes Nominal
                                    break;
                                case 15:
                                    $this->loanArray[$this->j - 1]['K'] = trim($td->nodeValue); //Loan start date
                                    break;
                                case 17:
                                    $str = array_values(array_unique(explode(" ", trim($td->nodeValue))));
                                    print_r($str);
                                    $this->loanArray[$this->j - 1]['K'] = $str[2]; //Duration
                                    break;
                                //case 21 SECTOR
                            }
                        }
                        break;
                    }
                }

                print_r($this->loanArray);
                //$this->loanArray[$this->j - 1]['B'];


                if ($this->j < $this->maxUserLoans) {
                    $this->idForSwitch = 8;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['dummy']);
                    break;
                } else {
                    $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                    $this->saveFilePFP($this->fileName, json_encode($this->loanArray));
                    $this->idForSwitch++;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl['dummy']);
                    break;
                }
            case 9:
                echo 'Stop';
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
    function collectAmortizationTablesParallel($str = null) { //Queue_info example {"loanIds":{"704":["472"]}}
        switch ($this->idForSwitch) {
            case 0:
                /*
                  FIELDS USED BY LOANBOOK DURING LOGIN PROCESS

                  csrf		539d6241ffbb10437f4fe6e27552bfe9
                  password	cede_4040
                  signin		Login
                  username	antoine.de.poorter@gmail.com
                 */
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Go to home page of the company
                break;
            case 1:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  // Click "login" needed so I can read the csrf code
                break;
            case 2:
                $credentials['username'] = $this->user;
                $credentials['password'] = $this->password;
                $credentials['signin'] = "Login";
                $dom = new DOMDocument;
                //echo $str;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $forms = $dom->getElementsByTagName('form');
                /* $this->verifyNodeHasElements($forms);
                  if (!$this->hasElements) {
                  return $this->getError(__LINE__, __FILE__);
                  } */
                $index = 0;
                foreach ($forms as $form) {
                    $index = $index + 1;
                    $inputs = $form->getElementsByTagName('input');
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('name'))) {  // check all hidden input fields, like csrf
                            if ($input->getAttribute('name') == "csrf") {
                                echo "AAAA" . $credentials[$input->getAttribute('name')] . "<br>";
                                $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                                break 2;
                            }
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 3:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                $resultMiLoanbook = false; // Could not login, credential error
                $uls = $dom->getElementsByTagName('ul');
                /* if (!$this->hasElements) {
                  return $this->getError(__LINE__, __FILE__);
                  } */
                foreach ($uls as $ul) {

                    $as = $ul->getElementsByTagName('a');
                    $this->verifyNodeHasElements($as);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    $index = 0;
                    foreach ($as as $a) {
                        if (strcasecmp(trim($a->nodeValue), "RESUMEN") == 0) {
                            $this->mainPortalPage = $str;
                            $resultMiLoanbook = true;
                            break 2;
                        }
                        $index++;
                    }
                }
                if (!$resultMiLoanbook) {   // Error while logging in
                    echo __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN<br>";
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . "ERROR WHILE LOGGING IN\n";
                    $tracings .= "Loanbook login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_LOGIN);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();  //str1 load Webpage into a string variable so it can be parsed	
                break;
            case 4:
                if (empty($this->tempUrl['invesmentUrl'])) {
                    $this->tempUrl['invesmentUrl'] = array_shift($this->urlSequence);
                }
                echo "Loan number " . $this->i . " is " . $this->loanIds[$this->i];
                $url = $this->tempUrl['invesmentUrl'] . $this->loanIds[$this->i];
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
                    if ($table->getAttribute('id') == 'paymentsTable') {
                        $AmortizationTable = new DOMDocument();
                        $clone = $table->cloneNode(TRUE); //Clone the table
                        //Mod the dom clone
                        $is = $clone->getElementsByTagName('i');
                        foreach ($is as $key => $status) {
                            echo "search status";
                            echo $status->getAttribute('class');
                            if ($status->getAttribute('class') == 'fa fa-circle') {
                                echo 'Finded';
                                echo $status->getAttribute('title');
                                $clone->getElementsByTagName('i')->item($key)->nodeValue = $status->getAttribute('title');
                            }
                        }

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
                    break;
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
     * 	@return	boolean	true: 		user has successfully logged in. $this->mainPortalPage contains the entry page of the user portal
     * 					false: 		user could not log in
     * 	
     */
    function companyUserLogin($user = "", $password = "", $options = array()) {
        /*
          FIELDS USED BY LOANBOOK DURING LOGIN PROCESS

          csrf		539d6241ffbb10437f4fe6e27552bfe9
          password	cede_4044
          signin		Login
          username	antoine.de.poorter@gmail.com
         */

        $credentials['username'] = $user;
        $credentials['password'] = $password;
        $credentials['signin'] = "Login";
        $str = $this->getCompanyWebpage();  // Go to home page of the company

        $str = $this->getCompanyWebpage();  // Click "login" needed so I can read the csrf code
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $dom->getElementsByTagName('form');

        $index = 0;
        foreach ($forms as $form) {
            $index = $index + 1;
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('name'))) {  // check all hidden input fields, like csrf
                    if ($input->getAttribute('name') == "csrf") {
                        echo "AAAA" . $credentials[$input->getAttribute('name')] . "<br>";
                        $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                        break 2;
                    }
                }
            }
        }

        $str = $this->doCompanyLogin($credentials);

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $confirm = 0;
        $uls = $dom->getElementsByTagName('ul');
        foreach ($uls as $ul) {

            $as = $ul->getElementsByTagName('a');
            $index = 0;
            foreach ($as as $a) {
                if (strcasecmp(trim($a->nodeValue), "RESUMEN") == 0) {
                    $this->mainPortalPage = $str;
                    return true;
                    break 2;
                }
                $index = $index + 1;
            }
        }
        return false;  // Could not login, credential error
    }

    /**
     *
     * 	Logout of user from to company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout() {

        $str = $this->doCompanyLogout();
        return true;
    }

    /**
     *
     * 	translate the html of loan state to the winvestify normalized state
     * 	@param	string		$str html of loanstate
     * 	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
     * 	
     */
    function getLoanState($actualState) {

        $loanStates = array("Pendiente" => PENDING,
            "ok" => OK,
            "retraso" => PAYMENT_DELAYED,
            "judicial" => DEFAULTED);
        foreach ($loanStates as $key => $state) {
            if ($key == $actualState) {
                return $state;
            }
        }
    }

    /**
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'span'), //Contain text that can change
            array('typeSearch' => 'element', 'tag' => 'i'), //Contain title that can change
            array('typeSearch' => 'element', 'tag' => 'a'), //Contain loan id
            array('typeSearch' => 'element', 'tag' => 'div'), //Contain an id
            array('typeSearch' => 'element', 'tag' => 'button'), //Contain loan id
                ), array('title', 'href', 'contracttypeid', 'style', 'id'));

        $node1 = $this->cleanDom($node1, array(//We only want delete class of the span div, not class of the other tags
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('class'));

        /* $node1 = $this->cleanDomTag($node1, array(
          array('typeSearch' => 'tagElement', 'tag' => 'div', 'attr' => 'class', 'value' => 'highyield2'), //this div only appear in a few investment,
          )); */

        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'span'),
            array('typeSearch' => 'element', 'tag' => 'i'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'button'),
                ), array('title', 'href', 'contracttypeid', 'style', 'id'));


        $node2 = $this->cleanDom($node2, array(//We only want delete class of the span div, not class of the other tags
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('class'));

        /* $node2 = $this->cleanDomTag($node2, array(
          array('typeSearch' => 'tagElement', 'tag' => 'div', 'attr' => 'class', 'value' => 'highyield2'), //this div only appear in a few investment,
          )); */


        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

    /**
     * Function to translate the company specific payment frequency to the Winvestify standardized
     * payment frequency
     * @param string $inputData     company specific payment frequency
     * @return int                  Winvestify standardized payment frequency
     */
    public function translatePaymentFrequency($inputData) {
        $type = WIN_PAYMENTFREQUENCY_UNKNOWN;
        $inputData = strtoupper(trim($inputData));
        switch ($inputData) {
            case "PAGO ÚNICO":
                $type = WIN_PATMENTFREQUENCY_ONEPAYMENT;
                break;
            case "TRIMESTRAL":
                $type = WIN_PAYMENTFREQUENCY_YEAR_CUARTER;
                break;
            case "SEMESTRAL":
                $type = WIN_PAYMENTFREQUENCY_YEAR_SEMESTER;
                break;
            case "PAGOS MÚLTIPLES":
                $type = WIN_PAYMENTFREQUENCY_UNKNOWN;
                break;
        }
        return $type;
    }

    /**
     * Function to translate the company specific loan type to the Winvestify standardized
     * loan type
     * @param string $inputData     company specific loan type
     * @return int                  Winvestify standardized loan type
     */
    public function translateLoanType($inputData) {
        $type = WIN_TYPEOFLOAN_UNKNOWN;
        $inputData = strtoupper($inputData);
        switch ($inputData) {
            case "PRÉSTAMO":
                $type = WIN_TYPEOFLOAN_MORTGAGE;
                break;
            case "PAGARÉ":
                $type = WIN_TYPEOFLOAN_PAGARE;
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
    public function translateAmortizationMethod($inputData) { //We don't have this in loanbook

    }   
    
    /**
     * Function to translate the company specific type of investment to the Winvestify standardized
     * type of investment
     * @param string $inputData     company specific type of investment
     * @return int                  Winvestify standardized type of investment
     */
    public function translateTypeOfInvestment($inputData) { //We don't have this in loanbook

    }
    
    /**
     * Function to translate the type of investment market to an to the Winvestify standardized
     * investment market concept
     * @param string $inputData     company specific investment market concept
     * @return int                  Winvestify standardized investment marke concept
     */
    public function translateInvestmentMarket($inputData) { //We don't have this in loanbook
        
    }
    
    /**
     * Function to translate the company specific investmentBuyBackGuarantee to the Winvestify standardized
     * investmentBuyBackGuarantee
     * @param string $inputData     company specific investmentBuyBackGuarantee
     * @return int                  Winvestify standardized investmentBuyBackGuarantee
     */
    public function translateInvestmentBuyBackGuarantee($inputData) { //we don't have this in loanbook
        
    }

}
