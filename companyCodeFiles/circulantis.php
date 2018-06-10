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
 * function calculateLoanCost()										[not OK, not tested]
 * function collectCompanyMarketplaceData()								[OK, tested]
 * function companyUserLogin()										[OK, tested]
 * function companyUserLogout										[OK, tested]
 * function collectUserInvestmentData()									[OK, tested]
 * parallelization                                                                                         [OK, tested]
 *
 * 2016-11-10	  version 2016_0.1
 * Basic version
 *
 * 2017/05/11
 * OUTSTANDING PRINCIPAL
 * transaction id
 * period of investiment
 *
 * 2017-05-16          version 2017_0.2
 * Added parallelization
 * Dom verification
 * 
 *
 * 2017-05-25
 * There is an array_shift to delete the first url of urlsequence on case 0 of the switch
 * We would need to delete the urlsequence on DB for Circulantis to work
 * 
 * 2017-07-26
 * Urlseuqnces fix marketplace
 * $attr class fix col-xs-12 col-sm-6 col-md-3 col-lg-3 line 113
 * 
 * 2017-08-07
 * collectCompanyMarketplaceData - pagination loop added
 * collectHistorical - added
 * 
 * 2017-10-2017
 * Structure revision added
 * 
 * 2017-11-2017
 * Status definition added
 * Pending:
 *
 *
 *
 *
 */
