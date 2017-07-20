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
* @date 2017-02-16
* @package


2016-08-29	  version 2016_0.1
Basic version
function calculateLoanCost()                                                            	[OK, tested]
function collectCompanyMarketplaceData()								[OK]
function companyUserLogin()									[OK, tested]
function collectUserInvestmentData()								[Not OK, not tested]
function isNewEntry()										[Not Done]

2017-02-16		version 2017_0.1

"Error" in collectCompanyMarketplaceData function. The Web of comunitae generates bad html for the
['marketplace_name'] field, it contained  ...CONSTRUCCIONES ALVEDRO SL "SYCA" (extra "") in title		[OK, tested]

Added loading of ALL investments






PENDING:
if subscriptionProgress = "finalizado" then write -1 in DB field
			if (strcasecmp (trim($a->nodeValue), trim($user)) == 0) {   line 112 should also
			
			
			have length indicator
*/


class comunitae extends p2pCompany{

		
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
     * 	Collects the marketplace data
     * 	@return array	Each investment option as an element of an array
     * 	
     */
    function collectCompanyMarketplaceData() {

        $totalArray = array();

        for ($i = 0; $i < 2; $i++) {
            $str = $this->getCompanyWebpage();
            $dom = new DOMDocument;
            $dom->preserveWhiteSpace = false;
            if ($i == 0) {
                //pymeList
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                $listing = $dom->getElementById("pymeList");
                if (count($listing) == 0) {
                    return totalArray;
                }

                $rows = $listing->getElementsByTagName('article');
            }
            
            else if($i == 1) {
                //Factoring
                $dom->loadHTML($str); // load Webpage into a string variable so it can be parsed
                $rows = $dom->getElementsByTagName('article');
            }
            
            foreach ($rows as $row) {
                $loanId = $row->getAttribute('id');
                $tempArray['marketplace_loanReference'] = $loanId;
                $nameFound = false;

                $as = $row->getElementsByTagName('a');

                foreach ($as as $a) {
                    $tempValue = $a->nodeValue;

                    if ($nameFound == false) {   // first <a> tag contains the name
                        $tempArray['marketplace_name'] = $tempValue;
                        $tempArray['marketplace_purpose'] = $tempValue;
                        $nameFound = true;
                    }

    //			if  (strcasecmp( trim($checkedAttribute), trim($tempValue)) == 0) {
    //				$tempArray['marketplace_name'] = $tempValue;
    //			}			

                    $checkedAttribute = $a->getAttribute('data-risk');
                    if (!empty(trim($checkedAttribute))) {
                        $tempArray['marketplace_rating'] = trim($a->nodeValue);
                    }

                    $checkedAttribute = $a->getAttribute('title');
                    if (strncasecmp(trim($checkedAttribute), 'Tipo de inter', 12) == 0) {
                        $tempArray['marketplace_interestRate'] = $this->getPercentage(trim($a->nodeValue));
                    }

                    $checkedAttribute = $a->getAttribute('title');
                    if (strncasecmp(trim($checkedAttribute), 'Plazo del pr', 12) == 0) {
                        list($tempArray['marketplace_duration'], $tempArray['marketplace_durationUnit'] ) = $this->getDurationValue($a->nodeValue);
                    }

                    $checkedAttribute = $a->getAttribute('title');
                    if (strncasecmp(trim($checkedAttribute), 'Importe pr', 10) == 0) {
                        $tempArray['marketplace_amount'] = $this->getMonetaryValue($a->nodeValue);
                    }
                }

                $spans = $row->getElementsByTagName('span');
                foreach ($spans as $span) {
                    $checkedAttribute = $span->getAttribute('class');
                    if (strcasecmp(trim($checkedAttribute), 'center-percentage') == 0) {

                        if (stristr(trim($span->nodeValue), "%") == true) {
                            echo "Comunitae: % found, so store in marketplace<br>";
                            $tempArray['marketplace_subscriptionProgress'] = $this->getPercentage($span->nodeValue);
                        } else {
                            $tempArray['marketplace_subscriptionProgress'] = 10000;  // completed, retrasado orr amortización ..
                        }
                    }
                }

                $totalArray[] = $tempArray;
                unset($tempArray);
            }
        }
        return $totalArray;
    }

    /**
*
*	Collects the investment data of the user
*	@return array	Data of each investment of the user as an element of an array
*	NOT READY
*/
function collectUserInvestmentData($user, $password) {
// user = "antoine@winvestify.com"
// pw = "Zastac2015"
// manoloherrero@msn.com    Mecano1980E

//$configurationParameters = array('appDebug' => true);
//$this->defineConfigParms($configurationParameters);

	$resultComunitae = $this->companyUserLogin($user, $password);	
	if (!$resultComunitae) {			// Error while logging in
		echo __FILE__ . " " . __LINE__  . "ERROR WHILE LOGGING IN<br>";
		$tracings = "Tracing:\n";
		$tracings .= __FILE__ . " " . __LINE__  . "ERROR WHILE LOGGING IN\n";
		$tracings .= "Comunitae login: userName =  " . $this->config['company_username'] .  ", password = " . $this->config['company_password'] . " \n";
		$tracings .= " \n";
		$msg = "Error while logging in user's portal. Wrong userid/password \n";
		$msg = $msg . $tracings . " \n";
		$this->logToFile("Warning", $msg);
		exit;
	}
echo __FILE__ . " " . __LINE__ . " LOGIN CONFIRMED<br>";	
	$dom = new DOMDocument;
 	$dom->loadHTML($this->mainPortalPage);	// obtained in the function	"companyUserLogin"	
	$dom->preserveWhiteSpace = false;
	
echo __FILE__ . " " . __LINE__ . "<br>";
	$str = $this->getCompanyWebpage();		// load Webpage into a string variable so it can be parsed
echo $str;
	$random = rand(111,900);	
	$url = array_shift($this->urlSequence);
	$str = $this->getCompanyWebpage($url . time() . $random);	// https://www.comunitae.com/pastillaCuenta.html?aleat=1487321358524	
echo __FILE__ . " " . __LINE__ . "<br>";
echo $str;
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false;

	$accountSummarys = $this->getElements($dom, "div", "id", "pastillaCuenta_10");
	$items = $this->getElements($accountSummarys[0], "span", "class", "list-group-item");
	$spans = $this->getElements($items[2], "span", "class", "badge");

	$random = $random + 1;	
	$url = array_shift($this->urlSequence);
	$str = $this->getCompanyWebpage($url . time() . $random);	// https://www.comunitae.com/www.comunitae.com/mi-cartera/pagares

	$random = $random + 1;	
	$url = array_shift($this->urlSequence);
	$str = $this->getCompanyWebpage($url);			// https://www.comunitae.com/listarParticipaciones.html?method=mostrarBloques&_=1487322997347 

 	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false;
        $spanwa = $this->getElements($dom, "span", "class", "text-muted pull-right");
	$tempArray['global']['myWallet'] = $this->getMonetaryValue($spanwa[0]->nodeValue);


	$random = $random + 1;	
	$url = array_shift($this->urlSequence);		// https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=1&PARAM_PAGINACION_PAGINA_ACTUAL=
	$str = $this->getCompanyWebpage($url . "1");	

	$domAccount = new DOMDocument;
	$domAccount->loadHTML($str);
	$domAccount->preserveWhiteSpace = false;
	$accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
	$index = -1;	
	foreach ($accountSummaries as $key => $account) {
		$investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
		$as = $this->getElements($investmentInfos[0], "a");
		$tempStatus = $this->getLoanState($as[0]->getAttribute("title"));	// status of actual investment
		if ($tempStatus == TERMINATED_OK) {
			continue;									// skip this one as investment has finished
		}		

		$index = $index + 1;
		$data1[$index]['status'] = $tempStatus;	// status of actual investment
		$data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
		$data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
		$tempData = explode("-", $investmentInfos[4]->nodeValue);
		$data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
		$as = $this->getElements($investmentInfos[3], "a");
		$data1[$index]['duration'] = filter_var( $as[2]->nodeValue , FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";		
		$data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);	 		
		$as = $this->getElements($investmentInfos[4], "a");		
		$spans = $this->getElements($investmentInfos[5], "span");		
		$data1[$index]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
		$as = $this->getElements($investmentInfos[0], "a");			
		$data1[$index]['status'] = $this->getLoanState($as[0]->getAttribute("title"));	// status of actual investment
		$tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] + $data1[$key]['profitGained'];
		$tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];			
		$tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$index]['invested'];			
		$tempArray['global']['totalPercentage'] = $tempArray['global']['totalPercentage'] + $data1[$index]['interest'];
	}
	
	$tempArray['global']['activeInInvestments'] = $tempArray['global']['totalInvested'] - $tempArray['global']['totalAmortized'];
