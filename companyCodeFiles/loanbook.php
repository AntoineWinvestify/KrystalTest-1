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
* @date 2016-10-05
* @package


function calculateLoanCost()										[OK not tested]
function collectCompanyMarketplaceData()								[OK, tested]
function companyUserLogin()										[OK, tested]
function collectUserInvestmentData()									[OK, tested]
function companyUserLogout()										[OK, tested]
parallelization                                                                                         [OK, tested]

2016-10-05	  version 2016_0.1
Basic version
 
2017-04-27
Duration fixed

2017-05-16       version 2017_0.2
 * Added parallelization
 * Added logout
 * Added verification of dom elements
 * 

2017-08-01      version 0.3
 * Fixed error to take more than one investment on lines 364 and 395

PENDING:


*/


class loanbook extends p2pCompany{

		
function __construct() {
	parent::__construct();	
// Do whatever is needed for this subsclass


}





/**
*
*	Calculates how must it will cost in total to obtain a loan for a certain amount
*	from a company
* 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
* 	@param	int $duration		: The amortization period (in month) of the loan
* 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
* 	@return int					: Total cost (in Eurocents) of the loan
*
*/
function calculateLoanCost($amount, $duration, $interestRate)  {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked:xx-xx-xxxx

	$minimumCommission = 12000;			// in  €cents

	$fixedCost = 3 * $amount/100;
	if ($fixedCost < $minimumCommission) {
		$fixedCost = $minimumCommission;
	}
	
	$interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12) ;
	$totalCost = $fixedCost + $interest + $amount;
	return $fixedCost + $interest + $amount;
	
	
}





/**
*
*	Collects the marketplace data
*	@return array	Each investment option as an element of an array
* 	
*/	
function collectCompanyMarketplaceData() {

	$totalArray = array();		
	$str = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed
	
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 

	$sections = $dom->getElementsByTagName ('section');
	foreach( $sections as $section) {

		$trs = $section->getElementsByTagName('tr');
		foreach ($trs as $tr) {
			$tempAttribute = $tr->getAttribute('class');
			if ($tempAttribute == 'fila_subasta') {

				$tds = $tr->getElementsByTagName('td');
				$index = -1;
				foreach ($tds as $td)	{
					$index++;
					
					switch ($index) {
						case 0:	
							break;	
						case 1:
							$divs = $td->getElementsByTagName('div');
							foreach ($divs as $div) {

								$tempData = explode(",", $div->nodeValue);
								$tempDataAmount = explode(" ", $tempData[count($tempData)-1]);

								$tempArray['marketplace_amount'] = $this->getMonetaryValue($tempDataAmount[1]);
								$tempArray['marketplace_loanReference'] = trim($tempDataAmount[18]);
								$as = $div->getElementsByTagName('a');		//just one is found
								foreach ($as as $a) {
									$tempArray['marketplace_purpose'] = trim($a->nodeValue);
								}
								break;
							}
							break;	
						case 2:
							$tempProductType = trim($td->nodeValue);
							if (stripos($tempProductType, "stamo"))	{		// LOAN
								$tempArray['marketplace_productType'] = LOAN;
							}
							if (stripos($tempProductType, "agar"))	{		// PAGARÉ
								$tempArray['marketplace_productType'] = PAGARE;
							}
							break;	
						case 3:
							$tempArray['marketplace_rating'] = trim($td->nodeValue);
							break;
						case 4:
							break ;
						case 5:
							$tempArray['marketplace_interestRate'] = $this->getPercentage($td->nodeValue);
							break;	
						case 7:
							list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) =
															$this->getDurationValue($td->nodeValue);
							break;	
						case 6:
							break;
						case 8:
							$tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($td->nodeValue);
							break ;
						case 9:
							list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) =
															$this->getDurationValue($td->nodeValue);
							break ;
					}
					
				}		
			}
			$totalArray[] = $tempArray;
			$this->print_r2($tempArray);
			unset($tempArray);
		}
		return $totalArray;
	}
}




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
                /*$this->verifyNodeHasElements($forms);
                if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }*/
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
                /*if (!$this->hasElements) {
                    return $this->getError(__LINE__, __FILE__);
                }*/
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
                $this->tempArray['global']['profitibility'] = $this->getPercentage(trim($spans[0]->nodeValue));

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
                for ($key = 0 ; $key < $trs->length; $key++) {
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
                        if ($subIndex == 2) {   // normalize the status, needed for payment calculations
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
                $this->accountPosition++;
                if ($this->accountPosition != ($this->numberOfInvestments-1)) {
                    $this->idForSwitch = 10;
                    $this->getCompanyWebpageMultiCurl($this->tempUrl[$this->accountPosition]);
                    break;
                }
                else {
                    $this->tempArray['global']['investments'] = $this->numberOfInvestments;
                    $this->tempArray['investments'] = $this->data1;
                    $this->print_r2($this->tempArray);
                    return $this->tempArray; 
                }

        }
    }



