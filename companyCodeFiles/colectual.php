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
function calculateLoanCost()											[not OK, not tested]
function collectCompanyMarketplaceData()								[OK, not tested]
function companyUserLogin()												[not OK, not tested]
function collectUserInvestmentData()									[not OK, not tested]
function isNewEntry()													[not OK, not tested]





PENDING:

*/


class colectual extends p2pCompany{

		
function __construct() {
	parent::__construct();	
// Do whatever is needed for this subsclass


}





/**
*
*	Calculates how much it will cost in total to obtain a loan for a certain amount
*	from a company
* 	@param  int	$amount 		: The amount (in Eurocents) that you like to borrow 
* 	@param	int $duration		: The amortization period (in month) of the loan
* 	@param	int $interestRate	: The interestrate to be applied (1% = 100)
* 	@return int					: Total cost (in Eurocents) of the loan
*
*/
function calculateLoanCost($amount, $duration, $interestRate)  {
// Fixed cost: 3% of requested amount with a minimum of 120 €	Checked: 26-08-2016
/*
	$minimumCommission = 12000;			// in  €cents

	$fixedCost = 3 * $amount/100;
	if ($fixedCost < $minimumCommission) {
		$fixedCost = $minimumCommission;
	}
	
	$interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12) ;
	$totalCost = $fixedCost + $interest + $amount;
	return $fixedCost + $interest + $amount;
	*/
	
}





/**
*
*	Collects the marketplace data. We must login first in order to obtain the marketplace data
*	@return array	Each investment option as an element of an array
* 	
*/	
function collectCompanyMarketplaceData() {


	$this->config['appDebug'] = true;

	$resultColectual = $this->companyUserLogin($user, $password);

	if (!$resultColectual) {			// Error while logging in
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . " \n";
		$tracings .= "Colectual login: userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while logging in user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}
echo "LOGIN COLECTUAL CONFIRMED<br>";
	
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 

	$divs = $this->getElements($dom, "div", "class", "collapse navbar-collapse");

	$projectas = $this->getElements($divs[0], "a");
	foreach ($prjectas as $key => $a) {
		
		$tempArray['marketplace_rating'] = trim($projectDivs[2]->nodeValue);
		$tempArray['marketplace_subscriptionProgress'] = $this->getPercentage( $projectDivs[2]->nodeValue);		// WRONG INDEX CHECK WITH REAL PROJECT
		$tempArray['marketplace_name'] = trim($projectDivs[12]->nodeValue);
		$tempArray['marketplace_amount'] = $this->getMonetaryValue( $projectDivs[13]->nodeValue);
		$tempArray['marketplace_interestRate'] = $this->getPercentage( $projectDivs[19]->nodeValue);
		list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) =
															$this->getDurationValue($projectDivs[26]->nodeValue);
/*******************************************************/															
/* HARD CODED AS PREVIOUS STATEMENT GENERATES AN ERROR */
		$tempArray['marketplace_durationUnit'] = 2;
/*******************************************************/
		$tempArray['marketplace_requestorLocation'] = trim($projectDivs[31]->nodeValue);
		$tempArray['marketplace_sector'] = trim($projectDivs[37]->nodeValue);

		$as = $this->getElements($div, "a");
		$loanId = explode(":", $as[0]->getAttribute("title"));
		$tempArray['marketplace_loanReference'] = trim($loanId[1]);

		$totalArray[] = $tempArray;			
		$this->print_r2($tempArray);	
		unset($tempArray);	
	}
	$this->print_r2($totalArray);
	return $totalArray;	

/*	$totalArray = array();
	
	$str = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed
	
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 

	$tables = $dom->getElementsByTagName('table');	
	foreach ($tables as $table) {			// only deal with FIRST table in document
		$trs = $table->getElementsByTagName('tr');
			foreach ($trs as $tr) {
//echo "<br>" .  __FILE__ . " " . __LINE__ . "<br>";
//echo $tr->nodeValue;
//echo "<br>" .  __FILE__ . " " . __LINE__ . "<br>";
				$tds = $tr->getElementsByTagName('td');
				$index = -1;
				$tempArray = array();
				foreach ($tds as $td) {
					
					$index = $index + 1;
//echo "index = $index and value = " . $td->nodeValue . "<br>";
					switch($index) {
						case 1:
							$tempArray['marketplace_loanReference'] = $td->nodeValue ;
							break;
						case 2:
							$innerIndex = 0;
							$as = $td->getElementsByTagName('a');
							
							foreach ($as as $a){		// only 1 will be found
									$tempArray['marketplace_purpose'] = trim($a->nodeValue);
							}
							$tempArray['marketplace_requestorLocation'] = trim(str_replace( $tempArray['marketplace_purpose'],
																			"",
																			$td->nodeValue ));			   
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
							list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue( $td->nodeValue);
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
echo __FILE__ . " " . __LINE__ . "<br>";
	return $totalArray;*/
}




		
/**
*
*	Collects the investment data of the user
*	@return array	Data of each investment of the user as an element of an array
*	
*/
function collectUserInvestmentData() {
	
	
	
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
function companyUserLogin($user = "", $password = "") {
//manu.azarus@gmail.com
//Azarus2016!
/*
https://api.colectual.com/token
client_id=ngAuthApp
grant_type=password
password=Azarus2016!
username=manu.azarus@gmail.com

look for "Mi cuenta"
*/


	$credentials['username'] = $user;
	$credentials['password'] = $password;
	$credentials['client_id'] = "ngAuthApp";
	$credentials['grant_type'] = "password";

	$str = $this->getCompanyWebpage();		// Main page
echo "STRING1" . $str . "<br>";	
	$str = $this->doCompanyLogin($credentials);
echo __FILE__ . " " . __LINE__ . "<br>";
echo "STRING2" . $str. "<br>";

	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 
echo __FILE__ . " " . __LINE__ . "<br>";
	$divs = $this->getElements($dom, "div", "class", "collapse navbar-collapse");
echo __FILE__ . " " . __LINE__ . "<br>";
	$projectas = $this->getElements($divs[0], "a");
echo __FILE__ . " " . __LINE__ . "<br>";	
	foreach ($projectas as $key => $a) {
echo __FILE__ . " " . __LINE__ . "<br>";
		echo "key = $key " . $a->getAttribute("href") ."<br>";
		if ($a->getAttribute("href") == "#/inversor/inversiones/activas") {
			
			echo "login confirmed";
			return;
		}
	}





	return 1;
	$confirm = 0;
	$uls = $dom->getElementsByTagName('ul');
	foreach ($uls as $ul) {
		$as = $ul->getElementsByTagName('a');
		foreach ($as as $a) {
			if (strcasecmp (trim($a->nodeValue), "Mi cuenta") == 0) {
				$confirm++;
				break;
			}
		}
	}

	if ($confirm == 1) {
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