$this->print_r2($data1);
// Check number of pages. It seems the investments are shown in pages of 15 at the time.
	$pages = $this->getElements($domAccount, "ul", "class", "pagination no-margin");
echo __FILE__ . " " . __LINE__ . "<br>";
	if (empty($pages)) {
		$numberOfPages = 1;
	}
	else {
		$as = $this->getElements($pages[0], "a");
		$numberOfPages = count($as);
	}
echo __FILE__ . " " . __LINE__ . "$number of pages = $numberOfPages<br>";
	if ($numberOfPages > 1) {
		for ($i = 2; $i <= $numberOfPages; $i++)  {
			$str = $this->getCompanyWebpage($url . $i);	// https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=2&PARAM_PAGINACION_PAGINA_ACTUAL=
			$domAccount = new DOMDocument;
			$domAccount->loadHTML($str);
			$domAccount->preserveWhiteSpace = false;
			$accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
			
			foreach ($accountSummaries as $key => $account) {
				$index = $index + 1;
				$investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
	//			foreach ($investmentInfos as $summary){
					$data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
					$data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
					$tempData = explode("-", $investmentInfos[4]->nodeValue);
					$data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
					$as = $this->getElements($investmentInfos[3], "a");
					$data1[$index]['duration'] = filter_var( $as[2]->nodeValue , FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";		
					$data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);	 		
					$as = $this->getElements($investmentInfos[4], "a");		
					$spans = $this->getElements($investmentInfos[5], "span");		
					$data1[$index]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
					$as = $this->getElements($investmentInfos[0], "a");
					$data1[$index]['status'] = $this->getLoanState($as[0]->getAttribute("title"));	// status of actual investment
	//			}
				$tempArray['global']['totalEarnedInterest'] = $tempArray['global']['totalEarnedInterest'] +
																$data1[$key]['profitGained'];	
				$tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];			
				$tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$index]['invested'];
				$tempArray['global']['totalPercentage'] = $tempArray['global']['totalPercentage'] + $data1[$index]['interest'];			
			}
		}
	}
	$tempArray['global']['activeInInvestments'] = $tempArray['global']['totalInvestment'] - $tempArray['global']['totalAmortized'];	
	$tempArray['global']['profitibility'] = (int) ($tempArray['global']['totalPercentage'] / ($tempArray['global']['investments'] = $index + 1));