/**
*
*	Collects the investment data of the user
*	@return array	Data of each investment of the user as an element of an array
*	
*/
function collectUserInvestmentData($user, $password) {

	$resultMiLoanbook = $this->companyUserLogin($user, $password);
     
	if (!$resultMiLoanbook) {			// Error while logging in
		echo __FILE__ . " " . __LINE__  . "ERROR WHILE LOGGING IN<br>";
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . "ERROR WHILE LOGGING IN\n";
		$tracings .= "Loanbook login: userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while logging in user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}


	$dom = new DOMDocument;
 	$dom->loadHTML($this->mainPortalPage);	// obtained in the function	"companyUserLogin"	
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


	$str1 = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed	
	$str2 = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed
	$str3 = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed	
	$str4 = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed	
	$str5 = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed	
	$str6 = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed	

        $dom = new DOMDocument;
 	$dom->loadHTML($str6);	// obtained in the function	"companyUserLogin"	
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
		$data1[$key]['duration'] = filter_var( $tempDuration, FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as"; 
		$data1[$key]['invested'] = $this->getMonetaryValue($tds[5]->nodeValue);
		$data1[$key]['commission'] = 0;
		$data1[$key]['interest'] = $this->getPercentage($tds[6]->nodeValue);		

// Get amortization table. first get base URL for amortization table
		$baseUrl = array_shift($this->urlSequence);
		$as = $tds[0]->getElementsByTagName('a');		 // only 1 will be found
		$dataId =  $as[0]->getAttribute("data-id");

// Deal with the amortization table
		$strAmortizationTable = $this->getCompanyWebpage($baseUrl . "/" .$dataId);
		$domAmortizationTable = new DOMDocument;
	 	$domAmortizationTable->loadHTML($strAmortizationTable);	
		$domAmortizationTable->preserveWhiteSpace = false;		
		$amortizationData = $this->getElements($domAmortizationTable, "tr", "class", "detail");	// only 1 found

// Convert into table
		$mainIndex = -1;	

// map status to Winvestify normalized status, (PENDING), OK, DELAYED, DEFAULTED	
		$data1[$key]['status'] = 0;
	
// prepare amortization table and normalize (payment) status (PENDING), OK, DELAYED, DEFAULTED
// and get the "real" status of theloan. Index 2 of table represents the loan state
		foreach ($amortizationData as $key1 => $trAmortizationTable ) {
			$mainIndex = $mainIndex + 1;
			$subIndex = -1;
			$tdsAmortizationTable  = $trAmortizationTable->getElementsByTagName('td');

			foreach( $tdsAmortizationTable  as $tdAmortizationTable ) {
				$subIndex = $subIndex + 1;
				if ($subIndex == 2) {			// normalize the status, needed for payment calculations
					$is = $tdAmortizationTable->getElementsByTagName('i');
					$actualState = $is[0]->getAttribute("title");
					$amortizationTable[$mainIndex][$subIndex] = $this->getLoanState($actualState);
				}
				else {
					$amortizationTable[$mainIndex][$subIndex] = trim($tdAmortizationTable->nodeValue);
				}
			}
		}
		$data1[$key]['amortized'] = $this->getCurrentAccumulativeRowValue($amortizationTable,
																			 date("Y-m-d"),
																			 "dd-mm-yyyy",
																			 1, 3, 2);
		$data1[$key]['profitGained'] = $this->getCurrentAccumulativeRowValue($amortizationTable,
																			 date("Y-m-d"),
																			 "dd-mm-yyyy",
																			 1, 4, 2);
 
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
*
*	Checks if the user can login to its portal. Typically used for linking a company account
*	to our account
*	
*	@param string	$user		username
*	@param string	$password	password
* 	@return	boolean	true: 		user has successfully logged in. $this->mainPortalPage contains the entry page of the user portal
*					false: 		user could not log in
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
			if (!empty($input->getAttribute('name'))) {		// check all hidden input fields, like csrf
				if ($input->getAttribute('name') == "csrf" ) {
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
			if (strcasecmp (trim($a->nodeValue), "RESUMEN") == 0) {
				$this->mainPortalPage = $str;
				return true;
				break 2;
			}
			$index = $index + 1;
		}
	}
	return false;		// Could not login, credential error
}





/**
*
*	Logout of user from to company portal.
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
*	translate the html of loan state to the winvestify normalized state
*	@param	string		$str html of loanstate
*	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
*	
*/
function getLoanState($actualState) {	

	$loanStates = array("Pendiente" => PENDING,
								"ok" => OK,
							"retraso" => PAYMENT_DELAYED,
							"judicial" => DEFAULTED);
	foreach( $loanStates as $key => $state) {
		if ($key == $actualState) {
			return $state;
		}
	}
}



}

