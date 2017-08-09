<?php

/*
 * +-----------------------------------------------------------------------+
 * | Copyright (C) 2016, http://beyond-language-skills.com                 |
 * +-----------------------------------------------------------------------+
 * | This file is free software; you can redistribute it and/or modify     |
 * | it under the terms of the GNU General Public License as published by  |
 * | the Free Software Foundation; either version 2 of the License, or     |
 * | (at your option) any later version.                                   |
 * | This file is distributed in the hope that it will be useful           |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
 * | GNU General Public License for more details.                          |
 * +-----------------------------------------------------------------------+
 * | Author: Antoine de Poorter                                            |
 * +-----------------------------------------------------------------------+
 *
 *
 * Contains all the code required for accessing the website of "Circulantis"
 *
 * 
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-11-10
 * @package

  function calculateLoanCost()										[not OK, not tested]
  function collectCompanyMarketplaceData()								[OK, tested]
  function companyUserLogin()										[OK, tested]
  function companyUserLogout										[OK, tested]
  function collectUserInvestmentData()									[OK, tested]
  parallelization                                                                                         [OK, tested]

  2016-11-10	  version 2016_0.1
  Basic version

  2017/05/11
 * OUTSTANDING PRINCIPAL
 * transaction id
 * period of investiment

  2017-05-16          version 2017_0.2
 * Added parallelization
 * Dom verification
 * 

  2017-05-25
 * There is an array_shift to delete the first url of urlsequence on case 0 of the switch
 * We would need to delete the urlsequence on DB for Circulantis to work
 * 
  2017-07-26
 * Urlseuqnces fix marketplace
 * $attr class fix col-xs-12 col-sm-6 col-md-3 col-lg-3 line 113
 * 
 * 2017-08-07
 * collectCompanyMarketplaceData - pagination loop added
 * collectHistorical - added
  Pending:




 */

class circulantis extends p2pCompany {

    function __construct() {
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
     * 	Collects the marketplace data.
     * @param type $companyBackup
     * @return string
     */
    function collectCompanyMarketplaceData($companyBackup) {

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

            foreach ($tables as $table) {
                $rows = $table->getElementsByTagName("tr"); //Get investment row

                foreach ($rows as $key => $row) {

                    if ($key % 2 == 0) {
                        continue; //Even row are useless
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
                                    $tempArray['marketplace_status'] = 2;
                                    break;
                                case 'Finalizada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = 1;
                                    break;
                                case 'Atrasada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = 2;
                                    break;
                                case 'Cobrada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = 2;
                                    break;
                                case 'Cobrada parcialmente':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = 2;
                                    break;
                                case 'No formalizada':
                                    $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                    $tempArray['marketplace_status'] = 3;
                            }


                            if ($tempArray['marketplace_subscriptionProgress'] == 10000) {
                                foreach ($companyBackup as $inversionBackup) { //If completed investmet with same status in backup
                                    if ($tempArray['marketplace_loanReference'] == $inversionBackup['Marketplacebackup']['marketplace_loanReference'] && $inversionBackup['Marketplacebackup']['marketplace_statusLiteral'] == $tempArray['marketplace_statusLiteral']) {
                                        echo 'already exist';
                                        $readController++;
                                        $investmentController = true;
                                    }
                                }
                            }
                        }
                    }
                    $this->print_r2($tempArray);


                    if ($investmentController) { //Don't save a already existing investment
                        unset($tempArray);
                        $investmentController = false;
                    } else {
                        if ($tempArray) {
                            $totalArray[] = $tempArray;
                            unset($tempArray);
                        }
                    }
                    $investmentNumber++;
                }

                $page++; //Advance page
                if ($readController > 2 || $investmentNumber < 15) {
                    echo 'stop reading ' . print_r($investmentNumber) . ' pag: ' . $page;
                    $reading = false;
                } //Stop reading
                break;
            }
        }

        $this->print_r2($totalArray);
        return $totalArray;
    }

    /**
     * Collect historival
     * @param boolean $pageNumber
     * @return type
     */
    function collectHistorical($pageNumber) {

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

        foreach ($tables as $table) {
            $rows = $table->getElementsByTagName("tr"); //Get investment row

            foreach ($rows as $key => $row) {

                if ($key % 2 == 0) {
                    continue; //Even row are useless
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
                                $tempArray['marketplace_status'] = 2;
                                break;
                            case 'Finalizada':
                                $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                $tempArray['marketplace_status'] = 1;
                                break;
                            case 'Atrasada':
                                $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                $tempArray['marketplace_status'] = 2;
                                break;
                            case 'Cobrada':
                                $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                $tempArray['marketplace_status'] = 2;
                                break;
                            case 'Cobrada parcialmente':
                                $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                $tempArray['marketplace_status'] = 2;
                                break;
                            case 'No formalizada':
                                $tempArray['marketplace_statusLiteral'] = $button->getAttribute('title');
                                $tempArray['marketplace_status'] = 3;
                        }
                    }
                }
                $this->print_r2($tempArray);



                $totalArray[] = $tempArray;
                unset($tempArray);
                $investmentNumber++;
            }
            if ($investmentNumber < 15) {
                echo 'stop reading ' . print_r($investmentNumber) . ' pag: ' . $pageNumber;
                $pageNumber = false;
            } //Stop reading
            break; //Only read one table
        }

        $this->print_r2($totalArray);
        return [$totalArray, $pageNumber];
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
//$user = "inigo.iturburua@gmail.com";
//$password = "Ap_94!56";
// manoloherrero@msn.com  Mecano1980

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

}

?> 