$this->print_r2($tempArray);

//$this->getElements($investmentInfos[15], "span");	// 	Generate Error
	

//=================================================================================
/*		
// Get the investments which DEFAULTED  NOT YET CORRECT, first page
	$random = $random + 1;	
	$url = array_shift($this->urlSequence);
echo __FILE__ . " " . __LINE__  . " 666<br>";	
	$str = $this->getCompanyWebpage($url . "1");
//echo "666 " . $str;
	$domAccount = new DOMDocument;
	$domAccount->loadHTML($str);
	$domAccount->preserveWhiteSpace = false;
	$accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
	
	foreach ($accountSummaries as $key1 => $account) {
		$index = $index + 1;
		$investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
		foreach ($investmentInfos as $summary){
			$data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
			$data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
			$tempData = explode("-", $investmentInfos[4]->nodeValue);
			$data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
			$as = $this->getElements($investmentInfos[3], "a");
			$data1[$index]['duration'] = filter_var( $as[2]->nodeValue , FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";		
			$data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);	 		
			$as = $this->getElements($investmentInfos[4], "a");		
			$spans = $this->getElements($investmentInfos[5], "span");		
			$data1[$index]['invested'] = $this->getMonetaryValue($spans[0]->nodeValue);
			$data1[$index]['status'] = PENDING;
			
			$tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];			
			$tempArray['global']['totalInvestment'] = $tempArray['global']['totalInvestment'] + $data1[$index]['invested'];			
			$tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments']
															+ $data1[$index]['invested'] - $data1[$index]['amortized'];
		}
	}


	
// Check number of pages. It seems the investments are shown in pages of 15 at the time.
	$pages = $this->getElements($domAccount, "ul", "class", "pagination no-margin");
	$as = $this->getElements($pages[0], "a");
	$numberOfPages = count($as);

	if ($numberOfPages > 1) {
		for ($i = 2; $i <= $numberOfPages; $i++)  {
			$str = $this->getCompanyWebpage($url . $i);	// https://www.comunitae.com/listarParticipaciones.html?method=irPestanaListado&id=2&PARAM_PAGINACION_PAGINA_ACTUAL=
			$domAccount = new DOMDocument;
			$domAccount->loadHTML($str);
			$domAccount->preserveWhiteSpace = false;
			$accountSummaries = $this->getElements($domAccount, "article", "class", "row panel-row prestamo");
			$tempArray['global']['totalInvested'] = 0;
			
			foreach ($accountSummaries as $key => $account) {
				$index = $index + 1;
				$investmentInfos = $this->getElements($account, "p", "class", "form-control-static");
				foreach ($investmentInfos as $summary){
					$data1[$index]['loanId'] = trim($investmentInfos[1]->nodeValue);
					$data1[$index]['name'] = trim($investmentInfos[2]->nodeValue);
					$tempData = explode("-", $investmentInfos[4]->nodeValue);
					$data1[$index]['date'] = $this->getSpanishMonthNumber(trim($tempData[0])) . "-" . trim($tempData[1]);
					$as = $this->getElements($investmentInfos[3], "a");
					$data1[$index]['duration'] = filter_var( $as[2]->nodeValue , FILTER_SANITIZE_NUMBER_INT) . " D&iacute;as";		
					$data1[$index]['interest'] = $this->getPercentage($as[1]->nodeValue);	 		
					$as = $this->getElements($investmentInfos[4], "a");		
					$spans = $this->getElements($investmentInfos[5], "span");		
					$data1[$index]['investment'] = $this->getMonetaryValue($spans[0]->nodeValue);
					$data1[$index]['status'] = PENDING;
					
					$tempArray['global']['totalAmortized'] = $tempArray['global']['totalAmortized'] + $data1[$index]['amortized'];			
					$tempArray['global']['totalInvested'] = $tempArray['global']['totalInvested'] + $data1[$index]['invested'];			
					$tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments']
																	+ $data1[$index]['invested'] - $data1[$index]['amortized'];
				}
			}
		}
	}
*/

	$tempArray['global']['investments'] = $index + 1;
	$tempArray['investments'] = $data1;
	
