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
* Contains all the code required for accessing the website of "Excelend"
*
* 
* @author Antoine de Poorter
* @version 0.1
* @date 2016-11-10
* @package


2016-11-10	  version 2016_0.1
Basic version
function calculateLoanCost()											[not OK, not tested]
function collectCompanyMarketplaceData()								[not OK, not tested]
function companyUserLogin()												[not OK, not tested]
function companyUserLogout												[not OK, not tested]
function collectUserInvestmentData()									[not OK, not tested]






Pending:
login to be done first


*/


class excelend extends p2pCompany{
	
function __construct() {
	parent::__construct();	
// Do whatever is needed for this subsclass
	
}





/**
*
*	Calculates how must it will cost in total to obtain a loan for a certain amount
*	from a company. This includes fixed fee amortization fee(s) etc.
* 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
* 	@param	int $duration		: The amortization period (in months) of the loan
* 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
* 	@return int					: Total cost (in Eurocents) of the loan
*
*/
function calculateLoanCost($amount, $duration, $interestRate)  { 
// Fixed cost: 2% of requested amount with a minimum of 20 €	Checked: 25-08-2016

	$minimumCommission = 12000;			// in  €cents

	$fixedCost = 2 * $amount/100;
	if ($fixedCost < $minimumCommission) {
		$fixedCost = $minimumCommission;
	}
	
	$interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12) ;
	$totalCost = $fixedCost + $interest + $amount;
	return $fixedCost + $interest + $amount;
}





