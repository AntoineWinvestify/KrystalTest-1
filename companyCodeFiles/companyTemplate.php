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
* Contains all the code required for accessing the website of "xxx"
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



*/


class xxxx extends p2pCompany{
	
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
*
*	Collects the marketplace data.
*	XXXX is special as one has to logon in order to see all the details of the offers in their marketplace
* 	@return array	Each investment option as an element of an array
*

All fields that contain a special character like € or % should be checked for the existence of that character.
If it does not exist then a "technical error" happened and this should be flagged

*/	
function collectCompanyMarketplaceData() {
//	Also check what happens if a loan goes to 100%, its state changes to "completed, amortización" etc.
//	If completed then set 'marketplace_subscriptionProgress' to 100%
	$this->print_r2($this->urlSequence);
//	$url = $this->config['company_urlMarketplace'];
	$result = $this->companyUserLogin($this->config['company_username'], $this->config['company_password']);

	if (!$result) {			// Error while logging in
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . " \n";
		$tracings .= "userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while entering user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}
	
	$str = $this->getCompanyWebpage();		
	$str = $this->getCompanyWebpage();		
	
	$totalArray = array();
	$pos1 = stripos($str, '[');
	$pos2 = stripos($str, ']');	
	$resultPreJSON = substr($str, $pos1, ($pos2 - $pos1 + 1));

	$jsonResults = json_decode($resultPreJSON, true);

	foreach( $jsonResults as $jsonEntry) {
		$tempArray = array();
		$tempArray['marketplace_loanReference'] = strip_tags($jsonEntry['Prestamo']);
		$tempArray['marketplace_category'] =  strtoupper(strip_tags($jsonEntry['Categoria']));
		$tempArray['marketplace_interestRate'] = $this->getPercentage(strip_tags($jsonEntry['Rentabilidad']));

		$tempInformation = explode("€", strip_tags($jsonEntry['Informacion']));
		$tempArray['marketplace_amount'] = $this->getMonetaryValue($tempInformation[0]);
	
		list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($tempInformation[1]);

		$dom = new DOMDocument;
		$dom->loadHTML($jsonEntry['Completado']);
		$dom->preserveWhiteSpace = false; 
		$divs = $dom->getElementsByTagName('div');
	
	
/*	
	<div class="tabla-faltan info-tooltip clompletado">
		<a href="#" data-toggle="tooltip" data-original-title="Inversores que han invertido.">
			23
			<i class="fa fa-user">
			</i>
		</a>
	</div>
	
	<div class="progress">
		<div class="progress-bar progress-bar-striped active" role="progressbar" style="width:24.66%">24,66 %
		</div>
	</div>
	
	<div class="tabla-faltan text-warning">Quedan 30 días
	</div>	
	
	-------------------
	
	<div class="tabla-faltan info-tooltip clompletado">
		<a href="#" data-toggle="tooltip" data-original-title="Inversores que han invertido.">
			16
			<i class="fa fa-user">
			</i><
		/a>
	</div>

	<div class="progress">
		<div class="progress-bar progress-bar-success" role="progressbar" style="width:100%">Completado
		</div>
	</div>	
	
*/
	
	
		
		$index = 0;
		foreach($divs as $div) {
			switch($index) {
				case 0:
					$tempArray['marketplace_numberOfInvestors'] = strtoupper($div->nodeValue);
					break;					
				case 1:
					echo "ZANK, progress = " . $div->nodeValue . "<br>"; 
					if ( stristr(trim($div->nodeValue), "%") == true) {
						echo "ZANK22: % found, so store in marketplace<br>";
						$tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($div->nodeValue);
					}
					else {
						$tempArray['marketplace_subscriptionProgress'] = 10000;		// completed, retrasado orr amortización ..
					}
					break;
				case 2:
					// Error in HTML of ZANK website source. It generates and extra "/div" tag. Do not do anything
					break;
				case 4:		//
				echo "ZANK timeleft: " . $div->nodevalue . "<br>";	
					list($tempArray['marketplace_timeLeft'], $tempArray['marketplace_timeLeftUnit']) = $this->getDurationValue($div->nodeValue);
					break;										
				default:
			}	
			$index++;
		}
			
		$dom = new DOMDocument;
		$dom->loadHTML($jsonEntry['Finalidad']);
		$dom->preserveWhiteSpace = false; 

		$as = $dom->getElementsByTagName('a');
		foreach ($as as $a) {
			$tempArray['marketplace_purpose'] = $a->getAttribute('data-original-title');
		}
	$totalArray[] = $tempArray;
echo __FILE__ . " " . __LINE__ . "<br>";
$this->print_r2($tempArray);
	unset($tempArray);
	}
	return $totalArray;
}




		

/**
*
*	Collects the investment data of the user
*	@return array	Data of each investment of the user as an element of an array
*
*
*
Variables:
global





per investment

an exception should be thrown in the following cases:
	A No data is available, i.e. the user has no investments on the current platform. Note that he might have funds
	on that platform
	B Authentication error while accessing the platform
	C No code exists for reading the platform structure. This happens after a new platform has been introduced, but
	the internal structure of the portalpage with investments is NOT known. A method is required to "automatically"
	read links and follow these links and determine the "usablility" of these links
	



*/
function collectUserInvestmentData($user, $password) {

	$resultXXX = $this->companyUserLogin($user, $password);
	
	if (!$resultXXX) {			// Error while logging in
		echo __FILE__ . " " . __LINE__  . "ERROR WHILE LOGGING IN<br>";
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . "ERROR WHILE LOGGING IN\n";
		$tracings .= "XXX login: userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while logging in user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}	
	echo "LOGIN CONFIRMED";		
	$dom = new DOMDocument;
 	$dom->loadHTML($this->mainPortalPage);	// obtained in the function	"companyUserLogin"	
	$dom->preserveWhiteSpace = false;
	
/*	
examine the page and identify new links
examine the page and identity "global variables"
read amortization table and normalize state using routine getLoanState($actualState)


*/
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
* 	@return	boolean	true: 		user has succesfully logged in. $this->mainPortalPage contains the entry page of the user portal
*					false: 		user could not log in
*	
*/
/*
check which fields are mandatory to send in the POST message to the server 
if afirmative, load page in variable $this->mainPage and return true

*/	
function companyUserLogin($user, $password) {

	
echo __FUNCTION__ . " " . __LINE__ . "<br>";
	$credentials['username'] = $user;
	$credentials['password'] = $password;	

	$str = $this->getCompanyWebpage();  // needed so I can read the csrf code, or other elements that needs to be returned to server
echo $str;
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false;
	
	$forms = $dom->getElementsByTagName('form');
	$index = 0;

	
	foreach ($forms as $form) {
		$index = $index + 1;	
echo __FUNCTION__ . " " . __LINE__ . "<br>";
		$inputs = $form->getElementsByTagName('input');
		foreach ($inputs as $input) {
			if (!empty($input->getAttribute('name'))) {		// look for the post variables
				if ($input->getAttribute('type') == "hidden" ) {
					$credentials[$input->getAttribute('name')] = $input->getAttribute('value');
				}
			}
		}
	}
	
echo __FUNCTION__ . " " . __LINE__ . "<br>";


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
*	Logout of user from the company portal.
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
*	@param	string		html of loanstate
*	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
*NOT TESTED FRO MYTRIPLEAAA
*/
function getLoanState($actualState) {	
	if (empty($actualState)) {
		return PENDIENTE;
	}
	$loanStates = array("al d"	=> OK,
						"impago"	=> PAYMENT_DELAYED,
						"retrasado" => DEFAULTED);
	foreach( $loanStates as $key => $state) {
		if ($key == $actualState) {
			return $state;
		}
	}
	echo "normalizedState = $normalizedState<br>";
	return $normalizedState;
}



}

?>