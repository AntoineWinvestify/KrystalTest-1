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
 * Contains the code required for accessing the website of "Comunitae"
 *
 * 
 * @author Antoine de Poorter
 * @version 0.1
 * @date 2016-11-05
 * @package


  2016-11-05	  version 2016_0.1
  Basic version
  function calculateLoanCost()											[Not OK]
  function collectCompanyMarketplaceData()								[OK, tested]
  function companyUserLogin()												[OK, tested]
  function collectUserInvestmentData()									[OK, tested]
  function companyUserLogout()											[Not yet OK]


  2017-3-27
  line 251 - changed parameter for url.

  2017-3-18
  Captured exceptions for table errors.


  2017-4-18
  Login error fixed always forcing login
 * They forced logout after a login
  PENDING:
  logout procedure

 */

class arboribus extends p2pCompany {

    function __construct() {
        parent::__construct();
// Do whatever is needed for this subsclass
    }

    /**
     *
     * 	Calculates how much it will cost in total to obtain a loan for a certain amount
     * 	from a company
     * 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
     * 	@param	int $duration		: The amortization period (in month) of the loan
     * 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
     * 	@return int					: Total cost (in Eurocents) of the loan
     *
     */
    function calculateLoanCost($amount, $duration, $interestRate) {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked: 26-08-2016

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
     *
     * 	Collects the marketplace data
     * 	@return array	Each investment option as an element of an array
     * 	
     */
    function collectCompanyMarketplaceData() {
        $totalArray = array();

        $str = $this->getCompanyWebpage();  // load Webpage into a string variable so it can be parsed

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $tables = $dom->getElementsByTagName('table');
        foreach ($tables as $table) {   // only deal with FIRST table in document
            $trs = $table->getElementsByTagName('tr');
            foreach ($trs as $tr) {
                $tds = $tr->getElementsByTagName('td');
                $index = -1;
                $tempArray = array();
                foreach ($tds as $td) {
                    $index = $index + 1;

                    switch ($index) {
                        case 1:
                            $tempArray['marketplace_loanReference'] = $td->nodeValue;
                            break;
                        case 2:
                            $innerIndex = 0;
                            $as = $td->getElementsByTagName('a');

                            foreach ($as as $a) {  // only 1 will be found
                                $tempArray['marketplace_purpose'] = trim($a->nodeValue);
                            }
                            $tempArray['marketplace_requestorLocation'] = trim(str_replace($tempArray['marketplace_purpose'], "", $td->nodeValue));
                            break;
                        case 3:
                            $tempArray['marketplace_rating'] = $td->nodeValue;
                            break;
                        case 4:
                            $tempArray['marketplace_amount'] = $this->getMonetaryValue($td->nodeValue);
                            break;
                        case 6:
                            $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($td->nodeValue));
                            break;
                        case 7:
                            $divs = $td->getElementsByTagName('div');
                            $innerIndex = 0;
                            foreach ($divs as $div) {

                                if ($innerIndex == 1) {
                                    $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage(trim($div->nodeValue));
                                }
                                $innerIndex = $innerIndex + 1;
                            }
                            break;
                        case 8:
                            list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($td->nodeValue);
                            break;
                    }
                }

                $this->print_r2($tempArray);
                if ($tempArray['marketplace_subscriptionProgress'] <> 10000) {
                    $totalArray[] = $tempArray;
                }
                unset($tempArray);
            }
            break;
        }
        return $totalArray;
    }
    
    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentData($str) {

        switch ($this->idForSwitch) {
            /////////////LOGIN
            case 0:
                $this->idForSwitch++;
                $this->getCompanyWebpage();
                //$resultMyArboribus = $this->companyUserLogin($user, $password);
                break;
            case 1:
                $dom = new DOMDocument;
                libxml_use_internal_errors(true);
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $this->credentials['username'] = $this->user;
                $this->credentials['password'] = $this->password;

                $forms = $dom->getElementsByTagName('form');

                foreach ($forms as $form) {
                    $inputs = $form->getElementsByTagName('input');
                    foreach ($inputs as $input) {
                        if (!empty($input->getAttribute('name'))) {  // look for the post variables
                            if ($input->getAttribute('type') == "hidden") {
                                $this->credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                            }
                        }
                    }
                }
                $this->idForSwitch++;
                $this->doCompanyLogin($this->credentials);
                break;
            case 2:
                $this->idForSwitch++;
                $this->getCompanyWebpage();
                break;
            case 3:
                $dom = new DOMDocument;
                $dom->loadHTML($str);
                $dom->preserveWhiteSpace = false;
                $as = $dom->getElementsByTagName('a');
                foreach ($as as $a) {

                    if (strcasecmp(trim($a->nodeValue), "RESUMEN") == 0) {  // Login confirmed
                        $this->mainPortalPage = $str;
                        if ($this->numberOfTries == 1) {         // First try successfull, so flush some entries
                            $dummy = array_shift($this->urlSequence);
                            $dummy = array_shift($this->urlSequence);
                        }
                        //Cambiar true por idForSwitch
                        $this->idForSwitch++;
                        $resultMyArboribus = true;
                    } else {
                        $this->numberOfTries++;
                        $resultMyArboribus = false;
                    }
                }
                collectUserInvestmentLogin($resultMyArboribus);
                break;
            case 4:
                echo $strListInvestments = $str;
                echo $strListInvestments;
                echo __FILE__ . " " . __LINE__ . "<br>";

                $divs = $this->getElements($dom, "div", "class", "arb_detail_right");
                echo __FILE__ . " " . __LINE__ . "<br>";
                echo "ANTOINE";
                foreach ($divs as $key => $div) {
                    echo "key = $key, " . $div->nodeValue . "<br>";
                }
                echo "DE POORTER";
                $trs = $this->getElements($divs[1], "td", "class", "tcell-align-right");
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->tempArray['global']['myWallet'] = $this->getMonetaryValue($trs[1]->nodeValue);
                echo __FILE__ . " " . __LINE__ . "<br>";
                $h3s = $this->getElements($dom, "h3", "style", "font-size:xx-large;");
                $this->tempArray['global']['profitibility'] = $this->getPercentage($h3s[0]->nodeValue);
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->getCompanyWebpage();     // list of investments as JSON
                break;
            case 5:
                $str1 = $str;
                echo $str1;
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->getCompanyWebpage();
                break;
            case 6:
                $str2 = $str;
                echo __FILE__ . " " . __LINE__ . "<br>";
                echo $str2;
                $this->print_r2($str2);
                $investmentListItems = json_decode($this->strListInvestments, true);
                echo __FILE__ . " " . __LINE__ . "<br>";
                $this->print_r2($investmentListItems);
                echo __FILE__ . " " . __LINE__ . "<br>";
                // Get next msg from the urlSequence queue:
                $url = array_shift($this->urlSequence);
                echo __FILE__ . " " . __LINE__ . "<br>";
                print_r($this->urlSequence);
                echo __FILE__ . " " . __LINE__ . "<br>";
                $numberIfInvestments = 0;
                foreach ($investmentListItems as $key => $investmentListItem) {
                    $numberIfInvestments++;

                    // mapping of the investment data to internal dashboard format of Winvestify
                    $tempDataInvestment['loanId'] = $investmentListItem['id_company'];
                    $tempDataInvestment['interest'] = $this->getPercentage(trim($investmentListItem['interes']));
                    $tempDataInvestment['xxxx'] = $this->getMonetaryValue(trim($investmentListItem['capitalpendiente']));
                    $this->print_r2($tempDataInvestment);
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $str = $this->getCompanyWebpage($url . $investmentListItem['id_company']);   // is the amortization table
                    
                    //compañía
                    echo $str;
                    $dom = new DOMDocument;
                    $dom->loadHTML($str);
                    $dom->preserveWhiteSpace = false;
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    // deal with amortization table and normalize the loan state
                    $projectAmortizationData = $this->getElements($dom, "table", "class", "resumen"); // only 1 found
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $trs = $projectAmortizationData[0]->getElementsByTagName('tr');
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $mainIndex = -1;
                    foreach ($trs as $key1 => $tr) {
                        $mainIndex = $mainIndex + 1;
                        $subIndex = -1;
                        $tds = $tr->getElementsByTagName('td');
                        echo __FILE__ . " " . __LINE__ . "<br>";
                        foreach ($tds as $td) {
                            $subIndex = $subIndex + 1;
                            echo __FILE__ . " " . __LINE__ . "<br>";
                            if ($subIndex == 7) {
                                $imgs = $this->getElements($td, "img");
                                if (!empty($imgs)) { // We found the footer, simply ignore			
                                    $actualState = $imgs[0]->getAttribute("title");
                                    $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
                                }
                            } else {
                                $amortizationTable[$mainIndex][$subIndex] = $td->nodeValue;
                            }
                        }
                    }
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $tempInvested = array_pop($amortizationTable);  // get contents of "footer" and remove it from the amortization table 
                    //	$tempDataInvestment['invested'] = stripos(trim($tempInvested[3]));
                    $tempDataInvestment['invested'] = trim(preg_replace('/\D/', '', $tempInvested[3]));

                    // map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED		
                    //		if (strncasecmp($investmentListItem['estado'], "Al d", 2) == 0) {		// checking for status words "Al día"
                    //			$tempDataInvestment['status'] = OK;
                    //		}
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    $tempDataInvestment['commission'] = 0;
                    //Duration	Unit (=meses) is hard coded		
                    $tempDataInvestment['duration'] = count($amortizationTable) . " Meses";
                    $tempDataInvestment['date'] = $this->getHighestDateValue($amortizationTable, "dd-mm-yyyy", 1);
                    $tempDataInvestment['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 4, 7);
                    $tempDataInvestment['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 3, 7);
                    $this->tempArray['investments'][] = $tempDataInvestment;
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    // update the global data of Arboribus
                    $this->tempArray['global']['activeInInvestments'] = $this->tempArray['global']['activeInInvestments'] +
                            $tempDataInvestment['xxxx'];
                    $this->tempArray['global']['totalEarnedInterest'] = $this->tempArray['global']['totalEarnedInterest'] +
                            $tempDataInvestment['profitGained'];
                    $this->tempArray['global']['totalInvestment'] = $this->tempArray['global']['totalInvestment'] + $tempDataInvestment['invested'];
                    $this->tempArray['global']['investments'] = $this->tempArray['global']['investments'] + $numberOfInvestments + 1;
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    unset($tempDataInvestment);
                }
                break;
            case 7:
                
                break;
        }
        

        //return $tempArray;
    }

    /**
     *
     * 	Collects the investment data of the user
     * 	@return array	Data of each investment of the user as an element of an array
     * 	
     */
    function collectUserInvestmentDataSequencial($user, $password) {

        $resultMyArboribus = $this->companyUserLogin($user, $password);
        if (!$resultMyArboribus) {   // Error while logging in
            echo __FILE__ . " " . __LINE__ . " LOGIN ERROR<br>";
            $tracings = "Tracing:\n";
            $tracings .= __FILE__ . " " . __LINE__ . " \n";
            $tracings .= "Arboribus login: userName =  " . $this->config['company_username'] . ", password = " . $this->config['company_password'] . " \n";
            $tracings .= " \n";
            $msg = "Error while logging in user's portal. Wrong userid/password \n";
            $msg = $msg . $tracings . " \n";
            $this->logToFile("Warning", $msg);
            exit;
        }
        echo __FILE__ . " " . __LINE__ . " LOGIN CONFIRMED<br>";
        $dom = new DOMDocument;
        $dom->loadHTML($this->mainPortalPage); // "Mi Cuenta" page as obtained in the function "companyUserLogin"	
        $dom->preserveWhiteSpace = false;
//        echo $this->mainPortalPage;
        echo __FILE__ . " " . __LINE__ . "<br>";

        echo __FILE__ . " " . __LINE__ . "<br>";
        $resumen = $this->getCompanyWebpage();
//      echo $resumen;
        $dom = new DOMDocument;
        $dom->loadHTML($resumen); // "Mi Cuenta" page as obtained in the function "companyUserLogin"	
        $dom->preserveWhiteSpace = false;


        echo __FILE__ . " " . __LINE__ . "<br>";

        $divs = $this->getElements($dom, "div", "class", "arb_detail_right col-xs-12 col-sm-6");

        echo __FILE__ . " " . __LINE__ . "<br>";

        foreach ($divs as $key => $div) {
            echo "key = $key, " . $div->nodeValue . "<br>";
        }

        $trs = $this->getElements($divs[1], "td", "class", "tcell-align-right");
        echo __FILE__ . " " . __LINE__ . "<br>";
        $tempArray['global']['myWallet'] = $this->getMonetaryValue($trs[1]->nodeValue);
        echo __FILE__ . " " . __LINE__ . "<br>";
        $h3s = $this->getElements($dom, "h3", "style", "font-size:xx-large;");
        $tempArray['global']['profitibility'] = $this->getPercentage($h3s[0]->nodeValue);
        echo __FILE__ . " " . __LINE__ . "<br>";
        $str1 = $this->getCompanyWebpage();     // list of investments as JSON
//       $this->print_r2($str1);
        $strListInvestments = $str1;

        
        $investmentListItems = json_decode($strListInvestments, true);
        echo __FILE__ . " " . __LINE__ . "<br>";
//        $this->print_r2($investmentListItems);
        echo __FILE__ . " " . __LINE__ . "<br>";
// Get next msg from the urlSequence queue:
        $url = array_shift($this->urlSequence);
        echo __FILE__ . " " . __LINE__ . "<br>";
        echo $url;
        print_r($this->urlSequence);
 //       echo __FILE__ . " " . __LINE__ . "<br>";
        $numberIfInvestments = 0;
        foreach ($investmentListItems as $key => $investmentListItem) {
            $numberIfInvestments = $numberIfInvestments + 1;

// mapping of the investment data to internal dashboard format of Winvestify
            $tempDataInvestment['loanId'] = $investmentListItem['id_company'];
            $tempDataInvestment['interest'] = $this->getPercentage(trim($investmentListItem['interes']));
            $tempDataInvestment['xxxx'] = $this->getMonetaryValue(trim($investmentListItem['capitalpendiente']));
//           $this->print_r2($tempDataInvestment);
            echo __FILE__ . " " . __LINE__ . "<br>";
//Changed the parameter for the url

            $str = $this->getCompanyWebpage($url . $investmentListItem['id_company']);   // is the amortization table
            echo $str;
            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;
            echo __FILE__ . " " . __LINE__ . "<br>";
// deal with amortization table and normalize the loan state
            /*try {*/
                if (!$this->getElements($dom, "table", "class", "resumen")) {
                    throw new Exception('error tabla');
                }
                $projectAmortizationData = $this->getElements($dom, "table", "class", "resumen"); // only 1 found

                echo __FILE__ . " " . __LINE__ . "<br>";
                if (!$projectAmortizationData[0]->getElementsByTagName('tr')) {
                    throw new Exception('error tabla');
                }
                $trs = $projectAmortizationData[0]->getElementsByTagName('tr');
                echo __FILE__ . " " . __LINE__ . "<br>";

                $mainIndex = -1;
                foreach ($trs as $key1 => $tr) {
                    $mainIndex = $mainIndex + 1;
                    $subIndex = -1;
                    $tds = $tr->getElementsByTagName('td');
                    echo __FILE__ . " " . __LINE__ . "<br>";
                    foreach ($tds as $td) {
                        $subIndex = $subIndex + 1;
 //                       echo __FILE__ . " " . __LINE__ . "<br>";
                        //¿
                        if ($subIndex == 7) {
                            $imgs = $this->getElements($td, "img", "title", "pagado");
                            if (!empty($imgs)) { // We found the footer, simply ignore			
                                $actualState = $imgs[0]->getAttribute("title");
                                $amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
                            }
                        } else {
                            $amortizationTable[$mainIndex][$subIndex] = $td->nodeValue;
                        }
                    }
                }
//                echo __FILE__ . " " . __LINE__ . "<br>";
                $tempInvested = array_pop($amortizationTable);  // get contents of "footer" and remove it from the amortization table 
//		$tempDataInvestment['invested'] = stripos(trim($tempInvested[3]));
                $tempDataInvestment['invested'] = trim(preg_replace('/\D/', '', $tempInvested[3]));

// map status to Winvestify normalized status, PENDING, OK, DELAYED, DEFAULTED		
//		if (strncasecmp($investmentListItem['estado'], "Al d", 2) == 0) {		// checking for status words "Al día"
//			$tempDataInvestment['status'] = OK;
//		}
 //               echo __FILE__ . " " . __LINE__ . "<br>";
                $tempDataInvestment['commission'] = 0;
//Duration	Unit (=meses) is hard coded		
                $tempDataInvestment['duration'] = count($amortizationTable) . " Meses";
                $tempDataInvestment['date'] = $this->getHighestDateValue($amortizationTable, "dd-mm-yyyy", 1);
                $tempDataInvestment['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 4, 7);
                $tempDataInvestment['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable, date("Y-m-d"), "dd-mm-yyyy", 1, 3, 7);
                $tempArray['investments'][] = $tempDataInvestment;
 //               echo __FILE__ . " " . __LINE__ . "<br>";
// update the global data of Arboribus
                $tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] +
                        $tempDataInvestment['xxxx'];
                $tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
                        $tempDataInvestment['profitGained'];
                $tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $tempDataInvestment['invested'];
                $tempArray['global']['investments'] = $tempArray['global']['investments'] + $numberOfInvestments + 1;
//                echo __FILE__ . " " . __LINE__ . "<br>";
                unset($tempDataInvestment);
            /*} catch (Exception $e) {
                echo 'Excepción capturada: ', $e->getMessage(), "\n";
                $tempArray['global']['myWallet'] = 0;
                $tempArray['global']['profitibility'] = 0;
                $tempArray['global']['activeInInvestments'] = 0;
                $tempArray['global']['totalEarnedInterest'] = 0;
                $tempArray['global']['totalInvestment'] = 0;
                $tempArray['global']['investments'] = 0;
            }*/
        }
        echo __FILE__ . " " . __LINE__ . "<br>";

// The normal logout procedure does not work so do a workaround
// Force a logout with data elements provided in the last read page.
        $this->companyUserLogout();
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
    function companyUserLogin($user = "", $password = "") {
        /* 	
          FIELDS USED BY ARBORI DURING LOGIN PROCESS

          5f48fa2e824927e1d3e1f04cd...	1
          Submit.x						12
          Submit.y						7
          option							com_users
          password						Pepa2016
          remember						yes
          return							aW5kZXgucGhwP0l0ZW1pZD0xMjU=
          task							user.login
          username						pepamiras@gmail.com
         */
        $credentials['username'] = $user;
        $credentials['password'] = $password;


//Login fixed

        $str = $this->getCompanyWebpage();    // Load main page, needed so I can read the csrf code

        $dom = new DOMDocument;
        $dom->loadHTML($str);
        $dom->preserveWhiteSpace = false;

        $forms = $dom->getElementsByTagName('form');

        foreach ($forms as $form) {
            $inputs = $form->getElementsByTagName('input');
            foreach ($inputs as $input) {
                if (!empty($input->getAttribute('name'))) {  // look for the post variables
                    if ($input->getAttribute('type') == "hidden") {
                            $credentials[$input->getAttribute('name')] = $input->getAttribute('value');
                        }
                    }
                }
            }
            //Forced user.login
            $credentials['task'] = "user.login";
            $str = $this->doCompanyLogin($credentials);   // Send the post request with authentication information
            $str = $this->getCompanyWebpage();

            $dom = new DOMDocument;
            $dom->loadHTML($str);
            $dom->preserveWhiteSpace = false;

            $as = $dom->getElementsByTagName('a');
            foreach ($as as $a) {
                if (strcasecmp(trim($a->nodeValue), "Salir") == 0) {  // Login confirmed
                    $this->mainPortalPage = $str;
                    array_shift($this->urlSequence);
                    array_shift($this->urlSequence);
                    return true;
                }
            }
        return 0; // Log in fails due to authentication error 
    }

    /**
     *
     * 	Logout of user from to company portal.
     * 	
     * 	@returnboolean	true: user has logged out 
     * 	
     */
    function companyUserLogout() {
        return true;
        $str = $this->doCompanyLogout();
        return true;
    }

    /**
     *
     * 	translate the html of loan state to the winvestify normalized state
     * 	@param	string		html of loanstate
     * 	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
     * 	
     */
    function getLoanState($actualState) {

        $loanStates = array("pendiente" => PENDING,
            "pagado" => OK,
            "impago" => PAYMENT_DELAYED,
            "retrasado" => DEFAULTED);
        foreach ($loanStates as $key => $state) {
            if ($key == $actualState) {
                return $state;
            }
        }
        echo "normalizedState = $normalizedState<br>";
        return $normalizedState;
    }

}
?>