/**
*A LOGIN IS MADATORY AND NOT YET DONE
*	Collects the marketplace data.
*
* 	@return array	Each investment option as an element of an array
*
*/	
function collectCompanyMarketplaceData() {
	
	$totalArray = array();	

	$str = $this->getCompanyWebpage();				
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	
	$dom->preserveWhiteSpace = false; 
	$uls = $dom->getElementsByTagName('ul');
	
	foreach( $uls as $ul) {
		$correctClass = $ul->getAttribute('class');	
		if ($correctClass <> "thumbnails list-unstyled") {
			continue;
		}
		$divs = $ul->getElementsByTagName('div');
		$index = 0;
		foreach( $divs as $div) {
			$class = $div->getAttribute('class');
			echo "Index = $index  and value = " . $div->nodeValue . "<br>";
			$tds = $div->getElementsByTagName('td');		
				switch($index) {	
					case 7:	
						$tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($div->nodeValue);
						break;
					case 8:		// OK
						$tempArray['marketplace_amount'] = $this->getMonetaryValue( $div->nodeValue);
						break;					
					case 9:	
						list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit'] ) =
															$this->getDurationValue($div->nodeValue);				
						break;
					default:
				}
			$index = $index + 1;
		}
		
		$ps = $ul->getElementsByTagName('p');
		$innerIndex = 0;
		foreach( $ps as $p) {
			switch($innerIndex) {
				case 0:
					list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) =
															$this->getDurationValue($p->nodeValue);
					break;
				case 1:
					$tempArray['marketplace_purpose'] = trim($p->nodeValue);
					$as = $p->getElementsByTagName('a');
					foreach( $as as $a) {
						$tempLoanId = explode("/", trim($a->getAttribute('href')));
						$tempArray['marketplace_loanReference'] = $tempLoanId[3];
					}
					break;
			}			
			$innerIndex = $innerIndex + 1;
		}

		$h3s = $ul->getElementsByTagName('h3');
		foreach( $h3s as $h3) {
			$tempRating = explode(" ", $h3->nodeValue);
			$tempArray['marketplace_rating'] = $tempRating[1];
		}

		$h2s = $ul->getElementsByTagName('h2');
		foreach( $h2s as $h2) {		
			$tempArray['marketplace_interestRate'] = $this->getPercentage($h2->nodeValue);
		}
		
		$totalArray[] = $tempArray;
		unset($tempArray);
	}	
	$this->print_r2($totalArray);
	exit;
	return $totalArray;
}




		
/**
*
*	Collects the investment data of the user
*	@return array	Data of each investment of the user as an element of an array
*	NOT DONE
*/
function collectUserInvestmentData($user, $password) {

//	$url = $this->config['company_urlMarketplace'];
	$resultMiZank = $this->companyUserLogin($user, $password);

	if (!$resultMiZank) {			// Error while logging in
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . " \n";
		$tracings .= "userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while logging in user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}

// We are at page: "MI ZANK"
	$dom = new DOMDocument;
	$dom->loadHTML($this->mainPortalPage);
	$dom->preserveWhiteSpace = false;
	
	$scripts = $dom->getElementsByTagName('script');	
	foreach ($scripts as $script) {
		$position = stripos($script->nodeValue, "$.ajax");
			if ($position !== false) {		// We found an entry
				break;
			}
	}
	$testArray = explode(":", $script->nodeValue);

	$userId = trim(preg_replace('/\D/', ' ', $testArray[4]));

	if (!is_numeric($userId)) {
		echo "<br>An eror has occured, could not find internal userId<br>";		
	}
	
	$needle = "kpi_panel";

	$index = 0;
	$ps = $dom->getElementsByTagName('p');
	foreach ($ps as $p) {
		$class = trim($p->getAttribute('class'));
		$position = stripos($class, $needle);
		if ($position !== false) {		// found a kpi
			switch ($index) {
				case 0:
					$tempArray['global']['myWallet'] = $p->nodeValue;
					break;	
				case 1:
					$tempArray['global']['activeInInvestments'] = $p->nodeValue;
					break;	
				case 2:
					$tempArray['global']['totalEarnedInterest'] = $p->nodeValue;
					break;
				case 3:
					$tempArray['global']['profitibility'] = $p->nodeValue;
					break ;
			}
			$index++;
		}	
	}
	
// estamos en la página "MI CARTERA"
//echo __FILE__ . " " . __FUNCTION__ . " " . __LINE__ ." estamos en la página 'MI CARTERA'<br>";
	$str = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed	
	
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false;
	$needle = "kpi_panel";
	$index = 0;

// Look for the kpi's 
	$ps = $dom->getElementsByTagName('p');
	foreach ($ps as $p) {
		$class = trim($p->getAttribute('class'));
		$position = stripos($class, $needle);
		if ($position !== false) {		// found a kpi
			switch ($index) {
				case 0:
					$tempArray['global']['totalInvestments'] = $p->nodeValue;
					break;	
				case 1:
					$tempArray['global']['activeInvestments'] = $p->nodeValue;
					break;	
			}
			$index++;
		}	
	}

// build the Web URL for downloading data of the individual investments of this user
	$url = array_shift($this->urlSequence);
	$url = $url . $userId . "/0";	
	$str = $this->getCompanyWebpage($url);	

	$totalArray = array();
	$pos1 = stripos($str, '[');
	$pos2 = stripos($str, ']');	
	$resultPreJSON = substr($str, $pos1, ($pos2 - $pos1 + 1));
	$resultPreJSON = preg_replace("/\\\u([0-9a-z]{4})/", "&#x$1;", $resultPreJSON);		// without this I cannot decode JSON due
																						// to spanish characters

	$jsonResults = json_decode($resultPreJSON, true);
	$tempArray['investments'] = $jsonResults;
	return $tempArray;
}




	
/**
*
*	Checks if the user can login to its portal. Typically used for linking a company account
*	to our account
*	
*	@param string	$user		username
*	@param string	$password	password
* 	@return	boolean	true: 		user has succesfully logged in. $this->mainPortalPage contains the entry page of the user portal
*					false: 		user could not log in
*	
*/	
function companyUserLogin($user, $password) {

	$credentials = array();
	$credentials['_username'] = $user;
	$credentials['_password'] = $password;
	
	$str = $this->doCompanyLogin($credentials);

// check if user actually has entered the portal of the company
// by means of checking of 2 unique identifiers of the portal
// This should be done by checking a field in the Webpage (button, link etc)
// and the email of the user (if aplicable)
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 

	$confirm = 0;
	$tables = $dom->getElementsByTagName('a');

	foreach ($tables as $tableContent) {
		if (strcasecmp (trim($tableContent->nodeValue), "MI ZANK") == 0) {
			$confirm++;
		}
		if (strcasecmp (trim($tableContent->nodeValue), trim($user)) == 0) {
			$confirm++;
		}					
	}

	if ($confirm == 2) {
		$this->mainPortalPage = $str;
		return 1;
	}
	return 0;
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



}

?> 