class circulantis extends p2pCompany {

// CIRCULANTIS
// MOVIMIENTO                                                                   REFERENCIA IMPORTE â‚¬	FECHA	   DISPONIBLE â‚¬   OFERTADO â‚¬    INVERTIDO â‚¬    TOTAL â‚¬
// Traspaso                                                                     H03337	   1,000.00	5/9/2016   1,000.00	          0             0       	1,000.00
// OperaciÃ³n formalizada ID Puja: 180626, ID Subasta: 1893,Mayentis S.L....	F180626     0          7/31/2017    572.18          66.34           15,049.39	     15,687.91
// OperaciÃ³n realizada ID Puja: 154197, ID Subasta: 1637,TradiciÃ³n Alimentaria, S.L....	P154197	100	5/29/2017	2,936.42	300	12,264.55	15,500.97
// OperaciÃ³n cobrada ID Puja: 112205, ID Subasta: 1247,Construcciones y Excavaciones Erri-Berri, S.L....	C112205	159.63	5/30/2017	3,096.05	0	12,409.21	15,505.26
    protected $valuesTransaction = [
         "A" => [
            [
                "type" => "original_concept", // Winvestify standardized name   OK
                "inputData" => [// trick to get the complete cell data as purpose
                    "input2" => "", // May contain trailing spaces
                    "input3" => "ID Puja",
                    "input4" => 1                                   // 'input3' is mandatory. If not found then return "global_xxxxxx"
                ],
                "functionName" => "extractDataFromString",
            ],
            [
                "type" => "investment_sliceId", // Winvestify standardized name   OK
                "inputData" => [// trick to get the complete cell data as purpose
                    "input2" => "ID Puja: ", // May contain trailing spaces
                    "input3" => ",",
                    "input4" => 1                                   // 'input3' is mandatory. If not found then return "global_xxxxxx"
                ],
                "functionName" => "extractDataFromString",
            ],
            [
                "type" => "investment_loanId", // Winvestify standardized name   OK
                "inputData" => [// trick to get the complete cell data as purpose
                    "input2" => "ID Subasta: ", // May contain trailing spaces
                    "input3" => ",",
                    "input4" => 1                                   // 'input3' is mandatory. If not found then return "global_xxxxxx"
                ],
                "functionName" => "extractDataFromString",
            ],
        ],
        "B" => [
            [
                "type" => "transactionDetail", // Winvestify standardized name   OK
                "inputData" => [// List of all concepts that the platform can generate format ["concept string platform", "concept string Winvestify"]
                    "input2" => "#current.original_concept",
                    "input3" => [
                        "Alta en Circulantis" => "Cash_deposit",
                        "Traspaso" => "Cash_deposit",
                        "Traspaso" => "Cash_withdrawal",
                        "OperaciÃ³n realizada" => "Primary_market_investment_preactive",
                        "OperaciÃ³n formalizada" => "Primary_market_investment_active_verification",
                        "OperaciÃ³n cobrada" => "Capital_repayment"
                        //"OperaciÃ³n cobrada parcialmente" => "Capital_repayment",
                    ],
                ],
                "functionName" => "getComplexTransactionDetail",
            ]
        ],
        "C" => [
            [
                "type" => "amount", // This is *mandatory* field which is required for the 
                "inputData" => [// "transactionDetail"
                    "input2" => "", // and which BY DEFAULT is a Winvestify standardized variable name.
                    "input3" => ".", // and its content is the result of the "getAmount" method
                    "input4" => 4
                ],
                "functionName" => "getAmount",
            ],
        ],
        "D" => [
            [
                "type" => "date", // Winvestify standardized name 
                "inputData" => [
                    "input2" => "d/m/Y",
                ],
                "functionName" => "normalizeDate",
            ]
        ],
            /* "E" => "disponible",
              "F" => "ofertado",*/
             // "G" => "invertido",
             /* "H" => "total" */
    ];
    protected $valuesInvestment = [// All types/names will be defined as associative index in array
        [
            "A" => [
                "name" => "investment_debtor"                              // Winvestify standardized name  OK
            ],
            "B" => [
                "name" => "investment_riskRating",
            ],
            "C" => [
                [
                    "type" => "investment_myInvestment", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 4
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "D" => [
                [
                    "type" => "investment_nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "100",
                        "input3" => 2,
                        "input4" => ","
                    ],
                    "functionName" => "handleNumber",
                ]
            ],
            "E" => [
                [
                    "type" => "investment_dueDate", // Winvestify standardized name 
                    "inputData" => [
                        "input2" => "d/m/Y", // Input parameters. The first parameter
                    ],
                    "functionName" => "normalizeDate",
                ],
            ],
            "F" => [
                "name" => "investment_originalState",
            ],
            //"G" => [], ?
            //"H" => ?
            "I" => [
                "name" => "investment_loanId"
            ],
            "J" => [
                "name" => "investment_sliceId",
            ],
            "K" => [
                [
                    "type" => "date", // Winvestify standardized name 
                    "inputData" => [
                        "input2" => "d/m/Y", // Input parameters. The first parameter
                    ],
                    "functionName" => "normalizeDate",
                ],
            ],
        ]
    ];
    protected $valuesExpiredLoan = [// We are only interested in the investment_loanId
        [
            "A" => [
                [
                    "type" => "date", // Winvestify standardized name 
                    "inputData" => [
                        "input2" => "d/m/Y", // Input parameters. The first parameter
                    ],
                    "functionName" => "normalizeDate",
                ],
            ],
            "B" => [
                "name" => "investment_debtor",
            ],
            "C" => [
                "name" => "investment_loanId",
            ],
            "D" => [
                [
                    "type" => "investment_sliceIdentifier", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "1",
                        "input3" => 0,
                        "input4" => ","
                    ],
                    "functionName" => "handleNumber",
                ]
            ],
            "E" => [
                "name" => "investment_riskRating",
            ],
            "F" => [
                [
                    "type" => "investment_myInvestment", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 4
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "G" => [
                [
                    "type" => "investment_nominalInterestRate", // Winvestify standardized name   OK
                    "inputData" => [
                        "input2" => "100",
                        "input3" => 2,
                        "input4" => ","
                    ],
                    "functionName" => "handleNumber",
                ]
            ],
            "H" => [
                "name" => "investment_originalDuration",
            ],
            "I" => [
                "name" => "investment_originalState",
            ],
        ]
    ];
    protected $valuesControlVariables = [
        [
            "myWallet" => [
                [
                    "type" => "myWallet", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "activeInvestments" => [
                [
                    "type" => "activeInvestments", // Winvestify standardized name  OK
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
                    "type" => "outstandingPrincipal", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16
                    ],
                    "functionName" => "getAmount",
                ]
            ],
            "reservedFunds" => [
                [
                    "type" => "reservedFunds", // Winvestify standardized name  OK
                    "inputData" => [
                        "input2" => "",
                        "input3" => ",",
                        "input4" => 16,
                    ],
                    "functionName" => "getAmount",
                ]
            ],
        ]
    ];
    protected $transactionConfigParms = array(
        [
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'separatorChar' => ";",
            'sortParameter' => array("date", "investment_loanId")          // used to "sort" the array and use $sortParameter as prime index.
        ]
    );
    protected $investmentConfigParms = array(
        [
            'offsetStart' => 1,
            'offsetEnd' => 0,
            'separatorChar' => ";",
            'sortParameter' => array("investment_loanId")          // used to "sort" the array and use $sortParameter as prime index.
        ]
    );
    protected $compareHeaderConfigParam = array(
        'separatorChar' => ";",
        'chunkInit' => 1,
        'chunkSize' => 1
    );
    protected $investmentHeader = array(
        'A' => 'NOMBRE EMISOR/DEUDOR',
        'B' => 'RATING',
        'C' => 'IMPORTE â‚¬',
        'D' => 'INTERES %',
        'E' => 'VENCIMIENTO',
        'F' => 'ESTADO',
        'G' => 'FONDOS INVERTIDOS %',
        'H' => 'IMPORTE COBRO â‚¬',
        'I' => 'ID SUBASTA',
        'J' => 'ID PUJA',
        'F' => 'FECHA INICIO SUBASTA',
    );
    protected $transactionHeader = array(
        'A' => 'MOVIMIENTO',
        'B' => 'REFERENCIA',
        'C' => 'IMPORTE â‚¬',
        'D' => 'FECHA',
        'E' => 'DISPONIBLE â‚¬',
        'F' => 'OFERTADO â‚¬',
        'G' => 'INVERTIDO â‚¬',
        'H' => 'TOTAL â‚¬',
    );

    function __construct() {
        $this->typeFileTransaction = "csv";
        $this->typeFileInvestment = "csv";
        $this->typeFileExpiredLoan = "csv";
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how must it will cost in total to obtain a loan for a certain amount
     * 	from a company. This includes fixed fee amortization fee(s) etc.
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in months) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
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
     * @param Array $companyBackup
     * @param Array $structure
     * @return Array
     */
    function collectCompanyMarketplaceData($companyBackup, $structure) {

        $user = "inigo.iturburua@gmail.com";
        $password = "Ap_94!56";

        $resultMicirculantis = $this->companyUserLogin($user, $password);   //We need login to see the status
        echo __FILE__ . " " . __LINE__ . "<br>";

        if (!$resultMicirculantis) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }



        $totalArray = array();

        $page = 1;
        $url = array_shift($this->urlSequence);

        $reading = true;
        $readController = 0;
        $investmentController = false;


        while ($reading) { //Pagination loop */
            $investmentNumber = 0;

            $str = $this->getCompanyWebpage($url . $page);
            //echo $str;
            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;

            $tables = $dom->getElementsByTagName("table"); //Get investment table

            foreach ($tables as $keyTable => $table) {
                $rows = $table->getElementsByTagName("tr"); //Get investment row

                if ($totalArray !== false) { //Structure control 
                    foreach ($rows as $key => $row) {

                        if ($key % 2 == 0) {
                            continue; //Even row are useless
                        }



                        echo 'para entrar ' . $page . ' 0 ' . $key . ' ' . $keyTable . HTML_ENDOFLINE;
                        if ($page == 1 && $key == 1 && $keyTable == 0) { //Compare structures, olny compare the first element
                            $structureRevision = $this->htmlRevision($structure, 'tr', $table);
                            if ($structureRevision[1]) {
                                $totalArray = false; //Stop reading in error    
                                $reading = false;
                                break;
                            }
                        }

                        echo 'Investment:  ' . $key . '<br>';

                        $tempArray['marketplace_country'] = 'ES';

                        $tds = $row->getElementsByTagName("td"); //Get investment data

                        foreach ($tds as $key => $td) {
                            echo $key . ': ' . $td->nodeValue . '<br>';


                            switch ($key) {
                                case 1:
                                    $tempArray['marketplace_name'] = $td->nodeValue;
                                    $tempArray['marketplace_purpose'] = $td->nodeValue;
                                    break;
                                case 4:
                                    $tempArray['marketplace_amount'] = $this->getMonetaryValue($td->nodeValue);
                                    break;
                                case 5:
                                    $tempArray['marketplace_interestRate'] = $this->getPercentage($td->nodeValue);
                                    break;
                                case 6:
                                    $tempArray['marketplace_rating'] = $td->nodeValue;
                                    break;
                                case 8:
                                    $tempArray['marketplace_vencimiento'] = $td->nodeValue;
                                    break;
                                case 9:
                                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($td->nodeValue);
                                    break;
                            }

                            $as = $td->getElementsByTagName("a"); //Get loanId
                            foreach ($as as $key => $a) {
                                echo $key . ' loan Id: ' . $a->getAttribute('href') . '<br>';
                                $loanId = trim(preg_replace('/\D/', ' ', $a->getAttribute('href')));
                                echo $loanId . '<br>';
                                $tempArray['marketplace_loanReference'] = $loanId;
                                echo $a->getAttribute('href');
                                echo 'URL: ' . $a->getAttribute('href') . HTML_ENDOFLINE;
                                $urlInvestment = $a->getAttribute('href');
                            }



                            $buttons = $td->getElementsByTagName("button"); //Get status data
                            foreach ($buttons as $key => $button) {
                                echo $key . ' status: ' . $button->getAttribute('title') . '<br>';

                                switch ($button->getAttribute('title')) {
                                    case 'Abierta':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        break;
                                    case 'Formalizada':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        $tempArray['marketplace_status'] = CONFIRMED;
                                        break;
                                    case 'Finalizada':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        $tempArray['marketplace_status'] = PERCENT;
                                        break;
                                    case 'Atrasada':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                        break;
                                    case 'Cobrada':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                        break;
                                    case 'Cobrada parcialmente':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                        break;
                                    case 'No formalizada':
                                        $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                        $tempArray['marketplace_status'] = REJECTED;
                                }


                                if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                                    foreach ($companyBackup as $inversionBackup) { //If completed investmet with same status in backup
                                        if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_statusLiteral'] == $tempArray['marketplace_statusLiteral']) {
                                            echo 'Already exist';
                                            $readController++;
                                            $investmentController = true;
                                        }
                                    }
                                } else {
                                    //Get time left only if the investment is in progress
                                    $strInvestment = $this->getCompanyWebpage($urlInvestment);
                                    $domInvestment = new DOMDocument;
                                    $domInvestment->loadHTML($strInvestment);
                                    $domInvestment->preserveWhiteSpace = false;
                                    $ps = $domInvestment->getElementsByTagName('p');
                                    foreach ($ps as $keyP => $p) {
                                        //echo $keyP . ': ' . $p->nodeValue . HTML_ENDOFLINE;
                                        if (trim($p->getAttribute('id')) == 'pdiasfinanciados') {
                                            /* $future = strtotime($p->nodeValue); //Future date.
                                              $timefromdb = date();
                                              $timeleft = $future - $timefromdb;
                                              $daysleft = round((($timeleft / 24) / 60) / 60);
                                              $tempArray['marketplace_timeLeft'] = $daysleft; */
                                            $tempArray['marketplace_duration'] = $p->nodeValue;
                                            //$tempArray['marketplace_timeLeftUnit'] = 1;
                                            $tempArray['marketplace_durationUnit'] = 1;
                                            // echo 'days left: ' . $daysleft . HTML_ENDOFLINE;
                                        }
                                    }
                                }
                            }
                        }



                        if ($investmentController) { //Don't save a already existing investment
                            unset($tempArray);
                            $investmentController = false;
                        } else {
                            if ($tempArray) {
                                $this->print_r2($tempArray);
                                $totalArray[] = $tempArray;
                                unset($tempArray);
                            }
                        }
                        $investmentNumber++;
                    }
                }

                $page++; //Advance page
                if ($readController > 12 || $investmentNumber < 15) {
                    echo 'stop reading ' . print_r($investmentNumber) . ' pag: ' . $page;
                    $reading = false;
                } //Stop reading
                break;
            }
        }

        $this->print_r2($totalArray);
        return [$totalArray, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     * collect all investment
     * @param Array $structure
     * @param Int $page
     * @param Int $type
     * @return Array
     */
    function collectHistorical($structure, $pageNumber, $type = null) {

        $user = "inigo.iturburua@gmail.com";
        $password = "Ap_94!56";

        $resultMicirculantis = $this->companyUserLogin($user, $password);   //We need login to see the status
        echo __FILE__ . " " . __LINE__ . "<br>";

        if (!$resultMicirculantis) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }


        $totalArray = array();

        $pageNumber++; //Advance page, first page is 1, we sent 0
        $investmentNumber = 0;
        $url = array_shift($this->urlSequence);


        $str = $this->getCompanyWebpage($url . $pageNumber);
        //echo $str;
        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $tables = $dom->getElementsByTagName("table"); //Get investment table
        $this->verifyNodeHasElements($tables);
        if (!$this->hasElements) {
            return $this->getError(__LINE__, __FILE__);
        }
        foreach ($tables as $keyTable => $table) {
            $rows = $table->getElementsByTagName("tr"); //Get investment row
            $this->verifyNodeHasElements($rows);
            if (!$this->hasElements) {
                return $this->getError(__LINE__, __FILE__);
            }
            if ($totalArray !== false) {
                foreach ($rows as $key => $row) {

                    if ($key % 2 == 0) {
                        continue; //Even row are useless
                    }


                    if ($pageNumber == 1 && $key == 1 && $keyTable == 0) { //Compare structures, olny compare the first element
                        $structureRevision = $this->htmlRevision($structure, 'tr', $table);
                        if ($structureRevision[1]) {
                            $totalArray = false; //Stop reading in error    
                            $pageNumber = false;
                            break;
                        }
                    }

                    echo 'Investment:  ' . $key . '<br>';

                    $tempArray['marketplace_country'] = 'ES';

                    $tds = $row->getElementsByTagName("td"); //Get investment data
                    $this->verifyNodeHasElements($tds);
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
                    foreach ($tds as $key => $td) {
                        echo $key . ': ' . $td->nodeValue . '<br>';


                        switch ($key) {
                            case 1:
                                $tempArray['marketplace_name'] = $td->nodeValue;
                                $tempArray['marketplace_purpose'] = $td->nodeValue;
                                break;
                            case 4:
                                $tempArray['marketplace_amount'] = $this->getMonetaryValue($td->nodeValue);
                                break;
                            case 5:
                                $tempArray['marketplace_interestRate'] = $this->getPercentage($td->nodeValue);
                                break;
                            case 6:
                                $tempArray['marketplace_rating'] = $td->nodeValue;
                                break;
                            case 8:
                                $tempArray['marketplace_vencimiento'] = $td->nodeValue;
                                break;
                            case 9:
                                $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($td->nodeValue);
                                break;
                        }

                        $as = $td->getElementsByTagName("a"); //Get loanId
                        $this->verifyNodeHasElements($as);
                        if (!$this->hasElements) {
                            return $this->getError(__LINE__, __FILE__);
                        }
                        foreach ($as as $key => $a) {
                            echo $key . ' loan Id: ' . $a->getAttribute('href') . '<br>';
                            $loanId = trim(preg_replace('/\D/', ' ', $a->getAttribute('href')));
                            echo $loanId . '<br>';
                            $tempArray['marketplace_loanReference'] = $loanId;
                        }

                        $buttons = $td->getElementsByTagName("button"); //Get status data
                        $this->verifyNodeHasElements($buttons);
                        if (!$this->hasElements) {
                            return $this->getError(__LINE__, __FILE__);
                        }
                        foreach ($buttons as $key => $button) {
                            echo $key . ' status: ' . $button->getAttribute('title') . '<br>';
                            switch ($button->getAttribute('title')) {
                                case 'Abierta':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    break;
                                case 'Formalizada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = CONFIRMED;
                                    break;
                                case 'Finalizada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = PERCENT;
                                    break;
                                case 'Atrasada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                    break;
                                case 'Cobrada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                    break;
                                case 'Cobrada parcialmente':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = BEFORE_CONFIRMED;
                                    break;
                                case 'No formalizada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = REJECTED;
                            }
                        }
                    }
                    $this->print_r2($tempArray);



                    $totalArray[] = $tempArray;
                    unset($tempArray);
                    $investmentNumber++;
                }
            }
            if ($investmentNumber < 15) {
                echo 'stop reading ' . print_r($investmentNumber) . ' pag: ' . $pageNumber;
                $pageNumber = false;
            } //Stop reading
            break; //Only read one table
        }

        $this->print_r2($totalArray);
        return [$totalArray, $pageNumber, null, $structureRevision[0], $structureRevision[2]];
        //$totalarray Contain the pfp investment or is false if we have an error
        //$structureRevision[0] retrurn a new structure if we find an error, return 1 is all is alright
        //$structureRevision[2] return the type of error
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentDataParallel($str) {

        //CHANGE URLSEQUENCES ON DB
        switch ($this->idForSwitch) {
            case 0:
                echo __FILE__ . " " . __LINE__ . "<br>";

                $this->idForSwitch++;
                //We need to delete a urlsequence on DB for Circulantis to work
                array_shift($this->urlSequence);
            //$this->getCompanyWebpage();
            //$resultMicirculantis = $this->companyUserLogin($user, $password);
            //break;
            case 1:
                $credentials = array();
                /**
                 * Change user and password
                 */
                $credentials['user'] = $this->user;
                $credentials['password'] = $this->password;
                $credentials['login'] = 1;
                $credentials['tipo'] = "I";
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 2:
                /* $dom = new DOMDocument;
                  libxml_use_internal_errors(true);
                  $dom->loadHTML($str);
                  $dom->preserveWhiteSpace = false; */
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $divs = $this->getElements($dom, 'div', 'id', 'sub-menu');
                if (empty($divs)) {
                    return $this->getError(__LINE__, __FILE__);
                }
                /*
                 * MAKE COMPROBATION
                  if (empty($divs)) {
                  return 0;
                  }
                 */
                $lis = $this->getElements($divs[0], 'li');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $resultMicirculantis = false;
                if ($lis[0]->nodeValue === "Mis datos") {   // JSON response with wallet value
                    $resultMicirculantis = true;
                }

                if (!$resultMicirculantis) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }

                // Load page  panel-inversor
                array_shift($this->urlSequence);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                //echo "INVERSOR_PANEL" . $str;	
                break;
            case 4:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                //echo $str . __LINE__;
                $dom->preserveWhiteSpace = false;

                //error_reporting(2);
                // Get information about each individual transaction
                $numberOfInvestments = 0;

                $rows = $this->getElements($dom, "div", "class", "row");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                // get all current investments
                echo __FILE__ . " " . __LINE__ . "<br>";

                $trs = $this->getElements($rows[3], "tr");   // operaciones vigentes
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($trs as $keyTemp => $tr) {
                    if ($keyTemp == 0) {
                        continue;       // don't treat the table header	
                    }
                    $key = $keyTemp - 1;
                    //echo __FILE__ . " " . __LINE__ . "<br>";
                    $numberOfInvestments++;
                    $tds = $this->getElements($tr, "td");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
//Duration. The unit (=dÃ­as) is hardcoded
                    $data1[$key]['loanId'] = trim($tds[1]->nodeValue); //trim($tds[2]->nodeValue);
                    $data1[$key]['date'] = trim($tds[6]->nodeValue);

                    $now = time();
                    $date = trim($tds[6]->nodeValue);
                    $dateAux = explode("/", $date);
                    $date = strtotime($dateAux[2] . "/" . $dateAux[1] . "/" . $dateAux[0]);
                    $duration = $date - $now;
                    //echo "<h1>" . $date . " " . $now . "</h1>";

                    $data1[$key]['duration'] = floor($duration / (60 * 60 * 24)) + 1 . __(" dias"); //trim($tds[1]->nodeValue);
                    $data1[$key]['invested'] = $this->getMonetaryValue($tds[4]->nodeValue);
                    $data1[$key]['commission'] = 0;
                    $data1[$key]['interest'] = $this->getPercentage($tds[5]->nodeValue);
                    $mainIndex = -1;

// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED			
                    $data1[$key]['status'] = OK;
                    $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + ($data1[$key]['invested'] /* - $data1[$key]['amortized'] */);
                    $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
                    $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
                }


                $trs = $this->getElements($rows[4], "tr");  // Operaciones con incidencias
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                foreach ($trs as $key => $tr) {
                    if ($key == 0) {
                        continue;       // don't treat the table header	
                    }

                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $numberOfInvestments++;
                    $tds = $this->getElements($tr, "td");
                    if (!$this->hasElements) {
                        return $this->getError(__LINE__, __FILE__);
                    }
//Duration. The unit (=días) is hardcoded
                    $data1[$key]['loanId'] = trim($tds[2]->nodeValue);
                    $data1[$key]['date'] = trim($tds[6]->nodeValue);
                    $data1[$key]['duration'] = trim($tds[1]->nodeValue);
                    $data1[$key]['invested'] = $this->getMonetaryValue($tds[4]->nodeValue);
                    $data1[$key]['commission'] = 0;
                    $data1[$key]['interest'] = $this->getPercentage($tds[5]->nodeValue);
                    $mainIndex = -1;
// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED	
                    $data1[$key]['status'] = PAYMENT_DELAYED;

                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
                    $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
                    echo __FILE__ . " " . __LINE__ . "<br>";
                }

// Get global data, like "fondos disponible"
                $tables = $this->getElements($rows[2], "table");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $tds = $this->getElements($tables[2], "td");
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $tempArray['global']['myWallet'] = $this->getMonetaryValue($tds[1]->nodeValue);

                echo __FILE__ . " " . __LINE__ . "<br>";

// Get global data, like profitability
                $divs = $this->getElements($rows[6], "class", "total_fondos");
                foreach ($divs as $key => $div) {         // get mean profit value divs[0]->nodeValue
                    // get Rentabilidad
                    echo "key = $key and " . $div->nodeValue . "<br>";
                    echo __FILE__ . " " . __LINE__ . "<br>";
                }
                $prof = $this->getElements($dom, "div", "class", "col-lg-2 total_fondos");
                $tempArray['global']['profitibility'] = $this->getPercentage($prof[0]->nodeValue);
                $tempArray['global']['investments'] = $numberOfInvestments;
                $tempArray['investments'] = $data1;
                $this->print_r2($tempArray);
                return $tempArray;
        }
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($user, $password) {
        echo __FILE__ . " " . __LINE__ . "<br>";
        $resultMicirculantis = $this->companyUserLogin($user, $password);
        echo __FILE__ . " " . __LINE__ . "<br>";

        if (!$resultMicirculantis) {   // Error while logging in
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        echo __FILE__ . " " . __LINE__ . "<br>";
// Load page  panel-inversor
        $str = $this->getCompanyWebpage();
//	echo "INVERSOR_PANEL" . $str;	

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        error_reporting(2);
// Get information about each individual transaction
        $numberOfInvestments = 0;

        $rows = $this->getElements($dom, "div", "class", "row");
        // get all current investments
        echo __FILE__ . " " . __LINE__ . "<br>";

        $trs = $this->getElements($rows[3], "tr");   // operaciones vigentes
        foreach ($trs as $keyTemp => $tr) {
            if ($keyTemp == 0) {
                continue;       // don't treat the table header	
            }
            $key = $keyTemp - 1;
            echo __FILE__ . " " . __LINE__ . "<br>";
            $numberOfInvestments = $numberOfInvestments + 1;
            $tds = $this->getElements($tr, "td");
            echo __FILE__ . " " . __LINE__ . "<br>";

//Duration. The unit (=días) is hardcoded
            $data1[$key]['loanId'] = trim($tds[1]->nodeValue); //trim($tds[2]->nodeValue);
            $data1[$key]['date'] = trim($tds[6]->nodeValue);

            $now = time();
            $date = trim($tds[6]->nodeValue);
            $dateAux = explode("/", $date);
            $date = strtotime($dateAux[2] . "/" . $dateAux[1] . "/" . $dateAux[0]);
            $duration = $date - $now;
            echo "<h1>" . $date . " " . $now . "</h1>";

            $data1[$key]['duration'] = floor($duration / (60 * 60 * 24)) + 1 . __(" dias"); //trim($tds[1]->nodeValue);
            $data1[$key]['invested'] = $this->getMonetaryValue($tds[4]->nodeValue);
            $data1[$key]['commission'] = 0;
            $data1[$key]['interest'] = $this->getPercentage($tds[5]->nodeValue);
            echo __FILE__ . " " . __LINE__ . "<br>";
// Get amortization table. first get base URL for amortization table
//		$baseUrl = array_shift($this->urlSequence);
//		$as = $tds[0]->getElementsByTagName('a');		 // only 1 will be found
//		$dataId =  $as[0]->getAttribute("data-id");
// Deal with the amortization table
//		$strAmortizationTable = $this->getCompanyWebpage($baseUrl . "/" .$dataId);
//		$domAmortizationTable = new DOMDocument;
//	 	$domAmortizationTable->loadHTML($strAmortizationTable);	
//		$domAmortizationTable->preserveWhiteSpace = false;		
//		$amortizationData = $this->getElements($domAmortizationTable, "tr", "class", "detail");	// only 1 found
// Convert into table
            $mainIndex = -1;
            /*
              foreach ($amortizationData as $key1 => $trAmortizationTable ) {
              $mainIndex = $mainIndex + 1;
              $subIndex = -1;
              $tdsAmortizationTable  = $trAmortizationTable ->getElementsByTagName('td');
              foreach( $tdsAmortizationTable  as $tdAmortizationTable ) {
              $subIndex = $subIndex + 1;
              $amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
              }
              }

              $data1[$key]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable,
              date("Y-m-d"),
              "dd-mm-yyyy",
              1, 3);
              $data1[$key]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable,
              date("Y-m-d"),
              "dd-mm-yyyy",
              1, 4);
             */
// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED			
            $data1[$key]['status'] = OK;
            $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + ($data1[$key]['invested'] /* - $data1[$key]['amortized'] */);
            $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
            $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];
        }


        $trs = $this->getElements($rows[4], "tr");  // Operaciones con incidencias
        foreach ($trs as $key => $tr) {
            if ($key == 0) {
                continue;       // don't treat the table header	
            }

            echo __FILE__ . " " . __LINE__ . "<br>";
            $numberOfInvestments = $numberOfInvestments + 1;
            $tds = $this->getElements($tr, "td");

//Duration. The unit (=días) is hardcoded
            $data1[$key]['loanId'] = trim($tds[2]->nodeValue);
            $data1[$key]['date'] = trim($tds[6]->nodeValue);
            $data1[$key]['duration'] = trim($tds[1]->nodeValue);
            $data1[$key]['invested'] = $this->getMonetaryValue($tds[4]->nodeValue);
            $data1[$key]['commission'] = 0;
            $data1[$key]['interest'] = $this->getPercentage($tds[5]->nodeValue);

// Get amortization table. first get base URL for amortization table
//		$baseUrl = array_shift($this->urlSequence);
//		$as = $tds[0]->getElementsByTagName('a');		 // only 1 will be found
//		$dataId =  $as[0]->getAttribute("data-id");
// Deal with the amortization table
//		$strAmortizationTable = $this->getCompanyWebpage($baseUrl . "/" .$dataId);
//		$domAmortizationTable = new DOMDocument;
//	 	$domAmortizationTable->loadHTML($strAmortizationTable);	
//		$domAmortizationTable->preserveWhiteSpace = false;		
//		$amortizationData = $this->getElements($domAmortizationTable, "tr", "class", "detail");	// only 1 found
// Convert into table
            $mainIndex = -1;
            /*
              foreach ($amortizationData as $key1 => $trAmortizationTable ) {
              $mainIndex = $mainIndex + 1;
              $subIndex = -1;
              $tdsAmortizationTable  = $trAmortizationTable ->getElementsByTagName('td');
              foreach( $tdsAmortizationTable  as $tdAmortizationTable ) {
              $subIndex = $subIndex + 1;
              $amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
              }
              }
              echo __FILE__ . " " . __LINE__ . "<br>";
              $data1[$key]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable,
              date("Y-m-d"),
              "dd-mm-yyyy",
              1, 3);
              $data1[$key]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable,
              date("Y-m-d"),
              "dd-mm-yyyy",
              1, 4);
             */
// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED	
            $data1[$key]['status'] = PAYMENT_DELAYED;

            echo __FILE__ . " " . __LINE__ . "<br>";
            $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
            $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];

            echo __FILE__ . " " . __LINE__ . "<br>";
        }

// Get global data, like "fondos disponible"
        $tables = $this->getElements($rows[2], "table");
        foreach ($tables as $key => $table) {         // get mean profit value divs[0]->nodeValue
        }
        $tds = $this->getElements($tables[2], "td");
        $tempArray['global']['myWallet'] = $this->getMonetaryValue($tds[1]->nodeValue);

        echo __FILE__ . " " . __LINE__ . "<br>";

// Get global data, like profitability
        $divs = $this->getElements($rows[6], "class", "total_fondos");
        foreach ($divs as $key => $div) {         // get mean profit value divs[0]->nodeValue
            // get Rentabilidad
            echo "key = $key and " . $div->nodeValue . "<br>";
            echo __FILE__ . " " . __LINE__ . "<br>";
        }
        $prof = $this->getElements($dom, "div", "class", "col-lg-2 total_fondos");
        $tempArray['global']['profitibility'] = $this->getPercentage($prof[0]->nodeValue);
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
                echo __FILE__ . " " . __LINE__ . "<br>";

                $this->idForSwitch++;
                //We need to delete a urlsequence on DB for Circulantis to work
                array_shift($this->urlSequence);
            //$this->getCompanyWebpage();
            //$resultMicirculantis = $this->companyUserLogin($user, $password);
            //break;
            case 1:
                $credentials = array();
                /**
                 * Change user and password
                 */
                $credentials['user'] = $this->user;
                $credentials['password'] = $this->password;
                $credentials['login'] = 1;
                $credentials['tipo'] = "I";
                $this->idForSwitch++;
                $this->doCompanyLoginMultiCurl($credentials);
                break;
            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                break;
            case 3:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $divs = $this->getElements($dom, 'div', 'id', 'sub-menu');
                if (empty($divs)) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $lis = $this->getElements($divs[0], 'li');
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }
                $resultMicirculantis = false;
                if ($lis[0]->nodeValue === "Mis datos") {   // JSON response with wallet value
                    $resultMicirculantis = true;
                }

                if (!$resultMicirculantis) {   // Error while logging in
                    $tracings = "Tracing:\n";
                    $tracings .= __FILE__ . " " . __LINE__ . " \n";
                    $tracings .= "userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
                    $tracings .= " \n";
                    $msg = "Error while logging in user's portal. Wrong userid/password \n";
                    $msg = $msg . $tracings . " \n";
                    $this->logToFile("Warning", $msg);
                    return $this->getError(__LINE__, __FILE__);
                }

                // Load page  panel-inversor
                array_shift($this->urlSequence);
                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl();
                //echo "INVERSOR_PANEL" . $str;	
                break;
            case 4:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;

                //Control varibles
                $tables = $this->getElements($dom, 'table', 'class', 'table table-striped');
                $this->verifyNodeHasElements($tables);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $trs = $tables[0]->getElementsByTagName('tr');
                $this->verifyNodeHasElements($trs);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                //Table doesnt have id
                $loanReferenceId = array();
                $tempArray["global"]["activeInvestment"] = 0;
                foreach ($trs as $key => $tr) {
                    if ($key == 0) {
                        continue;
                    }
                    $idTd = $tr->getElementsByTagName('td')[1];
                    $loanReferenceId[] = $idTd->nodeValue;
                    $tempArray["global"]["activeInvestment"] ++;
                }

                $fondos = $this->getElements($dom, 'ul', 'class', 'distribucion_fondos');
                $this->verifyNodeHasElements($fondos);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_STRUCTURE);
                }
                $li1 = $fondos[0]->getElementsByTagName('li');
                $li2 = $this->getElements($fondos[1], 'li', 'class', 'fondos_fi');
                $tempArray["global"]["outstandingPrincipal"] = $li2[0]->nodeValue;
                $tempArray["global"]["myWallet"] = $li1[2]->nodeValue;
                $tempArray["global"]["reservedFunds"] = $li1[1]->nodeValue;

                //Investment finished doesnt have,  we cant get it.
                $trs = $tables[3]->getElementsByTagName('tr');
                foreach ($trs as $key => $tr) {
                    if ($key == 0) {
                        $finishedInvestment[$key]['A'] = "investment_dueDate ";
                        $finishedInvestment[$key]['B'] = "investment_debtor";
                        $finishedInvestment[$key]['C'] = "investment_loanId";
                        $finishedInvestment[$key]['D'] = "investment_sliceIdentifier";
                        $finishedInvestment[$key]['E'] = "investment_riskRating";
                        $finishedInvestment[$key]['F'] = "investment_myInvestment";
                        $finishedInvestment[$key]['G'] = "investment_nominalInterestRate";
                        $finishedInvestment[$key]['H'] = "investment_originalDuration";
                        $finishedInvestment[$key]['I'] = "investment_originalState";
                        //$finishedInvestment[$key]['J'] = ?
                        continue;
                    }
                    $td = $tr->getElementsByTagName('td');
                    $finishedInvestment[$key]['A'] = $td[0]->nodeValue;
                    $finishedInvestment[$key]['B'] = $td[1]->nodeValue;
                    $finishedInvestment[$key]['C'] = $td[2]->nodeValue;
                    $finishedInvestment[$key]['D'] = $td[3]->nodeValue;
                    $finishedInvestment[$key]['E'] = $td[4]->nodeValue;
                    $finishedInvestment[$key]['F'] = $td[5]->nodeValue;
                    $finishedInvestment[$key]['G'] = $td[6]->nodeValue;
                    $finishedInvestment[$key]['H'] = $td[7]->nodeValue;
                    $finishedInvestment[$key]['I'] = $td[8]->nodeValue;
                    //$finishedInvestment[$key]['J'] = $td[9]->nodeValue;
                }

                $this->fileName = $this->$nameFileExpiredLoan . $this->numFileExpiredLoan . "." . $this->typeFileExpiredLoan;
                $this->saveFilePFP($this->fileName, json_encode($finishedInvestment));

                $dayStart = 1;
                $monthStart = 1;
                $yearStart = 2013;
                $dayFinish = date('d');
                $monthFinish = date('m');
                $yearFinish = date('Y');

                //Investment
                $this->downloadUrl = array_shift($this->urlSequence);
                $this->investmentCredentials = array_shift($this->urlSequence);
                $this->transactionCredentials = array_shift($this->urlSequence);
                $this->headers = array_shift($this->urlSequence);
                $this->transactionCredentials2 = array("filtrofechas" => 1,
                    "desde_d" => $dayStart,
                    "desde_m" => $monthStart,
                    "desde_y" => $yearStart,
                    "hasta_d" => $dayFinish,
                    "hasta_m" => $monthFinish,
                    "hasta_y" => $yearFinish);
                $this->fileName = $this->nameFileInvestment . $this->numFileInvestment . "." . $this->typeFileInvestment;
                $this->headerComparation = $this->investmentHeader;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($this->downloadUrl, $this->downloadUrl, $this->investmentCredentials, $this->headers, $this->fileName);
                break;
            case 5:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if (mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain") {  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }

                $this->idForSwitch++;
                $this->getCompanyWebpageMultiCurl($this->downloadUrl, $this->transactionCredentials2);
                break;
            case 6:
                $this->fileName = $this->nameFileTransaction . $this->numFileTransaction . "." . $this->typeFileTransaction;
                $this->headerComparation = $this->transactionHeader;
                $this->idForSwitch++;
                $this->getPFPFileMulticurl($this->downloadUrl, $this->downloadUrl, $this->transactionCredentials, $this->headers, $this->fileName);
                break;
            case 7:
                if (!$this->verifyFileIsCorrect()) {
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_WRITING_FILE);
                }
                if (mime_content_type($this->getFolderPFPFile() . DS . $this->fileName) !== "text/plain") {  //Compare mine type for finanzarel files
                    echo 'mine type incorrect: ';
                    echo mime_content_type($this->getFolderPFPFile() . DS . $this->fileName);
                    return $this->getError(__LINE__, __FILE__, WIN_ERROR_FLOW_MIME_TYPE);
                }
                $headerError = $this->compareHeader();
                if ($headerError === WIN_ERROR_FLOW_NEW_MIDDLE_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                } else if ($headerError === WIN_ERROR_FLOW_NEW_FINAL_HEADER) {
                    return $this->getError(__LINE__, __FILE__, $headerError);
                }
                return $tempArray["global"];
        }
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
    function companyUserLogin($user, $password) {

        $str = $this->getCompanyWebpage();  // load main page as default starting page

        $credentials = array();
        $credentials['user'] = $user;
        $credentials['password'] = $password;
        $credentials['login'] = 1;
        $credentials['tipo'] = "I";
        $str = $this->doCompanyLogin($credentials);

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $str = $this->getCompanyWebpage();   // We've obtained the main page of the user portal, i.e. user has logged in.
        // check for words "Mis datos"

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $divs = $this->getElements($dom, 'div', 'id', 'sub-menu');
        if (empty($divs)) {
            return 0;
        }

        $lis = $this->getElements($divs[0], 'li');

        if ($lis[0]->nodeValue === "Mis datos") {
            $str = $this->getCompanyWebpage();   // JSON response with wallet value
            return 1;
        } else {
            return 0;         // not authenticated or similar error
        }
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
     * Dom clean for structure revision
     * @param Dom $node1
     * @param Dom $node2
     * @return boolean
     */
    function structureRevision($node1, $node2) {

        $node1->removeAttribute('class'); //This class is the loan type, can change
        $node2->removeAttribute('class');

        $node1 = $this->cleanDom($node1, array(
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'input'),
            array('typeSearch' => 'element', 'tag' => 'button'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('style', 'href', 'aria-valuenow', 'rel', 'id', 'title', 'value'));

        $node1 = $this->cleanDom($node1, array(//We only want delete the class of td and tr, no other classes
            array('typeSearch' => 'element', 'tag' => 'td'),
                ), array('class'));

        $node1 = $this->cleanDomTag($node1, array(
            array('typeSearch' => 'tagElement', 'tag' => 'input', 'attr' => 'class', 'value' => 'time-for2'),
            array('typeSearch' => 'tagElement', 'tag' => 'div', 'attr' => 'class', 'value' => 'timefor2'),
        ));



        $node2 = $this->cleanDom($node2, array(
            array('typeSearch' => 'element', 'tag' => 'div'),
            array('typeSearch' => 'element', 'tag' => 'a'),
            array('typeSearch' => 'element', 'tag' => 'input'),
            array('typeSearch' => 'element', 'tag' => 'button'),
            array('typeSearch' => 'element', 'tag' => 'span'),
                ), array('style', 'href', 'aria-valuenow', 'rel', 'id', 'title', 'value'));

        $node2 = $this->cleanDom($node2, array(//We only want delete the class of td and tr, no other classes
            array('typeSearch' => 'element', 'tag' => 'td'),
                ), array('class'));

        $node2 = $this->cleanDomTag($node2, array(
            array('typeSearch' => 'tagElement', 'tag' => 'input', 'attr' => 'class', 'value' => 'time-for2'),
            array('typeSearch' => 'tagElement', 'tag' => 'div', 'attr' => 'class', 'value' => 'timefor2'),
        ));


        $structureRevision = $this->verifyDomStructure($node1, $node2);
        return $structureRevision;
    }

}
?> 