//	$tempArray['global']['activeInInvestments'] = $tempArray['global']['activeInInvestments'] + $data1[$key]['amortized'];
//	$tempArray['global']['totalInvested'] = $tempArray['global']['totalInvested'] + $data1[$key]['invested'];

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
function companyUserLogin($user = "", $password = "") {

	$credentials['j_username'] = $user;
	$credentials['j_password'] = $password;

	$str = $this->doCompanyLogin($credentials);

// Check if user actually has entered the portal of the company.
// by means of checking of 2 unique identifiers of the portal
// This should be done by checking a field in the Webpage (button, link etc)
// and the email of the user (if aplicable)
	$dom = new DOMDocument;
	$dom->loadHTML($str);
	$dom->preserveWhiteSpace = false; 

	$confirm = 0;
	$uls = $dom->getElementsByTagName('ul');
	foreach ($uls as $ul) {
		$as = $ul->getElementsByTagName('a');
		foreach ($as as $a) {
			if (strcasecmp (trim($a->nodeValue), trim($user)) == 0) {
				$confirm++;
				break 2;
			}
		}
	}

	$as = $dom->getElementsByTagName('a');
	foreach ($as as $a) {		
		if (strncasecmp (trim($a->getAttribute('href')), "/mi-posicion", 12) === 0) {
			$confirm++;
			break;
		}					
	}
	if ($confirm > 0) {
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





/**
*
*	translate the html of loan state to the winvestify normalized state
*	@param	string		$str html of loanstate
*	@return integer		Normalized state, PENDIENTE, OK, DELAYED_PAYMENT, DEFAULTED
*	only OK is considered so far
*/
function getLoanState($actualState) {	

	$loanStates = array("PENDIENTE" => PENDING,
							"OK" => OK,
							"AMORTIZADO" => TERMINATED_OK,
							"ATRASADO" => PAYMENT_DELAYED,
							"JUDICIAL" => DEFAULTED);

	$actualState = trim($actualState);
	foreach($loanStates as $key => $state) {
		if (strcasecmp($key, $actualState) == 0) {
			return $state;
		}
	}
	return PAYMENT_DELAYED;				// Nothing found so I invent something
}


}

?>