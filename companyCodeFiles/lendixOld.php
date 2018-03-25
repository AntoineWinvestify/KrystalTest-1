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
* Contains the code required for accessing the website of "grow.ly"
*
* 
* @author Antoine de Poorter
* @version 0.1
* @date 2016-11-06
* @package


2016-11-06	  version 2016_0.2
Updated according to new structure of web of Lendix
 
function calculateLoanCost()											[Not OK, not tested]
function collectCompanyMarketplaceData()								[OK, tested]
function companyUserLogin()												[OK, tested]
function collectUserInvestmentData()									[Not OK]
function companyUserLogout()											[Not OK, not tested]
 
 
 
TODO
I loanId exists in the public market place
GET https://api.lendix.com/projects?limit=10&offset=0 in order to get the marketplace list including loanId
This can only be done after logging . Result comes back as a JSON list

$tempArray['marketplace_durationUnit'] = 2; is hardcoded.


*/


class lendix extends p2pCompany{
	
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
// Fixed cost: 2% of requested amount

	$fixedCost = 2 * $amount/100;
	
	$interest = ($interestRate / 100) * ($amount / 12 ) * ($duration / 12) ;
	$totalCost = $fixedCost + $interest + $amount;
	return $fixedCost + $interest + $amount;
	
	
}





/**
*
*	Collects the marketplace data
* 	@return array	Each open investment option as an element of an array
*
*/	
function collectCompanyMarketplaceData() {

	$tempArray = array();
	$totalArray = array();
	
	$str = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed

	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 

	$divs = $this->getElements($dom, "div", "class", "card online");  

	foreach ($divs as $key => $div) {
		$projectDivs = $this->getElements($div, "div");
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
}

	


		
/**
*
*	Collects the investment data of the user
*	@return array	Data of each investment of the user as an element of an array
*	also do logout
*	
*/
function collectUserInvestmentData($user, $password) {
// user: inigo.iturburua@gmail.com
// password: Ap_94!56

//	$this->config['appDebug'] = true;

	$resultLendix = $this->companyUserLogin($user, $password);

	if (!$resultLendix) {			// Error while logging in
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . " \n";
		$tracings .= "Lendix login: userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while logging in user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}

	$session = json_decode($this->mainPortalPage, $assoc = true);
//$this->print_r2($session);
	$lendixSessionId = $session['session']['id'];
	$lendixSessionToken = $session['session']['token'];
	$userId = $session['session']['user']['id'];
	$header1 = "sessionToken: $lendixSessionToken";
	$header2 = "userId: $userId";
// construct extra headers for next http message
	$this->defineHeaderParms(array ($header1, $header2));
	

	$str = $this->getCompanyWebpage();		// https://api.lendix.com/transactions/summary?finsquare=true
	$summaryData = json_decode($str, $assoc = true);
$this->print_r2($summaryData);

	foreach ($summaryData['investments'] as $key => $item) {
		$data1[$key]['name'] = $item['project']['name'];
		$data1[$key]['loanId'] = $item['project']['name'];
		$data1[$key]['date'] = "00-00-0000";
		$data1[$key]['duration'] = $item['investment']['monthsLeft'] . " Meses";
		$data1[$key]['invested'] = (int)(preg_replace('/\D/', '', $item['investment']['total'])) * 100;
		$data1[$key]['commission1'] = $item['investment']['taxes'];
		$data1[$key]['commission'] = (int)(preg_replace('/\D/', '', $item['investment']['taxes'])) * 100;
		$data1[$key]['interest'] = $this->getPercentage($item['project']['rate']);		
		$data1[$key]['amortized'] = $data1[$key]['invested'] - $item['investment']['remaining'];	
		$data1[$key]['profitGained2'] = $item['investment']['interests'];	
		$data1[$key]['profitGained1'] = (int)(preg_replace('/\D/', '', $item['investment']['interests'])) * 100;

		$data1[$key]['profitGained'] = $this->getMonetaryValue(str_replace(".", ",", $item['investment']['interests']));		
		$tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
															$data1[$key]['profitGained'];
		$tempArray['global']['totalInvestment']	= $tempArray['global']['totalInvestment'] + $data1[$key]['invested'];	
		$tempArray['global']['activeInInvestments']	= $tempArray['global']['activeInInvestments'] +
																$item['investment']['remaining'];
		$tempArray['global']['totalInvestments'] = $tempArray['global']['totalInvestments'] +
																$data1[$key]['invested'];
															

	}																
	$tempArray['global']['profitibility1'] = $summaryData['averageRate'];	
	$tempArray['global']['profitibility'] = $this->getPercentage($summaryData['averageRate']);	
	$tempArray['global']['investments'] = count($summaryData['investments']);
	$tempArray['investments'] = $data1;
	$tempArray['global']['myWallet'] = $session['session']['user']['credit'];

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
* 	@return	boolean	true: 		user has succesfully logged in. $this->mainPortalPage contains the entry page of the user portal
*					false: 		user could not log in
*	
*/	
function companyUserLogin($user, $password) {

	$str = $this->getCompanyWebpage();

	$credentials = array();
	$credentials['email'] = $user;
	$credentials['password'] = $password;
	$credentials['user'] = null;
	
	$this->mainPortalPage = $this->doCompanyLogin($credentials);
	$result = json_decode($this->mainPortalPage, $assoc = true);

// check if user actually has entered the portal of the company
	if (!empty($result['session']['user']['id'])) {
		return 1;
	}
	return 0;
}





/**
*
*	Logout of user from the company portal.
*	
* 	@returnboolean	true: user has logged out 
*	
*/	
function companyUserLogout() {
// logout sequence is https://api.lendix.com/sessions/58a03f48b04a650016a7d72c with session-id, send as a DELETE msg
	$str = $this->doCompanyLogout();
	return true;
}




}

?>